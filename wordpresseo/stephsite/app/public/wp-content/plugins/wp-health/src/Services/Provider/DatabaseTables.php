<?php
namespace WPUmbrella\Services\Provider;

if (!defined('ABSPATH')) {
    exit;
}

class DatabaseTables
{
    const NAME_SERVICE = 'DatabaseTablesProvider';

    public function getTables()
    {
        try {
            global $wpdb;
            $tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'", ARRAY_N);
            return array_reduce($tables, function ($current, $item) {
                array_push($current, $item[0]);
                return $current;
            }, []);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getTablesSize()
    {
        try {
            global $wpdb;
            $tables = $wpdb->get_results("
				SELECT
					TABLE_NAME AS 'table',
					ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024) AS 'size_mb'
				FROM
					information_schema.TABLES
				WHERE
					TABLE_SCHEMA = '{$wpdb->dbname}'
				AND
					TABLE_NAME LIKE '{$wpdb->prefix}%'
				ORDER BY
					(DATA_LENGTH + INDEX_LENGTH)
				DESC
			", ARRAY_A);

            return array_map(function ($item) {
                return [
                    'table' => $item['table'],
                    'size_mb' => (int) $item['size_mb'],
                ];
            }, $tables);
        } catch (\Exception $th) {
            return [];
        }
    }

    public function getColumnCharLengthCheckByTable($table)
    {
        global $wpdb;

        switch ($table) {
            case "{$wpdb->prefix}postmeta":
                return [
                    'column' => 'meta_value',
                    'id' => 'meta_id'
                ];
            case "{$wpdb->prefix}posts":
                return [
                    'column' => 'post_content',
                    'id' => 'ID'
                ];
            default:
                return apply_filters('wp_umbrella_get_column_char_length', [
                    'column' => '',
                    'id' => ''
                ], $table);
        }
    }

    public function getTableSizeDetailWithHighValue($table)
    {
        try {
            global $wpdb;

            $data = $this->getColumnCharLengthCheckByTable($table);
            $column = $data['column'];
            $id = $data['id'];

            // Multiple by 3 for prevent unicode
            $tables = $wpdb->get_results("
				SELECT (CHAR_LENGTH({$column})*3) as bytes, `{$id}` as id
				FROM {$table}
				HAVING bytes IS NOT NULL
			", ARRAY_A);

            return $tables;
        } catch (\Exception $th) {
            return [];
        }
    }

    public function getTableSizeDetailWithNoHighValue($table)
    {
        try {
            global $wpdb;

            $data = $this->getColumnCharLengthCheckByTable($table);
            $column = $data['column'];
            $id = $data['id'];

            // Multiple by 3 for prevent unicode
            $tables = $wpdb->get_results("
				SELECT (CHAR_LENGTH({$column})*3) as bytes, `{$id}` as id
				FROM {$table}
				HAVING bytes IS NULL
			", ARRAY_A);

            return $tables;
        } catch (\Exception $th) {
            return [];
        }
    }

    public function getCountRows($table)
    {
        global $wpdb;
        $result = $wpdb->get_row("SELECT COUNT(*) as 'count' FROM {$table}", ARRAY_A);
        return $result['count'];
    }
}
