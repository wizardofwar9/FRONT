<?php
namespace WPUmbrella\Core\Backup\Source;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupSource;
use WPUmbrella\Models\Backup\BackupProcessCommandLine;
use Coderatio\SimpleBackup\SimpleBackup;

class MySqlManualSource implements BackupSource, BackupProcessCommandLine
{
    const DEFAULT_USER = 'root';
    const DEFAULT_TIMEOUT = 900;

    protected $name;
    protected $database;
    protected $host;
    protected $user;
    protected $password;
    protected $timeout;

    /**
     * @param string      $name
     * @param string      $database
     * @param array       $databaseConnexion
     * @param array       $sshConnexion
     * @param int         $timeout
     */
    public function __construct($namer, $database, $databaseConnexion = [], $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->namer = $namer;
        $this->database = $database;
        $this->host = isset($databaseConnexion['host']) ? $databaseConnexion['host'] : self::DEFAULT_USER;
        $this->user = isset($databaseConnexion['user']) ? $databaseConnexion['user'] : null;
        $this->password = isset($databaseConnexion['password']) ? $databaseConnexion['password'] : null;
        $this->timeout = $timeout;
    }

    public function getTimeout()
    {
        return apply_filters('wp_umbrella_backup_source_mysqlmanual_timeout', $this->timeout);
    }

    public function getCommandLine()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($scratchDir)
    {
        try {
            $destination = sprintf('%s/%s/tables', $scratchDir, $this->getName());

            if (!\file_exists($destination)) {
                @\mkdir($destination, 0777, true);
            }

            \wp_umbrella_get_service('Logger')->info(sprintf('[fetch database manual] %s', $this->getName()));

            $tables = wp_umbrella_get_service('BackupDatabaseConfiguration')->getTablesBatch();

            $simpleBackup = SimpleBackup::setDatabase([
                $this->database,
                $this->user,
                $this->password,
                $this->host
            ]);

            foreach ($tables as $key => $table) {
                $simpleBackup->includeOnly([$table['table']]);

                if (!empty($table['batch'])) {
                    foreach ($table['batch'] as $keyBatch => $value) {
                        $simpleBackup->setTableLimitsOn([
                            $table['table'] => sprintf('%s, %s', $value['offset'], $value['limit'])
                        ]);
                        $simpleBackup->storeAfterExportTo($destination, \sprintf('%s-part-%s', $table['table'], $keyBatch + 1));
                    }
                } else {
                    $simpleBackup->storeAfterExportTo($destination, \sprintf('%s', $table['table']));
                }
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->namer->getName();
    }
}
