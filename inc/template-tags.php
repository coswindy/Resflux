<?php

function techpress_breadcrumbs() {
    \TechPress\Theme::instance()->get('breadcrumbs')->render();
}

function techpress_reading_time($post_id = 0) {
    echo \TechPress\Theme::instance()->get('reading_time')->get($post_id);
}

function techpress_related_posts($post_id = 0, $count = 3) {
    return \TechPress\Theme::instance()->get('related_posts')->get($post_id, $count);
}

function techpress_get_thumbnail_url($post_id = 0, $skip_content = false) {
    return \TechPress\Theme::instance()->get('thumbnail')->get_url($post_id, $skip_content);
}

function techpress_the_thumbnail($post_id = 0, $size = 'large', $class = '', $skip_content = false, $attr = []) {
    \TechPress\Theme::instance()->get('thumbnail')->the_img($post_id, $size, $class, $skip_content, $attr);
}

function techpress_get_post_views($post_id = 0) {
    return \TechPress\Theme::instance()->get('post_views')->get($post_id);
}

function techpress_ad_slot($slot, $class = '') {
    \TechPress\Theme::instance()->get('ad_manager')->render($slot, $class);
}

function techpress_has_ad($slot) {
    return \TechPress\Theme::instance()->get('ad_manager')->has($slot);
}

function techpress_social_icon($key, $label, $svg) {
    return \TechPress\Theme::instance()->get('social_icons')->get_icon($key, $label, $svg);
}

function techpress_social_links() {
    return \TechPress\Theme::instance()->get('social_icons')->get_links();
}

function techpress_social_footer() {
    \TechPress\Theme::instance()->get('social_icons')->render_footer();
}

function techpress_category_icon($term_id = 0) {
    return \TechPress\Theme::instance()->get('category_icons')->get_icon($term_id);
}

function techpress_get_friend_links($limit = 50) {
    return \TechPress\Theme::instance()->get('friend_links')->get_all($limit);
}

function techpress_get_home_resources($limit = 6) {
    return \TechPress\Theme::instance()->get('home_modules')->get_resources($limit);
}

function techpress_get_home_navigation_links($limit = 8) {
    return \TechPress\Theme::instance()->get('home_modules')->get_navigation_links($limit);
}

function techpress_home_layout() {
    return techpress_setting('techpress_home_layout', 'grid');
}

function techpress_highlight_search_terms($text) {
    $query = trim(get_search_query(false));
    $text = esc_html(wp_strip_all_tags($text));

    if ('' === $query || '' === $text) {
        return $text;
    }

    $terms = preg_split('/\s+/u', $query, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($terms as $term) {
        $term = preg_quote(esc_html($term), '/');
        if ('' === $term) {
            continue;
        }
        $text = preg_replace('/(' . $term . ')/iu', '<mark class="search-highlight">$1</mark>', $text);
    }

    return $text;
}

function techpress_comment_callback($comment, $args, $depth) {
    $tag = ('div' === $args['style']) ? 'div' : 'li';
    ?>
    <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class('comment-item'); ?>>
        <div class="comment-body">
            <div class="comment-author">
                <?php echo get_avatar($comment, $args['avatar_size']); ?>
                <div>
                    <cite class="fn"><?php comment_author_link(); ?></cite>
                    <span class="comment-meta">
                        <time datetime="<?php comment_time('c'); ?>"><?php echo esc_html(human_time_diff(get_comment_time('U'), current_time('timestamp'))) . '前'; ?></time>
                        <?php if ($comment->comment_approved == '0') : ?>
                            <span class="comment-awaiting">等待审核</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="comment-text"><?php comment_text(); ?></div>
            <div class="comment-reply">
                <?php
                comment_reply_link(array_merge($args, [
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth'],
                    'reply_text' => '回复',
                ]));
                ?>
            </div>
        </div>
    <?php
}

function techpress_render_post_card($layout = 'grid') {
    if ('list' === $layout) : ?>
        <article class="blog-post-card<?php echo is_sticky() ? ' sticky' : ''; ?>">
            <div class="thumb">
                <a href="<?php the_permalink(); ?>">
                    <?php techpress_the_thumbnail(get_the_ID(), 'techpress-list'); ?>
                </a>
            </div>
            <div class="post-body">
                <h2><?php if (is_sticky()) : ?><span class="sticky-inline"><?php _e('置顶：', 'techpress'); ?></span><?php endif; ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="meta">
                    <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                    <span>· <?php the_category(', '); ?></span>
                    <span>· <?php comments_number('0 评论', '1 评论', '% 评论'); ?></span>
                    <span>· <?php techpress_reading_time(); ?></span>
                    <span>· <?php echo esc_html(techpress_get_post_views()); ?> <?php _e('次阅读', 'techpress'); ?></span>
                </div>
                <div class="excerpt"><?php echo wp_trim_words(get_the_excerpt(), 40); ?></div>
            </div>
        </article>
    <?php else : ?>
        <article class="grid-item<?php echo is_sticky() ? ' sticky' : ''; ?>">
            <a href="<?php the_permalink(); ?>" class="thumb">
                <?php techpress_the_thumbnail(get_the_ID(), 'techpress-grid'); ?>
                <?php $cats = get_the_category();
                if (!empty($cats)) :
                    $icon = techpress_category_icon($cats[0]->term_id); ?>
                    <span class="cat-badge"><?php echo $icon . esc_html($cats[0]->name); ?></span>
                <?php endif; ?>
            </a>
            <div class="info">
                <h3><?php if (is_sticky()) : ?><span class="sticky-inline"><?php _e('置顶：', 'techpress'); ?></span><?php endif; ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="meta-row">
                    <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
                    <span><?php techpress_reading_time(); ?></span>
                </div>
            </div>
        </article>
    <?php endif;
}
