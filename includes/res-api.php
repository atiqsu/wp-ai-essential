<?php
/**
 * Register a custom REST API endpoint to retrieve CPT names
 */
function wai_register_cpt_endpoint() {
	register_rest_route('wai/v2', '/content-cpt', array(
		'methods'  => 'GET',
		'callback' => 'wai_get_cpt_names',
	));
}
add_action('rest_api_init', 'wai_register_cpt_endpoint');

function wai_get_cpt_names() {
	$cpt_names = get_option('wp_ai_essential_selected_post_types');

	if ($cpt_names && is_array($cpt_names)) {
		return $cpt_names;
	}

	return array();
}