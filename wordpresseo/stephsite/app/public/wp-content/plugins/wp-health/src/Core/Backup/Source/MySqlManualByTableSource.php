<?php
namespace WPUmbrella\Core\Backup\Source;

if (!defined('ABSPATH')) {
    exit;
}

use WPUmbrella\Models\Backup\BackupSource;
use WPUmbrella\Models\Backup\BackupProcessCommandLine;
use Coderatio\SimpleBackup\SimpleBackup;

class MySqlManualByTableSource implements BackupSource, BackupProcessCommandLine
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

		$dataObj = wp_umbrella_get_service('BackupBatchData')->getData('database');

        try {
            $destination = sprintf('%s/%s/tables', $scratchDir, $this->getName());

            if (!\file_exists($destination)) {
                @\mkdir($destination, 0777, true);
            }

            \wp_umbrella_get_service('Logger')->info(sprintf('[fetch database manual] %s', $this->getName()));

            $current = $dataObj->getCurrentBatchProcessorSql();

            \wp_umbrella_get_service('Logger')->info(sprintf('[current iterator database] %s', $current));

            $tables = wp_umbrella_get_service('BackupDatabaseConfiguration')->getTablesBatch();

            $simpleBackup = SimpleBackup::setDatabase([
                $this->database,
                $this->user,
                $this->password,
                $this->host
            ]);

            if (!isset($tables[$current])) {
                $dataObj->setIteratorPositionSql($current + 1); // not possible ; next table
                return [
                    'success' => true,
                    'iterator_position' => ++$current,
                    'processed_data' => $dataObj
                ];
            }

            $table = $tables[$current];
            $simpleBackup->includeOnly([$table['table']]);
            if (!empty($table['batch'])) {
                $part = $dataObj->getPartBatchSqlProcessor();
                \wp_umbrella_get_service('Logger')->info(sprintf('[current part database] %s', $part));

                if (!isset($table['batch'][$part])) {
                    $dataObj->setIteratorPositionSql($current + 1); // not possible ; next table
                    return [
                        'success' => true,
                        'iterator_position' => ++$current,
                        'processed_data' => $dataObj
                    ];
                }

                $item = $table['batch'][$part];

                $simpleBackup->setTableConditions([
                    $table['table'] => sprintf('%s %s', $table['id'], "IN ('" . implode("','", $item) . "')")
                ]);

                $simpleBackup->storeAfterExportTo($destination, \sprintf('%s-part-%s', $table['table'], $part + 1));

                if (count($table['batch']) === (int) $part + 1) {
                    $dataObj->setIteratorPositionSql($current + 1); // Next table
                } else {
                    $dataObj->setPartBatchSqlProcessor($part + 1); // Next batch table
                }
                \wp_umbrella_get_service('Logger')->info(sprintf('[Finish part database and next step] %s', $part + 1));
            } else {
                $simpleBackup->storeAfterExportTo($destination, \sprintf('%s', $table['table']));
                $dataObj->setIteratorPositionSql($current + 1); // Next table
                $dataObj->setPartBatchSqlProcessor(0); // Prevent batch table
            }
        } catch (\Exception $e) {
            \wp_umbrella_get_service('Logger')->error($e->getMessage());
            $dataObj->setIteratorPositionSql($current + 1); // Next table
            return [
                'success' => false,
                'iterator_position' => ++$current,
                'processed_data' => $dataObj
            ];
        }

        return [
            'success' => true,
            'iterator_position' => ++$current,
            'processed_data' => $dataObj
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->namer->getName();
    }
}
