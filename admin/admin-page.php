<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wp-authify-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="card">
        <h2><?php _e('Блокировка пользователя', 'wp-authify'); ?></h2>
        <form method="post" action="" class="block-user-form">
            <?php wp_nonce_field('wp_authify_action'); ?>
            <input type="hidden" name="action" value="block_user">
            
            <div class="form-group">
                <label><?php _e('Способ выбора пользователя', 'wp-authify'); ?></label>
                <div class="search-type-selector">
                    <label class="radio-label">
                        <input type="radio" name="search_type" value="input" checked>
                        <span><?php _e('Поиск по имени/email', 'wp-authify'); ?></span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="search_type" value="select">
                        <span><?php _e('Выбор из списка', 'wp-authify'); ?></span>
                    </label>
                </div>
            </div>

            <div class="form-group search-input-group">
                <label for="user_search"><?php _e('Поиск пользователя', 'wp-authify'); ?></label>
                <input type="text" id="user_search" class="regular-text" placeholder="<?php _e('Введите имя пользователя или email...', 'wp-authify'); ?>">
                <div id="user_search_results" class="search-results"></div>
            </div>

            <div class="form-group search-select-group" style="display: none;">
                <label for="user_id"><?php _e('Выберите пользователя', 'wp-authify'); ?></label>
                <?php
                wp_dropdown_users(array(
                    'name' => 'user_id',
                    'id' => 'user_id',
                    'show_option_none' => __('Выберите пользователя...', 'wp-authify'),
                    'exclude' => array(get_current_user_id()),
                    'class' => 'regular-text',
                ));
                ?>
            </div>

            <div class="form-group">
                <label for="reason"><?php _e('Причина блокировки', 'wp-authify'); ?></label>
                <textarea name="reason" id="reason" rows="3" class="large-text"></textarea>
            </div>
            
            <?php submit_button(__('Заблокировать пользователя', 'wp-authify')); ?>
        </form>
    </div>

    <div class="card">
        <h2><?php _e('Заблокированные пользователи', 'wp-authify'); ?></h2>
        
        <div class="table-search">
            <input type="text" id="table_search" class="regular-text" placeholder="<?php _e('Поиск по имени, email или причине...', 'wp-authify'); ?>">
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Пользователь', 'wp-authify'); ?></th>
                    <th><?php _e('Заблокирован', 'wp-authify'); ?></th>
                    <th><?php _e('Дата', 'wp-authify'); ?></th>
                    <th><?php _e('Причина', 'wp-authify'); ?></th>
                    <th><?php _e('Действия', 'wp-authify'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($blocked_users)) : ?>
                    <?php foreach ($blocked_users as $user) : ?>
                        <tr>
                            <td><?php echo esc_html($user->blocked_user_name); ?></td>
                            <td><?php echo esc_html($user->blocked_by_name); ?></td>
                            <td><?php echo esc_html($user->blocked_date); ?></td>
                            <td><?php echo esc_html($user->reason); ?></td>
                            <td>
                                <form method="post" action="" style="display:inline;">
                                    <?php wp_nonce_field('wp_authify_action'); ?>
                                    <input type="hidden" name="action" value="unblock_user">
                                    <input type="hidden" name="user_id" value="<?php echo esc_attr($user->user_id); ?>">
                                    <?php submit_button(__('Разблокировать', 'wp-authify'), 'small', 'submit', false); ?>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5"><?php _e('Заблокированных пользователей не найдено.', 'wp-authify'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.wp-authify-admin .card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-top: 20px;
    padding: 20px;
}

.wp-authify-admin .form-group {
    margin-bottom: 15px;
}

.wp-authify-admin label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.search-type-selector {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.radio-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.radio-label input[type="radio"] {
    margin-right: 8px;
}

.wp-authify-admin .search-results {
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    display: none;
}

.wp-authify-admin .search-results.active {
    display: block;
}

.wp-authify-admin .search-result-item {
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.wp-authify-admin .search-result-item:hover {
    background: #f5f5f5;
}

.wp-authify-admin .search-result-item:last-child {
    border-bottom: none;
}

.table-search {
    margin-bottom: 15px;
}

.table-search input {
    width: 100%;
    max-width: 400px;
}

.wp-authify-admin tr.hidden {
    display: none;
}
</style>

<script>
jQuery(document).ready(function($) {
    var searchTimeout;
    var $searchInput = $('#user_search');
    var $searchResults = $('#user_search_results');
    var $userSelect = $('#user_id');
    var $searchTypeInputs = $('input[name="search_type"]');
    var $searchInputGroup = $('.search-input-group');
    var $searchSelectGroup = $('.search-select-group');

    // Переключение между способами поиска
    $searchTypeInputs.on('change', function() {
        var searchType = $(this).val();
        if (searchType === 'input') {
            $searchInputGroup.show();
            $searchSelectGroup.hide();
            $userSelect.val('');
        } else {
            $searchInputGroup.hide();
            $searchSelectGroup.show();
            $searchInput.val('');
            $searchResults.removeClass('active').empty();
        }
    });

    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        var query = $(this).val();

        if (query.length < 2) {
            $searchResults.removeClass('active').empty();
            return;
        }

        searchTimeout = setTimeout(function() {
            $.ajax({
                url: ajaxurl,
                data: {
                    action: 'wp_authify_search_users',
                    query: query,
                    nonce: '<?php echo wp_create_nonce('wp_authify_search_users'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        var html = '';
                        response.data.forEach(function(user) {
                            html += '<div class="search-result-item" data-id="' + user.ID + '" data-name="' + user.display_name + '">' +
                                   user.display_name + ' (' + user.user_email + ')' +
                                   '</div>';
                        });
                        $searchResults.html(html).addClass('active');
                    }
                }
            });
        }, 300);
    });

    $searchResults.on('click', '.search-result-item', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $userSelect.val(id);
        $searchInput.val(name);
        $searchResults.removeClass('active');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.form-group').length) {
            $searchResults.removeClass('active');
        }
    });

    // Поиск в таблице
    $('#table_search').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        
        $('.wp-list-table tbody tr').each(function() {
            var $row = $(this);
            var rowText = $row.text().toLowerCase();
            
            if (rowText.indexOf(searchText) === -1) {
                $row.addClass('hidden');
            } else {
                $row.removeClass('hidden');
            }
        });
    });
});
</script> 