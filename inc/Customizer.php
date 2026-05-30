<?php

namespace TechPress;

class Customizer {

    public function __construct() {
        add_action('customize_register', [$this, 'register']);
    }

    public function register($wp_customize) {
        $this->seo($wp_customize);
        $this->footer($wp_customize);
        $this->social($wp_customize);
        $this->appearance($wp_customize);
    }

    private function seo($wp_customize) {
        $section = 'techpress_seo';
        $wp_customize->add_section($section, [
            'title'    => __('SEO 设置', 'techpress'),
            'priority' => 75,
        ]);

        $wp_customize->add_setting('techpress_seo_home_title', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_seo_home_title', [
            'label'       => __('首页 SEO 标题', 'techpress'),
            'description' => __('留空使用默认标题', 'techpress'),
            'section'     => $section,
            'type'        => 'text',
        ]);

        $wp_customize->add_setting('techpress_seo_home_desc', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_seo_home_desc', [
            'label'       => __('首页 SEO 描述', 'techpress'),
            'description' => __('建议 150 字以内', 'techpress'),
            'section'     => $section,
            'type'        => 'textarea',
        ]);

        $wp_customize->add_setting('techpress_seo_home_keywords', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_seo_home_keywords', [
            'label'       => __('首页 SEO 关键词', 'techpress'),
            'description' => __('英文逗号分隔', 'techpress'),
            'section'     => $section,
            'type'        => 'text',
        ]);

        $wp_customize->add_setting('techpress_seo_og_image', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'techpress_seo_og_image', [
            'label'       => __('默认分享图 (OG Image)', 'techpress'),
            'description' => __('建议 1200×630', 'techpress'),
            'section'     => $section,
        ]));

        $wp_customize->add_setting('techpress_seo_sitemap', [
            'default'           => '1',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_seo_sitemap', [
            'label'   => __('站点地图', 'techpress'),
            'section' => $section,
            'type'    => 'checkbox',
        ]);
    }

    private function footer($wp_customize) {
        $section = 'techpress_footer';
        $wp_customize->add_section($section, [
            'title'    => __('页脚设置', 'techpress'),
            'priority' => 120,
        ]);

        $fields = [
            'footer_copyright' => [
                'label'   => __('版权信息', 'techpress'),
                'sanitize' => 'sanitize_text_field',
            ],
            'footer_icp' => [
                'label'   => __('ICP备案号', 'techpress'),
                'sanitize' => 'sanitize_text_field',
            ],
            'footer_gongan' => [
                'label'   => __('公安备案号', 'techpress'),
                'sanitize' => 'sanitize_text_field',
            ],
        ];

        foreach ($fields as $key => $config) {
            $wp_customize->add_setting($key, [
                'default'           => '',
                'sanitize_callback' => $config['sanitize'],
            ]);
            $wp_customize->add_control($key, [
                'label'   => $config['label'],
                'section' => $section,
                'type'    => 'text',
            ]);
        }
    }

    private function social($wp_customize) {
        $section = 'techpress_social';
        $wp_customize->add_section($section, [
            'title'    => __('社交链接', 'techpress'),
            'priority' => 121,
        ]);

        $socials = [
            'social_weibo'    => '微博',
            'social_wechat'   => '微信',
            'social_bilibili' => 'B站',
            'social_github'   => 'GitHub',
            'social_twitter'  => 'Twitter / X',
            'social_rss'      => 'RSS',
        ];

        foreach ($socials as $key => $label) {
            $wp_customize->add_setting($key, [
                'default'           => '',
                'sanitize_callback' => 'esc_url_raw',
            ]);
            $wp_customize->add_control($key, [
                'label'   => $label,
                'section' => $section,
                'type'    => 'url',
            ]);
        }
    }

    private function appearance($wp_customize) {
        $section = 'techpress_appearance';
        $wp_customize->add_section($section, [
            'title'    => __('外观设置', 'techpress'),
            'priority' => 80,
        ]);

        $wp_customize->add_setting('techpress_dark_mode', [
            'default'           => 'auto',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_dark_mode', [
            'label'   => __('深色模式', 'techpress'),
            'section' => $section,
            'type'    => 'radio',
            'choices' => [
                'auto' => __('跟随系统', 'techpress'),
                'on'   => __('始终开启', 'techpress'),
                'off'  => __('始终关闭', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_breadcrumb_align', [
            'default'           => 'left',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_breadcrumb_align', [
            'label'   => __('面包屑对齐', 'techpress'),
            'section' => $section,
            'type'    => 'radio',
            'choices' => [
                'left'   => __('居左', 'techpress'),
                'center' => __('居中', 'techpress'),
                'right'  => __('居右', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_primary_nav_align', [
            'default'           => 'center',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_primary_nav_align', [
            'label'   => __('主导航对齐', 'techpress'),
            'section' => $section,
            'type'    => 'radio',
            'choices' => [
                'left'   => __('靠左', 'techpress'),
                'center' => __('居中', 'techpress'),
                'right'  => __('靠右', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_logo_url', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'techpress_logo_url', [
            'label'       => __('站点 Logo', 'techpress'),
            'description' => __('上传 Logo 图片，会替换默认站点标题文字', 'techpress'),
            'section'     => $section,
        ]));

        $wp_customize->add_setting('techpress_color_scheme', [
            'default'           => 'aurora-blue',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_color_scheme', [
            'label'   => __('配色方案', 'techpress'),
            'section' => $section,
            'type'    => 'select',
            'choices' => [
                'aurora-blue'   => __('极光蓝', 'techpress'),
                'glacier-teal'  => __('冰川青', 'techpress'),
                'nebula-purple' => __('星云紫', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_home_layout', [
            'default'           => 'grid',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_home_layout', [
            'label'   => __('首页布局', 'techpress'),
            'section' => $section,
            'type'    => 'radio',
            'choices' => [
                'grid' => __('网格 (2列)', 'techpress'),
                'list' => __('列表 (1列)', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_single_layout', [
            'default'           => 'sidebar',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_single_layout', [
            'label'   => __('文章页布局', 'techpress'),
            'section' => $section,
            'type'    => 'radio',
            'choices' => [
                'sidebar' => __('带侧栏', 'techpress'),
                'full'    => __('全宽', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_readmore_threshold', [
            'default'           => 1800,
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control('techpress_readmore_threshold', [
            'label'       => __('长文折叠高度 (px)', 'techpress'),
            'description' => __('文章内容超出此高度后自动折叠，默认 1800', 'techpress'),
            'section'     => $section,
            'type'        => 'number',
            'input_attrs' => ['min' => 500, 'max' => 5000, 'step' => 100],
        ]);

        $wp_customize->add_setting('techpress_sticky_count', [
            'default'           => 3,
            'sanitize_callback' => 'absint',
        ]);
        $wp_customize->add_control('techpress_sticky_count', [
            'label'       => __('首页置顶文章数', 'techpress'),
            'description' => __('首页置顶区域展示的置顶文章数量', 'techpress'),
            'section'     => $section,
            'type'        => 'number',
            'input_attrs' => ['min' => 0, 'max' => 6],
        ]);

        $wp_customize->add_setting('techpress_font_family', [
            'default'           => 'default',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_font_family', [
            'label'   => __('字体', 'techpress'),
            'section' => $section,
            'type'    => 'select',
            'choices' => [
                'default' => __('系统默认', 'techpress'),
                'yahei'   => '微软雅黑',
                'noto'    => 'Noto Sans SC',
                'custom'  => __('自定义', 'techpress'),
            ],
        ]);

        $wp_customize->add_setting('techpress_custom_font', [
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_custom_font', [
            'label'       => __('自定义字体栈', 'techpress'),
            'description' => __('选择"自定义"后生效', 'techpress'),
            'section'     => $section,
            'type'        => 'text',
        ]);

        $wp_customize->add_setting('techpress_gravatar_mirror', [
            'default'           => 'www.gravatar.com',
            'sanitize_callback' => 'sanitize_text_field',
        ]);
        $wp_customize->add_control('techpress_gravatar_mirror', [
            'label'   => __('Gravatar 镜像服务器', 'techpress'),
            'section' => $section,
            'type'    => 'select',
            'choices' => [
                'www.gravatar.com'    => 'www.gravatar.com',
                'cn.gravatar.com'     => 'cn.gravatar.com',
                'secure.gravatar.com' => 'secure.gravatar.com',
                '0.gravatar.com'      => '0.gravatar.com',
            ],
        ]);

        $wp_customize->add_setting('techpress_footer_qrcode_qq', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'techpress_footer_qrcode_qq', [
            'label'       => __('QQ 二维码图片', 'techpress'),
            'description' => __('显示在"关注我们"区域', 'techpress'),
            'section'     => $section,
        ]));

        $wp_customize->add_setting('techpress_footer_qrcode_wechat', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'techpress_footer_qrcode_wechat', [
            'label'       => __('微信二维码图片', 'techpress'),
            'description' => __('显示在"关注我们"区域', 'techpress'),
            'section'     => $section,
        ]));

        $wp_customize->add_setting('techpress_default_thumbnail', [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'techpress_default_thumbnail', [
            'label'       => __('默认缩略图', 'techpress'),
            'description' => __('当文章无特色图像且无内容图片时显示的默认图片', 'techpress'),
            'section'     => $section,
        ]));
    }
}
