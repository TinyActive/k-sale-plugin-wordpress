<?php

class BaseController extends WP_REST_Controller
{
    protected $namespace = '/k-sale/v1';
    protected $table_name;

    protected $key = "id";


    public function __construct()
    {
        global $wpdb;
        // $wpdb->hide_errors();
        $this->table_name = $this->get_table_name($this->table_name);
    }

    protected function get_table_name($value)
    {
        global $wpdb;
        return $wpdb->prefix . 'k_sale_' . $value;
    }

    protected function get_controller_name()
    {
        $class_name = get_class($this);
        $base_text = str_replace('Controller', '', $class_name); // replace 'Controller' with ''
        return strtolower($base_text) . 's'; // add 's' to the end
    }

    protected function ok($data = null)
    {
        return new WP_REST_Response((object) $data, 200);
    }

    public function register_routes($data_router = [])
    {

        // Base
        register_rest_route($this->namespace, $this->get_controller_name(), [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_item'],
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'insert'],
            ],
            [
                'methods' => 'PUT',
                'callback' => [$this, 'update'],
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete'],
            ],
        ]);

        // Base
        register_rest_route($this->namespace, $this->get_controller_name() . '/list', [
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_items'],
            ]
        ]);

        // Custom
        if (count($data_router) > 0) {

            foreach ($data_router as $router) {
                register_rest_route($this->namespace, '/' . $this->get_controller_name() . $router['path'], [
                    [
                        'methods' => $router['methods'],
                        'callback' => $router['callback'],
                    ],
                ]);
            }
        }
    }

    public function insert($request)
    {
        global $wpdb;
        $data = $request->get_json_params();
        $wpdb->insert($this->table_name, $data);
        if ($wpdb->last_error) {
            return new WP_REST_Response((object) ['message' => "Lỗi"], 500);
        }
        $data[$this->key] = $wpdb->insert_id;
        return $this->ok($data);

    }

    public function update($request)
    {
        global $wpdb;
        $data = $request->get_json_params();
        $id = $data[$this->key];
        $wpdb->update($this->table_name, $data, [$this->key => $id]);
        if ($wpdb->last_error) {
            return new WP_REST_Response((object) ['message' => "Lỗi"], 500);
        }
        return $this->ok($data);
    }

    public function delete($request){
        global $wpdb;
        $id = $request['id'] ?? 0;
        if ($id > 0) {
            $wpdb->delete($this->table_name, [$this->key => $id]);
            if ($wpdb->last_error) {
                return new WP_REST_Response((object) ['message' => "Lỗi"], 500);
            }
            return $this->ok();
        }
        return new WP_REST_Response((object) ['message' => "ID không hợp lệ"], 400);
    }

    protected function base_get_query($query)
    {
        return $query;
    }

    public function get_items($request)
    {
        global $wpdb;

        $per_page = $request['per_page'] ?? 50;
        $page = $request['page'] ?? 1;
        $offset = ($page - 1) * $per_page;

        $items = $wpdb->get_results(
            $wpdb->prepare($this->base_get_query("SELECT * FROM $this->table_name ORDER BY $this->key DESC LIMIT %d OFFSET %d"), $per_page, $offset)
        );

        // Query to get the total number of items
        $total = $wpdb->get_var("SELECT COUNT(1) FROM $this->table_name");

        // Calculate the total number of pages
        $total_pages = ceil($total / $per_page);

        // Prepare the data for the response
        $data = array();
        foreach ($items as $item) {
            $data[] = $this->prepare_item_for_response($item, $request);
        }

        // Add pagination info to the response
        return $this->ok([
            'items' => $data,
            'total' => $total,
            'total_pages' => $total_pages,
            'per_page' => $per_page,
            'page' => $page,
        ]);
    }

    public function get_item($request)
    {
        global $wpdb;

        // Handle get data by ID
        $id = $request['id'] ?? 0;
        if ($id > 0) {
            $item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->table_name WHERE $this->key = %d", $id));
            if ($item) {
                return $this->ok($item);
            }
            return new WP_REST_Response((object) ['message' => "Không tìm thấy"], 404);
        }

        return new WP_REST_Response((object) ['message' => "ID không hợp lệ"], 400);
    }

    public function prepare_item_for_response($item, $request)
    {
        return $item;
    }

    public function get_route_access($request)
    {
        return true;
    }
}
?>