<?php

namespace TechPress;

class Ajax {

    private $action_map = [
        'techpress_record_view'   => 'record_view',
        'techpress_load_more'     => 'load_more',
        'techpress_load_carousel' => 'load_carousel',
        'techpress_login'         => 'login',
        'techpress_register'      => 'register',
        'techpress_submit_post'   => 'submit_post',
    ];

    public function __construct() {
        foreach ($this->action_map as $action => $method) {
            add_action("wp_ajax_{$action}", [$this, $method]);
            if ('submit_post' !== $method) {
                add_action("wp_ajax_nopriv_{$action}", [$this, $method]);
            }
        }
    }

    private function verify() {
        check_ajax_referer('techpress_nonce', 'nonce');
    }

    public function record_view() {
        $this->verify();

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        if (!$post_id || 'publish' !== get_post_status($post_id) || wp_is_post_revision($post_id)) {
            wp_send_json_error('invalid');
        }

        $count = (int) get_post_meta($post_id, '_techpress_views', true);
        update_post_meta($post_id, '_techpress_views', $count + 1);
        wp_send_json_success(['views' => $count + 1]);
    }

    public function load_more() {
        $this->verify();

        $page   = isset($_POST['page']) ? absint($_POST['page']) : 2;
        $cat_id = isset($_POST['cat']) ? absint($_POST['cat']) : 0;
        $layout = techpress_home_layout();
        $ad_slot = 'grid' === $layout ? 'grid_infeed' : 'list_infeed';
        $ad_freq = 'grid' === $layout ? 4 : 3;
        $ad_class = 'grid' === $layout ? 'ad-slot-grid-infeed' : 'ad-slot-list-infeed';
        $ad_wrap_class = 'grid' === $layout ? 'grid-item ad-grid-placeholder' : 'blog-post-card infeed-ad-placeholder';

        $args = [
            'post_type'           => 'post',
            'posts_per_page'      => 8,
            'paged'               => $page,
            'ignore_sticky_posts' => false,
        ];

        if ($cat_id > 0) {
            $args['cat'] = $cat_id;
        }

        $query = new \WP_Query($args);
        ob_start();

        if ($query->have_posts()) :
            $count = 0;
            while ($query->have_posts()) :
                $query->the_post();
                $count++;
                if ($count % $ad_freq === 0 && techpress_has_ad($ad_slot)) {
                    echo '<div class="' . esc_attr($ad_wrap_class) . '">';
                    techpress_ad_slot($ad_slot, $ad_class);
                    echo '</div>';
                }
                techpress_render_post_card($layout);
            endwhile;
        endif;
        wp_reset_postdata();

        $html = ob_get_clean();
        wp_send_json_success([
            'html'     => $html,
            'has_next' => $query->max_num_pages > $page,
            'page'     => $page,
        ]);
    }

