<?php

/**
 * Fired during plugin activation
 *
 * @link       https://thangdangblog.com
 * @since      1.0.0
 *
 * @package    K_Sale
 * @subpackage K_Sale/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    K_Sale
 * @subpackage K_Sale/includes
 * @author     Đặng Quốc Thắng <thangdangblog@gmail.com>
 */

 class K_Sale_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/database/run-schema.php';
		new RunSchema();
	}

}
