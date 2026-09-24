<?php
/**
 * Yol Yardım Merkezi - 1. Hero Bölümü (Türkiye'nin Güvenilir Yol Yardım Ağı)
 * "Yolda kaldığınızda rastgele aramayın. Güvenilir yardıma ulaşın."
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$all_cities = array(
    'İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya', 'Adana', 'Konya', 'Kayseri',
    'Gaziantep', 'Şanlıurfa', 'Kocaeli', 'Mersin', 'Diyarbakır', 'Hatay', 'Manisa',
    'Samsun', 'Balıkesir', 'Kahramanmaraş', 'Van', 'Aydın', 'Denizli', 'Sakarya',
    'Tekirdağ', 'Muğla', 'Eskişehir', 'Mardin', 'Malatya', 'Trabzon', 'Erzurum',
    'Ordu', 'Afyonkarahisar', 'Sivas', 'Adıyaman', 'Batman', 'Tokat', 'Zonguldak',
    'Kütahya', 'Çorum', 'Elazığ', 'Isparta', 'Yozgat', 'Ağrı', 'Kırklareli',
    'Uşak', 'Aksaray', 'Niğde', 'Düzce', 'Rize', 'Bitlis', 'Kastamonu', 'Siirt',
    'Osmaniye', 'Kırıkkale', 'Bolu', 'Bingöl', 'Karabük', 'Amasya', 'Muş',
    'Çanakkale', 'Edirne', 'Giresun', 'Nevşehir', 'Erzincan', 'Kars', 'Kilis',
    'Yalova', 'Burdur', 'Çankırı', 'Karaman', 'Kırşehir', 'Hakkari', 'Sinop',
    'Şırnak', 'Bilecik', 'Bartın', 'Iğdır', 'Artvin', 'Gümüşhane', 'Bayburt', 'Tunceli', 'Ardahan'
);

$services = array(
    'cekici'        => 'Oto Çekici & Kurtarıcı',
    'aku'           => 'Yerinde Akü Takviyesi',
    'lastik'        => 'Mobil Lastik Tamiri',
    'cilingir'      => 'Acil Oto Çilingir',
    'yakit'         => 'Yakıt İkmal Desteği',
    'kurtarma'      => 'Şarampol & Çamur Kurtarma',
    'agir-vasita'   => 'Ağır Vasıta & Vinç',
    'motosiklet'    => 'Motosiklet Özel Taşıma'
);
?>

<section class="yym-hero-area">
    <div class="yym-hero-bg-overlay"></div>
    
    <div class="lst-container yym-hero-container">
        <!-- Canlı Ağ ve Güven Rozeti -->
        <div class="yym-hero-emergency-badge">
            <span class="yym-badge-live-pulse"></span>
            <span class="yym-live-dot">Türkiye Genelinde Yol Yardım ve İşletme Rehberi</span>
        </div>

        <!-- Ana Başlık ve Alt Başlık -->
        <h1 class="yym-hero-main-title">
            Yolda Kaldığınızda Rastgele Aramayın.<br class="yym-br-desktop">
            <span class="yym-highlight-orange">Güvenilir Yardıma Ulaşın.</span>
        </h1>
        <p class="yym-hero-lead-text">
            İl, ilçe ve hizmet türüne göre firmaları inceleyin. Hizmet kapsamını, müsaitliği, varış süresini ve ücreti doğrudan işletmeyle görüşün.
        </p>

        <!-- Akıllı Konum & Arama Kutusu -->
        <div class="yym-hero-search-wrapper">
            <form class="yym-hero-search-form" method="get" action="<?php echo esc_url(home_url('/firmalar/')); ?>" id="yymHeroSearchForm">
                <!-- İl / İlçe Seçimi -->
                <div class="yym-search-field-box">
                    <span class="yym-field-icon"><?php echo yym_icon('location-pin', 'location', 'yym-icon-md'); ?></span>
                    <?php mis360_location_fields(); ?>
                </div>

                <div class="yym-search-divider"></div>

                <!-- Hizmet Seçimi -->
                <div class="yym-search-field-box">
                    <span class="yym-field-icon"><?php echo yym_icon('tow-truck', 'services', 'yym-icon-md'); ?></span>
                    <select name="category" class="yym-search-select" aria-label="Gereken hizmeti seçin">
                        <option value="">Hizmet seçin (Tümü)</option>
                        <?php mis360_category_options(''); ?>
                    </select>
                </div>

                <!-- Hızlı Arama Butonu -->
                <button type="submit" class="yym-hero-submit-btn">
                    <?php echo yym_icon('gps', 'location', 'yym-icon-md'); ?>
                    <span>Bölgemdeki Firmaları Bul</span>
                </button>
            </form>
        </div>

        <!-- 4 Güven Maddesi -->
        <div class="yym-hero-trust-row yym-hero-trust-pillars">
            <div class="yym-trust-item">
                <span class="yym-trust-check"><?php echo yym_icon('shield-check', 'trust', 'yym-icon-md'); ?></span>
                <span><strong>Firma Profilleri</strong> (Hizmet ve iletişim bilgileri)</span>
            </div>
            <div class="yym-trust-item">
                <span class="yym-trust-check"><?php echo yym_icon('document-check', 'trust', 'yym-icon-md'); ?></span>
                <span><strong>Konuma Göre Arama</strong> (İl ve ilçe filtreleri)</span>
            </div>
            <div class="yym-trust-item">
                <span class="yym-trust-check"><?php echo yym_icon('lock', 'trust', 'yym-icon-md'); ?></span>
                <span><strong>Doğrudan İletişim</strong> (Firmayla telefon üzerinden görüşün)</span>
            </div>
            <div class="yym-trust-item">
                <span class="yym-trust-check"><?php echo yym_icon('clock-24', 'location', 'yym-icon-md'); ?></span>
                <span><strong>Güncellik Bildirimi</strong> (Eksik veya yanlış bilgiyi iletin)</span>
            </div>
        </div>
    </div>
</section>
