<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';

class Location extends Migration
{
    protected $table_name = 'location';

    public function __construct()
    {
        parent::__construct();
    }

    public function up()
    {
        $sql = "CREATE TABLE {$this->table_name} (
            LocationID int(9) NOT NULL AUTO_INCREMENT,
            LocationName varchar(55) DEFAULT '' NOT NULL,
            PRIMARY KEY  (LocationID)
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