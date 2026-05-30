<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="search-field"><?php esc_html_e('搜索关键词', 'techpress'); ?></label>
    <input
        type="search"
        id="search-field"
        class="search-field"
        name="s"
        value="<?php echo esc_attr(get_search_query()); ?>"
        placeholder="<?php esc_attr_e('输入关键词搜索文章、页面、分类或标签', 'techpress'); ?>"
    >
    <button type="submit" class="search-submit"><?php esc_html_e('搜索', 'techpress'); ?></button>
</form>
