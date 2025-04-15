<?php
if (!defined('ABSPATH')) {
    exit;
}

$redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url();
?>

<div class="wp-authify-login-form">
    <form method="post" action="">
        <?php wp_nonce_field('wp_authify_login'); ?>
        <input type="hidden" name="wp_authify_login" value="1">
        <input type="hidden" name="redirect_to" value="<?php echo esc_url($redirect_to); ?>">

        <div class="form-header">
            <h2><?php _e('Вход в систему', 'wp-authify'); ?></h2>
        </div>

        <div class="form-group">
            <label for="user_login"><?php _e('Имя пользователя или Email', 'wp-authify'); ?></label>
            <input type="text" name="user_login" id="user_login" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="user_password"><?php _e('Пароль', 'wp-authify'); ?></label>
            <input type="password" name="user_password" id="user_password" class="form-control" required>
        </div>

        <div class="form-group checkbox-group">
            <label class="checkbox-label">
                <input type="checkbox" name="rememberme" value="1">
                <span><?php _e('Запомнить меня', 'wp-authify'); ?></span>
            </label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary"><?php _e('Войти', 'wp-authify'); ?></button>
        </div>
    </form>
</div>

<style>
.wp-authify-login-form {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
}

.form-header {
    text-align: center;
    margin-bottom: 2rem;
}

.form-header h2 {
    color: #333;
    margin: 0 0 0.5rem;
    font-size: 1.8rem;
}

.form-group {
    margin-bottom: 1.5rem;
    width: 100%;
    box-sizing: border-box;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #4a90e2;
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.checkbox-group {
    display: flex;
    align-items: center;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 0.5rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 500;
    text-align: center;
    text-decoration: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.btn-primary {
    background: #4a90e2;
    color: #fff;
    border: none;
}

.btn-primary:hover {
    background: #357abd;
}
</style> 