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

//function wp_ai_essential_page_frontend() {
//	?>
    <!--    <div class="wrap">-->
    <!--        <form method="post" action="options.php">-->
    <!--			--><?php
//			settings_fields( 'wp_ai_essential_option_field' );
//			do_settings_sections( 'wp-ai-essential' );
//			submit_button();
//			?>
    <!--        </form>-->
    <!--    </div>-->
    <!--	--><?php
//}

