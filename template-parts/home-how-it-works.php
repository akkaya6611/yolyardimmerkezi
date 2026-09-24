<?php
/**
 * Yol Yardım Merkezi — Nasıl Çalışır Bölümü (Timeline)
 * İkon sistemi: yym_icon() helper ile SVG, emoji yok.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="yym-section yym-how-it-works-section" id="nasil-calisir">
    <div class="lst-container">
        <!-- Bölüm Başlığı -->
        <div class="yym-section-header text-center">
            <span class="yym-section-tag">KOLAY VE HIZLI</span>
            <h2 class="yym-section-title">Nasıl Çalışır?</h2>
            <p class="yym-section-subtitle">
                Konumunuza ve ihtiyacınıza göre firma aramak için üç adımı izleyin.
            </p>
        </div>

        <!-- Timeline 3 Adım -->
        <div class="yym-timeline-container">
            <!-- Bağlantı Çizgisi -->
            <div class="yym-timeline-track"></div>

            <div class="yym-timeline-steps">
                <!-- Adım 1 -->
                <div class="yym-timeline-step-card">
                    <div class="yym-step-badge-wrap">
                        <span class="yym-step-number">1</span>
                        <div class="yym-step-icon-box">
                            <?php echo yym_icon('location-pin', 'location', 'yym-icon-lg yym-icon-white'); ?>
                        </div>
                    </div>
                    <div class="yym-step-body">
                        <h3 class="yym-step-title">Konumunuzu Seçin</h3>
                        <p class="yym-step-text">
                            Arama kutusundan il ve ilçe seçin. İhtiyacınız olan hizmet türüne göre sonuçları filtreleyin.
                        </p>
                    </div>
                </div>

                <!-- Adım 2 -->
                <div class="yym-timeline-step-card">
                    <div class="yym-step-badge-wrap">
                        <span class="yym-step-number">2</span>
                        <div class="yym-step-icon-box">
                            <?php echo yym_icon('search', 'ui', 'yym-icon-lg yym-icon-white'); ?>
                        </div>
                    </div>
                    <div class="yym-step-body">
                        <h3 class="yym-step-title">Size Yakın Firmaları Görüntüleyin</h3>
                        <p class="yym-step-text">
                            Bölgenizde listelenen firmaların açıklamalarını, iletişim bilgilerini ve mevcut değerlendirmelerini inceleyin.
                        </p>
                    </div>
                </div>

                <!-- Adım 3 -->
                <div class="yym-timeline-step-card">
                    <div class="yym-step-badge-wrap">
                        <span class="yym-step-number">3</span>
                        <div class="yym-step-icon-box">
                            <?php echo yym_icon('phone-call', 'location', 'yym-icon-lg yym-icon-white'); ?>
                        </div>
                    </div>
                    <div class="yym-step-body">
                        <h3 class="yym-step-title">Telefon veya WhatsApp'tan Ulaşın</h3>
                        <p class="yym-step-text">
                            Aracı çağrı merkezi olmadan doğrudan firmanın kendi numarasını arayın veya WhatsApp'tan tek dokunuşla GPS konumunuzu iletin.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
