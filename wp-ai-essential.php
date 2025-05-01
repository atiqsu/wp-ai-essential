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


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// TODO: (Ali Hasan) enqueue the build files - for now - assets/js/index-C6eaxcnc.js  and assets/index-D1JBE9HO.css

// Add the following code to all front end pages - <div id="wai-chatbox"></div> and mount the react app in that div


include_once 'includes/admin-menu.php';

include_once 'includes/admin-settings.php';
// Include Rest API File
include_once 'includes/rest-api.php';


/**
 * Add the chatbox div to the footer.
 */
function add_wai_chatbox_to_footer() {
    echo '<div id="wai-chatbox"></div>';
}

add_action('wp_footer', 'add_wai_chatbox_to_footer');


add_action('wp_enqueue_scripts', 'enqueue_wai_chatbox_scripts');

function enqueue_wai_chatbox_scripts() {
    wp_enqueue_script('wai-chatbox-script', plugin_dir_url(__FILE__) . 'build/index.js', array(), '1.0.0.'.time(), true);
    wp_enqueue_style('wai-chatbox-style', plugin_dir_url(__FILE__) . 'build/index.css', array(), '1.0.0.'.time());
}
