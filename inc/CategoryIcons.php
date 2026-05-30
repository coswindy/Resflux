<?php

namespace TechPress;

class CategoryIcons {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_fa']);
        add_action('wp_enqueue_scripts', [$this, 'frontend_enqueue_fa']);

        foreach (['category', 'post_tag'] as $tax) {
            add_action($tax . '_add_form_fields', [$this, 'field_add'], 10);
            add_action($tax . '_edit_form_fields', [$this, 'field_edit'], 10);
            add_action('created_' . $tax, [$this, 'field_save']);
            add_action('edited_' . $tax, [$this, 'field_save']);
        }
    }

    public function admin_enqueue_fa() {
        wp_enqueue_style('techpress-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], null);
    }

    public function frontend_enqueue_fa() {
        if (is_front_page() || is_singular('post') || is_category() || is_tag()) {
            wp_enqueue_style('techpress-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], null);
        }
    }

    public function field_add($tax) {
        ?>
        <div class="form-field">
            <label for="techpress_icon"><?php _e('图标 (Font Awesome)', 'techpress'); ?></label>
            <input type="text" name="techpress_icon" id="techpress_icon" value="" placeholder="fa-solid fa-rocket">
            <p><a href="https://fontawesome.com/icons" target="_blank">Font Awesome 图标库</a> — 如 <code>fa-solid fa-code</code> 也支持直接输入 Emoji</p>
        </div>
        <?php
    }

    public function field_edit($term) {
        $icon = get_term_meta($term->term_id, 'techpress_icon', true);
        ?>
        <tr class="form-field">
            <th><label for="techpress_icon"><?php _e('图标 (Font Awesome)', 'techpress'); ?></label></th>
            <td>
                <input type="text" name="techpress_icon" id="techpress_icon" value="<?php echo esc_attr($icon); ?>" placeholder="fa-solid fa-rocket">
                <p class="description">如 <code>fa-solid fa-code</code> 或直接输入 Emoji</p>
                <?php if ($icon) : ?>
                    <span style="font-size:24px;margin-top:8px;display:inline-block;">
                        <?php if (strpos($icon, 'fa-') !== false) : ?>
                            <i class="<?php echo esc_attr($icon); ?>"></i>
                        <?php else : ?>
                            <?php echo esc_html($icon); ?>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        <?php
    }

    public function field_save($term_id) {
        if (isset($_POST['techpress_icon'])) {
            update_term_meta($term_id, 'techpress_icon', sanitize_text_field(wp_unslash($_POST['techpress_icon'])));
        }
    }

    public function get_icon($term_id = 0) {
        if (!$term_id) {
            $cats = get_the_category();
            if (empty($cats)) {
                return '';
            }
            $term_id = $cats[0]->term_id;
        }

        $icon = get_term_meta($term_id, 'techpress_icon', true);
        if (!$icon) {
            return '';
        }

        if (strpos($icon, 'fa-') !== false) {
            return '<i class="' . esc_attr($icon) . '" style="margin-right:4px;"></i>';
        }
        return '<span style="margin-right:4px;">' . esc_html($icon) . '</span>';
    }
}
