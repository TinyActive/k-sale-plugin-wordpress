<?php

include_once plugin_dir_path(dirname(__FILE__)) . 'BaseController.php';

class ImageController extends BaseController
{
    protected $table_name = 'Image';
    protected $key = "ImageID";

    public function register_routes($a = [])
    {
        $data_router = [
            [
                'path' => '/upload',
                'methods' => 'POST',
                'callback' => [$this, 'upload'],
            ],
        ];

        parent::register_routes($data_router);
    }

    public function upload($request)
    {
        require_once (ABSPATH . 'wp-admin/includes/image.php');
        require_once (ABSPATH . 'wp-admin/includes/file.php');
        require_once (ABSPATH . 'wp-admin/includes/media.php');

        $file = $request->get_file_params();

        if (!empty($file['file'])) {
            $overrides = ['test_form' => false];
            $file_info = wp_handle_upload($file['file'], $overrides);

            if (isset($file_info['error'])) {
                return new WP_Error('upload_error', $file_info['error'], ['status' => 500]);
            }

            $file_url = $file_info['url'];
            $file_type = wp_check_filetype($file_url, null);

            $attachment = [
                'post_mime_type' => $file_type['type'],
                'post_title' => sanitize_file_name($file['file']['name']),
                'post_content' => '',
                'post_status' => 'inherit',
                'guid' => $file_url,
            ];

            $attachment_id = wp_insert_attachment($attachment, $file_url);

            if (is_wp_error($attachment_id)) {
                return new WP_Error('insert_error', $attachment_id->get_error_message(), ['status' => 500]);
            }

            $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_url);
            wp_update_attachment_metadata($attachment_id, $attachment_data);

            return new WP_REST_Response(['id' => $attachment_id, 'url' => $file_url], 200);
        } else {
            return new WP_Error('no_file', 'No file was uploaded', ['status' => 400]);
        }
    }

}
?>