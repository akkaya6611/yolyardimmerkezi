<?php
/**
 * Yol Yardım Merkezi - Standart Sayfa Şablonu (page.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$phone_raw = yym_get_phone_raw();
?>

<div class="yym-page-header">
    <div class="yym-container">
        <h1 class="yym-page-title"><?php the_title(); ?></h1>
    </div>
</div>

<div class="yym-container yym-page-container">
    <div class="yym-page-layout">
        <main id="primary" class="yym-main-content">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('yym-article'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="yym-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="yym-entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </main>

        <aside class="yym-sidebar">
            <div class="yym-sidebar-widget yym-widget-emergency">
                <span class="yym-widget-icon">🚨</span>
                <h3><?php _e('7/24 Acil Yol Yardım', 'yol-yardim-merkezi'); ?></h3>
                <p><?php _e('En yakın nöbetçi ekibimiz 15-30 dakikada yanınızda. Hemen arayın veya konum atın.', 'yol-yardim-merkezi'); ?></p>
                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>" class="yym-btn yym-btn-call yym-btn-block">
                    ✉️ <?php echo esc_html(yym_get_email()); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn">
                    📍 <?php _e('WhatsApp İle Konum Gönder', 'yol-yardim-merkezi'); ?>
                </button>
            </div>

            <div class="yym-sidebar-widget">
                <h4><?php _e('Hizmetlerimiz', 'yol-yardim-merkezi'); ?></h4>
                <ul class="yym-widget-links">
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Oto Çekici & Kurtarıcı</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Akü Takviye & Satış</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Mobil Lastik Tamiri</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Acil Yakıt Desteği</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Motosiklet Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Ağır Vasıta & Minibüs</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
