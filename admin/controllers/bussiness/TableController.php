<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class TableController extends BaseController
{
    protected $table_name = 'Table';
    protected $key = "TableID";

    public function register_routes($a = [])
    {
        $data_router = [
            [
                'path' => '/get-table-by-location',
                'methods' => 'GET',
                'callback' => [$this, 'getTableByLocation'],
            ],
        ];

        parent::register_routes($data_router);
    }

    public function getTableByLocation($request)
    {
        global $wpdb;

        // Validate and sanitize LocationID
        $locationID = intval($request['LocationID']);
        if (!$locationID) {
            return $this->error('Invalid LocationID', 400);
        }

        // Define table names
        $order_table = $wpdb->prefix . "k_sale_order";
        $table_name = $this->get_table_name("table");

        // Prepare the query
        $query = "
            SELECT wkst.TableID, wkst.TableName, wkso.Status, wkso.OrderDate 
            FROM $table_name wkst
            LEFT JOIN $order_table wkso 
            ON wkst.LocationID = wkso.LocationID 
            AND wkst.TableID = wkso.TableID 
            AND wkso.Status = 3
            WHERE wkst.LocationID = %d
            GROUP BY wkst.TableID, wkst.TableName, wkso.Status, wkso.OrderDate
        ";

        try {
            // Execute the query
            $tables = $wpdb->get_results($wpdb->prepare($query, $locationID));
            return $this->ok($tables);
        } catch (Exception $e) {
            return $this->error('Failed to fetch tables', 500, $e->getMessage());
        }
    }
}
?>
