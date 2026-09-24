<?php
/**
 * Listivo Demo 5 - Hizmet Kategorileri ve 81 İl Plaka Rehberi
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$services = array(
    array('name' => 'Oto Çekici',       'count' => '81 İlde 7/24 Aktif',   'icon' => 'tow-truck'),
    array('name' => 'Akü Takviye',      'count' => 'Yerinde Test & Satış', 'icon' => '⚡'),
    array('name' => 'Mobil Lastik',     'count' => 'Yerinde Tamir & Yama', 'icon' => '🔧'),
    array('name' => 'Yakıt İkmali',     'count' => 'Benzin / Dizel İkmal', 'icon' => '⛽'),
    array('name' => 'Motosiklet Çekici','count' => 'Özel Sabitleme Aparatı','icon' => '🏍️'),
    array('name' => 'Ağır Vasıta',      'count' => 'Vinç & Ahtapot Çekici','icon' => '🏗️'),
);

$cities = array(
    array('plate' => '01', 'name' => 'Adana'),
    array('plate' => '06', 'name' => 'Ankara'),
    array('plate' => '07', 'name' => 'Antalya'),
    array('plate' => '16', 'name' => 'Bursa'),
    array('plate' => '27', 'name' => 'Gaziantep'),
    array('plate' => '34', 'name' => 'İstanbul'),
    array('plate' => '35', 'name' => 'İzmir'),
    array('plate' => '38', 'name' => 'Kayseri'),
    array('plate' => '41', 'name' => 'Kocaeli'),
    array('plate' => '42', 'name' => 'Konya'),
    array('plate' => '55', 'name' => 'Samsun'),
    array('plate' => '61', 'name' => 'Trabzon'),
);
?>

<section class="lst-section lst-browse-section" id="kategoriler">
    <div class="lst-container">
        <!-- 1. Hizmet Türleri Izgarası -->
        <div class="lst-section-heading-bar">
            <div class="lst-heading-group">
                <span class="lst-pill-sub">Yol Yardım Hizmetleri</span>
                <h2 class="lst-section-title">Hizmet Türüne Göre İnceleyin</h2>
            </div>
        </div>

        <div class="lst-styles-grid">
            <?php foreach ($services as $svc) : ?>
                <a href="#firmalar" class="lst-style-card" title="<?php echo esc_attr($svc['name']); ?> Firmaları">
                    <div class="lst-style-icon"><?php echo $svc['icon'] === 'tow-truck' ? mis360_tow_symbol() : esc_html($svc['icon']); ?></div>
                    <h3 class="lst-style-name"><?php echo esc_html($svc['name']); ?></h3>
                    <span class="lst-style-count"><?php echo esc_html($svc['count']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- 2. Plakaya Göre İl Firmaları (81 İl Rehberi) -->
        <div class="lst-brands-wrap" id="sehirler">
            <h3 class="lst-brands-title">İlinizdeki Çekici Firmalarını İncelemek İçin Plakanıza Tıklayınız</h3>
            <div class="lst-brands-grid">
                <?php foreach ($cities as $city) : ?>
                    <a href="#firmalar" class="lst-brand-card" title="<?php echo esc_attr($city['name']); ?> Çekici Firmaları">
                        <span style="background: #FA5343; color: #fff; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-right: 6px;"><?php echo esc_html($city['plate']); ?></span>
                        <span class="lst-brand-name"><?php echo esc_html($city['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
