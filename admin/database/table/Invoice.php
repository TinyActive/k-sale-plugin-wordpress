<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class Invoice extends Migration
{
    protected $table_name = 'invoice';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $sql = "CREATE TABLE {$this->table_name} (
            InvoiceID int(9) NOT NULL AUTO_INCREMENT,
            InvoiceDate DATETIME NOT NULL,
            OrderID int(9) NOT NULL,
            TotalAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            Status int(9) DEFAULT 0 NOT NULL,
            CancelReason text DEFAULT '',
            PRIMARY KEY  (InvoiceID)
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