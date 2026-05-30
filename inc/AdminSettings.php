<?php

namespace TechPress;

class AdminSettings {

    private $social_fields = [
        'social_weibo'    => '微博',
        'social_wechat'   => '微信',
        'social_bilibili' => 'B站',
        'social_github'   => 'GitHub',
        'social_twitter'  => 'Twitter / X',
        'social_rss'      => 'RSS订阅',
    ];

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
        add_action('wp_head', [$this, 'print_header_code'], 999);
        add_action('wp_footer', [$this, 'print_footer_code'], 999);

        add_action('update_option_techpress_breadcrumb_align', function ($old, $val) {
            set_theme_mod('techpress_breadcrumb_align', $val);
        }, 10, 2);

        add_filter('pre_update_option_techpress_gravatar_mirror', function ($value) {
            if ('custom' === $value && !empty($_POST['techpress_gravatar_custom'])) {
                $custom = sanitize_text_field(wp_unslash($_POST['techpress_gravatar_custom']));
                return preg_match('#^https?://#', $custom) ? $custom : 'https://' . $custom;
            }
            return $value;
        }, 10, 2);
    }

    public function enqueue($hook) {
        if ('appearance_page_techpress-settings' !== $hook) {
            return;
        }
        wp_add_inline_style('common', '
.techpress-settings-wrap { max-width: 860px; }
.techpress-settings-wrap > h1 { font-size: 22px; font-weight: 600; margin-bottom: 20px; }
.techpress-accordion-section { margin:0 0 2px; border:1px solid #dcdcde; border-radius:6px; background:#fff; overflow:hidden; }
.techpress-accordion-title { margin:0; padding:12px 16px; font-size:14px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:space-between; user-select:none; background:#f9f9f9; transition:background .15s; }
.techpress-accordion-title:hover { background:#f0f0f1; }
.techpress-accordion-title .dashicons { color:#787c82; transition:transform .2s; font-size:18px; width:18px; height:18px; }
.techpress-accordion-title.open .dashicons { transform:rotate(180deg); }
.techpress-accordion-content { border-top:1px solid #f0f0f1; padding:8px 16px 12px; display:none; }
.techpress-accordion-content.open { display:block; }
.techpress-accordion-content .form-table th { padding:10px 10px 10px 0; font-weight:500; font-size:13px; width:160px; vertical-align:top; }
.techpress-accordion-content .form-table td { padding:8px 0; }
.techpress-accordion-content .form-table td input[type="text"],
.techpress-accordion-content .form-table td input[type="url"],
.techpress-accordion-content .form-table td input[type="number"],
.techpress-accordion-content .form-table td select { font-size:13px; }
.techpress-accordion-content .form-table td textarea { font-size:13px; }
.techpress-accordion-content .form-table td .description { font-size:12px; color:#787c82; margin-top:4px; }
.techpress-accordion-content .form-table td label { font-size:13px; }
.techpress-sep { border:none; border-top:1px solid #f0f0f1; margin:12px 0; }
.techpress-section-desc { color:#64748b; font-size:13px; margin:0 0 8px; line-height:1.5; }
#submit { margin-top:12px; }
');
    }

    public function add_menu() {
        add_theme_page(
            __('Resflux 主题设置', 'techpress'),
            __('主题设置', 'techpress'),
            'manage_options',
            'techpress-settings',
            [$this, 'render']
        );
    }

    public function register() {
        $this->seo_section();
        $this->appearance_section();
        $this->home_modules_section();
        $this->footer_section();
        $this->social_section();
        $this->advanced_section();
        $this->security_section();
    }

    /* ═══════════════════════════════════════
       Section 1: SEO 设置
       ═══════════════════════════════════════ */

    private function seo_section() {
        add_settings_section('techpress_seo_section', __('SEO 设置', 'techpress'), function () {
            echo '<p class="techpress-section-desc">' . __('配置搜索引擎优化相关选项，包括 meta 标签、自动内链、图片重命名和站点地图。', 'techpress') . '</p>';
        }, 'techpress-settings');

        register_setting('techpress_settings', 'techpress_seo_enabled', 'sanitize_text_field');
        add_settings_field('techpress_seo_enabled', __('启用 SEO 功能', 'techpress'), function () {
            $val = get_option('techpress_seo_enabled', '1');
            echo '<label><input type="checkbox" name="techpress_seo_enabled" value="1"' . checked('1', $val, false) . '> ' . __('开启后输出 meta 标签、Open Graph、canonical', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_home_title', 'sanitize_text_field');
        add_settings_field('techpress_seo_home_title', __('首页 SEO 标题', 'techpress'), function () {
            $val = get_option('techpress_seo_home_title', '');
            echo '<input type="text" name="techpress_seo_home_title" value="' . esc_attr($val) . '" class="regular-text" placeholder="' . esc_attr(get_bloginfo('name')) . '"><p class="description">' . __('留空则使用 WordPress 默认标题格式', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_home_desc', 'sanitize_text_field');
        add_settings_field('techpress_seo_home_desc', __('首页 SEO 描述', 'techpress'), function () {
            $val = get_option('techpress_seo_home_desc', '');
            echo '<textarea name="techpress_seo_home_desc" rows="3" class="large-text" placeholder="' . esc_attr(get_bloginfo('description')) . '">' . esc_textarea($val) . '</textarea><p class="description">' . __('建议 150 字以内，展示在搜索结果摘要中', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_home_keywords', 'sanitize_text_field');
        add_settings_field('techpress_seo_home_keywords', __('首页 SEO 关键词', 'techpress'), function () {
            $val = get_option('techpress_seo_home_keywords', '');
            echo '<input type="text" name="techpress_seo_home_keywords" value="' . esc_attr($val) . '" class="large-text" placeholder="' . esc_attr__('关键词1, 关键词2, 关键词3', 'techpress') . '"><p class="description">' . __('英文逗号分隔', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_title_sep', 'sanitize_text_field');
        add_settings_field('techpress_seo_title_sep', __('标题分隔符', 'techpress'), function () {
            $val = get_option('techpress_seo_title_sep', '|');
            $seps = ['|' => '| (竖线)', '-' => '- (短横)', '–' => '– (长横)', '·' => '· (中点)', '—' => '— (破折号)'];
            echo '<select name="techpress_seo_title_sep">';
            foreach ($seps as $k => $v) {
                echo '<option value="' . esc_attr($k) . '"' . selected($k, $val, false) . '>' . esc_html($v) . '</option>';
            }
            echo '</select><p class="description">' . __('页面标题与站点名之间的分隔符', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_og_enabled', 'sanitize_text_field');
        add_settings_field('techpress_seo_og_enabled', __('Open Graph 标签', 'techpress'), function () {
            $val = get_option('techpress_seo_og_enabled', '1');
            echo '<label><input type="checkbox" name="techpress_seo_og_enabled" value="1"' . checked('1', $val, false) . '> ' . __('输出 og:title / og:image 等，便于社交平台分享', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_og_image', 'esc_url_raw');
        add_settings_field('techpress_seo_og_image', __('默认分享图', 'techpress'), function () {
            $val = get_option('techpress_seo_og_image', '');
            echo '<input type="url" name="techpress_seo_og_image" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://..."><p class="description">' . __('文章无特色图时使用的默认 OG 图片（建议 1200×630）', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_auto_link', 'sanitize_text_field');
        add_settings_field('techpress_seo_auto_link', __('自动内链', 'techpress'), function () {
            echo '<hr class="techpress-sep">';
            $val = get_option('techpress_seo_auto_link', '0');
            echo '<label><input type="checkbox" name="techpress_seo_auto_link" value="1"' . checked('1', $val, false) . '> ' . __('文章正文中自动将关键词转为内链', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_auto_link_rules', 'sanitize_textarea_field');
        add_settings_field('techpress_seo_auto_link_rules', __('内链规则', 'techpress'), function () {
            $val = get_option('techpress_seo_auto_link_rules', '');
            echo '<textarea name="techpress_seo_auto_link_rules" rows="5" class="large-text code" placeholder="WordPress|https://example.com/wordpress">' . esc_textarea($val) . '</textarea><p class="description">' . __('每行一条，格式：关键词|URL', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_auto_link_max', 'absint');
        add_settings_field('techpress_seo_auto_link_max', __('每篇最大替换数', 'techpress'), function () {
            $val = get_option('techpress_seo_auto_link_max', 5);
            echo '<input type="number" name="techpress_seo_auto_link_max" value="' . esc_attr($val) . '" class="small-text" min="1" max="20"><p class="description">' . __('每篇文章最多自动替换几个关键词，避免过度优化', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_image_rename', 'sanitize_text_field');
        add_settings_field('techpress_seo_image_rename', __('图片自动重命名', 'techpress'), function () {
            echo '<hr class="techpress-sep">';
            $val = get_option('techpress_seo_image_rename', '0');
            echo '<label><input type="checkbox" name="techpress_seo_image_rename" value="1"' . checked('1', $val, false) . '> ' . __('上传图片自动按日期时间重命名（如 2026-05-25-143022-a7f3.jpg）', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_seo_section');

        register_setting('techpress_settings', 'techpress_seo_sitemap', 'sanitize_text_field');
        add_settings_field('techpress_seo_sitemap', __('站点地图', 'techpress'), function () {
            $val = get_option('techpress_seo_sitemap', '1');
            echo '<label><input type="checkbox" name="techpress_seo_sitemap" value="1"' . checked('1', $val, false) . '> ' . __('生成 XML 站点地图（/sitemap.xml），并自动添加到 robots.txt', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_seo_section');
    }

    /* ═══════════════════════════════════════
       Section 2: 外观设置
       ═══════════════════════════════════════ */

    private function appearance_section() {
        add_settings_section('techpress_appearance_section', __('外观设置', 'techpress'), null, 'techpress-settings');

        register_setting('techpress_settings', 'techpress_color_scheme', 'sanitize_text_field');
        add_settings_field('techpress_color_scheme', __('配色方案', 'techpress'), function () {
            $val = get_option('techpress_color_scheme', get_theme_mod('techpress_color_scheme', 'aurora-blue'));
            $schemes = \TechPress\Theme::instance()->get_color_schemes();
            echo '<select name="techpress_color_scheme">';
            foreach ($schemes as $key => $scheme) {
                echo '<option value="' . esc_attr($key) . '"' . selected($key, $val, false) . '>' . esc_html($scheme['label']) . '</option>';
            }
            echo '</select>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_logo_url', 'esc_url_raw');
        add_settings_field('techpress_logo_url', __('站点 Logo', 'techpress'), function () {
            $val = get_option('techpress_logo_url', get_theme_mod('techpress_logo_url', ''));
            echo '<input type="url" name="techpress_logo_url" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://...">';
            if ($val) {
                echo '<br><img src="' . esc_url($val) . '" style="max-width:200px;max-height:60px;margin-top:8px;border-radius:8px;">';
            }
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_font_family', 'sanitize_text_field');
        register_setting('techpress_settings', 'techpress_custom_font', 'sanitize_text_field');
        add_settings_field('techpress_font_family', __('字体', 'techpress'), function () {
            $val = get_option('techpress_font_family', get_theme_mod('techpress_font_family', 'default'));
            $fonts = [
                'default' => __('系统默认', 'techpress'),
                'yahei'   => '微软雅黑',
                'noto'    => 'Noto Sans SC',
                'custom'  => __('自定义', 'techpress'),
            ];
            echo '<select name="techpress_font_family" id="techpress-font-select">';
            foreach ($fonts as $k => $v) {
                echo '<option value="' . esc_attr($k) . '"' . selected($k, $val, false) . '>' . esc_html($v) . '</option>';
            }
            echo '</select>';
            $custom_font = get_option('techpress_custom_font', get_theme_mod('techpress_custom_font', ''));
            echo '<div id="techpress-custom-font-wrap" style="margin-top:8px;' . ($val !== 'custom' ? 'display:none;' : '') . '">
                <input type="text" name="techpress_custom_font" value="' . esc_attr($custom_font) . '" class="regular-text" placeholder="例: -apple-system, sans-serif">
            </div>
            <script>
            jQuery(function($) {
                $(\'#techpress-font-select\').on(\'change\', function() {
                    $(\'#techpress-custom-font-wrap\').toggle(\'custom\' === $(this).val());
                });
            });
            </script>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_home_layout', 'sanitize_text_field');
        add_settings_field('techpress_home_layout', __('首页布局', 'techpress'), function () {
            $val = get_option('techpress_home_layout', get_theme_mod('techpress_home_layout', 'grid'));
            echo '<select name="techpress_home_layout">
                <option value="grid"' . selected('grid', $val, false) . '>' . __('网格 (2列)', 'techpress') . '</option>
                <option value="list"' . selected('list', $val, false) . '>' . __('列表 (1列)', 'techpress') . '</option>
            </select>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_breadcrumb_align', 'sanitize_text_field');
        add_settings_field('techpress_breadcrumb_align', __('面包屑对齐', 'techpress'), function () {
            $val = get_option('techpress_breadcrumb_align', get_theme_mod('techpress_breadcrumb_align', 'left'));
            echo '<select name="techpress_breadcrumb_align">
                <option value="left"' . selected('left', $val, false) . '>' . __('居左', 'techpress') . '</option>
                <option value="center"' . selected('center', $val, false) . '>' . __('居中', 'techpress') . '</option>
                <option value="right"' . selected('right', $val, false) . '>' . __('居右', 'techpress') . '</option>
            </select>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_primary_nav_align', 'sanitize_text_field');
        add_settings_field('techpress_primary_nav_align', __('主导航对齐', 'techpress'), function () {
            $val = get_option('techpress_primary_nav_align', get_theme_mod('techpress_primary_nav_align', 'center'));
            echo '<select name="techpress_primary_nav_align">
                <option value="left"' . selected('left', $val, false) . '>' . __('靠左', 'techpress') . '</option>
                <option value="center"' . selected('center', $val, false) . '>' . __('居中', 'techpress') . '</option>
                <option value="right"' . selected('right', $val, false) . '>' . __('靠右', 'techpress') . '</option>
            </select>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_single_layout', 'sanitize_text_field');
        add_settings_field('techpress_single_layout', __('文章页布局', 'techpress'), function () {
            $val = get_option('techpress_single_layout', get_theme_mod('techpress_single_layout', 'sidebar'));
            echo '<select name="techpress_single_layout">
                <option value="sidebar"' . selected('sidebar', $val, false) . '>' . __('带侧栏', 'techpress') . '</option>
                <option value="full"' . selected('full', $val, false) . '>' . __('全宽', 'techpress') . '</option>
            </select>';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_readmore_threshold', 'absint');
        add_settings_field('techpress_readmore_threshold', __('长文折叠高度', 'techpress'), function () {
            $val = get_option('techpress_readmore_threshold', get_theme_mod('techpress_readmore_threshold', 1800));
            echo '<input type="number" name="techpress_readmore_threshold" value="' . esc_attr($val) . '" class="small-text" min="500" max="5000" step="100"> px';
        }, 'techpress-settings', 'techpress_appearance_section');

        register_setting('techpress_settings', 'techpress_sticky_count', 'absint');
        add_settings_field('techpress_sticky_count', __('首页置顶文章数', 'techpress'), function () {
            $val = get_option('techpress_sticky_count', get_theme_mod('techpress_sticky_count', 3));
            echo '<input type="number" name="techpress_sticky_count" value="' . esc_attr($val) . '" class="small-text" min="0" max="6">';
        }, 'techpress-settings', 'techpress_appearance_section');
    }

    /* ═══════════════════════════════════════
       Section 3: 首页模块
       ═══════════════════════════════════════ */

    private function home_modules_section() {
        add_settings_section('techpress_home_modules_section', __('首页模块', 'techpress'), function () {
            echo '<p class="techpress-section-desc">' . __('控制首页模块是否渲染和模块顺序。关闭模块后，前台不会执行该模块对应查询。资源聚合和网址导航需要先在后台添加对应内容。', 'techpress') . '</p>';
        }, 'techpress-settings');

        register_setting('techpress_settings', 'techpress_home_modules_enabled', 'techpress_sanitize_home_modules');
        add_settings_field('techpress_home_modules_enabled', __('启用模块', 'techpress'), function () {
            $enabled = get_option('techpress_home_modules_enabled', techpress_default_home_modules());
            $enabled = techpress_sanitize_home_modules($enabled);

            echo '<input type="hidden" name="techpress_home_modules_enabled[]" value="">';
            foreach (techpress_home_module_definitions() as $key => $module) {
                echo '<label style="display:block;margin:0 0 8px;">';
                echo '<input type="checkbox" name="techpress_home_modules_enabled[]" value="' . esc_attr($key) . '"' . checked(in_array($key, $enabled, true), true, false) . '> ';
                echo esc_html($module['label']);
                echo '</label>';
            }
        }, 'techpress-settings', 'techpress_home_modules_section');

        register_setting('techpress_settings', 'techpress_home_modules_order', 'techpress_sanitize_home_module_order');
        add_settings_field('techpress_home_modules_order', __('模块顺序', 'techpress'), function () {
            $order = get_option('techpress_home_modules_order', implode(',', techpress_default_home_module_order()));
            $order = techpress_sanitize_home_module_order($order);
            echo '<input type="text" name="techpress_home_modules_order" value="' . esc_attr($order) . '" class="large-text code">';
            echo '<p class="description">' . esc_html__('用英文逗号分隔。可用模块：featured, carousel_ad, category_feed, resources, navigation, latest, sidebar。未启用的模块会自动跳过。', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_home_modules_section');

        register_setting('techpress_settings', 'techpress_home_resources_count', 'absint');
        add_settings_field('techpress_home_resources_count', __('资源显示数量', 'techpress'), function () {
            $val = get_option('techpress_home_resources_count', 6);
            echo '<input type="number" name="techpress_home_resources_count" value="' . esc_attr($val) . '" class="small-text" min="1" max="24">';
        }, 'techpress-settings', 'techpress_home_modules_section');

        register_setting('techpress_settings', 'techpress_home_navigation_count', 'absint');
        add_settings_field('techpress_home_navigation_count', __('导航显示数量', 'techpress'), function () {
            $val = get_option('techpress_home_navigation_count', 8);
            echo '<input type="number" name="techpress_home_navigation_count" value="' . esc_attr($val) . '" class="small-text" min="1" max="36">';
        }, 'techpress-settings', 'techpress_home_modules_section');
    }

    /* ═══════════════════════════════════════
       Section 3: 页脚设置
       ═══════════════════════════════════════ */

    private function footer_section() {
        add_settings_section('techpress_footer_section', __('页脚设置', 'techpress'), null, 'techpress-settings');

        register_setting('techpress_settings', 'footer_copyright', 'sanitize_text_field');
        add_settings_field('footer_copyright', __('版权信息', 'techpress'), function () {
            echo '<input type="text" name="footer_copyright" value="' . esc_attr(get_option('footer_copyright', get_theme_mod('footer_copyright', ''))) . '" class="regular-text">';
        }, 'techpress-settings', 'techpress_footer_section');

        register_setting('techpress_settings', 'footer_icp', 'sanitize_text_field');
        add_settings_field('footer_icp', __('ICP 备案号', 'techpress'), function () {
            echo '<input type="text" name="footer_icp" value="' . esc_attr(get_option('footer_icp', get_theme_mod('footer_icp', ''))) . '" class="regular-text" placeholder="京ICP备2024XXXXXX号">';
        }, 'techpress-settings', 'techpress_footer_section');

        register_setting('techpress_settings', 'footer_gongan', 'sanitize_text_field');
        add_settings_field('footer_gongan', __('公安备案号', 'techpress'), function () {
            echo '<input type="text" name="footer_gongan" value="' . esc_attr(get_option('footer_gongan', get_theme_mod('footer_gongan', ''))) . '" class="regular-text" placeholder="京公网安备 11010802000000号">';
        }, 'techpress-settings', 'techpress_footer_section');

        register_setting('techpress_settings', 'techpress_footer_qrcode_qq', 'esc_url_raw');
        add_settings_field('techpress_footer_qrcode_qq', __('QQ 二维码', 'techpress'), function () {
            $val = get_option('techpress_footer_qrcode_qq', get_theme_mod('techpress_footer_qrcode_qq', ''));
            echo '<input type="url" name="techpress_footer_qrcode_qq" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://...">';
            if ($val) {
                echo '<br><img src="' . esc_url($val) . '" style="max-width:80px;margin-top:6px;border-radius:6px;">';
            }
        }, 'techpress-settings', 'techpress_footer_section');

        register_setting('techpress_settings', 'techpress_footer_qrcode_wechat', 'esc_url_raw');
        add_settings_field('techpress_footer_qrcode_wechat', __('微信二维码', 'techpress'), function () {
            $val = get_option('techpress_footer_qrcode_wechat', get_theme_mod('techpress_footer_qrcode_wechat', ''));
            echo '<input type="url" name="techpress_footer_qrcode_wechat" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://...">';
            if ($val) {
                echo '<br><img src="' . esc_url($val) . '" style="max-width:80px;margin-top:6px;border-radius:6px;">';
            }
        }, 'techpress-settings', 'techpress_footer_section');
    }

    /* ═══════════════════════════════════════
       Section 4: 社交链接
       ═══════════════════════════════════════ */

    private function social_section() {
        add_settings_section('techpress_social_section', __('社交链接', 'techpress'), null, 'techpress-settings');

        foreach ($this->social_fields as $key => $label) {
            register_setting('techpress_settings', $key, 'esc_url_raw');
            add_settings_field($key, $label, function () use ($key) {
                echo '<input type="url" name="' . esc_attr($key) . '" value="' . esc_attr(get_option($key, get_theme_mod($key, ''))) . '" class="regular-text" placeholder="https://...">';
            }, 'techpress-settings', 'techpress_social_section');
        }
    }

    /* ═══════════════════════════════════════
       Section 5: 高级设置
       ═══════════════════════════════════════ */

    private function advanced_section() {
        add_settings_section('techpress_advanced_section', __('高级设置', 'techpress'), null, 'techpress-settings');

        register_setting('techpress_settings', 'techpress_gravatar_mirror', 'sanitize_text_field');
        add_settings_field('techpress_gravatar_mirror', __('Gravatar 镜像', 'techpress'), function () {
            $val = get_option('techpress_gravatar_mirror', get_theme_mod('techpress_gravatar_mirror', 'www.gravatar.com'));
            $mirrors = [
                'www.gravatar.com'    => 'www.gravatar.com (全球)',
                'cn.gravatar.com'     => 'cn.gravatar.com (国内镜像)',
                'secure.gravatar.com' => 'secure.gravatar.com (SSL)',
                '0.gravatar.com'      => '0.gravatar.com',
                'custom'              => __('自定义', 'techpress'),
            ];
            $is_custom = !array_key_exists($val, $mirrors);
            $selected = $is_custom ? 'custom' : $val;
            $custom_url = $is_custom ? $val : '';
            echo '<select name="techpress_gravatar_mirror" id="techpress-gravatar-select">';
            foreach ($mirrors as $k => $v) {
                echo '<option value="' . esc_attr($k) . '"' . selected($k, $selected, false) . '>' . esc_html($v) . '</option>';
            }
            echo '</select>';
            echo '<div id="techpress-gravatar-custom" style="margin-top:8px;' . (!$is_custom ? 'display:none;' : '') . '">
                <input type="text" name="techpress_gravatar_custom" value="' . esc_attr($custom_url) . '" class="regular-text" placeholder="avatars.example.com">
                <p class="description">' . __('输入自定义镜像域名，如 gravatar.wp-developer.com', 'techpress') . '</p>
            </div>';
            echo '<script>
            jQuery(function($) {
                var $sel = $("#techpress-gravatar-select");
                var $custom = $("#techpress-gravatar-custom");
                $sel.on("change", function() { $custom.toggle("custom" === $(this).val()); });
            });
            </script>';
        }, 'techpress-settings', 'techpress_advanced_section');

        register_setting('techpress_settings', 'techpress_header_code', 'wp_kses_post');
        add_settings_field('techpress_header_code', __('Header 代码', 'techpress'), function () {
            echo '<textarea name="techpress_header_code" rows="6" class="large-text code">' . esc_textarea(get_option('techpress_header_code', '')) . '</textarea><p class="description">' . __('插入 &lt;head&gt;，适合统计代码、自定义 meta', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_advanced_section');

        register_setting('techpress_settings', 'techpress_footer_code', 'wp_kses_post');
        add_settings_field('techpress_footer_code', __('Footer 代码', 'techpress'), function () {
            echo '<textarea name="techpress_footer_code" rows="6" class="large-text code">' . esc_textarea(get_option('techpress_footer_code', '')) . '</textarea><p class="description">' . __('插入 &lt;/body&gt; 前，适合客服、第三方脚本', 'techpress') . '</p>';
        }, 'techpress-settings', 'techpress_advanced_section');
    }

    /* ═══════════════════════════════════════
       Section 6: 安全设置
       ═══════════════════════════════════════ */

    private function security_section() {
        add_settings_section('techpress_security_section', __('安全设置', 'techpress'), function () {
            echo '<p class="techpress-section-desc">' . __('屏蔽不必要的 WordPress 功能，减少攻击面和页面负载。所有选项独立开关，互不影响。', 'techpress') . '</p>';
        }, 'techpress-settings');

        // ── WordPress 信息隐藏 ──

        register_setting('techpress_settings', 'techpress_sec_hide_generator', 'sanitize_text_field');
        add_settings_field('techpress_sec_hide_generator', __('隐藏 WordPress 版本号', 'techpress'), function () {
            $val = get_option('techpress_sec_hide_generator', '1');
            echo '<label><input type="checkbox" name="techpress_sec_hide_generator" value="1"' . checked('1', $val, false) . '> ' . __('移除 &lt;meta name="generator"&gt; 中的版本信息', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        register_setting('techpress_settings', 'techpress_sec_remove_rsd', 'sanitize_text_field');
        add_settings_field('techpress_sec_remove_rsd', __('移除 RSD / WLW / Shortlink', 'techpress'), function () {
            $val = get_option('techpress_sec_remove_rsd', '1');
            echo '<label><input type="checkbox" name="techpress_sec_remove_rsd" value="1"' . checked('1', $val, false) . '> ' . __('移除编辑器入口和短链接头，减少攻击面', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        register_setting('techpress_settings', 'techpress_sec_remove_rest_link', 'sanitize_text_field');
        add_settings_field('techpress_sec_remove_rest_link', __('移除 REST API 发现链接', 'techpress'), function () {
            $val = get_option('techpress_sec_remove_rest_link', '1');
            echo '<label><input type="checkbox" name="techpress_sec_remove_rest_link" value="1"' . checked('1', $val, false) . '> ' . __('隐藏 &lt;link rel="https://api.w.org/"&gt; 端点', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        register_setting('techpress_settings', 'techpress_sec_remove_oembed', 'sanitize_text_field');
        add_settings_field('techpress_sec_remove_oembed', __('移除 oEmbed 发现链接', 'techpress'), function () {
            $val = get_option('techpress_sec_remove_oembed', '1');
            echo '<label><input type="checkbox" name="techpress_sec_remove_oembed" value="1"' . checked('1', $val, false) . '> ' . __('隐藏 embed 端点，防止用户信息泄露', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        // ── 功能裁剪 ──

        register_setting('techpress_settings', 'techpress_sec_disable_emoji', 'sanitize_text_field');
        add_settings_field('techpress_sec_disable_emoji', __('禁用 Emoji 脚本和样式', 'techpress'), function () {
            echo '<hr class="techpress-sep">';
            $val = get_option('techpress_sec_disable_emoji', '1');
            echo '<label><input type="checkbox" name="techpress_sec_disable_emoji" value="1"' . checked('1', $val, false) . '> ' . __('减少每页约 40KB 负载，博客不需要表情功能', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        register_setting('techpress_settings', 'techpress_sec_disable_feed', 'sanitize_text_field');
        add_settings_field('techpress_sec_disable_feed', __('禁用 RSS Feed', 'techpress'), function () {
            $val = get_option('techpress_sec_disable_feed', '0');
            echo '<label><input type="checkbox" name="techpress_sec_disable_feed" value="1"' . checked('1', $val, false) . '> ' . __('关闭后 RSS 地址返回 404，如仍在使用请勿开启', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        // ── 接口安全 ──

        register_setting('techpress_settings', 'techpress_sec_disable_xmlrpc', 'sanitize_text_field');
        add_settings_field('techpress_sec_disable_xmlrpc', __('禁用 XML-RPC', 'techpress'), function () {
            echo '<hr class="techpress-sep">';
            $val = get_option('techpress_sec_disable_xmlrpc', '1');
            echo '<label><input type="checkbox" name="techpress_sec_disable_xmlrpc" value="1"' . checked('1', $val, false) . '> ' . __('防止暴力破解和 DDoS 放大攻击，推荐开启', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');

        register_setting('techpress_settings', 'techpress_sec_obscure_login_errors', 'sanitize_text_field');
        add_settings_field('techpress_sec_obscure_login_errors', __('登录错误信息模糊化', 'techpress'), function () {
            $val = get_option('techpress_sec_obscure_login_errors', '1');
            echo '<label><input type="checkbox" name="techpress_sec_obscure_login_errors" value="1"' . checked('1', $val, false) . '> ' . __('统一提示"用户名或密码不正确"，防止用户名枚举', 'techpress') . '</label>';
        }, 'techpress-settings', 'techpress_security_section');
    }

    /* ═══════════════════════════════════════
       Render
       ═══════════════════════════════════════ */

    public function render() {
        global $wp_settings_sections, $wp_settings_fields;

        $raw = isset($wp_settings_sections['techpress-settings']) ? $wp_settings_sections['techpress-settings'] : [];

        $order = [
            'techpress_seo_section',
            'techpress_appearance_section',
            'techpress_home_modules_section',
            'techpress_footer_section',
            'techpress_social_section',
            'techpress_advanced_section',
            'techpress_security_section',
            'techpress_ads_section',
        ];

        $sections = [];
        foreach ($order as $id) {
            if (isset($raw[$id])) {
                $sections[$id] = $raw[$id];
            }
        }
        foreach ($raw as $id => $section) {
            if (!isset($sections[$id])) {
                $sections[$id] = $section;
            }
        }
        ?>
        <div class="wrap techpress-settings-wrap">
            <h1><?php _e('Resflux 主题设置', 'techpress'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('techpress_settings'); ?>

                <?php foreach ($sections as $section) : ?>
                    <div class="techpress-accordion-section">
                        <div class="techpress-accordion-title">
                            <?php echo esc_html($section['title']); ?>
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </div>
                        <div class="techpress-accordion-content">
                            <?php
                            if ($section['callback']) {
                                call_user_func($section['callback'], $section);
                            }
                            if (isset($wp_settings_fields['techpress-settings'][$section['id']])) {
                                echo '<table class="form-table" role="presentation">';
                                do_settings_fields('techpress-settings', $section['id']);
                                echo '</table>';
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php submit_button(); ?>
            </form>
            <hr>
            <div style="color:#64748b;line-height:1.8;">
                <p><?php printf(__('站点地图地址：<code>%s</code>', 'techpress'), esc_url(home_url('/sitemap.xml'))); ?></p>
                <p><?php printf(__('以上部分设置也在 <a href="%s">外观 → 自定义</a> 中可用。', 'techpress'), esc_url(admin_url('customize.php'))); ?></p>
                <?php if (defined('WP_CACHE') && WP_CACHE) : ?>
                    <p><?php _e('检测到缓存插件已启用，浏览量通过 AJAX 计数，不受缓存影响。', 'techpress'); ?></p>
                <?php endif; ?>
                <?php echo apply_filters('techpress_settings_page_extra', ''); ?>
            </div>
        </div>

        <script>
        jQuery(function($) {
            $('.techpress-accordion-title').on('click', function() {
                $(this).toggleClass('open');
                $(this).next('.techpress-accordion-content').toggleClass('open');
            });
        });
        </script>
        <?php
    }

    public function print_header_code() {
        $code = get_option('techpress_header_code', '');
        if ($code) {
            echo "\n" . $code . "\n";
        }
    }

    public function print_footer_code() {
        $code = get_option('techpress_footer_code', '');
        if ($code) {
            echo "\n" . $code . "\n";
        }
    }
}
