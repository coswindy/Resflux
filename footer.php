</main><!-- .site-main -->

<footer class="site-footer" id="site-footer">
    <?php techpress_ad_slot('footer_banner', 'ad-slot-footer-banner'); ?>

    <div class="footer-compact">
        <div class="footer-links-row">
            <?php
            $has_friends = false;
            $fl = techpress_get_friend_links();
            if ($fl->have_posts()) :
                $has_friends = true;
                echo '<div class="friend-links friend-links--horizontal">';
                while ($fl->have_posts()) : $fl->the_post();
                    $url    = get_post_meta(get_the_ID(), '_fl_url', true);
                    $desc   = get_post_meta(get_the_ID(), '_fl_desc', true);
                    $logo   = get_post_meta(get_the_ID(), '_fl_logo', true);
                    $target = get_the_title();
                    if ($url) :
                        echo '<a href="' . esc_url($url) . '" target="_blank" rel="friend noopener" title="' . esc_attr($desc ?: $target) . '">';
                        if ($logo) {
                            echo '<img src="' . esc_url($logo) . '" alt="" class="fl-avatar" loading="lazy">';
                        }
                        echo esc_html($target) . '</a>';
                    endif;
                endwhile;
                wp_reset_postdata();
                echo '</div>';
            endif;
            ?>

            <?php if ($has_friends) : ?>
                <span class="footer-links-sep"></span>
            <?php endif; ?>

            <div class="footer-social-inline">
                <?php
                $qq_qr = techpress_setting('techpress_footer_qrcode_qq', '');
                $wx_qr = techpress_setting('techpress_footer_qrcode_wechat', '');

                if ($qq_qr) :
                    echo '<button class="footer-social-btn" data-qrcode="' . esc_url($qq_qr) . '" aria-label="QQ">'
                        . '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>'
                        . '<span>QQ</span></button>';
                endif;

                if ($wx_qr) :
                    echo '<button class="footer-social-btn" data-qrcode="' . esc_url($wx_qr) . '" aria-label="微信">'
                        . '<svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M8.691 2.188C3.891 2.188 0 5.476 0 9.53c0 2.212 1.17 4.203 3.002 5.55a.59.59 0 0 1 .213.665l-.39 1.48c-.019.07-.048.141-.048.213 0 .163.13.295.29.295a.326.326 0 0 0 .167-.054l1.903-1.114a.864.864 0 0 1 .717-.098 10.16 10.16 0 0 0 2.837.403c.276 0 .543-.027.811-.05-.857-2.578.157-4.972 1.932-6.446 1.703-1.415 3.882-1.98 5.853-1.838-.576-3.583-4.196-6.348-8.596-6.348zM5.785 5.991c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178A1.17 1.17 0 0 1 4.623 7.17c0-.651.52-1.18 1.162-1.18zm5.813 0c.642 0 1.162.529 1.162 1.18a1.17 1.17 0 0 1-1.162 1.178 1.17 1.17 0 0 1-1.162-1.178c0-.651.52-1.18 1.162-1.18zm5.34 2.867c-1.797-.052-3.746.512-5.28 1.786-1.72 1.428-2.687 3.72-1.78 6.22.942 2.453 3.666 4.229 6.884 4.229.826 0 1.622-.12 2.361-.336a.72.72 0 0 1 .598.082l1.584.926a.272.272 0 0 0 .14.045.246.246 0 0 0 .241-.245c0-.06-.024-.12-.04-.178l-.325-1.233a.49.49 0 0 1 .177-.553C23.028 18.48 24 16.82 24 14.98c0-3.21-2.931-5.837-7.062-6.122zM14.033 13.5c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.97-.982zm4.844 0c.535 0 .969.44.969.982a.976.976 0 0 1-.969.983.976.976 0 0 1-.969-.983c0-.542.434-.982.97-.982z"/></svg>'
                        . '<span>微信</span></button>';
                endif;

                echo techpress_social_links();
                ?>
            </div>
        </div>

        <hr class="footer-divider">

        <div class="footer-legal">
            <p>
                <?php
                $copyright = techpress_setting('footer_copyright', '');
                echo esc_html($copyright ?: 'Copyright © ' . wp_date('Y') . ' ' . get_bloginfo('name') . '. All Rights Reserved.');
                ?>
                <?php
                $icp    = techpress_setting('footer_icp', '');
                $gongan = techpress_setting('footer_gongan', '');
                if ($icp) : echo ' · <a href="https://beian.miit.gov.cn/" target="_blank" rel="nofollow">' . esc_html($icp) . '</a>'; endif;
                if ($gongan) : echo ' · <a href="http://www.beian.gov.cn/" target="_blank" rel="nofollow">' . esc_html($gongan) . '</a>'; endif;
                ?>
            </p>
        </div>
    </div>
</footer>

<div class="qrcode-modal" id="qrcode-modal">
    <div class="qrcode-modal-backdrop" id="qrcode-backdrop"></div>
    <div class="qrcode-modal-content">
        <button class="qrcode-modal-close" id="qrcode-close">&times;</button>
        <img src="" alt="QR Code" id="qrcode-image">
    </div>
</div>

<button class="back-to-top" id="back-to-top" aria-label="<?php esc_attr_e('返回顶部', 'techpress'); ?>">↑</button>

<?php wp_footer(); ?>
</body>
</html>
