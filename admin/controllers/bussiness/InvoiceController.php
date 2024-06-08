<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class InvoiceController extends BaseController
{
    protected $table_name = 'Invoice';
    protected $key = "InvoiceID";

    public function register_routes($a = [])
    {
        $data_router = [
            [
                'path' => '/get-invoice',
                'methods' => 'GET',
                'callback' => [$this, 'getInvoices'],
            ],
        ];

        parent::register_routes($data_router);
    }


    public function getInvoices($request)
    {
        global $wpdb;
        $invoiceDetailTable = $wpdb->prefix . 'k_sale_invoice_detail';
        // Get OrderID from request
        $invoiceID = $request['InvoiceID'];

        // Get order
        $invoices = $wpdb->get_row("SELECT * FROM {$this->table_name} WHERE InvoiceID = $invoiceID", ARRAY_A);

        // Get order details
        $invoiceDetails = $wpdb->get_results("SELECT * FROM {$invoiceDetailTable} WHERE InvoiceID = $invoiceID", ARRAY_A);

        return $this->ok((object) ['Invoice' => $invoices, 'InvoiceDetails' => $invoiceDetails]);
    }

}
?>