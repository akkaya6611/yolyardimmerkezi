<?php
/**
 * Yol Yardım Merkezi — Öne Çıkan Firmalar Bölümü
 * İkon sistemi: yym_icon() helper ile SVG, emoji yok.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$args = array(
    'post_type'      => 'firma',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
);
$firms_query = new WP_Query($args);

// Kayıtlı firma yoksa vitrin bölümünü gösterme.
if (!$firms_query->have_posts()) {
    return;
}
?>

<section class="yym-section yym-featured-firms-section" id="firmalar">
    <div class="lst-container">
        <!-- Bölüm Başlığı & Sağ Link -->
        <div class="yym-section-header-row">
            <div>
                <span class="yym-section-tag">FİRMA REHBERİ</span>
                <h2 class="yym-section-title">Rehberdeki Firmaları İnceleyin</h2>
                <p class="yym-section-subtitle">
                    İletişim bilgileri ve hizmet kategorileriyle listelenen işletmeler.
                </p>
            </div>
            <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-header-view-all-link">
                <span>Tüm Firmaları Gör</span>
                <?php echo yym_icon('arrow-right', 'ui', 'yym-icon-md'); ?>
            </a>
        </div>

        <div class="mis360-firma-grid">
            <?php while ($firms_query->have_posts()) : $firms_query->the_post(); ?>
                <?php get_template_part('template-parts/firma-card', null, array('heading' => 'h3')); ?>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
