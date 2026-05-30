<?php

namespace TechPress\Widgets;

class PopularPosts extends \WP_Widget {

    public function __construct() {
        parent::__construct('techpress_popular', __('Resflux 热门文章', 'techpress'), [
            'description' => __('按浏览量排序展示热门文章', 'techpress'),
        ]);
    }

    public function widget($args, $instance) {
        $count = !empty($instance['count']) ? absint($instance['count']) : 5;
        $title = esc_html($instance['title'] ?? __('热门文章', 'techpress'));

        $cache_key = 'techpress_popular_' . $count;
        $cached = wp_cache_get($cache_key, 'widgets');
        if (false !== $cached) {
            echo $args['before_widget'] . $args['before_title'] . $title . $args['after_title'] . $cached . $args['after_widget'];
            return;
        }

        ob_start();

        $query = new \WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => $count,
            'meta_key'            => '_techpress_views',
            'orderby'             => 'meta_value_num',
            'order'               => 'DESC',
            'ignore_sticky_posts' => true,
            'no_found_rows'       => true,
            'update_term_meta_cache' => false,
        ]);

        if ($query->have_posts()) :
            echo '<div class="widget_popular_posts">';
            while ($query->have_posts()) :
                $query->the_post(); ?>
                <div class="popular-item">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo esc_url(\techpress_get_thumbnail_url(get_the_ID())); ?>" class="popular-thumb" alt="" loading="lazy">
                    </a>
                    <div class="popular-info">
                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <span class="views"><?php echo \techpress_get_post_views(); ?> <?php _e('次阅读', 'techpress'); ?></span>
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
        $title = $instance['title'] ?? __('热门文章', 'techpress');
        $count = $instance['count'] ?? 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'techpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('显示数量:', 'techpress'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" value="<?php echo esc_attr($count); ?>" min="1" max="20">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance          = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['count'] = absint($new_instance['count']);
        return $instance;
    }
}
