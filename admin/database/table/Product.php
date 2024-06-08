<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class Product extends Migration
{
    protected $table_name = 'product';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $sql = "CREATE TABLE {$this->table_name} (
            ProductID int(9) NOT NULL AUTO_INCREMENT,
            ProductName text DEFAULT '' NOT NULL,
            Price int(9) DEFAULT 0 NOT NULL,
            CostPrice int(9) DEFAULT 0 NOT NULL,
            Unit text DEFAULT '',
            Note text DEFAULT '',
            ImageUrl text DEFAULT '',
            PRIMARY KEY  (ProductID)
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