<?php
class RunSchema
{
    public function __construct()
    {
        $this->load_dependencies();
        $this->run();
    }

    private function load_dependencies()
    {

        foreach (glob(plugin_dir_path(dirname(__FILE__)) . "/database/table/*.php") as $file) {
            require_once $file;
        }
    }

    private function run()
    {

        foreach (glob(plugin_dir_path(dirname(__FILE__)) . "/database/table/*.php") as $file) {
            $class = basename($file, '.php');
            if (class_exists($class)) {
                new $class();
            }
        }
    }
}
?>