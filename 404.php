<?php
/**
 * 404 - Sayfa Bulunamadı Şablonu
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- 1. ÜST HERO BÖLÜMÜ -->
<div class="yym-page-hero-section yym-hero-404">
    <div class="lst-container" style="text-align: center;">
        <span class="yym-hero-mini-badge">⚠️ HATA 404</span>
        <h1 class="yym-page-hero-title">Aradığınız Sayfa veya Rehber Bulunamadı</h1>
        <p class="yym-page-hero-desc">
            Ulaşmaya çalıştığınız sayfa silinmiş, adresi değişmiş ya da geçici olarak kullanım dışı olabilir.
        </p>
    </div>
</div>

<!-- 2. ANA İÇERİK KARTI -->
<div class="yym-404-container lst-container">
    <div class="yym-404-card">
        <div class="yym-404-icon-wrap">
            <span class="yym-404-icon">🚗💨</span>
        </div>
        
        <h2 class="yym-404-subheading">Yolunuza Güvenle Devam Edin</h2>
        <p class="yym-404-text">
            Yolda mı kaldınız veya çekiciye mi ihtiyacınız var? Türkiye genelinde 81 ilde nöbetçi çekici ve yol yardım ekiplerimize aşağıdaki bağlantılardan hemen ulaşabilirsiniz.
        </p>

        <!-- Arama Formu -->
        <form role="search" method="get" class="yym-404-search-form" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" class="yym-404-search-input" placeholder="İl, ilçe veya yol yardım hizmeti ara..." value="<?php echo get_search_query(); ?>" name="s" />
            <button type="submit" class="yym-404-search-submit">🔍 Ara</button>
        </form>

        <!-- Hızlı Yönlendirme Kartları -->
        <div class="yym-404-actions-grid">
            <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-404-action-btn yym-404-btn-primary">
                <span class="yym-404-ab-icon">🚨</span>
                <div>
                    <strong>81 İl Çekici Ağı</strong>
                    <small>Nöbetçi çekicileri listele</small>
                </div>
            </a>
            <a href="<?php echo esc_url(home_url('/sehirler/')); ?>" class="yym-404-action-btn">
                <span class="yym-404-ab-icon">📍</span>
                <div>
                    <strong>Şehir Seçin</strong>
                    <small>İl ve ilçenize göre ara</small>
                </div>
            </a>
            <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="yym-404-action-btn">
                <span class="yym-404-ab-icon">📰</span>
                <div>
                    <strong>Sürücü Rehberi & Blog</strong>
                    <small>Yol yardım makaleleri</small>
                </div>
            </a>
            <a href="<?php echo esc_url(home_url('/')); ?>" class="yym-404-action-btn">
                <span class="yym-404-ab-icon">🏠</span>
                <div>
                    <strong>Ana Sayfaya Dön</strong>
                    <small>Yol Yardım Merkezi</small>
                </div>
            </a>
        </div>
    </div>
</div>

<?php
get_footer();
