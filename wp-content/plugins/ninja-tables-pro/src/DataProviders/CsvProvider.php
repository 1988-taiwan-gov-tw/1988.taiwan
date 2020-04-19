<?php

namespace NinjaTablesPro\DataProviders;

class CsvProvider
{
    public function boot()
    {
        add_filter('ninja_tables_get_table_google-csv', array($this, 'getTableSettings'));
        add_filter('ninja_tables_get_table_csv', array($this, 'getTableSettings'));
        add_filter('ninja_tables_get_table_data_google-csv', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_get_table_data_csv', array($this, 'getTableData'), 10, 4);
        add_filter('ninja_tables_fetching_table_rows_csv', array($this, 'data'), 10, 5);

        add_action('wp_ajax_ninja_table_external_data_source_create', array($this, 'createTableWithExternalDataSource'));

        add_filter('ninja_table_activated_features', function ($features) {
            $features['external_data_source'] = true;
            return $features;
        });

    }

    public function createTableWithExternalDataSource()
    {
        if (!current_user_can(ninja_table_admin_role())) {
            return;
        }

        $tableCreated = false;
        $tableId = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : null;
        $url = isset($_REQUEST['remoteURL']) ? $_REQUEST['remoteURL'] : $_REQUEST['remote_url'];

        $messages = array();
        // Validate Title
        if (!$tableId && empty($_REQUEST['post_title'])) {
            $messages['title'] = __('The title field is required.', 'ninja-tables');
        }

        // Validate URL
        if (empty($url) || !ninja_tables_is_valid_url($url)) {
            $messages['url'] = __('The url field is empty or invalid.', 'ninja-tables');
        }

        // If Validation failed
        if (array_filter($messages)) {
            wp_send_json_error(array('message' => $messages), 422);
            wp_die();
        }

        // Ensure the correct url if requesting goggle spreadsheet
        if (($type = $_REQUEST['type']) == 'google-csv') {
            $parsedUrl = parse_url($url);
            parse_str($parsedUrl['query'], $query);
            unset($query['output']);
            $query = build_query($query);
            $path = substr($parsedUrl['path'], 0, strrpos($parsedUrl['path'], '/'));
            $url = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . $path . '/pub?' . $query;
            $url .= '&output=csv';
        }

        // For csv data type (google or other)
        if (in_array($type, array('csv', 'google-csv'))) {
            if (!empty($_REQUEST['get_headers_only'])) {
                // Make sure no error occured from wp_remote_get
                add_filter('https_ssl_verify', '__return_false');
                $timeOut = apply_filters('ninja_tables_remote_csv_timeout', 10);
                $response = wp_remote_get($url, ['timeout' => $timeOut]);
                if (is_wp_error($response)) {
                    wp_send_json_error(array(
                        'message' => array(
                            'error' => __($response->get_error_message(), 'ninja-tables'),
                        )
                    ), 400);
                    wp_die();
                }

                $headers = $response['headers'];

                if (
                    strpos($headers['content-type'], 'csv') !== false ||
                    $headers['content-type'] == 'application/octet-stream' ||
                    $headers['content-type'] == 'application/binary'
                ) {
                    $headers = \League\Csv\Reader::createFromString(
                        $response['body']
                    )->fetchOne();

                    $formattedHeader = array();
                    foreach ($headers as $header) {
                        $formattedHeader[$header] = $header;
                    }
                    wp_send_json_success($formattedHeader);
                } else {
                    wp_send_json_error(array(
                        'message' => array(
                            'error' => __(
                                'Expected CSV but received invalid data type from the given url.',
                                'ninja-tables'
                            ),
                        )
                    ), 400);
                }
                wp_die();
            }


            $fields = array_map(function ($field) {
                return trim($field['name']);
            }, $_REQUEST['fields']);

            // Validate Fields
            if (empty($_REQUEST['fields'])) {
                $messages['fields'] = __('No fields were selected / no changes made', 'ninja-tables');
                if (array_filter($messages)) {
                    wp_send_json_error(array('message' => $messages), 422);
                    wp_die();
                }
            }

            $headers = ninja_table_format_header($fields);

            $columns = array();

            foreach ($headers as $key => $column) {
                $columns[] = array(
                    'name'                => $column,
                    'key'                 => $key,
                    'breakpoints'         => null,
                    'data_type'           => 'text',
                    'dateFormat'          => null,
                    'header_html_content' => null,
                    'enable_html_content' => false,
                    'contentAlign'        => null,
                    'textAlign'           => null,
                    'original_name'       => $column
                );
            }


            if ($tableId) {
                $oldColumns = get_post_meta(
                    $tableId, '_ninja_table_columns', true
                );
                foreach ($columns as $key => $newColumn) {
                    foreach ($oldColumns as $oldColumn) {
                        if ($oldColumn['original_name'] == $newColumn['original_name']) {
                            $columns[$key] = $oldColumn;
                        }
                    }
                }
                // Reset/Reorder array indices
                $columns = array_values($columns);
            } else {
                $tableId = $this->saveTable();
                $tableCreated = true;
                $tableSettings = ninja_table_get_table_settings($tableId, 'admin');
                $tableSettings['caching_interval'] = 5;
                update_post_meta($tableId, '_ninja_table_settings', $tableSettings);
            }
            update_post_meta($tableId, '_ninja_table_columns', $columns);
            update_post_meta($tableId, '_ninja_tables_data_provider', $type);
            update_post_meta($tableId, '_ninja_tables_data_provider_url', $url);

            $message = 'Table Successfully updated';

            if ($tableCreated) {
                $message = 'Table successfully created';
            }

            wp_send_json_success(array(
                'ID'            => $tableId,
                'message'       => $message,
                'remote_url'    => $url,
                'table_created' => $tableCreated
            ));
        }
    }

    public function getTableSettings($table)
    {
        $table->isEditable = false;
        $table->dataSourceType = get_post_meta($table->ID, '_ninja_tables_data_provider', true);
        $table->remoteURL = get_post_meta($table->ID, '_ninja_tables_data_provider_url', true);
        $table->isEditableMessage = 'You may edit your table settings here.';
        $table->isExportable = true;
        $table->isImportable = false;
        $table->isSortable = false;
        $table->hasCacheFeature = false;
        $table->isCreatedSortable = false;
        $table->hasExternalCachingInterval = true;
        return $table;
    }

    public function getTableData($data, $tableId, $perPage, $offset)
    {
        $newData = array();

        $cachedData = ninjaTableGetExternalCachedData($tableId);

        if ($cachedData) {
            $csvData = $cachedData;
        } else {
            $url = get_post_meta($tableId, '_ninja_tables_data_provider_url', true);
            $csvData = $this->getDataFromCsv($tableId, $url);

            if ($csvData) {
                ninjaTableSetExternalCacheData($tableId, $csvData);
            }
        }

        $totalData = count($csvData);

        $responseData = array_slice($csvData, $offset, $perPage);

        foreach ($responseData as $key => $value) {
            $newData[] = array(
                'id'       => $key + 1,
                'values'   => $value,
                'position' => $key + 1,
            );
        }
        return array(
            $newData,
            $totalData
        );
    }

    public function data($data, $tableId, $defaultSorting, $limitEntries = false, $skip = false)
    {
        if (!$limitEntries && !$skip) {
            $cachedData = ninjaTableGetExternalCachedData($tableId);
            if ($cachedData) {
                return $cachedData;
            }
        }

        $url = get_post_meta($tableId, '_ninja_tables_data_provider_url', true);

        $cachedData = ninjaTableGetExternalCachedData($tableId);

        if ($cachedData) {
            $csvData = $cachedData;
        } else {
            $csvData = $this->getDataFromCsv($tableId, $url);

            if ($csvData) {
                ninjaTableSetExternalCacheData($tableId, $csvData);
            }

        }

        if ($skip || $limitEntries) {
            $csvData = array_slice($csvData, $skip, $limitEntries);
        }

        return $url ? $csvData : $data;
    }

    protected function getDataFromCsv($tableId, $url)
    {
        $columns = array();
        foreach (ninja_table_get_table_columns($tableId) as $column) {
            $columns[$column['original_name']] = $column;
        }
        return array_map(function ($row) use ($columns) {
            $newRow = array();
            foreach ($columns as $key => $column) {
                $newRow[$column['key']] = $row[$key];
            }
            return $newRow;
        }, $this->csvToArray($url));
    }

    protected function csvToArray($url)
    {
        add_filter('https_ssl_verify', '__return_false');
        $timeOut = apply_filters('ninja_tables_remote_csv_timeout', 20);
        $response = wp_remote_get($url, ['timeout' => $timeOut]);
        remove_filter('https_ssl_verify', '__return_false');

        if (is_wp_error($response)) {
            return array();
        }

        if (!class_exists('\League\Csv\Reader')) {
            return array();
        }

        try {
            $reader = \League\Csv\Reader::createFromString($response['body'])->fetchAll();
        } catch (\Exception $exception) {
            return array();
        }

        $data = array();
        $header = array_shift($reader);
        foreach ($reader as $row) {
            $data[] = array_combine($header, $row);
        }

        return $data;
    }

    protected function saveTable($postId = null)
    {
        $attributes = array(
            'post_title'   => sanitize_text_field($this->get($_REQUEST, 'post_title')),
            'post_content' => wp_kses_post($this->get($_REQUEST, 'post_content')),
            'post_type'    => 'ninja-table',
            'post_status'  => 'publish'
        );

        if (!$postId) {
            $postId = wp_insert_post($attributes);
        } else {
            $attributes['ID'] = $postId;
            wp_update_post($attributes);
        }
        return $postId;
    }

    protected function get($array, $key, $default = false)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }
}
