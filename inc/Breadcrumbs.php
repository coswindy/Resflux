<?php

namespace TechPress;

class Breadcrumbs {

    public function __construct() {}

    public function render() {
        if (is_front_page()) {
            return;
        }

        $align = techpress_setting('techpress_breadcrumb_align', 'left');
        if (!in_array($align, ['left', 'center', 'right'], true)) {
            $align = 'left';
        }
        ?>
        <nav class="breadcrumbs breadcrumbs--<?php echo esc_attr($align); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('首页', 'techpress'); ?></a>
            <span class="sep">/</span>
            <?php if (is_single()) : ?>
                <?php $cats = get_the_category();
                if (!empty($cats)) : ?>
                    <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>"><?php echo esc_html($cats[0]->name); ?></a>
                    <span class="sep">/</span>
                <?php endif; ?>
                <span><?php echo esc_html(get_the_title()); ?></span>
            <?php elseif (is_category()) : ?>
                <span><?php echo esc_html(single_cat_title('', false)); ?></span>
            <?php elseif (is_tag()) : ?>
                <span>#<?php echo esc_html(single_tag_title('', false)); ?></span>
            <?php elseif (is_search()) : ?>
                <span><?php printf(__('搜索: %s', 'techpress'), esc_html(get_search_query())); ?></span>
            <?php elseif (is_page()) : ?>
                <span><?php echo esc_html(get_the_title()); ?></span>
            <?php elseif (is_archive()) : ?>
                <span><?php the_archive_title(); ?></span>
            <?php elseif (is_404()) : ?>
                <span>404</span>
            <?php endif; ?>
        </nav>
        <?php
    }
}
