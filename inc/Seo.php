<?php

namespace TechPress;

class Seo {

    private $internal_link_count = 0;

    public function __construct() {
        add_filter('pre_get_document_title', [$this, 'filter_document_title']);
        add_action('wp_head', [$this, 'output_meta'], 1);
        add_action('wp_head', [$this, 'output_og'], 2);
        add_action('wp_head', [$this, 'output_canonical'], 3);

        add_filter('the_content', [$this, 'auto_internal_link'], 20);

        add_filter('wp_handle_upload_prefilter', [$this, 'rename_upload']);
        add_filter('wp_unique_filename', [$this, 'rename_filename'], 10, 4);

        add_action('init', [$this, 'register_sitemap_rewrite']);
        add_action('template_redirect', [$this, 'serve_sitemap']);
        add_filter('robots_txt', [$this, 'robots_sitemap'], 10, 2);
    }

    /* ───────── Meta Tags ───────── */

    public function filter_document_title($title) {
        if (!techpress_setting('techpress_seo_enabled', '1')) {
            return $title;
        }

        if (is_front_page()) {
            $custom_title = techpress_setting('techpress_seo_home_title', '');
            return $custom_title ?: $title;
        }

        return $title;
    }

    public function output_meta() {
        if (!techpress_setting('techpress_seo_enabled', '1')) {
            return;
        }

        if (is_front_page()) {
            $desc = techpress_setting('techpress_seo_home_desc', '');
            if ($desc) {
                echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
            }

            $kw = techpress_setting('techpress_seo_home_keywords', '');
            if ($kw) {
                echo '<meta name="keywords" content="' . esc_attr($kw) . '">' . "\n";
            }

            $robots = get_option('techpress_seo_home_robots', 'index, follow');
            echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
            return;
        }

        if (is_singular()) {
            global $post;
            $custom_desc = get_post_meta($post->ID, '_seo_desc', true);
            if ($custom_desc) {
                echo '<meta name="description" content="' . esc_attr($custom_desc) . '">' . "\n";
            } else {
                $auto_desc = wp_trim_words(wp_strip_all_tags($post->post_content), 120, '...');
                if ($auto_desc) {
                    echo '<meta name="description" content="' . esc_attr($auto_desc) . '">' . "\n";
                }
            }

            $custom_kw = get_post_meta($post->ID, '_seo_keywords', true);
            if ($custom_kw) {
                echo '<meta name="keywords" content="' . esc_attr($custom_kw) . '">' . "\n";
            }
        }

        if (is_category() || is_tag()) {
            $desc = term_description('', get_queried_object_id());
            $desc = wp_strip_all_tags($desc);
            if ($desc) {
                echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
            }
        }
    }

    /* ───────── Open Graph ───────── */

