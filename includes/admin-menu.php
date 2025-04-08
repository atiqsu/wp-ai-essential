<?php




function wp_ai_essential() {
	add_menu_page(
		'Wp AI Essential',
		'Wp AI Essential',
		'manage_options',
		'wp-ai-essential',
		'wp_ai_essential_page_frontend',
		'dashicons-admin-generic',
		99
	);
}
add_action('admin_menu', 'wp_ai_essential');

function wp_ai_essential_page_frontend() {
	?>
    <div class="wrap">
        <form method="post" action="options.php">
			<?php
			settings_fields('wp_ai_essential_option_field');
			do_settings_sections('wp-ai-essential');
			submit_button();
			?>
        </form>
    </div>
	<?php
}

 // this shortcode create for show custom post type testing parpase
function list_all_custom_post_types() {

	$args = array(
		'public'   => true,
		'_builtin' => false,
	);
	$output = 'names';
	$operator = 'and';

	$post_types = get_post_types($args, $output, $operator);

	if (empty($post_types)) {
	}

	$output_html = '<form>';

	foreach ($post_types as $post_type) {

		$pt_object = get_post_type_object($post_type);
		$label = $pt_object ? $pt_object->label : $post_type;
		$output_html .= '<label for='.esc_html($post_type).'>'.esc_html($label).'</label>';
        $output_html .= '<input name='.esc_html($post_type).' type="checkbox" id='. esc_html($post_type).'></br>';
	}
	$output_html .= '<input type="submit">';
	$output_html .= '</form';

	return $output_html;
}

add_shortcode('custom_post_types_list', 'list_all_custom_post_types');