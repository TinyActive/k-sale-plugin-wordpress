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
        $oder_table = $wpdb->prefix . "k_sale_order";
        $locationID = $request['LocationID'];
        $tableName = $this->get_table_name("table");
        $query = "SELECT * FROM $tableName WHERE LocationID = %d";
        $query = "SELECT  wkst.TableID,wkst.TableName,wkso.Status,wkso.OrderDate  from $tableName wkst
        LEFT JOIN $oder_table wkso ON wkst.LocationID = wkso.LocationID AND wkst.TableID = wkso.TableID AND wkso.Status = 3
        WHERE wkst.LocationID = %d
        GROUP BY wkst.TableName, wkso.Status, wkst.TableID,wkso.OrderDate";
        $tables = $wpdb->get_results($wpdb->prepare($query, $locationID));
        return $this->ok($tables);
    }


}
?>