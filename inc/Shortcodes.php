<?php

namespace TechPress;

class Shortcodes {

    public function __construct() {
        add_shortcode('techpress_login_form', [$this, 'login_form']);
        add_shortcode('techpress_submit_form', [$this, 'submit_form']);
    }

    public function login_form() {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            return '<div class="auth-card"><p>' . sprintf(__('已登录：%s', 'techpress'), '<strong>' . esc_html($user->display_name) . '</strong>') . '</p>
            <p><a href="' . esc_url(wp_logout_url(home_url('/'))) . '">' . __('退出登录', 'techpress') . '</a> | <a href="' . esc_url(home_url('/submit')) . '">' . __('投稿', 'techpress') . '</a></p></div>';
        }

        ob_start();
        ?>
        <div class="auth-card">
            <div class="auth-tabs">
                <button class="auth-tab active" data-tab="login"><?php _e('登录', 'techpress'); ?></button>
                <button class="auth-tab" data-tab="register"><?php _e('注册', 'techpress'); ?></button>
            </div>
            <div class="auth-form active" id="auth-login">
                <form class="ajax-auth-form" data-action="login">
                    <p><input type="text" name="username" placeholder="<?php esc_attr_e('用户名', 'techpress'); ?>" required></p>
                    <p><input type="password" name="password" placeholder="<?php esc_attr_e('密码', 'techpress'); ?>" required></p>
                    <p><button type="submit" class="btn-primary"><?php _e('登录', 'techpress'); ?></button></p>
                    <p class="auth-msg"></p>
                </form>
            </div>
            <div class="auth-form" id="auth-register">
                <form class="ajax-auth-form" data-action="register">
                    <p style="display:none"><input type="text" name="techpress_hp" autocomplete="off" tabindex="-1"></p>
                    <p><input type="text" name="username" placeholder="<?php esc_attr_e('用户名', 'techpress'); ?>" required></p>
                    <p><input type="email" name="email" placeholder="<?php esc_attr_e('邮箱', 'techpress'); ?>" required></p>
                    <p><input type="password" name="password" placeholder="<?php esc_attr_e('密码', 'techpress'); ?>" required minlength="6"></p>
                    <p><input type="password" name="password_confirm" placeholder="<?php esc_attr_e('确认密码', 'techpress'); ?>" required minlength="6"></p>
                    <p><button type="submit" class="btn-primary"><?php _e('注册', 'techpress'); ?></button></p>
                    <p class="auth-msg"></p>
                </form>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function submit_form() {
        if (!is_user_logged_in()) {
            return '<div class="auth-card"><p>' . sprintf(__('请先 <a href="%s">登录</a> 后投稿', 'techpress'), esc_url(home_url('/login'))) . '</p></div>';
        }

        $cats = get_categories(['hide_empty' => false, 'exclude' => 1]);
        ob_start();
        ?>
        <div class="submit-card">
            <h3><?php _e('发布新文章', 'techpress'); ?></h3>
            <form id="techpress-submit-form" enctype="multipart/form-data">
                <p><input type="text" name="post_title" placeholder="<?php esc_attr_e('文章标题', 'techpress'); ?>" required></p>
                <p>
                    <select name="post_cat">
                        <option value=""><?php _e('选择分类', 'techpress'); ?></option>
                        <?php foreach ($cats as $cat) : ?>
                            <option value="<?php echo esc_attr($cat->term_id); ?>"><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <p><input type="text" name="post_tags" placeholder="<?php esc_attr_e('标签（英文逗号分隔）', 'techpress'); ?>"></p>
                <p><textarea name="post_content" rows="12" placeholder="<?php esc_attr_e('文章内容...', 'techpress'); ?>" required></textarea></p>
                <p><label><?php _e('特色图像：', 'techpress'); ?><input type="file" name="featured_image" accept="image/*"></label></p>
                <p><input type="url" name="thumb_url" placeholder="<?php esc_attr_e('或输入外链缩略图 URL（可选）', 'techpress'); ?>"></p>
                <p><button type="submit" class="btn-primary"><?php _e('提交审核', 'techpress'); ?></button></p>
                <p class="submit-msg"></p>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
