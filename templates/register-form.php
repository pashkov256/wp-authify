<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wp-authify-register-form">
    <form method="post" action="">
        <?php wp_nonce_field('wp_authify_register'); ?>
        <input type="hidden" name="wp_authify_register" value="1">

        <div class="form-header">
            <h2><?php _e('Регистрация', 'wp-authify'); ?></h2>
            <p class="form-description"><?php _e('Создайте новый аккаунт', 'wp-authify'); ?></p>
        </div>

        <div class="form-group">
            <label for="user_login"><?php _e('Имя пользователя', 'wp-authify'); ?></label>
            <input type="text" name="user_login" id="user_login" class="form-control" required>
            <span class="form-text"><?php _e('Имя пользователя должно содержать только буквы, цифры и знаки подчеркивания', 'wp-authify'); ?></span>
        </div>

        <div class="form-group">
            <label for="user_email"><?php _e('Email', 'wp-authify'); ?></label>
            <input type="email" name="user_email" id="user_email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="user_pass"><?php _e('Пароль', 'wp-authify'); ?></label>
            <input type="password" name="user_pass" id="user_pass" class="form-control" required>
            <span class="form-text"><?php _e('Пароль должен содержать минимум 8 символов', 'wp-authify'); ?></span>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary"><?php _e('Зарегистрироваться', 'wp-authify'); ?></button>
        </div>
    </form>
</div>

<style>
.wp-authify-register-form {
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

.form-description {
    color: #666;
    margin: 0;
    font-size: 0.9rem;
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

.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #666;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.wp-authify-register-form form');
    const password = document.getElementById('user_pass');
    const confirmPassword = document.getElementById('user_pass_confirm');

    form.addEventListener('submit', function(e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert('<?php _e('Пароли не совпадают!', 'wp-authify'); ?>');
        }
    });
});
</script> 