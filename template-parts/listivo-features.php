<?php
/**
 * Listivo Demo 5 - 6'lı Yol Yardım Rehberi & Güven Kartları
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$features = array(
    array(
        'icon'  => '⚡',
        'title' => '15-30 Dk Varış Süresi',
        'desc'  => 'GPS tabanlı akıllı yönlendirme ile bulunduğunuz konuma en yakın nöbetçi çekici derhal yola çıkar.'
    ),
    array(
        'icon'  => '🛡️',
        'title' => '%100 Kaskolu Taşıma',
        'desc'  => 'Aracınız kurtarıcıya yüklendiği andan varış noktasına kadar resmi emtia sigortası güvencesi altındadır.'
    ),
    array(
        'icon'  => '👷',
        'title' => 'Sertifikalı Operatörler',
        'desc'  => 'Yılların tecrübesine sahip, eğitimli ve tam donanımlı profesyonel yol yardım ve çekici ekipleri.'
    ),
    array(
        'icon'  => '💰',
        'title' => 'Sabit Fiyat Güvencesi',
        'desc'  => 'Telefonda anlaşılan net ücret geçerlidir. Olay yerine varıldığında sürpriz ek ücret asla talep edilmez.'
    ),
    array(
        'icon'  => '🕒',
        'title' => '7/24 Kesintisiz Destek',
        'desc'  => 'Gece, gündüz, hafta sonu veya bayram günleri fark etmeksizin 365 gün kesintisiz canlı çağrı merkezi.'
    ),
    array(
        'icon'  => '🔧',
        'title' => 'Yerinde Hızlı Müdahale',
        'desc'  => 'Akü bitmesi veya lastik patlaması durumunda çekiciye gerek kalmadan mobil ekiplerimizle yerinde çözüm.'
    ),
);
?>

<section class="lst-section lst-features-section" id="neden-biz">
    <div class="lst-container">
        <div class="lst-section-heading-bar lst-text-center">
            <span class="lst-pill-sub">Neden Yol Yardım Merkezi?</span>
            <h2 class="lst-section-title">Yolda Kaldığınızda İhtiyacınız Olan<br>Tüm Güvenceler Yanınızda</h2>
        </div>

        <div class="lst-features-grid">
            <?php foreach ($features as $f) : ?>
                <div class="lst-feature-card">
                    <div class="lst-feature-icon-box">
                        <span class="lst-feature-icon"><?php echo esc_html($f['icon']); ?></span>
                    </div>
                    <h3 class="lst-feature-title"><?php echo esc_html($f['title']); ?></h3>
                    <p class="lst-feature-desc"><?php echo esc_html($f['desc']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
