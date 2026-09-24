<?php
/**
 * Yol Yardım Merkezi - 7. Firma Kayıt Alanı (B2B CTA Banner)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="yym-section yym-firm-cta-section" id="firma-ekle-alani">
    <div class="lst-container">
        <div class="yym-firm-cta-card">
            <!-- Arka Plan Dekoratif Işık Efekti -->
            <div class="yym-cta-glow-effect"></div>

            <div class="yym-firm-cta-inner">
                <!-- Sol: Metin ve Avantajlar -->
                <div class="yym-firm-cta-content">
                    <span class="yym-cta-mini-pill"><?php echo mis360_tow_symbol(); ?> ÇEKİCİ & YOL YARDIM ESNAFI İÇİN</span>
                    <h2 class="yym-firm-cta-title">
                        Firmanızı Türkiye'nin Yol Yardım Ağına Ekleyin
                    </h2>
                    <p class="yym-firm-cta-desc">
                        Bölgenizde arıza yapan veya yolda kalan binlerce araç sahibine doğrudan ulaşın, telefon ve WhatsApp çağrılarını anında alın.
                    </p>

                    <!-- Premium Üyelik Vurguları -->
                    <div class="yym-firm-benefits-list">
                        <div class="yym-benefit-chip">
                            <span class="yym-benefit-check">✓</span>
                            <span><strong>Daha Fazla Görünürlük:</strong> Firma bilgilerinizle rehberde yer alın.</span>
                        </div>
                        <div class="yym-benefit-chip">
                            <span class="yym-benefit-check">✓</span>
                            <span><strong>Firma Profiliniz:</strong> Hizmetlerinizi ve iletişim bilgilerinizi paylaşın.</span>
                        </div>
                        <div class="yym-benefit-chip">
                            <span class="yym-benefit-check">✓</span>
                            <span><strong>Doğrudan İletişim:</strong> Ziyaretçiler işletmenizle doğrudan görüşebilsin.</span>
                        </div>
                    </div>
                </div>

                <!-- Sağ: Buton ve Hızlı Başvuru Kartı -->
                <div class="yym-firm-cta-action-box">
                    <div class="yym-cta-action-card-inner">
                        <span class="yym-cta-card-icon">⚡</span>
                        <h3>Lansmana Özel Ücretsiz Başlayın</h3>
                        <p>Firma başvurunuzu oluşturun ve inceleme durumunu hesabınızdan takip edin.</p>
                        <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-btn-create-firm-listing">
                            <span>Firma Kaydı Oluştur</span>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="5" y1="12" x2="19" y2="12"/>
                                <polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </a>
                        <small>🛡️ Kredi kartı gerekmez • %100 Komisyonsuz</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
