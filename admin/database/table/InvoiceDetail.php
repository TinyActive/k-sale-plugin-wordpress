<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class InvoiceDetail extends Migration
{
    protected $table_name = 'invoice_detail';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        global $wpdb;
        $sql = "CREATE TABLE {$this->table_name} (
            InvoiceDetailID int(9) NOT NULL AUTO_INCREMENT,
            InvoiceID int(9) NOT NULL,
            OrderDetailID int(9) NOT NULL,
            ProductID int(9) NOT NULL,
            ProductName text DEFAULT '' NOT NULL,
            Quantity int(9) NOT NULL,
            Price DECIMAL(10, 2) NOT NULL,
            TotalAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            Note text DEFAULT '',
            PRIMARY KEY  (InvoiceDetailID)
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