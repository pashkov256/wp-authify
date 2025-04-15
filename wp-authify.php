<?php
/**
 * Plugin Name: WP Authify
 * Plugin URI: https://github.com/yourusername/wp-authify
 * Description: Плагин для управления доступом пользователей с возможностью блокировки
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/yourusername
 * Text Domain: wp-authify
 */

// Если файл вызван напрямую, прерываем выполнение
if (!defined('ABSPATH')) {
    exit;
}

// Определяем константы плагина
define('WP_AUTHIFY_VERSION', '1.0.0');
define('WP_AUTHIFY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_AUTHIFY_PLUGIN_URL', plugin_dir_url(__FILE__));

// Подключаем необходимые файлы
require_once WP_AUTHIFY_PLUGIN_DIR . 'includes/class-wp-authify.php';

// Инициализация плагина
function wp_authify_init() {
    $plugin = new WP_Authify();
    $plugin->init();
}
add_action('plugins_loaded', 'wp_authify_init');

// Активация плагина
register_activation_hook(__FILE__, 'wp_authify_activate');
function wp_authify_activate() {
    // Создаем таблицу для заблокированных пользователей
    global $wpdb;
    $table_name = $wpdb->prefix . 'authify_blocked_users';
    
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        blocked_by bigint(20) NOT NULL,
        blocked_date datetime DEFAULT CURRENT_TIMESTAMP,
        reason text,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Деактивация плагина
register_deactivation_hook(__FILE__, 'wp_authify_deactivate');
function wp_authify_deactivate() {
    // Очистка данных при деактивации
} 