<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            $comment_count = get_comments_number();
            printf(
                _nx('(%s) 条评论', '(%s) 条评论', $comment_count, 'comments title', 'techpress'),
                number_format_i18n($comment_count)
            );
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => 'techpress_comment_callback',
            ]);
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

        <?php if (!comments_open()) : ?>
            <p class="no-comments"><?php _e('评论已关闭。', 'techpress'); ?></p>
        <?php endif; ?>

    <?php endif; ?>

    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " required" : '');

    $fields = [
        'author'  => '<p class="comment-form-author">' .
            '<input id="author" name="author" type="text" placeholder="' . esc_attr__('姓名', 'techpress') . ($req ? ' *' : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30" maxlength="245"' . $aria_req . ' /></p>',
        'email'   => '<p class="comment-form-email">' .
            '<input id="email" name="email" type="email" placeholder="' . esc_attr__('邮箱', 'techpress') . ($req ? ' *' : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" maxlength="100"' . $aria_req . ' /></p>',
        'url'     => '<p class="comment-form-url">' .
            '<input id="url" name="url" type="text" placeholder="' . esc_attr__('网站', 'techpress') . '" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" maxlength="200" /></p>',
        'cookies' => '',
    ];

    comment_form([
        'title_reply_before'   => '<h4 id="reply-title" class="comment-reply-title">',
        'title_reply_after'    => '</h4>',
        'title_reply'          => __('发表评论', 'techpress'),
        'title_reply_to'       => __('回复 %s', 'techpress'),
        'cancel_reply_link'    => __('取消回复', 'techpress'),
        'label_submit'         => __('发表评论', 'techpress'),
        'comment_field'        => '<p class="comment-form-comment"><textarea id="comment" name="comment" placeholder="' . esc_attr__('写下你的评论...', 'techpress') . '" cols="45" rows="6" required></textarea></p>',
        'fields'               => $fields,
        'class_submit'         => 'submit comment-submit',
        'submit_button'        => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
    ]);
    ?>

</div>
