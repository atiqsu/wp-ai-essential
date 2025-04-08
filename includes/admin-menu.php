<?php


function wp_ai_essential() {
	add_menu_page(
		'WP AI Essential',
		'WP AI Essential',
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
		<h1 class="wp-heading-inline">Wp Ai Essential</h1>
	</div>
	<?php
}