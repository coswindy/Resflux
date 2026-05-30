<?php
$related = techpress_related_posts(get_the_ID(), 3);
if ($related->have_posts()) : ?>
<div class="related-posts">
    <h3>相关推荐</h3>
    <div class="related-grid">
        <?php while ($related->have_posts()) : $related->the_post(); ?>
        <div class="related-item">
            <a href="<?php the_permalink(); ?>" class="thumb">
                <?php techpress_the_thumbnail(get_the_ID(), 'techpress-related'); ?>
            </a>
            <div class="info">
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <span class="date"><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php endif; wp_reset_postdata(); ?>
