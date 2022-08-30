<?php
namespace WPUmbrella\Services\Backup;

if (!defined('ABSPATH')) {
    exit;
}

class BackupDatabaseConfiguration
{
    const MO_IN_BYTES = 1048576;

    protected function getHighDataTables()
    {
        global $wpdb;

        return apply_filters('wp_umbrella_get_high_data_tables', [
            "{$wpdb->prefix}posts",
            "{$wpdb->prefix}postmeta",
        ]);
    }

    public function getTablesConfiguration($excludeTables)
    {
        $data = wp_umbrella_get_service('DatabaseTablesProvider')->getTablesSize();

        foreach ($data as $key => $value) {
            if (in_array($value['table'], $excludeTables, true)) {
                unset($data[$key]);
                continue;
            }
        }

        return $data;
    }

    public function getTablesBatch(): array
	{
        $memoryLimit = wp_umbrella_get_service('WordPressProvider')->getMemoryLimitBytes();

        $maximumMemory = apply_filters('wp_umbrella_maximum_memory_limit_database', $memoryLimit / 2); // Bytes
        $maximumMemoryMo = $maximumMemory / self::MO_IN_BYTES; // Mo

        \wp_umbrella_get_service('Logger')->info(sprintf('[maximum memory] %s', $maximumMemory));

        $excludeTables = wp_umbrella_get_service('BackupBatchData')->getData('database')->getExcludeTables();
        $data = $this->getTablesConfiguration($excludeTables);

        $highTables = $this->getHighDataTables();
        foreach ($data as $key => $value) {
            if ((int) $value['size_mb'] <= $maximumMemoryMo) {
                continue;
            }

            if (!in_array($value['table'], $highTables, true)) {
                continue;
            }

            $rows = wp_umbrella_get_service('DatabaseTablesProvider')->getTableSizeDetailWithHighValue($value['table']);
            $columnData = wp_umbrella_get_service('DatabaseTablesProvider')->getColumnCharLengthCheckByTable($value['table']);
            $batch = [];

            $currentBatchBytes = 0;
            $ids = [];

            foreach ($rows as $row) {
                if ($currentBatchBytes + $row['bytes'] > $maximumMemory) {
                    $batch[] = $ids;
                    $currentBatchBytes = 0;
                    $ids = [];
                }

                $currentBatchBytes += (int) $row['bytes'];
                $ids[] = $row['id'];
            }

            $rowsWithoutHighContent = wp_umbrella_get_service('DatabaseTablesProvider')->getTableSizeDetailWithNoHighValue($value['table']);

            $currentBatchBytes = 0;
            $ids = [];

            foreach ($rowsWithoutHighContent as $row) {
                if ($currentBatchBytes + $row['bytes'] > $maximumMemory) {
                    $currentBatchBytes = 0;
                    $ids = [];
                }

                $currentBatchBytes += (int) $row['bytes'];
                $ids[] = $row['id'];
            }

            $batch[] = $ids;

            $data[$key]['batch'] = $batch;
            $data[$key]['id'] = $columnData['id'];
        }

        return $data;
    }
}
