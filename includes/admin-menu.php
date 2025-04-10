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
//	add_submenu_page(
//		'wp-ai-essential',
//		'Sub menu',
//		'Sub menu',
//		'manage_options',
//		'wp-ai-essential-sub-menu',
//		'wp_ai_essential_page_frontend_submenu'
//
//	);
}

add_action( 'admin_menu', 'wp_ai_essential' );

