<?php
/**
 * Yol Yardım Merkezi - Profesyonel Ana Sayfa Şablonu (front-page.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<main id="primary" class="site-main yym-home-main">
    <!-- 1. HERO BÖLÜMÜ: Gece Yol Manzarası, Büyük Arama Alanı ve Güven İkonları -->
    <?php get_template_part('template-parts/home-hero'); ?>

    <!-- 2. HİZMET KATEGORİLERİ: 8 Modern Kart -->
    <?php get_template_part('template-parts/home-services'); ?>

    <!-- 3. AIRBNB TARZI GÜVEN & DOĞRULAMA SİSTEMİ: 4 Aşamalı Denetim -->
    <?php get_template_part('template-parts/home-trust-system'); ?>

    <!-- 4. NASIL ÇALIŞIR?: 3 Aşamalı Modern Timeline -->
    <?php get_template_part('template-parts/home-how-it-works'); ?>

    <!-- 4. TÜRKİYE HARİTASI BÖLÜMÜ: İnteraktif Şehir Haritası & İl Rehberi -->
    <?php get_template_part('template-parts/home-turkey-map'); ?>

    <!-- 5. ÖNE ÇIKAN FİRMALAR: Marketplace & Google İşletme Tarzı Kartlar -->
    <?php get_template_part('template-parts/home-featured-firms'); ?>

    <!-- 6. NEDEN YOL YARDIM MERKEZİ?: 4 Avantaj Kartı -->
    <?php get_template_part('template-parts/home-why-us'); ?>

    <!-- 7. FİRMA KAYIT ALANI: Çekici Esnafı İçin Premium Katılım Kartı -->
    <?php get_template_part('template-parts/home-firm-cta'); ?>

    <!-- 8. BLOG / BİLGİ ALANI: SEO Odaklı Yol Yardım Rehberi -->
    <?php get_template_part('template-parts/home-blog'); ?>
</main>

<?php
get_footer();
