<?php

// Make sure the activation hook is registered properly with your main plugin file
register_activation_hook(__FILE__, 'wp_ai_essential_db');

function wp_ai_essential_db() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_sessions = $wpdb->prefix . 'chat_sessions';
    $table_threads = $wpdb->prefix . 'chat_threads';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // Create chat_sessions table first
    $sql_sessions = "
    CREATE TABLE IF NOT EXISTS $table_sessions (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_email VARCHAR(191),
        user_name VARCHAR(191),
        user_id BIGINT(20) UNSIGNED DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;
    ";
    dbDelta($sql_sessions);

    // Create chat_threads table with foreign key after sessions table is created
    $sql_threads = "
    CREATE TABLE IF NOT EXISTS $table_threads (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        session_id BIGINT(20) UNSIGNED NOT NULL,
        msg TEXT NOT NULL,
        sender ENUM('user', 'agent') DEFAULT 'user',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY session_id (session_id),
        CONSTRAINT fk_session_id FOREIGN KEY (session_id) REFERENCES $table_sessions(id) ON DELETE CASCADE
    ) $charset_collate;
    ";
    dbDelta($sql_threads);
}
