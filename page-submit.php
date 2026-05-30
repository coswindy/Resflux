<?php
get_header();
?>

<div class="content-area content-medium">
    <?php while (have_posts()) : the_post(); ?>
        <article class="entry">
            <header class="entry-header text-center">
                <h1><?php the_title(); ?></h1>
            </header>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php echo do_shortcode('[techpress_submit_form]'); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
