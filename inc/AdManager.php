<?php

namespace TechPress;

class AdManager {

    private $slots = [
        'carousel_banner',
        'grid_infeed',
        'single_top',
        'single_middle',
        'single_bottom',
        'list_infeed',
        'footer_banner',
    ];

    public function __construct() {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('wp_head', [$this, 'print_adsense_auto'], 1);
        add_filter('the_content', [$this, 'inject_middle_ad']);
        add_filter('techpress_settings_page_extra', [$this, 'settings_help']);
    }

    public function get_code($slot) {
        return get_option('techpress_ad_' . $slot, '');
    }

    public function has($slot) {
        return !empty($this->get_code($slot));
    }

    public function render($slot, $class = '') {
        $code = $this->get_code($slot);
        if (!$code) {
            return;
        }
        echo '<div class="ad-container ad-slot-' . esc_attr($slot) . ' ' . esc_attr($class) . '">' . $code . '</div>';
    }

    public function print_adsense_auto() {
        $enabled = get_option('techpress_adsense_auto', 0);
        $pub_id  = get_option('techpress_adsense_publisher', '');
        if ($enabled && $pub_id) {
            echo "\n" . '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=' . esc_attr($pub_id) . '" crossorigin="anonymous"></script>' . "\n";
        }
    }

    public function inject_middle_ad($content) {
        if (!is_single() || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $ad_code = $this->get_code('single_middle');
        if (!$ad_code) {
            return $content;
        }

        $paragraphs = preg_split('/<\/p>\s*/', $content);
        if (count($paragraphs) < 2) {
            return $content;
        }

        $first_half  = $paragraphs[0] . '</p>';
        $tail        = array_slice($paragraphs, 1);
        $last_empty  = end($tail) === '';
        if ($last_empty) {
            array_pop($tail);
        }
        $second_half = implode('</p>', $tail) . ($tail ? '</p>' : '') . ($last_empty ? '</p>' : '');
        $ad_html     = '<div class="ad-container ad-slot-single-middle">' . $ad_code . '</div>';

        return $first_half . $ad_html . $second_half;
    }

    public function settings_help($html) {
        $html .= '<p>📢 广告位说明：<br>
    — <strong>轮播下方横幅</strong>：首页轮播图和内容区域之间<br>
    — <strong>首页网格信息流</strong>：首页文章网格中每隔4篇插入<br>
    — <strong>文章顶部/中间/底部</strong>：单篇文章的上/中/下三个位置<br>
    — <strong>列表页信息流</strong>：分类/标签/搜索列表每隔3篇插入<br>
    — <strong>页脚横幅</strong>：页脚上方全宽广告<br>
    — <strong>侧栏广告</strong>：在小工具中拖拽 "Resflux 广告位"</p>';
        return $html;
    }

    public function register_settings() {
        add_settings_section('techpress_ads_section', __('广告设置', 'techpress'), function () {
            echo '<p>支持 Google AdSense 自动广告和自定义广告位。填入 AdSense Publisher ID 并开启自动广告后，Google 会在最佳位置自动展示广告。<br>手动广告位代码支持任何 HTML/JS（AdSense 单元、百度联盟、Carbon Ads 等）。</p>';
        }, 'techpress-settings');

        register_setting('techpress_settings', 'techpress_adsense_publisher', 'sanitize_text_field');
        add_settings_field('techpress_adsense_publisher', __('AdSense 发布商 ID', 'techpress'), function () {
            echo '<input type="text" name="techpress_adsense_publisher" value="' . esc_attr(get_option('techpress_adsense_publisher', '')) . '" class="regular-text" placeholder="pub-0000000000000000">';
        }, 'techpress-settings', 'techpress_ads_section');

        register_setting('techpress_settings', 'techpress_adsense_auto', 'absint');
        add_settings_field('techpress_adsense_auto', __('启用自动广告', 'techpress'), function () {
            echo '<label><input type="checkbox" name="techpress_adsense_auto" value="1" ' . checked(1, get_option('techpress_adsense_auto', 0), false) . '> 启用 Google AdSense 自动广告（自动广告位将自动填充）</label>';
        }, 'techpress-settings', 'techpress_ads_section');

        $labels = [
            'carousel_banner' => ['label' => __('轮播下方横幅', 'techpress'), 'desc' => __('首页轮播图下方，728×90 或全宽横幅', 'techpress')],
            'grid_infeed'     => ['label' => __('首页网格信息流', 'techpress'), 'desc' => __('首页文章网格中每隔4篇插入一个广告', 'techpress')],
            'single_top'      => ['label' => __('文章顶部广告', 'techpress'), 'desc' => __('文章内容上方', 'techpress')],
            'single_middle'   => ['label' => __('文章中间广告', 'techpress'), 'desc' => __('文章第一个段落之后插入', 'techpress')],
            'single_bottom'   => ['label' => __('文章底部广告', 'techpress'), 'desc' => __('文章内容与评论之间', 'techpress')],
            'list_infeed'     => ['label' => __('列表页信息流', 'techpress'), 'desc' => __('分类/标签/搜索等列表页每隔3篇插入一个广告', 'techpress')],
            'footer_banner'   => ['label' => __('页脚横幅', 'techpress'), 'desc' => __('页脚上方，全宽横幅', 'techpress')],
        ];

        foreach ($this->slots as $slot) {
            $option = 'techpress_ad_' . $slot;
            register_setting('techpress_settings', $option, 'wp_kses_post');
            add_settings_field($option, $labels[$slot]['label'], function () use ($option, $labels, $slot) {
                echo '<textarea name="' . esc_attr($option) . '" rows="4" class="large-text code">' . esc_textarea(get_option($option, '')) . '</textarea><p class="description">' . esc_html($labels[$slot]['desc']) . '</p>';
            }, 'techpress-settings', 'techpress_ads_section');
        }
    }
}
