<?php get_header(); ?>

<div class="content-area">
    <?php while (have_posts()) : the_post(); ?>
        <article class="entry">
            <header class="entry-header">
                <h1><?php the_title(); ?></h1>
            </header>

            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
