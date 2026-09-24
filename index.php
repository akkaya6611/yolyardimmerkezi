<?php
/**
 * Yol Yardım Merkezi - Standart Blog & Liste Şablonu
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
        <h1 class="yym-page-title"><?php single_post_title(); ?></h1>
        <p class="yym-page-subtitle"><?php _e('Yol yardım rehberi, sürücü tavsiyeleri ve güncel duyurular.', 'yol-yardim-merkezi'); ?></p>
    </div>
</div>

<div class="yym-container yym-page-container">
    <div class="yym-page-layout">
        <!-- Sol İçerik Alanı -->
        <main id="primary" class="yym-main-content">
            <?php if (have_posts()) : ?>
                <div class="yym-blog-grid">
                    <?php while (have_posts()) : the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('yym-blog-card'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="yym-blog-thumb">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('yym-card'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="yym-blog-body">
                                <span class="yym-blog-date"><?php echo get_the_date(); ?></span>
                                <h2 class="yym-blog-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <div class="yym-blog-excerpt"><?php the_excerpt(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="yym-read-more"><?php _e('Devamını Oku', 'yol-yardim-merkezi'); ?> →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>

                <div class="yym-pagination">
                    <?php the_posts_pagination(array(
                        'prev_text' => '← ' . __('Önceki', 'yol-yardim-merkezi'),
                        'next_text' => __('Sonraki', 'yol-yardim-merkezi') . ' →',
                    )); ?>
                </div>
            <?php else : ?>
                <p><?php _e('Henüz yayınlanmış bir yazı bulunmuyor.', 'yol-yardim-merkezi'); ?></p>
            <?php endif; ?>
        </main>

        <!-- Sağ Acil Durum Yan Paneli (Sidebar) -->
        <aside class="yym-sidebar">
            <div class="yym-sidebar-widget yym-widget-emergency">
                <span class="yym-widget-icon">🚨</span>
                <h3><?php _e('Yolda mı Kaldınız?', 'yol-yardim-merkezi'); ?></h3>
                <p><?php _e('15-30 dakikada en yakın çekiciyi bulunduğunuz noktaya yönlendirelim.', 'yol-yardim-merkezi'); ?></p>
                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>" class="yym-btn yym-btn-call yym-btn-block">
                    ✉️ <?php echo esc_html(yym_get_email()); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn">
                    📍 <?php _e('Konumumu WhatsApp ile Gönder', 'yol-yardim-merkezi'); ?>
                </button>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
