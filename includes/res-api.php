<?php
/**
 * Register a custom REST API endpoint to retrieve CPT names
 */
function wp_ai_essential_register_cpt_endpoint() {
	register_rest_route('wai/v2', '/content-cpt', array(
		'methods'  => 'GET',
		'callback' => 'wp_ei_essential_get_cpt_names',
	));
	register_rest_route(
		'wai/v2',
		'/(?P<cpt>[a-zA-Z0-9_-]+)/(?P<id>\d+)',
		array(
			'methods'  => 'GET',
			'callback' => 'wp_ei_essential_get_post',
			'args'     => array(
				'cpt' => array(
					'type'     => 'string',
					'required' => true,
				),
				'id' => array(
					'type'     => 'integer',
					'required' => true,
				),
			),
		)
	);

}
add_action('rest_api_init', 'wp_ai_essential_register_cpt_endpoint');

function wp_ei_essential_get_cpt_names() {
	$cpt_names = get_option('wp_ai_essential_selected_post_types');

	if ($cpt_names && is_array($cpt_names)) {
		return $cpt_names;
	}

	return array();
}

function wp_ei_essential_get_post( $request ) {
	$cpt = sanitize_text_field($request->get_param( 'cpt' ));
	$id  = (int) $request->get_param( 'id' );

	// Validate CPT exists (optional, but good practice)
	if ( ! post_type_exists( $cpt ) ) {
		return new WP_Error( 'invalid_cpt', 'Invalid post type.', array( 'status' => 400 ) );
	}

	$post = get_post( $id );

	// Validate post exists and matches post type
	if ( ! $post || $post->post_type !== $cpt ) {
		return new WP_Error( 'no_post', 'Post not found or mismatched CPT', array( 'status' => 404 ) );
	}

	// Success response
	return rest_ensure_response( array(
//		'ID'      => $post->ID,
//		'title'   => get_the_title( $post ),
//		'type'    => $post->post_type,
//		'content' => apply_filters( 'the_content', $post->post_content ),
		 $post,
	) );
}



