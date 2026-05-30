<div class="author-box">
    <div class="author-avatar">
        <?php echo get_avatar(get_the_author_meta('ID'), 80); ?>
    </div>
    <div class="author-info">
        <h4><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php the_author(); ?></a></h4>
        <p><?php the_author_meta('description'); ?></p>
    </div>
</div>
