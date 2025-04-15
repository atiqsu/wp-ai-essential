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

include_once 'includes/admin-menu.php';

include_once 'includes/admin-settings.php';

include_once 'includes/rest-api.php';

include "includes/db.php";