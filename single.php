<?php
/**
 * Yol Yardım Merkezi - Tekil Blog Yazısı Şablonu (single.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- 1. ÜST HERO BANNER -->
<section class="yym-page-hero-section">
    <div class="lst-container" style="text-align: center;">
        <nav class="yym-breadcrumbs" aria-label="Ekmek Kırıntısı">
            <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
            <span>›</span>
            <a href="<?php echo esc_url(home_url('/blog/')); ?>">Sürücü Rehberi & Blog</a>
            <span>›</span>
            <span class="active"><?php the_title(); ?></span>
        </nav>
        
        <div class="yym-page-hero-content" style="max-width: 840px; margin: 0 auto; text-align: center;">
            <span class="yym-hero-mini-badge">📰 SÜRÜCÜ BİLGİ REHBERİ</span>
            <h1 class="yym-page-hero-title"><?php the_title(); ?></h1>
            <div class="yym-post-meta-strip">
                <span>📅 <?php echo get_the_date(); ?></span>
                <span>•</span>
                <span>⏱️ Ortalama 3 Dk Okuma</span>
                <span>•</span>
                <span>🛡️ Doğrulanmış Rehber</span>
            </div>
        </div>
    </div>
</section>

<!-- 2. ANA İÇERİK VE YAN PANEL -->
<div class="yym-single-blog-wrap">
    <div class="lst-container">
        <div class="yym-blog-layout-grid">
            <!-- Sol: Makale İçeriği -->
            <main id="primary" class="yym-blog-main-col">
                <article id="post-<?php the_ID(); ?>" <?php post_class('yym-single-blog-article'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="yym-blog-featured-img-box">
                            <?php the_post_thumbnail('large', array('class' => 'yym-blog-feat-img')); ?>
                        </div>
                    <?php endif; ?>

                    <div class="yym-blog-entry-content">
                        <?php
                        while (have_posts()) :
                            the_post();
                            the_content();
                        endwhile;
                        ?>
                    </div>

                    <!-- Makale Sonu Acil Çekici Çağrı Kutusu (Yüksek Dönüşümlü & Şık) -->
                    <div class="yym-post-emergency-banner">
                        <div class="yym-peb-header">
                            <span class="yym-peb-icon">🚨</span>
                            <div>
                                <h3>Aracınız Şu An Yolda mı Kaldı?</h3>
                                <p>Zaman kaybetmeyin. 81 ilde vergi levhalı, K1/K2 ruhsatlı ve emtia sigortalı en yakın nöbetçi kurtarıcı ekipleri 15-25 dakikada yanınızda.</p>
                            </div>
                        </div>
                        <div class="yym-peb-actions">
                            <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-btn-peb-search">
                                <span>🔍 En Yakın Çekiciyi Bul (81 İl)</span>
                                <span>→</span>
                            </a>
                            <a href="<?php echo esc_url(home_url('/sehirler/')); ?>" class="yym-btn-peb-cities">
                                <span>📍 Şehir Seçin</span>
                            </a>
                        </div>
                    </div>

                    <!-- Makale Altı Bilgilendirme -->
                    <div class="yym-post-footer-bar">
                        <div class="yym-pf-author">
                            <strong>Yol Yardım Merkezi Bilgi Ağı</strong>
                            <p>Sürücüler için 81 ilde doğrulanmış yol yardım, oto kurtarma ve acil müdahale rehberi.</p>
                        </div>
                        <a href="<?php echo esc_url(home_url('/blog/')); ?>" class="yym-pf-back-link">
                            ← Blog Sayfasına Dön
                        </a>
                    </div>
                </article>
            </main>

            <!-- Sağ: Yapışkan Yan Panel (Sidebar) -->
            <aside class="yym-blog-sidebar-col">
                <div class="yym-sidebar-sticky-wrap">
                    <!-- 1. Hızlı Çekici Arama Kartı -->
                    <div class="yym-side-card yym-side-emergency-card">
                        <div class="yym-sec-badge">7/24 NÖBETÇİ KURTARICI</div>
                        <h4 class="yym-sec-title">Acil Çekici Çağır</h4>
                        <p class="yym-sec-desc">Bulunduğunuz konuma en yakın lisanslı ekipleri arayın, telefonda konuşulan sabit fiyatla güvenle hizmet alın.</p>
                        <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-btn-side-dispatch">
                            <span>🚨 Nöbetçi Çekici Bul</span>
                            <span>→</span>
                        </a>
                    </div>

                    <!-- 2. Acil Yardım Hizmetleri -->
                    <div class="yym-side-card">
                        <h4 class="yym-side-title">Yol Yardım Hizmetleri</h4>
                        <ul class="yym-side-service-links">
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=cekici')); ?>">🚗 <span>Oto Çekici & Kurtarıcı</span></a></li>
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=aku')); ?>">⚡ <span>Yerinde Akü Takviyesi</span></a></li>
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=lastik')); ?>">🛞 <span>Mobil Lastik Tamiri</span></a></li>
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=cilingir')); ?>">🔑 <span>Acil Oto Çilingir</span></a></li>
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=yakit')); ?>">⛽ <span>Yakıt İkmal Desteği</span></a></li>
                            <li><a href="<?php echo esc_url(home_url('/firmalar/?category=agir-vasita')); ?>">🚛 <span>Ağır Vasıta & Tır</span></a></li>
                        </ul>
                    </div>

                    <!-- 3. Güven Standartları -->
                    <div class="yym-side-card yym-side-guarantee-card">
                        <h4 class="yym-side-title">Ağ Güvenlik Standartları</h4>
                        <ul class="yym-side-g-list">
                            <li><span>✅</span> Vergi levhalı resmi mükellef esnaf</li>
                            <li><span>✅</span> K1 / K2 çekici nakliyat ruhsatı</li>
                            <li><span>✅</span> %100 hasarsız emtia kasko sigortası</li>
                            <li><span>✅</span> Telefonda bağlayıcı sabit fiyat taahhüdü</li>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<?php
get_footer();
