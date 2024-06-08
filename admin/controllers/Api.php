<?php

class Api extends WP_REST_Controller
{
    public function __construct()
    {
        $this->load_dependencies();
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    private function load_dependencies()
    {
        $directory = plugin_dir_path(__FILE__) . '/bussiness/';
        $files = glob($directory . '*.php');
        
        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                include_once $file;
            }
        }
    }

    public function register_routes()
    {
        $directory = plugin_dir_path(__FILE__) . '/bussiness/';
        $files = glob($directory . '*.php');
        
        foreach ($files as $file) {
            $class = basename($file, '.php');
            if (class_exists($class)) {
                $instance = new $class();
                if (method_exists($instance, 'register_routes')) {
                    $instance->register_routes();
                } else {
                    error_log("Class $class does not have a register_routes method.");
                }
            } else {
                error_log("Class $class does not exist.");
            }
        }
    }
}
?>
