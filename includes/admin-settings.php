<?php

function wp_ai_essential_page_frontend()
{
    $current_webhook = get_option('webhook_url');
    $saved_post_types = get_option('wp_ai_essential_selected_post_types', array());
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Wp Ai Essential Options</h1>
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="save_webhook_url">
            <table class="form-table">
                <tbody>
                <tr>
                    <th scope="row">
                        <label for="webhook_url"><?php _e('Webhook URL', 'wp-ai-essential'); ?></label>
                    </th>
                    <td>
                        <input type="url" name="webhook_url" id="webhook_url" class="regular-text"
                               value="<?php echo esc_attr($current_webhook); ?>">
                    </td>
                </tr>
                </tbody>
            </table>

            <h2 class="wp-heading-inline">Custom Post Types</h2>

            <table class="form-table">
                <tbody>
                <?php
                $args = array(
                    'public' => true,
                    '_builtin' => false,
                );
                $output = 'names';
                $operator = 'and';

                $post_types = get_post_types($args, $output, $operator);

                foreach ($post_types as $post_type) {
                    $pt_object = get_post_type_object($post_type);
                    $label = $pt_object ? $pt_object->label : $post_type;
                    $is_checked = in_array($post_type, $saved_post_types) ? 'checked' : '';
                    ?>
                    <tr>
                        <th scope="row">
                            <label for="post_type_<?php echo esc_attr($post_type); ?>"><?php echo esc_html($label); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="post_types[]" value="<?php echo esc_attr($post_type); ?>"
                                   id="post_type_<?php echo esc_attr($post_type); ?>" <?php echo $is_checked; ?>>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>

            </table>

            <h2 class="wp-heading-inline">Chatbox Support</h2>
            <table class="form-table">
                <tbody>
                <?php

                $support_values = get_option('wp_ai_essential_chatbox_support', array(
                    'active_voice' => false,
                    'active_image' => false,
                    'active_emoji' => false,
                ));
                ?>

                <tr>
                    <th scope="row">
                        <label for="audio_voice"><?php _e('Voice Record', 'wp-ai-essential'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="audio_voice" id="audio_voice" value="1"
                            <?php checked($support_values['active_voice'], true); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="image_support"><?php _e('Image Support', 'wp-ai-essential'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="image_support" id="image_support" value="1"
                            <?php checked($support_values['active_image'], true); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="emoji_support"><?php _e('Emoji Support', 'wp-ai-essential'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="emoji_support" id="emoji_support" value="1"
                            <?php checked($support_values['active_emoji'], true); ?> />
                    </td>
                </tr>
                </tbody>
            </table>

            <?php wp_nonce_field('wp-ai-essential'); ?>
            <?php submit_button(__('Save Changes', 'wp-ai-essential'), 'primary', 'submit-address'); ?>
        </form>
    </div>
    <?php
}

function wp_ai_essential_save_webhook()
{
    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wp-ai-essential')) {
        wp_die(__('Security check failed', 'wp-ai-essential'));
    }

    // Process and save the webhook URL
    if (isset($_POST['webhook_url'])) {
        $webhook_url = sanitize_text_field($_POST['webhook_url']);
        update_option('webhook_url', $webhook_url);
    }

    // Process and save the selected post types
    $post_types = isset($_POST['post_types']) ? (array)$_POST['post_types'] : array();
    $sanitized_post_types = array();

    foreach ($post_types as $post_type) {
        $sanitized_post_types[] = sanitize_text_field($post_type);
    }

    update_option('wp_ai_essential_selected_post_types', $sanitized_post_types);


    $active_voice = isset($_POST['audio_voice']) ? true : false;
    $active_image = isset($_POST['image_support']) ? true : false;
    $active_emoji = isset($_POST['emoji_support']) ? true : false;

    $chatbox_support = array(
        'active_voice' => $active_voice,
        'active_image' => $active_image,
        'active_emoji' => $active_emoji,
    );

    update_option('wp_ai_essential_chatbox_support', $chatbox_support);


    wp_redirect(add_query_arg('updated', 'true', wp_get_referer()));
    exit;
}

add_action('admin_post_save_webhook_url', 'wp_ai_essential_save_webhook');


function chatbox()
{
    echo '<pre>';
//    var_dump(get_option('wp_ai_essential_chatbox_support'));
    echo '</pre>';
}

add_shortcode('chatbox', 'chatbox');