<?php get_header(); ?>

<?php foreach (techpress_get_home_modules('top') as $module) : ?>
    <?php get_template_part('template-parts/home/' . str_replace('_', '-', $module)); ?>
<?php endforeach; ?>

<?php
$main_modules = techpress_get_home_modules('main');
$aside_modules = techpress_get_home_modules('aside');
$home_content_classes = ['home-content'];
if (empty($main_modules)) {
    $home_content_classes[] = 'home-content-no-main';
}
if (empty($aside_modules)) {
    $home_content_classes[] = 'home-content-no-aside';
}
?>

<?php if (!empty($main_modules) || !empty($aside_modules)) : ?>
    <div class="<?php echo esc_attr(implode(' ', $home_content_classes)); ?>">
        <?php if (!empty($main_modules)) : ?>
            <div class="content-left">
                <?php foreach ($main_modules as $module) : ?>
                    <?php get_template_part('template-parts/home/' . str_replace('_', '-', $module)); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($aside_modules)) : ?>
            <aside class="content-right">
                <?php foreach ($aside_modules as $module) : ?>
                    <?php get_template_part('template-parts/home/' . str_replace('_', '-', $module)); ?>
                <?php endforeach; ?>
            </aside>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php get_footer(); ?>
