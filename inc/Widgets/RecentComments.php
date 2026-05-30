<?php

namespace TechPress\Widgets;

class RecentComments extends \WP_Widget {

    public function __construct() {
        parent::__construct('techpress_comments', __('Resflux 最新评论', 'techpress'), [
            'description' => __('展示最新通过审核的评论', 'techpress'),
        ]);
    }

    public function widget($args, $instance) {
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        $show_avatar = !empty($instance['show_avatar']);
        $title = esc_html($instance['title'] ?? __('最新评论', 'techpress'));

        $cache_key = 'techpress_comments_' . $count . '_' . ($show_avatar ? '1' : '0');
        $cached = wp_cache_get($cache_key, 'widgets');
        if (false !== $cached) {
            echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . $cached . $args['after_widget'];
            return;
        }

        ob_start();

        $comments = get_comments([
            'number'      => $count,
            'status'      => 'approve',
            'post_status' => 'publish',
            'type'        => 'comment',
        ]);

        if ($comments) :
            echo '<div class="widget_recent_comments">';
            foreach ($comments as $comment) :
                $comment_excerpt = wp_trim_words(strip_tags($comment->comment_content), 12, '...');
                ?>
                <div class="comment-item-sidebar">
                    <?php if ($show_avatar) : ?>
                        <?php echo get_avatar($comment, 36, '', '', ['class' => 'comment-avatar-sidebar']); ?>
                    <?php endif; ?>
                    <div class="comment-info-sidebar">
                        <span class="comment-author-sidebar"><?php echo esc_html($comment->comment_author); ?></span>
                        <a class="comment-text-sidebar" href="<?php echo esc_url(get_comment_link($comment)); ?>"><?php echo esc_html($comment_excerpt); ?></a>
                    </div>
                </div>
            <?php endforeach;
            echo '</div>';
        endif;

        $html = ob_get_clean();
        wp_cache_set($cache_key, $html, 'widgets', 3600);

        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . $html . $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? __('最新评论', 'techpress');
        $count = $instance['count'] ?? 5;
        $show_avatar = !empty($instance['show_avatar']);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'techpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('显示数量:', 'techpress'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo esc_attr($count); ?>" min="1" max="10">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_avatar'); ?>" name="<?php echo $this->get_field_name('show_avatar'); ?>" type="checkbox" <?php checked($show_avatar); ?>>
            <label for="<?php echo $this->get_field_id('show_avatar'); ?>"><?php _e('显示头像', 'techpress'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['count'] = absint($new_instance['count']);
        $instance['show_avatar'] = !empty($new_instance['show_avatar']);
        return $instance;
    }
}
