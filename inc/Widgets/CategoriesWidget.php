<?php

namespace TechPress\Widgets;

class CategoriesWidget extends \WP_Widget {

    public function __construct() {
        parent::__construct('techpress_categories', __('Resflux 分类目录', 'techpress'), [
            'description' => __('以列表或下拉方式展示文章分类', 'techpress'),
        ]);
    }

    public function widget($args, $instance) {
        $title     = esc_html($instance['title'] ?? __('分类目录', 'techpress'));
        $show_count   = !empty($instance['show_count']);
        $show_post_count = !empty($instance['show_post_count']);
        $hierarchical  = !empty($instance['hierarchical']);
        $dropdown  = !empty($instance['dropdown']);
        $max_depth = absint($instance['max_depth'] ?? 0);

        echo $args['before_widget'];
        echo $args['before_title'] . $title . $args['after_title'];

        if ($dropdown) {
            $cat_args = [
                'show_option_all' => false,
                'show_count'      => $show_post_count,
                'hierarchical'    => $hierarchical,
                'depth'           => $max_depth ?: 1,
                'selected'        => is_category() ? get_queried_object_id() : 0,
                'value_field'     => 'term_id',
            ];
            echo '<div class="widget_categories_dropdown">';
            wp_dropdown_categories($cat_args);
            echo '</div>';
            ?>
            <script>
            (function() {
                var dropdown = document.getElementById('cat');
                if (dropdown) {
                    dropdown.addEventListener('change', function() {
                        if (this.value) {
                            location.href = '<?php echo esc_url(home_url('/')); ?>?cat=' + this.value;
                        }
                    });
                }
            })();
            </script>
            <?php
        } else {
            $cat_args = [
                'orderby'    => 'name',
                'order'      => 'ASC',
                'show_count' => $show_post_count,
                'hierarchical' => $hierarchical,
                'depth'      => $max_depth ?: 0,
                'title_li'   => '',
            ];
            echo '<ul class="widget_categories_list">';
            wp_list_categories($cat_args);
            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title     = $instance['title'] ?? __('分类目录', 'techpress');
        $show_post_count = !empty($instance['show_post_count']);
        $hierarchical    = !empty($instance['hierarchical']);
        $dropdown        = !empty($instance['dropdown']);
        $max_depth       = $instance['max_depth'] ?? 0;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:', 'techpress'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" type="checkbox" <?php checked($dropdown); ?>>
            <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('使用下拉方式显示', 'techpress'); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('show_post_count'); ?>" name="<?php echo $this->get_field_name('show_post_count'); ?>" type="checkbox" <?php checked($show_post_count); ?>>
            <label for="<?php echo $this->get_field_id('show_post_count'); ?>"><?php _e('显示文章数量', 'techpress'); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>" type="checkbox" <?php checked($hierarchical); ?>>
            <label for="<?php echo $this->get_field_id('hierarchical'); ?>"><?php _e('显示层级结构', 'techpress'); ?></label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('max_depth'); ?>"><?php _e('最大层级深度:', 'techpress'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('max_depth'); ?>" name="<?php echo $this->get_field_name('max_depth'); ?>" type="number" value="<?php echo esc_attr($max_depth); ?>" min="0" max="10" step="1">
            <span class="description"><?php _e('0 = 不限制', 'techpress'); ?></span>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title']          = sanitize_text_field($new_instance['title']);
        $instance['show_post_count'] = !empty($new_instance['show_post_count']);
        $instance['hierarchical']   = !empty($new_instance['hierarchical']);
        $instance['dropdown']       = !empty($new_instance['dropdown']);
        $instance['max_depth']      = absint($new_instance['max_depth'] ?? 0);
        return $instance;
    }
}
