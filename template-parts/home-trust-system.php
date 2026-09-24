<?php
/**
 * Yol Yardım Merkezi — Güven & Doğrulama Sistemi Bölümü
 * İkon sistemi: yym_icon() helper ile SVG, emoji yok.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="yym-section yym-trust-system-section" id="guven-sistemi">
    <div class="lst-container">
        <!-- Bölüm Başlığı -->
        <div class="text-center" style="max-width: 780px; margin: 0 auto 50px;">
            <span class="yym-section-tag">FİRMA REHBERİNİ KULLANIRKEN</span>
            <h2 class="yym-section-title">Hizmet Almadan Önce Bilgileri Kontrol Edin</h2>
            <p class="yym-section-subtitle">
                Rehberdeki bir kayıt, işletmenin tüm belgelerinin veya hizmet koşullarının doğrulandığı anlamına gelmez. Karar vermeden önce firmayla görüşün.
            </p>
        </div>

        <!-- 4 Aşamalı Güvenlik Kartları -->
        <div class="yym-trust-system-grid">
            <!-- 1. Aşama -->
            <div class="yym-trust-step-card">
                <div class="yym-tstep-num">01</div>
                <div class="yym-tstep-icon-wrap">
                    <?php echo yym_icon('document-check', 'trust'); ?>
                </div>
                <h3 class="yym-tstep-title">İşletme Bilgileri</h3>
                <p class="yym-tstep-desc">
                    Firma adı, adresi ve iletişim bilgilerini inceleyin. Firma sahipleri, belge incelemesi sonrasında kayıtlarını yönetmek için başvurabilir.
                </p>
                <div class="yym-tstep-badge">
                    <?php echo yym_icon('check', 'ui', 'yym-icon-sm'); ?>
                    Firma profilini inceleyin
                </div>
            </div>

            <!-- 2. Aşama -->
            <div class="yym-trust-step-card">
                <div class="yym-tstep-num">02</div>
                <div class="yym-tstep-icon-wrap">
                    <?php echo yym_icon('verified-badge', 'trust'); ?>
                </div>
                <h3 class="yym-tstep-title">Hizmet Kapsamı</h3>
                <p class="yym-tstep-desc">
                    Araç türünüzü ve ihtiyaç duyduğunuz hizmeti açıklayın. Gerekli ekipmanın ve hizmetin işletmede mevcut olduğunu doğrudan teyit edin.
                </p>
                <div class="yym-tstep-badge">
                    <?php echo yym_icon('check', 'ui', 'yym-icon-sm'); ?>
                    Uygun hizmeti teyit edin
                </div>
            </div>

            <!-- 3. Aşama -->
            <div class="yym-trust-step-card">
                <div class="yym-tstep-num">03</div>
                <div class="yym-tstep-icon-wrap">
                    <?php echo yym_icon('shield-check', 'trust'); ?>
                </div>
                <h3 class="yym-tstep-title">Taşıma Koşulları</h3>
                <p class="yym-tstep-desc">
                    Taşıma yöntemi, sigorta kapsamı ve işletmenin sunduğu koşullar hakkında hizmet öncesinde bilgi isteyin. Rehber, taşıma veya sigorta garantisi vermez.
                </p>
                <div class="yym-tstep-badge">
                    <?php echo yym_icon('check', 'ui', 'yym-icon-sm'); ?>
                    Koşulları firmaya sorun
                </div>
            </div>

            <!-- 4. Aşama -->
            <div class="yym-trust-step-card">
                <div class="yym-tstep-num">04</div>
                <div class="yym-tstep-icon-wrap">
                    <?php echo yym_icon('handshake', 'trust'); ?>
                </div>
                <h3 class="yym-tstep-title">Ücret ve Varış Süresi</h3>
                <p class="yym-tstep-desc">
                    Toplam ücreti, olası ek masrafları ve tahmini varış süresini işletmeyle görüşün. Müsaitlik ve ulaşım süresi konuma ve koşullara göre değişir.
                </p>
                <div class="yym-tstep-badge">
                    <?php echo yym_icon('check', 'ui', 'yym-icon-sm'); ?>
                    Hizmet öncesinde netleştirin
                </div>
            </div>
        </div>

        <!-- Güven Sistemi Alt Bilgilendirme Şeridi -->
        <div class="yym-trust-system-footer-bar">
            <div class="yym-ts-footer-text">
                <span class="yym-ts-f-icon">
                    <?php echo yym_icon('lock', 'trust', 'yym-icon-lg'); ?>
                </span>
                <div>
                    <strong>Firmayla Doğrudan Görüşün</strong>
                    <p>İletişim bilgileri üzerinden işletmeye ulaşın. Güncel olmayan bir bilgi gördüğünüzde bize bildirin.</p>
                </div>
            </div>
            <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-btn-join-network">
                <span>Kurtarıcı Mısınız? Ağa Katılın</span>
                <?php echo yym_icon('arrow-right', 'ui', 'yym-icon-md'); ?>
            </a>
        </div>
    </div>
</section>
