<?php

function wp_ai_essential_page_frontend() {
	$current_webhook  = get_option( 'webhook_url' );
	$saved_post_types = get_option( 'wp_ai_essential_selected_post_types', array() );
	?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Wp Ai Essential Options</h1>
        <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
            <input type="hidden" name="action" value="save_webhook_url">
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="webhook_url"><?php _e( 'Webhook URL', 'wp-ai-essential' ); ?></label>
                    </th>
                    <td>
                        <input type="url" name="webhook_url" id="webhook_url" class="regular-text"
                               value="<?php echo esc_attr( $current_webhook ); ?>">
                    </td>
                </tr>
                </tbody>
            </table>

            <h2 class="wp-heading-inline">Custom Post types</h2>

            <table class="form-table">
                <tbody>
				<?php
				$args     = array(
					'public'   => true,
					'_builtin' => false,
				);
				$output   = 'names';
				$operator = 'and';

				$post_types = get_post_types( $args, $output, $operator );

				foreach ( $post_types as $post_type ) {
					$pt_object  = get_post_type_object( $post_type );
					$label      = $pt_object ? $pt_object->label : $post_type;
					$is_checked = in_array( $post_type, $saved_post_types ) ? 'checked' : '';
					?>
                    <tr>
                        <th scope="row">
                            <label for="post_type_<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $label ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="post_types[]" value="<?php echo esc_attr( $post_type ); ?>"
                                   id="post_type_<?php echo esc_attr( $post_type ); ?>" <?php echo $is_checked; ?>>
                        </td>
                    </tr>
					<?php
				}
				?>
                </tbody>
            </table>
			<?php wp_nonce_field( 'wp-ai-essential' ); ?>
			<?php submit_button( __( 'Save Changes', 'wp-ai-essential' ), 'primary', 'submit-address' ); ?>
        </form>
    </div>
	<?php
}

function wp_ai_essential_save_webhook() {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wp-ai-essential' ) ) {
		wp_die( __( 'Security check failed', 'wp-ai-essential' ) );
	}

	// Process and save the webhook URL
	if ( isset( $_POST['webhook_url'] ) ) {
		$webhook_url = sanitize_text_field( $_POST['webhook_url'] );
		update_option( 'webhook_url', $webhook_url );
	}

	// Process and save the selected post types
	$post_types           = isset( $_POST['post_types'] ) ? (array) $_POST['post_types'] : array();
	$sanitized_post_types = array();

	foreach ( $post_types as $post_type ) {
		$sanitized_post_types[] = sanitize_text_field( $post_type );
	}

	update_option( 'wp_ai_essential_selected_post_types', $sanitized_post_types );

	wp_redirect( add_query_arg( 'updated', 'true', wp_get_referer() ) );
	exit;
}

add_action( 'admin_post_save_webhook_url', 'wp_ai_essential_save_webhook' );
