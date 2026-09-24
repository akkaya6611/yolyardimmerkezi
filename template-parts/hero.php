<?php
/**
 * Yol Yardım Merkezi - Canlı Hero Banner (Kullanıcı Ekran Görüntüsü ile 1:1)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="yym-live-hero-section">
    <!-- Arka Plan Karartma Katmanı -->
    <div class="yym-hero-overlay"></div>

    <!-- Ana Manşet ve Arama Alanı (Ortalanmış) -->
    <div class="lst-container yym-hero-center-box">

        <!-- Başlık ve Otomatik Yazı Efekti -->
        <h1 class="yym-main-welcome-title">
            Hoş Geldiniz: <span class="typed-words"></span><span class="typed-cursor">|</span>
        </h1>
        <h4 class="yym-sub-welcome-title">81 İl, 922 İlçeden Firma Burada!</h4>

        <!-- Yatay Beyaz Oval Arama Çubuğu (White Pill Search Bar) -->
        <div class="yym-pill-search-container">
            <form id="yymMainSearchForm" class="yym-pill-search-form" method="get" action="<?php echo esc_url(home_url('/firmalar/')); ?>">
                <!-- 1. Alan: Neyi Arıyorsunuz? -->
                <div class="yym-search-col yym-search-keyword">
                    <input type="text" name="keyword" class="yym-clean-input" placeholder="Neyi arıyorsunuz?" autocomplete="off">
                </div>

                <div class="yym-search-divider"></div>

                <!-- 2. Alan: Konum + Pin İkonu -->
                <div class="yym-search-col yym-search-location">
                    <?php mis360_location_fields(); ?>
                    <button type="button" class="yym-pin-btn js-share-location-btn" title="Konumumu Bul">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#8C9DAE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </button>
                </div>

                <div class="yym-search-divider"></div>

                <!-- 3. Alan: Tüm Kategoriler Dropdown -->
                <div class="yym-search-col yym-search-category">
                    <select name="category" class="yym-clean-select">
                        <option value="">Tüm Kategoriler</option>
                        <?php mis360_category_options(''); ?>
                    </select>
                </div>

                <!-- 4. Alan: Bordo / Koyu Kırmızı Ara Butonu -->
                <button type="submit" class="yym-search-submit-btn">
                    <span>Ara</span>
                </button>
            </form>
        </div>

        <!-- Arama Çubuğu Altı: Öne Çıkan Kategoriler -->
        <div class="yym-hero-tags-row">
            <span class="yym-tags-prompt">Yoksa öne çıkan kategorileri gezinin:</span>
            <a href="#firmalar" class="yym-tag-pill">Firmalar</a>
        </div>
    </div>
</section>
