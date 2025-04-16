<?php

/**
 * Plugin Name:       WP AI Essential
 * Plugin URI:        #
 * Description: Wp Ai Essential Admin Menu
 * Version:           1.0.0
 * Author:            Ali Hasan
 * Author URI:        https://github.com/alihasandeveloper
 * Text Domain:       wp-ai-essential
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

include_once 'includes/admin-menu.php';
include_once 'includes/admin-settings.php';
include_once 'includes/rest-api.php';
include_once 'includes/db.php';

// plugin activation hook
register_activation_hook(__FILE__, 'wp_ai_essential_db');


//function wp_ai_essential_add_assets()
//{
//    $dir = plugin_dir_url(__FILE__);
//    wp_enqueue_style('wp-ai-essential-react-style', $dir . 'build/assets/index-BdpZjVRh.css', false, '1.0', 'all');
//    wp_enqueue_script('wp-ai-essential-react-script', $dir . 'build/assets/index-Bs9L4ns3.js', false, '1.0', true);
//}
//
//add_action('enueue_scripts', 'wp_ai_essential_add_assets');

// echo '<pre>';
// printf(plugin_dir_url( __FILE__ ));
// echo '</pre>';
function wp_ai_essential_chatbox()
{
    $dir = plugin_dir_url(__FILE__);

    wp_enqueue_style(
        'wp-ai-essential-react-style',
        $dir . 'build/assets/index.css',
        false,
        '1.0',
        'all'
    );

    wp_enqueue_script(
        'wp-ai-essential-react-script',
        $dir . 'build/assets/index.js',
        false,
        '1.0',
        true
    );

    $supported_settings = get_option('wp_ai_essential_chatbox_support');

    wp_localize_script(
        'wp-ai-essential-react-script',
        'supported_settings',
        $supported_settings
    );
    echo '<div id="root"></div>';
}

add_action('wp_footer', 'wp_ai_essential_chatbox');
