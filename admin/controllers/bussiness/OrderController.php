<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class OrderController extends BaseController
{
    protected $table_name = 'Order';
    protected $key = "OrderID";

    public function register_routes($a = [])
    {
        $data_router = [
            [
                'path' => '/save-order',
                'methods' => 'POST',
                'callback' => [$this, 'saveOrder'],
            ],
            [
                'path' => '/get-order',
                'methods' => 'GET',
                'callback' => [$this, 'getOrder'],
            ],
            [
                'path' => '/unpaid',
                'methods' => 'GET',
                'callback' => [$this, 'get_order_unpaid'],
            ],
        ];

        parent::register_routes($data_router);
    }

    public function get_order_unpaid($request)
    {
        global $wpdb;

        $per_page = intval($request['per_page'] ?? 50);
        $page = intval($request['page'] ?? 1);
        $offset = ($page - 1) * $per_page;

        $items = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $this->table_name WHERE `Status` = 3 ORDER BY $this->key DESC LIMIT %d OFFSET %d", $per_page, $offset)
        );

        // Query to get the total number of items
        $total = $wpdb->get_var("SELECT COUNT(1) FROM $this->table_name WHERE `Status` = 3");

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

    public function getOrder($request)
    {
        global $wpdb;
        $orderDetailTable = $wpdb->prefix . 'k_sale_order_detail';
        $orderId = intval($request['OrderID']);

        // Get order
        $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE OrderID = %d", $orderId), ARRAY_A);

        // Get order details
        $orderDetails = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$orderDetailTable} WHERE OrderID = %d", $orderId), ARRAY_A);

        return $this->ok((object) ['Order' => $order, 'OrderDetails' => $orderDetails]);
    }

    public function saveOrder($request)
    {
        global $wpdb;

        // Get JSON payload from request
        $params = $request->get_json_params();
        $order = $params['Order'];
        $orderDetails = $params['OrderDetails'];
        $orderDetailTable = $wpdb->prefix . 'k_sale_order_detail';

        try {
            $wpdb->query('START TRANSACTION');

            // change OrderDate to now if State is 1
            if ($order['State'] == 1) {
                $order['OrderDate'] = date('Y-m-d H:i:s');
            }

            // Handle order based on state
            switch ($order['State']) {
                case 1: // Insert
                    unset($order['State']);
                    $wpdb->insert($this->table_name, $order);
                    break;
                case 2: // Update
                    unset($order['State']);
                    $wpdb->update($this->table_name, $order, ['OrderID' => $order['OrderID']]);
                    break;
                case 3: // Delete
                    unset($order['State']);
                    $wpdb->delete($this->table_name, ['OrderID' => $order['OrderID']]);
                    break;
            }

            if ($wpdb->last_error) {
                throw new Exception($wpdb->last_error);
            }

            // Get OrderID
            $orderId = $order['OrderID'] ?? $wpdb->insert_id;

            // Handle order details based on state
            $orderDetailsResult = [];
            foreach ($orderDetails as $detail) {
                $detail[$this->key] = $orderId;

                switch ($detail['State']) {
                    case 1: // Insert
                        unset($detail['State']);
                        $wpdb->insert($orderDetailTable, $detail);
                        $detail['OrderDetailID'] = $wpdb->insert_id;
                        array_push($orderDetailsResult, $detail);
                        break;
                    case 2: // Update
                        unset($detail['State']);
                        $wpdb->update($orderDetailTable, $detail, ['OrderDetailID' => $detail['OrderDetailID']]);
                        array_push($orderDetailsResult, $detail);
                        break;
                    case 3: // Delete
                        unset($detail['State']);
                        $wpdb->delete($orderDetailTable, ['OrderDetailID' => $detail['OrderDetailID']]);
                        break;
                    default:
                        array_push($orderDetailsResult, $detail);
                        break;
                }

                if ($wpdb->last_error) {
                    throw new Exception($wpdb->last_error);
                }
            }

            $wpdb->query('COMMIT');

            // update OrderID
            $order['OrderID'] = $orderId;
            $order['State'] = 0;

            // if status = 5, insert to invoice and invoice detail if not exist else update
            if ($order['Status'] == 5) {
                // get order in database 
                $orderFull = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$this->table_name} WHERE OrderID = %d", $orderId), ARRAY_A);
                $orderDetailFull = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$orderDetailTable} WHERE OrderID = %d", $orderId), ARRAY_A);
                $invoiceTable = $wpdb->prefix . 'k_sale_invoice';
                $invoiceDetailTable = $wpdb->prefix . 'k_sale_invoice_detail';
                $invoice = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$invoiceTable} WHERE OrderID = %d", $orderId), ARRAY_A);

                if ($invoice == null) {
                    $invoice = [
                        'InvoiceDate' => date('Y-m-d H:i:s'),
                        'TotalAmount' => $orderFull['TotalAmount'],
                        'Status' => 5,
                        'OrderID' => $orderId,
                    ];
                    $wpdb->insert($invoiceTable, $invoice);
                    $invoice['InvoiceID'] = $wpdb->insert_id;
                } else {
                    $invoice['TotalAmount'] = $orderFull['TotalAmount'];
                    $wpdb->update($invoiceTable, $invoice, ['InvoiceID' => $invoice['InvoiceID']]);
                }

                foreach ($orderDetailFull as $detail) {
                    $invoiceDetail = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$invoiceDetailTable} WHERE OrderDetailID = %d", $detail['OrderDetailID']), ARRAY_A);

                    if ($invoiceDetail == null) {
                        $invoiceDetail = [
                            'InvoiceID' => $invoice['InvoiceID'],
                            'ProductID' => $detail['ProductID'],
                            'ProductName' => $detail['ProductName'],
                            'Quantity' => $detail['Quantity'],
                            'Price' => $detail['Price'],
                            'TotalAmount' => $detail['TotalAmount'],
                            'OrderDetailID' => $detail['OrderDetailID'],
                        ];
                        $wpdb->insert($invoiceDetailTable, $invoiceDetail);
                    } else {
                        $invoiceDetail['Quantity'] = $detail['Quantity'];
                        $invoiceDetail['TotalAmount'] = $detail['TotalAmount'];
                        $wpdb->update($invoiceDetailTable, $invoiceDetail, ['InvoiceDetailID' => $invoiceDetail['InvoiceDetailID']]);
                    }
                }
            }

            return $this->ok((object) ['Order' => $order, 'OrderDetails' => $orderDetailsResult]);
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            return ['message' => 'Failed to save Order and Order Details', 'error' => $e->getMessage()];
        }
    }
}
?>
