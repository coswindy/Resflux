<?php
/**
 * Ad Display Template Part
 * Usage: get_template_part('template-parts/ad-display', 'carousel-banner');
 * Slot name is passed as the second parameter to get_template_part
 *
 * Or directly: techpress_ad_slot('carousel-banner', 'extra-class');
 *
 * Available slots:
 *   carousel-banner  — 首页轮播下方横幅
 *   grid-infeed      — 首页网格信息流
 *   single-top       — 文章顶部
 *   single-middle    — 文章中间
 *   single-bottom    — 文章底部
 *   list-infeed      — 列表页信息流
 *   footer-banner    — 页脚上方横幅
 */

$slot = $args['slot'] ?? '';
$class = $args['class'] ?? '';

if ($slot) {
    techpress_ad_slot($slot, $class);
}
