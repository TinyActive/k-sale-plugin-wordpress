<?php
include_once plugin_dir_path(dirname(__FILE__)) . '/migration.php';
class Invoice extends Migration
{
    protected $table_name = 'invoice';

    public function __construct()
    {
        parent::__construct();
        $this->table_name = sanitize_text_field($this->table_name);
        $this->charset_collate = sanitize_text_field($this->charset_collate);
    }

    public function up()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;
        $table_name = esc_sql($this->table_name);

        $sql = "CREATE TABLE $table_name (
            InvoiceID int(9) NOT NULL AUTO_INCREMENT,
            InvoiceDate DATETIME NOT NULL,
            OrderID int(9) NOT NULL,
            TotalAmount DECIMAL(10, 2) DEFAULT 0 NOT NULL,
            Status int(9) DEFAULT 0 NOT NULL,
            CancelReason text DEFAULT '',
            PRIMARY KEY  (InvoiceID)
        ) $this->charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function down()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;
        $table_name = esc_sql($this->table_name);
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
?>
