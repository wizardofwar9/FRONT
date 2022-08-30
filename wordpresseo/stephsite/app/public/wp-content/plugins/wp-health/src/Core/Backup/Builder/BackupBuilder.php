<?php
namespace WPUmbrella\Core\Backup\Builder;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Core\Backup\Profile;
use WPUmbrella\Core\Backup\Namer\BackupNamer;
use WPUmbrella\Core\Backup\Source\RsyncSource;
use WPUmbrella\Core\Backup\Source\FinderBySizeSource;
use WPUmbrella\Core\Backup\Source\FinderByFileSource;
use WPUmbrella\Core\Backup\Source\MySqlManualSource;
use WPUmbrella\Core\Backup\Source\MySqlManualByTableSource;
use WPUmbrella\Core\Backup\Source\MySqlDumpSource;
use WPUmbrella\Core\Backup\Processor\ZipArchiveProcessor;
use WPUmbrella\Core\Backup\Processor\GzipArchiveProcessor;
use WPUmbrella\Core\Backup\Processor\ZipPhpArchiveProcessor;
use WPUmbrella\Core\Backup\Destination\UmbrellaDestination;
use WPUmbrella\Models\Backup\BackupBuilder as BackupBuilderModel;

class BackupBuilder implements BackupBuilderModel
{
    protected $mainDestination = null;

    protected $processor = null;

    const SAFE_TYPE_FILE = 'finder-by-size';

    const DEFAULT_TYPE_FILE = 'finder-by-size';

    const DEFAULT_TYPE_SQL = 'mysqlmanual';

    const DEFAULT_PROCESSOR = 'zip-php';

    public function getBuildsProcessor()
    {
        return ['zip-php', 'zip', 'gzip'];
    }

    public function getBuildsSql()
    {
        return ['mysqlmanual', 'mysqldump'];
    }

    public function getBuildsFile()
    {
        return ['rsync', 'finder-by-file'];
    }

    public function reset()
    {
        $this->processor = null;
        $this->mainDestination = null;
        $this->fileSource = null;
        $this->sqlSource = null;
        $this->namer = null;
        $this->profile = null;
    }

    public function getDefaultSourceSql()
    {
        return apply_filters('wp_umbrella_backup_default_source_sql', self::DEFAULT_TYPE_SQL);
    }

    public function getDefaultSourceFile()
    {
        return apply_filters('wp_umbrella_backup_default_source_files', self::DEFAULT_TYPE_FILE);
    }

    public function getDefaultProcessor()
    {
        return apply_filters('wp_umbrella_backup_default_processor', self::DEFAULT_PROCESSOR);
    }

    public function buildNamer($name)
    {
        $namer = new BackupNamer();
        $namer->setName($name);

        $this->namer = $namer;

        return $this;
    }

    public function buildFileSource($options = [], $buildsAvailable = null)
    {
        $incrementalDate = isset($options['incremental_date']) ? $options['incremental_date'] : null;

        $type = isset($options['type']) ? $options['type'] : $this->getDefaultSourceFile();
        if ($type === null || empty($type)) {
            $type = $this->getDefaultSourceFile();
        }

        if ($incrementalDate) {
            $type = self::SAFE_TYPE_FILE;
        }

        $buildsAvailable = $buildsAvailable === null ? $this->getBuildsFile() : $buildsAvailable;

        $baseDirectory = isset($options['base_directory']) ? $options['base_directory'] : ABSPATH;
        if ($baseDirectory === null || empty($baseDirectory)) {
            $baseDirectory = ABSPATH;
        }

        $source = null;
        switch ($type) {
            case 'rsync':
                $source = new RsyncSource($this->namer, $baseDirectory);
                break;
            case 'finder-by-file':
                $source = new FinderByFileSource($this->namer, $baseDirectory);
                if ($incrementalDate) {
                    $source->setSinceDate($incrementalDate);
                }
                if (isset($options['size'])) {
                    $source->setSize($options['size']);
                }
                break;
            case 'finder-by-size':
            default:
                $source = new FinderBySizeSource($this->namer, $baseDirectory);
                if ($incrementalDate) {
                    $source->setSinceDate($incrementalDate);
                }
                if (isset($options['size'])) {
                    $source->setSize($options['size']);
                }
                break;
        }

        if ($source === null) {
            return $this;
        }

        $canExecute = wp_umbrella_get_service('CheckBackupProcessAvailable')->canExecuteSource($source);

        if (!$canExecute) {
            $buildsAvailable = array_diff($buildsAvailable, [$type]);
            if (!empty($buildsAvailable)) {
                $type = array_shift($buildsAvailable);
                $options['type'] = $type;
                $this->buildProcessor($options, $buildsAvailable);
                return $this;
            } else {
                $options['type'] = self::SAFE_TYPE_FILE;
                $this->buildProcessor($options, $buildsAvailable);
                return $this;
            }
        }

        $this->fileSource = $source;

        return $this;
    }

