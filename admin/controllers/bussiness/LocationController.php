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
        return "SELECT wksl.*, COUNT(wkst.TableID) as NumberOfTable FROM $this->table_name wksl LEFT JOIN $tableName wkst ON wksl.LocationID = wkst.LocationID GROUP BY wksl.LocationID LIMIT %d OFFSET %d;";
    }

    /**
     * Xử lý save Location và Table
     */
    public function saveLocation($request)
    {
        global $wpdb;
        $data = $request->get_json_params();
        $location_data = [
            'LocationName' => $data['LocationName'],
            'LocationID' => $data['LocationID'] ?? 0,
        ];
        $seat_data = (int)$data['NumberOfSeat'];
        try {
            $wpdb->query('START TRANSACTION');

            // Xử lý lưu trữ Location
            if ($location_data['LocationID'] > 0) {
                // Update the record
                $wpdb->update($this->table_name, $location_data, ['LocationID' => $location_data['LocationID']]);
            } else {
                // Insert a new record
                $wpdb->insert($this->table_name, $location_data);
                $data[$this->key] = $wpdb->insert_id;
            }

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Xử lý lưu trữ Table
            $tableName = $this->get_table_name("table");

            // I want to handle if exist TableID then update else insert
            // Get list of table in database has LocationID = $data['LocationID']
            $tables = $wpdb->get_results($wpdb->prepare("SELECT * FROM $tableName WHERE LocationID = %d", $data['LocationID']));
            for ($i = 1; $i <= $seat_data; $i++) {
                // if exist in $tables then update else insert
                $existing = null;
                
                foreach ($tables as $table) {
                    if ($table->TableName == $i) {
                        $existing = $table;
                        break;
                    }
                }

                if ($existing != null) {
                    // // Update the existing record
                    // $wpdb->update($tableName, [
                    //     'LocationID' => $data[$this->key],
                    //     'TableName' => $i,
                    // ], [
                    //     'TableID' => $existing->TableID,
                    // ]);
                } else {
                    // Insert a new record
                    $wpdb->insert($tableName, [
                        'LocationID' => $data[$this->key],
                        'TableName' => $i,
                    ]);
                }
                if ($wpdb->last_error) {
                    throw new Exception($wpdb->last_error);
                }
            }

            $wpdb->query('COMMIT');

            return $this->ok($data);
        } catch (Exception $e) {
            //throw $th;
        }
    }

}
?>