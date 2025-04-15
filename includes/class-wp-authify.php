<?php

class WP_Authify {
    private $version;

    public function __construct() {
        $this->version = WP_AUTHIFY_VERSION;
    }

    public function init() {
        // Добавляем пункт меню в админ-панель
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Регистрируем шорткоды
        add_shortcode('wp_authify_login', array($this, 'login_form_shortcode'));
        add_shortcode('wp_authify_register', array($this, 'register_form_shortcode'));
        
        // Обработка форм
        add_action('init', array($this, 'handle_form_submissions'));
        
        // Проверка блокировки при входе
        add_filter('authenticate', array($this, 'check_user_blocked'), 30, 3);

        // Добавляем обработчик AJAX
        add_action('wp_ajax_wp_authify_search_users', array($this, 'ajax_search_users'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'WP Authify',
            'WP Authify',
            'manage_options',
            'wp-authify',
            array($this, 'admin_page'),
            'dashicons-shield',
            30
        );
    }

    public function admin_page() {
        // Проверяем права доступа
        if (!current_user_can('manage_options')) {
            return;
        }

        // Обработка действий
        if (isset($_POST['action']) && check_admin_referer('wp_authify_action')) {
            if ($_POST['action'] === 'block_user') {
                $this->block_user($_POST['user_id'], get_current_user_id());
            } elseif ($_POST['action'] === 'unblock_user') {
                $this->unblock_user($_POST['user_id']);
            }
        }

        // Получаем список заблокированных пользователей
        global $wpdb;
        $table_name = $wpdb->prefix . 'authify_blocked_users';
        $blocked_users = $wpdb->get_results("
            SELECT b.*, u.display_name as blocked_user_name, a.display_name as blocked_by_name
            FROM $table_name b
            LEFT JOIN {$wpdb->users} u ON b.user_id = u.ID
            LEFT JOIN {$wpdb->users} a ON b.blocked_by = a.ID
            ORDER BY b.blocked_date DESC
        ");

        // Выводим страницу администратора
        include WP_AUTHIFY_PLUGIN_DIR . 'admin/admin-page.php';
    }

    public function ajax_search_users() {
        check_ajax_referer('wp_authify_search_users', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $query = sanitize_text_field($_GET['query']);
        
        $args = array(
            'search' => '*' . $query . '*',
            'search_columns' => array('user_login', 'display_name'),
            'exclude' => array(get_current_user_id()),
            'number' => 10,
            'orderby' => 'display_name',
            'order' => 'ASC'
        );

        $user_query = new WP_User_Query($args);
        $users = $user_query->get_results();
        
        $results = array();
        foreach ($users as $user) {
            $results[] = array(
                'ID' => $user->ID,
                'display_name' => $user->display_name
            );
        }
        
        wp_send_json_success($results);
    }

    public function login_form_shortcode($atts) {
        ob_start();
        include WP_AUTHIFY_PLUGIN_DIR . 'templates/login-form.php';
        return ob_get_clean();
    }

    public function register_form_shortcode($atts) {
        ob_start();
        include WP_AUTHIFY_PLUGIN_DIR . 'templates/register-form.php';
        return ob_get_clean();
    }

    public function handle_form_submissions() {
        if (isset($_POST['wp_authify_login'])) {
            $this->handle_login();
        } elseif (isset($_POST['wp_authify_register'])) {
            $this->handle_registration();
        }
    }

    private function handle_login() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'wp_authify_login')) {
            wp_die('Security check failed');
        }

        $creds = array(
            'user_login'    => $_POST['user_login'],
            'user_password' => $_POST['user_password'],
            'remember'      => isset($_POST['rememberme'])
        );

        $user = wp_signon($creds, false);

        if (is_wp_error($user)) {
            wp_die($user->get_error_message());
        }

        wp_redirect(home_url());
        exit;
    }

    private function handle_registration() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'wp_authify_register')) {
            wp_die('Security check failed');
        }

        $user_login = sanitize_user($_POST['user_login']);
        $user_pass = $_POST['user_pass'];

        $user_id = wp_create_user($user_login, $user_pass, '');

        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
        }

        wp_redirect(wp_login_url());
        exit;
    }

    public function block_user($user_id, $blocked_by) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'authify_blocked_users';
        
        return $wpdb->insert(
            $table_name,
            array(
                'user_id' => $user_id,
                'blocked_by' => $blocked_by
            ),
            array('%d', '%d')
        );
    }

    public function unblock_user($user_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'authify_blocked_users';
        
        return $wpdb->delete(
            $table_name,
            array('user_id' => $user_id),
            array('%d')
        );
    }

    public function check_user_blocked($user, $username, $password) {
        if ($user instanceof WP_User) {
            global $wpdb;
            $table_name = $wpdb->prefix . 'authify_blocked_users';
            
            $is_blocked = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name WHERE user_id = %d",
                $user->ID
            ));

            if ($is_blocked) {
                return new WP_Error(
                    'user_blocked',
                    __('Этот аккаунт заблокирован.', 'wp-authify')
                );
            }
        }
        return $user;
    }
} 