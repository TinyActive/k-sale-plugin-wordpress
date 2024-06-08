<?php

class RunSchema
{
    private static $instance = null;

    public function __construct()
    {
        // Load dependencies and run the schema setup
        $this->load_dependencies();
        $this->run();
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function load_dependencies()
    {
        // Include all PHP files in the specified directory
        foreach (glob(plugin_dir_path(dirname(__FILE__)) . "/database/table/*.php") as $file) {
            require_once $file;
        }
    }

    private function run()
    {
        // Instantiate each class found in the specified directory
        foreach (glob(plugin_dir_path(dirname(__FILE__)) . "/database/table/*.php") as $file) {
            $class = basename($file, '.php');
            if (class_exists($class)) {
                try {
                    new $class();
                } catch (Exception $e) {
                    // Handle exception if class instantiation fails
                    error_log('Failed to instantiate class ' . $class . ': ' . $e->getMessage());
                }
            } else {
                // Log if class does not exist
                error_log('Class ' . $class . ' not found.');
            }
        }
    }
}
?>
