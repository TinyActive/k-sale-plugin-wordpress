<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://thangdangblog.com
 * @since      1.0.0
 *
 * @package    K_Sale
 * @subpackage K_Sale/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    K_Sale
 * @subpackage K_Sale/admin
 * @author     Đặng Quốc Thắng <thangdangblog@gmail.com>
 */
class K_Sale_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in K_Sale_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The K_Sale_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/k-sale-admin.css', array(), $this->version, 'all');

		// Bổ sung thêm code này để thêm app.css
		wp_enqueue_style($this->plugin_name . '_app_css', plugin_dir_url(__FILE__) . 'build/app.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in K_Sale_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The K_Sale_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/k-sale-admin.js', array('jquery'), $this->version, false);

		// Bổ sung thêm code này để thêm app.js
		wp_enqueue_script($this->plugin_name . '_app_js', plugin_dir_url(__FILE__) . 'build/app.js', array(), $this->version, false);
		wp_enqueue_script($this->plugin_name . '_config_app_js', plugin_dir_url(__FILE__) . 'build/js/config.js', array(), $this->version, false);

	}

	public function script_loader_type_module($tag, $handle, $src)
	{

		if ($this->plugin_name . '_app_js' !== $handle) {
			return $tag;
		}
		// change the script tag by adding type="module" and return it.
		return '<script type="module" src="' . esc_url($src) . '"></script>';

	}

	function remove_default_stylesheets()
	{
		wp_deregister_style('wp-admin');
	}
	
	function add_default_stylesheets()
	{
		wp_register_style('wp-admin');
	}

	// Tạo trang trong admin
	function create_admin_page()
	{
		add_menu_page(
			'K Bán hàng',
			'K Bán hàng',
			'manage_options',
			'k-sale',
			[$this, 'sale_page_content'],
			'dashicons-cart',
			10
		);
	}

	// Nội dung trang
	function sale_page_content()
	{
		echo '<div id="app"></div>';
	}

	// Create parent and child pages in admin
	function create_setting_page()
	{
		// Create parent page
		add_menu_page(
			'Thiết lập cửa hàng',
			'Thiết lập cửa hàng',
			'manage_options',
			'setting-sale',
			[$this, 'custom_setting_page_content'],
			'dashicons-admin-settings',
			11
		);

		// Create child page
		add_submenu_page(
			'setting-sale', // Parent slug
			'Thiết lâp bàn', // Page title
			'Thiết lâp bàn', // Menu title
			'manage_options', // Capability
			'setting-seat-page', // Menu slug
			[$this, 'child_seat_page_content'] // Function to display the page content
		);
	}

	// Content for the child page
	function child_seat_page_content()
	{
		echo '<div id="app"></div>';
	}

	// Content for the child page
	function custom_setting_page_content()
	{
		echo '<div id="app"></div>';
	}


	function product_page()
	{
		// Create parent page
		add_menu_page(
			'Mặt hàng',
			'Mặt hàng',
			'manage_options',
			'product-page',
			[$this, 'product_page_content'],
			'dashicons-products',
			11
		);

	}

	// Content for the product page
	public function product_page_content()
	{
		echo '<div id="app">ProductPage</div>';
	}

	function add_product_page()
	{
		// Create parent page
		add_submenu_page(
			null,
			'',
			'',
			'manage_options',
			'add-product',
			[$this, 'add_product_page_content'],
			11
		);

	}

	public function add_product_page_content()
	{
		echo '<div id="app">ProductAddPage</div>';
	}

	function add_invoice_page()
	{
		// Create parent page
		add_menu_page(
			'Hóa đơn',
			'Hóa đơn',
			'manage_options',
			'invoice',
			[$this, 'add_invoice_page_content'],
			'dashicons-media-document',
			11
		);

	}

	public function add_invoice_page_content()
	{
		echo '<div id="app"></div>';
	}

}
