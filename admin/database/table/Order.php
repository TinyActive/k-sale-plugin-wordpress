<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class Order extends Migration
{
    protected $table_name = 'order';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $sql = "CREATE TABLE {$this->table_name} (
            OrderID int(9) NOT NULL AUTO_INCREMENT,
            OrderDate DATETIME NOT NULL,
            TotalAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            PaidAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            TipAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            Status int(9) DEFAULT 0 NOT NULL,
            PaymentMethod int(9) DEFAULT 0,
            LocationID int(9) DEFAULT 0,
            LocationName varchar(55) DEFAULT '',
            TableID int(9) DEFAULT 0,
            TableName varchar(55) DEFAULT '',
            CancelReason text DEFAULT '',
            Note text DEFAULT '',
            PRIMARY KEY  (OrderID)
        ) {$this->charset_collate};";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function down()
    {
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->table_name}");
    }
}
?>