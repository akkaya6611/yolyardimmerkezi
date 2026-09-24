<?php
/**
 * Yol Yardım Merkezi — Hizmet Kategorileri Bölümü
 * İkon sistemi: yym_icon() helper ile SVG, emoji yok.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$service_categories = array(
    array(
        'slug'  => 'cekici',
        'icon'  => 'tow-truck',
        'cat'   => 'services',
        'title' => 'Oto Çekici',
        'desc'  => 'Kaza ve mekanik arıza durumlarında kayar kasalı araçlarla güvenli nakliye.',
        'count' => 'Firma Rehberi',
    ),
    array(
        'slug'  => 'aku',
        'icon'  => 'car-battery',
        'cat'   => 'services',
        'title' => 'Akü Takviyesi',
        'desc'  => 'Akü takviyesi ve değişimi için işletmelerle görüşün.',
        'count' => 'Müsaitliği Sorun',
    ),
    array(
        'slug'  => 'lastik',
        'icon'  => 'flat-tire',
        'cat'   => 'services',
        'title' => 'Lastik Yardımı',
        'desc'  => 'Otoyol ve şehir içinde yerinde mobil stepne değişimi, fitil ve yama tamiri.',
        'count' => 'Yerinde Onarım',
    ),
    array(
        'slug'  => 'cilingir',
        'icon'  => 'car-key',
        'cat'   => 'services',
        'title' => 'Oto Çilingir',
        'desc'  => 'Araç anahtarı ve kilit sorunları için oto çilingir hizmetleri.',
        'count' => 'Oto Anahtar',
    ),
    array(
        'slug'  => 'yakit',
        'icon'  => 'fuel-can',
        'cat'   => 'services',
        'title' => 'Yakıt Takviyesi',
        'desc'  => 'Yakıt desteği için hizmet kapsamını ve teslimat koşullarını görüşün.',
        'count' => 'Acil İkmal',
    ),
    array(
        'slug'  => 'kurtarma',
        'icon'  => 'winch',
        'cat'   => 'services',
        'title' => 'Oto Kurtarma',
        'desc'  => 'Şarampole yuvarlanma, çamur veya kara saplanma durumunda vinçli kurtarma.',
        'count' => 'Ağır Donanım',
    ),
    array(
        'slug'  => 'agir-vasita',
        'icon'  => 'truck',
        'cat'   => 'vehicles',
        'title' => 'Ağır Vasıta Çekici',
        'desc'  => 'Tır, kamyon, otobüs ve iş makineleri için yüksek tonajlı kurtarıcı filosu.',
        'count' => 'Tır & Kamyon',
    ),
    array(
        'slug'  => 'motosiklet',
        'icon'  => 'motorcycle',
        'cat'   => 'vehicles',
        'title' => 'Motosiklet Yol Yardım',
        'desc'  => 'Motosiklet taşıma için uygun ekipman ve koşulları firmaya sorun.',
        'count' => 'Özel Aparatlı',
    ),
);
?>

<section class="yym-section yym-services-section" id="hizmetler">
    <div class="lst-container">
        <!-- Bölüm Başlığı -->
        <div class="yym-section-header text-center">
            <span class="yym-section-tag">HİZMET AĞIMIZ</span>
            <h2 class="yym-section-title">Yol Yardım Hizmetleri</h2>
            <p class="yym-section-subtitle">
                İhtiyacınıza uygun hizmet kategorisinden işletmeleri arayın.
            </p>
        </div>

        <!-- Hizmet Kartları Grid (8 Kart) -->
        <div class="yym-service-cards-grid">
            <?php foreach ($service_categories as $svc) : ?>
                <a href="<?php echo esc_url(home_url('/firmalar/?category=' . $svc['slug'])); ?>" class="yym-service-cat-card">
                    <div class="yym-icon-wrap">
                        <?php echo yym_icon($svc['icon'], $svc['cat'], 'yym-icon-lg'); ?>
                    </div>
                    <div class="yym-service-cat-info">
                        <div class="yym-service-cat-top">
                            <h3 class="yym-service-cat-name"><?php echo esc_html($svc['title']); ?></h3>
                            <span class="yym-service-cat-badge"><?php echo esc_html($svc['count']); ?></span>
                        </div>
                        <p class="yym-service-cat-desc"><?php echo esc_html($svc['desc']); ?></p>
                    </div>
                    <div class="yym-service-cat-arrow">
                        <?php echo yym_icon('arrow-right', 'ui', 'yym-icon-sm'); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
