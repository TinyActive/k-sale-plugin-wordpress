<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class LocationController extends BaseController
{
    protected $table_name = 'Location';
    protected $key = "LocationID";

    public function register_routes($a = [])
    {
        $data_router = [
            [
                'path' => '/save-location',
                'methods' => 'POST',
                'callback' => [$this, 'saveLocation'],
            ],
        ];

        parent::register_routes($data_router);
    }

    protected function base_get_query($query)
    {
        $tableName = $this->get_table_name("table");
        return $wpdb->prepare(
            "SELECT wksl.*, COUNT(wkst.TableID) as NumberOfTable FROM $this->table_name wksl LEFT JOIN $tableName wkst ON wksl.LocationID = wkst.LocationID GROUP BY wksl.LocationID LIMIT %d OFFSET %d;",
            $query['limit'], $query['offset']
        );
    }

    /**
     * Xử lý save Location và Table
     */
    public function saveLocation($request)
    {
        global $wpdb;
        $data = $request->get_json_params();
        $location_data = [
            'LocationName' => sanitize_text_field($data['LocationName']),
            'LocationID' => isset($data['LocationID']) ? intval($data['LocationID']) : 0,
        ];
        $seat_data = isset($data['NumberOfSeat']) ? intval($data['NumberOfSeat']) : 0;

        if ($seat_data <= 0) {
            return new WP_Error('invalid_seat_number', 'Number of seats must be greater than 0', ['status' => 400]);
        }

        try {
            $wpdb->query('START TRANSACTION');

            // Xử lý lưu trữ Location
            if ($location_data['LocationID'] > 0) {
                // Update the record
                $wpdb->update($this->table_name, $location_data, ['LocationID' => $location_data['LocationID']]);
            } else {
                // Insert a new record
                $wpdb->insert($this->table_name, $location_data);
                $location_data['LocationID'] = $wpdb->insert_id;
            }

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Xử lý lưu trữ Table
            $tableName = $this->get_table_name("table");

            // Get list of tables in the database with the same LocationID
            $tables = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName WHERE LocationID = %d", $location_data['LocationID']));
            $table_map = [];
            foreach ($tables as $table) {
                $table_map[intval($table->TableName)] = $table;
            }

            for ($i = 1; $i <= $seat_data; $i++) {
                if (isset($table_map[$i])) {
                    // Update the existing record (if needed)
                    // Uncomment and use the following line if you need to update existing tables
                    // $wpdb->update($tableName, ['LocationID' => $location_data['LocationID'], 'TableName' => $i], ['TableID' => $table_map[$i]->TableID]);
                } else {
                    // Insert a new record
                    $wpdb->insert($tableName, [
                        'LocationID' => $location_data['LocationID'],
                        'TableName' => $i,
                    ]);
                }
                if ($wpdb->last_error) {
                    throw new Exception($wpdb->last_error);
                }
            }

            $wpdb->query('COMMIT');

            return $this->ok($location_data);
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return new WP_Error('database_error', $e->getMessage(), ['status' => 500]);
        }
    }
}
?>
