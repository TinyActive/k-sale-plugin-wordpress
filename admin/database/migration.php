<?php
abstract class Migration
{
    protected $wpdb;
    protected $table_name;
    protected $charset_collate;

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
        $this->table_name = $wpdb->prefix . 'k_sale_' . $this->table_name;
        $this->up();
    }

    abstract public function up();

    abstract public function down();
}

?>