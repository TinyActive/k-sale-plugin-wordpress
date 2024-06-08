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
        foreach (glob(plugin_dir_path(__FILE__) . '/bussiness/*.php') as $file) {
            include_once $file;
        }
    }

    public function register_routes()
    {
        foreach (glob(plugin_dir_path(__FILE__) . '/bussiness/*.php') as $file) {
            $class = basename($file, '.php');
            if (class_exists($class)) {
                (new $class())->register_routes();
            }
        }
    }
}
?>