    public function buildSqlSource($options = [], $buildsAvailable = null)
    {
        $type = isset($options['type']) ? $options['type'] : $this->getDefaultSourceSql();
        if ($type === null || empty($type)) {
            $type = $this->getDefaultSourceSql();
        }

        $buildsAvailable = $buildsAvailable === null ? $this->getBuildsSql() : $buildsAvailable;

        $database = isset($options['database']) ? $options['database'] : DB_NAME;
        $databaseUser = isset($options['user']) ? $options['user'] : DB_USER;
        $databasePassword = isset($options['password']) ? $options['password'] : DB_PASSWORD;
        $databaseHost = isset($options['host']) ? $options['host'] : DB_HOST;

        $source = null;
        switch ($type) {
            case 'mysqldump':
                $source = new MySqlDumpSource($this->namer, $database, [
                    'user' => $databaseUser,
                    'password' => $databasePassword,
                    'host' => $databaseHost,
                ]);
                break;
            case 'mysqlmanual':
                $source = new MySqlManualSource($this->namer, $database, [
                    'user' => $databaseUser,
                    'password' => $databasePassword,
                    'host' => $databaseHost,
                ]);
                break;
            case 'mysqlmanual-by-table':
                $source = new MySqlManualByTableSource($this->namer, $database, [
                    'user' => $databaseUser,
                    'password' => $databasePassword,
                    'host' => $databaseHost,
                ]);
                break;
        }

        if ($source === null) {
            return $this;
        }

        $canExecute = wp_umbrella_get_service('CheckBackupProcessAvailable')->canExecuteSource($source);

        if (!$canExecute) {
            $buildsAvailable = array_diff($buildsAvailable, [$type]);
            if (!empty($buildsAvailable)) {
                $type = array_shift($buildsAvailable);
                $options['type'] = $type;
                $this->buildProcessor($options, $buildsAvailable);
                return $this;
            } else {
                $source = null;
            }
        }

        $this->sqlSource = $source;
        return $this;
    }

    public function buildProcessor($options = [], $buildsAvailable = null)
    {
        $type = isset($options['type']) ? $options['type'] : $this->getDefaultProcessor();
        $buildsAvailable = $buildsAvailable === null ? $this->getBuildsProcessor() : $buildsAvailable;

        $processor = null;
        switch ($type) {
            case 'zip':
                $processor = new ZipArchiveProcessor($this->namer);
                break;
            case 'gzip':
                $processor = new GzipArchiveProcessor($this->namer);
                break;
            case 'zip-php':
                $processor = new ZipPhpArchiveProcessor($this->namer);
                break;
                ;
        }

        if ($processor === null) {
            return $this;
        }

        $canExecute = wp_umbrella_get_service('CheckBackupProcessAvailable')->canExecuteProcessor($processor);

        if (!$canExecute) {
            $buildsAvailable = array_diff($buildsAvailable, [$type]);
            if (!empty($buildsAvailable)) {
                $type = array_shift($buildsAvailable);
                $options['type'] = $type;
                $this->buildProcessor($options, $buildsAvailable);
                return $this;
            } else {
                $processor = null;
            }
        }

        $this->processor = $processor;
        return $this;
    }

    /**
     *
     * @param array $options
     * @return BackupBuilder
     */
    public function buildDestination($options = [])
    {
        $type = isset($options['type']) ? $options['type'] : 'umbrella';

        $destination = null;
        switch ($type) {
            case 'umbrella':
                $destination = new UmbrellaDestination($this->namer);
                break;
        }

        $this->mainDestination = $destination;
        return $this;
    }

    /**
     *
     * @return void
     */
    public function buildProfile()
    {
        if ($this->namer === null) {
            return;
        }

        $sources = [];
        if ($this->fileSource !== null) {
            $sources[] = $this->fileSource;
        }

        if ($this->sqlSource !== null) {
            $sources[] = $this->sqlSource;
        }

        $sources = apply_filters('wp_umbrella_build_profile_backup_sources', $sources);

        $destinations = [];
        if ($this->mainDestination !== null) {
            $destinations[] = $this->mainDestination;
        }

        $destinations = apply_filters('wp_umbrella_build_profile_backup_destinations', $destinations);

        $this->profile = new Profile($this->namer, WP_UMBRELLA_DIR_SCRATCH_BACKUP, $this->processor, $sources, $destinations);
    }

    /**
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }
}
