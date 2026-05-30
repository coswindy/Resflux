<?php

namespace TechPress\Widgets;

class AdWidget extends \WP_Widget {

    public function __construct() {
        parent::__construct('techpress_ad', __('Resflux 广告位', 'techpress'), [
            'description' => __('在侧边栏展示广告代码', 'techpress'),
        ]);
    }

    public function widget($args, $instance) {
        $code = !empty($instance['code']) ? $instance['code'] : '';
        if (!$code) {
            return;
        }
        echo $args['before_widget'];
        echo '<div class="ad-container ad-widget">' . $code . '</div>';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $code = $instance['code'] ?? '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('code'); ?>"><?php _e('广告代码（HTML/JS）:', 'techpress'); ?></label>
            <textarea class="widefat code" rows="8" id="<?php echo $this->get_field_id('code'); ?>" name="<?php echo $this->get_field_name('code'); ?>"><?php echo esc_textarea($code); ?></textarea>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['code'] = wp_kses($new_instance['code'], [
            'script'  => true,
            'ins'     => ['class', 'style', 'data-ad-client', 'data-ad-slot', 'data-ad-format', 'data-full-width-responsive'],
            'iframe'  => ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'style', 'scrolling', 'sandbox'],
            'div'     => ['class', 'id', 'style', 'data-*'],
            'span'    => ['class', 'id', 'style'],
            'a'       => ['href', 'target', 'rel', 'class', 'id', 'style'],
            'img'     => ['src', 'alt', 'width', 'height', 'class', 'style', 'loading'],
            'p'       => ['class', 'style'],
            'br'      => true,
            'noscript' => true,
            'style'   => ['type'],
            'link'    => ['href', 'rel', 'type'],
            'meta'    => ['name', 'content', 'charset', 'http-equiv'],
        ]);
        return $instance;
    }
}