    public function load_carousel() {
        $this->verify();

        $page  = isset($_POST['page']) ? absint($_POST['page']) : 1;
        $query = new \WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => 5,
            'paged'               => $page,
            'meta_key'            => '_thumbnail_id',
            'ignore_sticky_posts' => true,
            'update_post_meta_cache' => false,
            'update_term_meta_cache' => false,
        ]);

        ob_start();
        if ($query->have_posts()) :
            while ($query->have_posts()) :
                $query->the_post();
                $cats     = get_the_category();
                $cat_name = !empty($cats) ? $cats[0]->name : '';
                ?>
                <div class="carousel-slide">
                    <a href="<?php the_permalink(); ?>">
                        <?php techpress_the_thumbnail(get_the_ID(), 'techpress-carousel'); ?>
                        <div class="carousel-overlay">
                            <?php if ($cat_name) : ?>
                                <span class="carousel-cat"><?php echo esc_html($cat_name); ?></span>
                            <?php endif; ?>
                            <h2><?php the_title(); ?></h2>
                            <div class="carousel-meta">
                                <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                                <span><?php comments_number('0 评论', '1 评论', '% 评论'); ?></span>
                                <span><?php techpress_reading_time(); ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            endwhile;
        endif;
        wp_reset_postdata();

        $html = ob_get_clean();
        wp_send_json_success(['html' => $html, 'has_next' => $query->max_num_pages > $page]);
    }

    public function login() {
        $this->verify();

        if (empty($_POST['username']) || empty($_POST['password'])) {
            wp_send_json_error(['msg' => __('请填写用户名和密码', 'techpress')]);
        }

        $creds = [
            'user_login'    => sanitize_user(wp_unslash($_POST['username'])),
            'user_password' => wp_unslash($_POST['password']),
            'remember'      => true,
        ];

        $user = wp_signon($creds);
        if (is_wp_error($user)) {
            wp_send_json_error(['msg' => __('用户名或密码错误', 'techpress')]);
        }
        wp_send_json_success(['msg' => __('登录成功', 'techpress')]);
    }

    public function register() {
        $this->verify();

        if (!empty($_POST['techpress_hp'])) {
            wp_send_json_error(['msg' => __('提交过于频繁，请稍后再试', 'techpress')]);
        }

        $ip = $this->get_client_ip();
        $rate_key = 'techpress_reg_' . md5($ip);
        $attempts = (int) get_transient($rate_key);
        if ($attempts >= 3) {
            wp_send_json_error(['msg' => __('注册过于频繁，请 1 小时后再试', 'techpress')]);
        }

        $username         = isset($_POST['username']) ? sanitize_user(wp_unslash($_POST['username'])) : '';
        $email            = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
        $password         = isset($_POST['password']) ? wp_unslash($_POST['password']) : '';
        $password_confirm = isset($_POST['password_confirm']) ? wp_unslash($_POST['password_confirm']) : '';

        if (empty($username) || empty($email) || empty($password)) {
            wp_send_json_error(['msg' => __('请填写完整信息', 'techpress')]);
        }
        if ($password !== $password_confirm) {
            wp_send_json_error(['msg' => __('两次密码输入不一致', 'techpress')]);
        }
        if (strlen($password) < 6) {
            wp_send_json_error(['msg' => __('密码长度至少 6 位', 'techpress')]);
        }
        if (username_exists($username)) {
            wp_send_json_error(['msg' => __('用户名已存在', 'techpress')]);
        }
        if (!is_email($email) || email_exists($email)) {
            wp_send_json_error(['msg' => __('邮箱无效或已被注册', 'techpress')]);
        }

        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_send_json_error(['msg' => __('注册失败，请重试', 'techpress')]);
        }

        set_transient($rate_key, $attempts + 1, HOUR_IN_SECONDS);

        wp_signon([
            'user_login'    => $username,
            'user_password' => $password,
            'remember'      => true,
        ]);
        wp_send_json_success(['msg' => __('注册成功', 'techpress')]);
    }

    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR'])));
            return trim($ips[0]);
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return sanitize_text_field(wp_unslash($_SERVER['HTTP_X_REAL_IP']));
        }
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }
        return '0.0.0.0';
    }

    public function submit_post() {
        $this->verify();

        if (!is_user_logged_in()) {
            wp_send_json_error(['msg' => __('请先登录', 'techpress')]);
        }
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(['msg' => __('您没有权限投稿', 'techpress')]);
        }

        $title     = sanitize_text_field(wp_unslash($_POST['post_title'] ?? ''));
        $content   = wp_kses_post(wp_unslash($_POST['post_content'] ?? ''));
        $cat_id    = absint($_POST['post_cat'] ?? 0);
        $tags      = sanitize_text_field(wp_unslash($_POST['post_tags'] ?? ''));
        $thumb_url = esc_url_raw(wp_unslash($_POST['thumb_url'] ?? ''));

        if (empty($title) || empty($content)) {
            wp_send_json_error(['msg' => __('标题和内容不能为空', 'techpress')]);
        }

        $post_data = [
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'pending',
            'post_author'  => get_current_user_id(),
            'post_type'    => 'post',
        ];

        if ($cat_id > 0) {
            $post_data['post_category'] = [$cat_id];
        }

        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id)) {
            wp_send_json_error(['msg' => __('发布失败', 'techpress')]);
        }

        if (!empty($tags)) {
            $tag_array = array_map('trim', explode(',', $tags));
            wp_set_post_tags($post_id, $tag_array);
        }

        if ($thumb_url) {
            update_post_meta($post_id, '_techpress_thumb_url', $thumb_url);
        }

        if (!empty($_FILES['featured_image']) && UPLOAD_ERR_OK === $_FILES['featured_image']['error']) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $attach_id = media_handle_upload('featured_image', $post_id);
            if (!is_wp_error($attach_id)) {
                set_post_thumbnail($post_id, $attach_id);
            }
        }

        wp_send_json_success(['msg' => __('投稿成功，审核后即可发布', 'techpress'), 'post_id' => $post_id]);
    }
}
