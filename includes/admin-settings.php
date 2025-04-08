<?php

add_action('admin_init', 'wp_ai_essential_option_init');

function wp_ai_essential_option_init() {
	add_settings_section(
		'wp_ai_essential_option_section',
		__('WP AI Essential Options', 'wp-ai-essential'),
		function () {},
		'wp-ai-essential'
	);

	add_settings_field(
		'wp_ai_webhook_url',
		__('Webhook URL', 'wp-ai-essential'),
		'wp_ai_essential_option_callback',
		'wp-ai-essential',
		'wp_ai_essential_option_section'
	);

	register_setting(
		'wp_ai_essential_option_field',
		'webhook_url'
	);
}

function wp_ai_essential_option_callback() {
	$value = esc_attr(get_option('webhook_url'));
	?>
    <input type="text" name="webhook_url" id="wp_ai_webhook_url" value="<?php echo $value; ?>" class="regular-text">
	<?php
}

