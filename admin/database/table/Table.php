<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class Table extends Migration
{
    protected $table_name = 'table';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $sql = "CREATE TABLE {$this->table_name} (
            TableID int(9) NOT NULL AUTO_INCREMENT,
            TableName varchar(55) DEFAULT '' NOT NULL,
            LocationID int(9) DEFAULT 0,
            PRIMARY KEY  (TableID)
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