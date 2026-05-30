<?php

namespace TechPress\Widgets;

class RecentPosts extends \WP_Widget {

    public function __construct() {
        parent::__construct('techpress_recent', __('Resflux 最新文章', 'techpress'), [
            'description' => __('显示最新发布的文章', 'techpress'),
        ]);
    }

    public function widget($args, $instance) {
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        $show_date = !empty($instance['show_date']);
        $title = esc_html($instance['title'] ?? __('最新文章', 'techpress'));

        $cache_key = 'techpress_recent_' . $count . '_' . ($show_date ? '1' : '0');
        $cached = wp_cache_get($cache_key, 'widgets');
        if (false !== $cached) {
            echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . $cached . $args['after_widget'];
            return;
        }

        ob_start();

        $query = new \WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'update_term_meta_cache' => false,
        ]);

        if ($query->have_posts()) :
            echo '<div class="widget_recent_posts">';
            while ($query->have_posts()) :
                $query->the_post(); ?>
                <div class="recent-item">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url(\techpress_get_thumbnail_url(get_the_ID(), true)); ?>" class="recent-thumb" alt="" loading="lazy">
                    </a>
                    <div class="recent-info">
                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <?php if ($show_date) : ?>
                            <span class="date"><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile;
            echo '</div>';
        endif;
        wp_reset_postdata();

        $html = ob_get_clean();
        wp_cache_set($cache_key, $html, 'widgets', 3600);

        echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . $html . $args['after_widget'];
    }

    public function form($instance) {
        $title = $instance['title'] ?? __('最新文章', 'techpress');
        $count = $instance['count'] ?? 5;
        $show_date = !empty($instance['show_date']);
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
            <input class="checkbox" id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" <?php checked($show_date); ?>>
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('显示日期', 'techpress'); ?></label>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['count'] = absint($new_instance['count']);
        $instance['show_date'] = !empty($new_instance['show_date']);
        return $instance;
    }
}
