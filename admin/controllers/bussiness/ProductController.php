<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class ProductController extends BaseController
{
    protected $table_name = 'product';
    protected $key = "ProductID";

    public function register_routes($a = [])
    {
        parent::register_routes($a);
    }

}
?>