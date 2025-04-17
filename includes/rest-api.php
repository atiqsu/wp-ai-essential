<?php
/**
 * Register a custom REST API endpoint to retrieve CPT names
 */
function wp_ai_essential_register_cpt_endpoint()
{

    register_rest_route('wai/v2', '/content-cpt', array(
        'methods' => 'GET',
        'callback' => 'wp_ei_essential_get_cpt_names',
    ));

    register_rest_route(
        'wai/v2',
        '/(?P<cpt>[a-zA-Z0-9_-]+)/(?P<id>\d+)',
        array(
            'methods' => 'GET',
            'callback' => 'wp_ei_essential_get_post',
            'args' => array(
                'cpt' => array(
                    'type' => 'string',
                    'required' => true,
                ),
                'id' => array(
                    'type' => 'integer',
                    'required' => true,
                ),
            ),
        )
    );
    register_rest_route('wai/v2', '/agent-chat', array(
        'methods' => 'GET',
        'callback' => 'wp_ei_essential_get_message',
    ));

    register_rest_route(
        'wai/v2',
        '/upload-media',
        array(
            'methods' => 'POST',
            'callback' => 'wp_ei_essential_upload_media',
            'premission_callback' => '__return_true',
        )
    );

}

add_action('rest_api_init', 'wp_ai_essential_register_cpt_endpoint');

function wp_ei_essential_get_cpt_names()
{

    $cpt_names = get_option('wp_ai_essential_selected_post_types');

    if ($cpt_names && is_array($cpt_names)) {
        return $cpt_names;
    }

    return array();
}

function wp_ei_essential_get_post($request)
{
    $cpt = sanitize_text_field($request->get_param('cpt'));
    $id = (int)$request->get_param('id');

    // Validate CPT exists (optional, but good practice)
    if (!post_type_exists($cpt)) {
        return new WP_Error('invalid_cpt', 'Invalid post type.', array('status' => 400));
    }

    $post = get_post($id);

    // Validate post exists and matches post type
    if (!$post || $post->post_type !== $cpt) {
        $response = rest_ensure_response(array(
            'error' => 'no_post',
            'message' => 'Post not found or mismatched CPT',
            'data' => array(
                'status' => 404,
            ),
        ));

        $response->set_status(404);
        return $response;

    }

    // Success response
    return rest_ensure_response(array(
        'ID' => $post->ID,
        'title' => get_the_title($post),
        'slug' => $post->post_name,
        'type' => $post->post_type,
        'content' => apply_filters('the_content', $post->post_content),
//		'content' =>  $post->post_content ,
    ));

}

function wp_ei_essential_get_message()
{
    $message = "Hello Buddy How are you?";
    return $message;
}


function wp_ei_essential_upload_media($request)
{
    $files = $request->get_file_params();

    if (empty($files) || empty($files['file'])) {
        return new WP_REST_Response([
            'error' => 'No file provided',
        ], 400);
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $file = $files['file'];

    $upload = wp_handle_upload($file, ['test_form' => false]);

    if (isset($upload['error'])) {
        return new WP_REST_Response([
            'error' => $upload['error'],
        ], 500);
    }

    $attachment = [
        'post_mime_type' => $upload['type'],
        'post_title'     => sanitize_file_name(basename($upload['file'])),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];

    $attach_id = wp_insert_attachment($attachment, $upload['file']);

    if (is_wp_error($attach_id)) {
        return new WP_REST_Response([
            'error' => 'Failed to insert attachment.',
        ], 500);
    }

    $attach_data = wp_generate_attachment_metadata($attach_id, $upload['file']);
    wp_update_attachment_metadata($attach_id, $attach_data);

    $url = wp_get_attachment_url($attach_id);

    return new WP_REST_Response([
        'url' => $url,
        'id'  => $attach_id
    ], 200);
}