    public function output_og() {
        if (!techpress_setting('techpress_seo_enabled', '1') || !get_option('techpress_seo_og_enabled', '1')) {
            return;
        }

        $site_name = get_bloginfo('name');
        $site_url  = home_url('/');

        if (is_front_page()) {
            $title = techpress_setting('techpress_seo_home_title', '') ?: $site_name;
            $desc  = techpress_setting('techpress_seo_home_desc', '') ?: get_bloginfo('description');
            $image = techpress_setting('techpress_seo_og_image', '');

            $this->output_og_tags($title, $desc, $site_url, $image, 'website');
            return;
        }

        if (is_singular()) {
            global $post;
            $title = get_the_title($post);
            $desc  = get_post_meta($post->ID, '_seo_desc', true) ?: wp_trim_words(wp_strip_all_tags($post->post_content), 120, '...');
            $url   = get_permalink($post);

            $image = '';
            if (has_post_thumbnail($post)) {
                $img = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'large');
                $image = $img ? $img[0] : '';
            }
            if (!$image) {
                $image = techpress_setting('techpress_seo_og_image', '');
            }

            $type = 'article';
            $this->output_og_tags($title, $desc, $url, $image, $type);

            echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c', $post)) . '">' . "\n";
            echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c', $post)) . '">' . "\n";

            $cats = get_the_category($post->ID);
            if (!empty($cats)) {
                echo '<meta property="article:section" content="' . esc_attr($cats[0]->name) . '">' . "\n";
            }

            $tags = get_the_tags($post->ID);
            if ($tags) {
                foreach ($tags as $tag) {
                    echo '<meta property="article:tag" content="' . esc_attr($tag->name) . '">' . "\n";
                }
            }
        }
    }

    private function output_og_tags($title, $desc, $url, $image, $type) {
        echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        }

        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }
    }

    /* ───────── Canonical ───────── */

    public function output_canonical() {
        if (!techpress_setting('techpress_seo_enabled', '1')) {
            return;
        }

        if (is_front_page()) {
            echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
            return;
        }

        if (is_singular()) {
            echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
            return;
        }

        if (is_category() || is_tag() || is_tax()) {
            echo '<link rel="canonical" href="' . esc_url(get_term_link(get_queried_object())) . '">' . "\n";
        }
    }

    /* ───────── Auto Internal Linking ───────── */

    public function auto_internal_link($content) {
        if (!get_option('techpress_seo_auto_link', '0')) {
            return $content;
        }

        if (!is_singular() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $raw = get_option('techpress_seo_auto_link_rules', '');
        if (!$raw) {
            return $content;
        }

        $max_per_post = (int) get_option('techpress_seo_auto_link_max', 5);
        $this->internal_link_count = 0;

        $rules = [];
        $lines = explode("\n", $raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!$line) {
                continue;
            }
            $parts = explode('|', $line, 2);
            if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
                $rules[] = [
                    'keyword' => trim($parts[0]),
                    'url'     => esc_url(trim($parts[1])),
                ];
            }
        }

        if (empty($rules)) {
            return $content;
        }

        usort($rules, function ($a, $b) {
            return mb_strlen($b['keyword']) - mb_strlen($a['keyword']);
        });

        foreach ($rules as $rule) {
            if ($this->internal_link_count >= $max_per_post) {
                break;
            }

            $keyword = preg_quote($rule['keyword'], '/');
            $pattern = '/(?<![">])(' . $keyword . ')(?![^<]*<\/a>)(?![^<]*<\/code>)(?![^<]*<\/pre>)/u';

            $callback = function ($matches) use ($rule) {
                if ($this->internal_link_count >= (int) get_option('techpress_seo_auto_link_max', 5)) {
                    return $matches[0];
                }
                $this->internal_link_count++;
                return '<a href="' . $rule['url'] . '" title="' . esc_attr($matches[0]) . '">' . $matches[0] . '</a>';
            };

            $content = preg_replace_callback($pattern, $callback, $content, 1);
        }

        return $content;
    }

    /* ───────── Image Auto Rename ───────── */

    public function rename_upload($file) {
        if (!get_option('techpress_seo_image_rename', '0')) {
            return $file;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];

        if (!in_array($ext, $allowed, true)) {
            return $file;
        }

        $new_name = current_time('Y-m-d-His') . '-' . wp_generate_password(4, false) . '.' . $ext;
        $file['name'] = $new_name;

        return $file;
    }

    public function rename_filename($filename, $ext, $dir, $unique_filename_callback) {
        if (!get_option('techpress_seo_image_rename', '0')) {
            return $filename;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'ico'];

        if (!in_array($extension, $allowed, true)) {
            return $filename;
        }

        $clean_ext = ltrim($ext, '.');
        if (!$clean_ext) {
            $clean_ext = $extension;
        }

        return current_time('Y-m-d-His') . '-' . wp_generate_password(4, false) . '.' . $clean_ext;
    }

    /* ───────── XML Sitemap ───────── */

    public function register_sitemap_rewrite() {
        if (!techpress_setting('techpress_seo_sitemap', '1')) {
            return;
        }
        add_rewrite_rule('^sitemap\.xml$', 'index.php?techpress_sitemap=index', 'top');
        add_rewrite_rule('^sitemap-(posts|pages|categories)\.xml$', 'index.php?techpress_sitemap=$matches[1]', 'top');
    }

    public function serve_sitemap() {
        if (!techpress_setting('techpress_seo_sitemap', '1')) {
            return;
        }

        $type = get_query_var('techpress_sitemap');
        if (!$type) {
            return;
        }

        header('Content-Type: application/xml; charset=UTF-8');
        header('X-Robots-Tag: noindex, follow');

        switch ($type) {
            case 'index':
                $this->render_sitemap_index();
                break;
            case 'posts':
                $this->render_sitemap_posts();
                break;
            case 'pages':
                $this->render_sitemap_pages();
                break;
            case 'categories':
                $this->render_sitemap_categories();
                break;
        }

        exit;
    }

    private function render_sitemap_index() {
        $base = home_url('/');
        $lastmod = current_time('c');

        $posts_last = $this->format_sitemap_lastmod($this->get_last_modified(['post', 'page']));
        $cats_last  = $this->format_sitemap_lastmod($this->get_term_last_modified('category'));

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        echo "  <sitemap>\n";
        echo '    <loc>' . esc_url($base . 'sitemap-posts.xml') . '</loc>' . "\n";
        echo '    <lastmod>' . esc_html($posts_last ?: $lastmod) . '</lastmod>' . "\n";
        echo "  </sitemap>\n";

        echo "  <sitemap>\n";
        echo '    <loc>' . esc_url($base . 'sitemap-pages.xml') . '</loc>' . "\n";
        echo '    <lastmod>' . esc_html($posts_last ?: $lastmod) . '</lastmod>' . "\n";
        echo "  </sitemap>\n";

        echo "  <sitemap>\n";
        echo '    <loc>' . esc_url($base . 'sitemap-categories.xml') . '</loc>' . "\n";
        echo '    <lastmod>' . esc_html($cats_last ?: $lastmod) . '</lastmod>' . "\n";
        echo "  </sitemap>\n";

        echo '</sitemapindex>';
    }

    private function render_sitemap_posts() {
        $posts = get_posts([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ]);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        echo "  <url>\n";
        echo '    <loc>' . esc_url(home_url('/')) . '</loc>' . "\n";
        echo '    <changefreq>daily</changefreq>' . "\n";
        echo '    <priority>1.0</priority>' . "\n";
        echo "  </url>\n";

        foreach ($posts as $post) {
            if ($this->is_noindex($post->ID)) {
                continue;
            }
            $priority = $this->get_post_priority($post);
            echo "  <url>\n";
            echo '    <loc>' . esc_url(get_permalink($post)) . '</loc>' . "\n";
            echo '    <lastmod>' . esc_html($this->format_sitemap_lastmod($post->post_modified_gmt)) . '</lastmod>' . "\n";
            echo '    <changefreq>monthly</changefreq>' . "\n";
            echo '    <priority>' . esc_html($priority) . '</priority>' . "\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }

    private function render_sitemap_pages() {
        $pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'modified',
            'order'          => 'DESC',
        ]);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $page) {
            if ($this->is_noindex($page->ID)) {
                continue;
            }
            echo "  <url>\n";
            echo '    <loc>' . esc_url(get_permalink($page)) . '</loc>' . "\n";
            echo '    <lastmod>' . esc_html($this->format_sitemap_lastmod($page->post_modified_gmt)) . '</lastmod>' . "\n";
            echo '    <changefreq>monthly</changefreq>' . "\n";
            echo '    <priority>0.6</priority>' . "\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }

    private function render_sitemap_categories() {
        $cats = get_categories(['hide_empty' => true]);

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($cats as $cat) {
            echo "  <url>\n";
            echo '    <loc>' . esc_url(get_category_link($cat->term_id)) . '</loc>' . "\n";
            echo '    <changefreq>weekly</changefreq>' . "\n";
            echo '    <priority>0.6</priority>' . "\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
    }

    public function robots_sitemap($output, $public) {
        if (!techpress_setting('techpress_seo_sitemap', '1')) {
            return $output;
        }
        $output .= "\nSitemap: " . home_url('/sitemap.xml') . "\n";
        return $output;
    }

    private function get_last_modified($post_types = ['post']) {
        global $wpdb;
        $in = implode(',', array_map(function ($t) use ($wpdb) {
            return "'" . esc_sql($t) . "'";
        }, $post_types));

        return $wpdb->get_var(
            "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts}
             WHERE post_status = 'publish' AND post_type IN ({$in})"
        );
    }

    private function get_term_last_modified($taxonomy) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(p.post_modified_gmt)
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
             INNER JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
             WHERE p.post_status = 'publish'
               AND p.post_type = 'post'
               AND tt.taxonomy = %s",
            $taxonomy
        ));
    }

    private function format_sitemap_lastmod($mysql_datetime) {
        if (!$mysql_datetime) {
            return current_time('c');
        }

        return mysql2date('c', $mysql_datetime, false);
    }

    private function is_noindex($post_id) {
        $meta = get_post_meta($post_id, '_seo_noindex', true);
        return $meta === '1';
    }

    private function get_post_priority($post) {
        $days = (time() - strtotime($post->post_date_gmt)) / 86400;
        if ($days < 7) {
            return '0.8';
        }
        if ($days < 30) {
            return '0.6';
        }
        return '0.4';
    }
}
