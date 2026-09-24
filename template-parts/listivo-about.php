<?php
/**
 * Listivo Demo 5 - Yol Yardım Merkezi Hakkımızda Bölümü
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$email = yym_get_email();
?>

<section class="lst-section lst-about-section" id="hakkimizda">
    <div class="lst-container lst-about-grid">
        <!-- Sol: Görsel ve Süzülen İstatistik Rozeti -->
        <div class="lst-about-media">
            <div class="lst-about-img-wrap">
                <img src="https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_2.jpg" alt="Yol Yardım Merkezi 81 İl Ağı" class="lst-about-img" loading="lazy">
                
                <div class="lst-floating-stat-card">
                    <span class="lst-stat-number">81 İl 922 İlçe</span>
                    <span class="lst-stat-label">Türkiye Geneli Nöbetçi Ağı</span>
                </div>
            </div>
        </div>

        <!-- Sağ: Açıklama, Güvenceler ve Buton -->
        <div class="lst-about-content">
            <span class="lst-pill-sub">Hakkımızda</span>
            <h2 class="lst-about-title">Türkiye'nin En Kapsamlı 7/24 Yol Yardım Platformu</h2>
            <p class="lst-about-desc">
                Yol Yardım Merkezi; Türkiye genelinde 81 il ve 922 ilçede faaliyet gösteren yüzlerce sertifikalı oto çekici, oto kurtarıcı, mobil akü ve lastik yardım firmasını tek bir çatı altında toplayan güvenilir yardım rehberidir. Yolda kaldığınız her an size en yakın ekibi saniyeler içinde yönlendiriyoruz.
            </p>

            <ul class="lst-checklist">
                <li>
                    <span class="lst-check-icon">✓</span>
                    <span>Ortalama 15-30 dakikada en yakın nöbetçi kurtarıcı yanınızda</span>
                </li>
                <li>
                    <span class="lst-check-icon">✓</span>
                    <span>%100 Kaskolu ve emtia nakliyat sigortalı güvenli araç taşıma</span>
                </li>
                <li>
                    <span class="lst-check-icon">✓</span>
                    <span>Sürpriz ek masraf yok: Şeffaf ve sabit fiyat güvencesi</span>
                </li>
                <li>
                    <span class="lst-check-icon">✓</span>
                    <span>Tüm kurtarıcı araçlarımızda mobil POS ile kredi kartıyla ödeme</span>
                </li>
            </ul>

            <div class="lst-about-btn-wrap">
                <a href="mailto:<?php echo esc_attr($email); ?>" class="lst-btn lst-btn-coral">
                    <span>✉️ İletişime Geç: <?php echo esc_html($email); ?></span>
                </a>
            </div>
        </div>
    </div>
</section>
