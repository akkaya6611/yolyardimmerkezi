<?php
/**
 * Template Name: İletişim & Acil Çağrı
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$address = yym_get_address();
$email   = yym_get_email();

$success_msg = '';
$error_msg   = '';

// Form Gönderimini İşleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['yym_contact_nonce']) && wp_verify_nonce($_POST['yym_contact_nonce'], 'yym_contact_submit_action')) {
    $contact_name    = sanitize_text_field($_POST['contact_name'] ?? '');
    $contact_email   = sanitize_email($_POST['contact_email'] ?? '');
    $contact_phone   = sanitize_text_field($_POST['contact_phone'] ?? '');
    $contact_subject = sanitize_text_field($_POST['contact_subject'] ?? '');
    $related_firm    = sanitize_text_field($_POST['related_firm'] ?? '');
    $contact_message = sanitize_textarea_field($_POST['contact_message'] ?? '');

    if (empty($contact_name) || empty($contact_email) || empty($contact_message) || empty($contact_phone)) {
        $error_msg = __('Lütfen tüm zorunlu (*) alanları eksiksiz doldurunuz.', 'yol-yardim-merkezi');
    } else {
        $to = $email ?: get_option('admin_email');
        $subject_line = sprintf('[Yol Yardım Merkezi Destek] %s - %s', $contact_subject ?: 'Genel Talep', $contact_name);
        
        $body  = "Yol Yardım Merkezi platform iletişim formundan yeni bir bildirim alındı:\n\n";
        $body .= "Gönderen Adı / Firma: {$contact_name}\n";
        $body .= "Telefon: {$contact_phone}\n";
        $body .= "E-Posta: {$contact_email}\n";
        $body .= "Başvuru Konusu: {$contact_subject}\n";
        if (!empty($related_firm)) {
            $body .= "İlgili Firma / Bölge: {$related_firm}\n";
        }
        $body .= "\nMesaj İçeriği:\n" . $contact_message . "\n\n";
        $body .= "---\nBu bildirim yolyardimmerkezi.com.tr portal yönetim formundan gönderilmiştir.";

        $headers = array('Content-Type: text/plain; charset=UTF-8');
        if (!empty($contact_email)) {
            $headers[] = 'Reply-To: ' . $contact_name . ' <' . $contact_email . '>';
        }

        @wp_mail($to, $subject_line, $body, $headers);

        $success_msg = __('Mesajınız Yol Yardım Merkezi site yönetimine başarıyla iletildi. Talebiniz en kısa sürede incelenerek sizinle irtibata geçilecektir.', 'yol-yardim-merkezi');
    }
}

get_header();
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <nav class="yym-breadcrumbs" aria-label="Ekmek Kırıntısı">
                <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
                <span>›</span>
                <span class="active">İletişim & Platform Yönetimi</span>
            </nav>
            <span class="yym-hero-mini-badge">🏢 PLATFORM DESTEK & YÖNETİM</span>
            <h1 class="yym-page-hero-title"><?php _e('İletişim & Portal Yönetimi', 'yol-yardim-merkezi'); ?></h1>
            <p class="yym-page-hero-desc">
                <?php _e('Yol Yardım Merkezi rehber ağı; platform bilgileri, firma listeleme, veri doğruluğu ve kurumsal iş birlikleri için hizmet vermektedir.', 'yol-yardim-merkezi'); ?>
            </p>
        </div>
    </div>

    <div class="lst-container" style="padding-top: 45px; padding-bottom: 80px;">
        
        <!-- 🚨 ACİL ÇEKİCİ VE YOL YARDIM ARAYANLAR İÇİN KRİTİK BİLGİLENDİRME -->
        <div class="yym-notice-box-emergency">
            <div class="yym-notice-em-badge-col">
                <div class="yym-notice-em-pulse-icon">🚨</div>
            </div>
            <div class="yym-notice-em-text-col">
                <span class="yym-notice-em-pill">⚠️ ÖNEMLİ BİLGİLENDİRME: ACİL ÇEKİCİ ARAYAN SÜRÜCÜLERİN DİKKATİNE</span>
                <h3 class="yym-notice-em-title">Aracınız Yolda mı Kaldı? Lütfen Bu Formu Beklemeyiniz!</h3>
                <p class="yym-notice-em-desc">
                    <strong>Yol Yardım Merkezi bir oto çekici işletmesi veya kurtarıcı servisi değildir.</strong> 
                    Platformumuz, Türkiye'nin 81 ilindeki bağımsız, doğrulanmış ve lisanslı yol yardım esnafını sürücülerle buluşturan <strong>tarafsız bir arama ve rehber portalıdır</strong>.
                </p>
                <p class="yym-notice-em-subdesc">
                    Yolda kaldıysanız ve acil çekiciye ihtiyacınız varsa; aşağıdaki butonları kullanarak size en yakın firmayı bulunuz ve <strong>doğrudan firmanın kendi telefon numarası veya WhatsApp hattı ile iletişime geçiniz</strong>. Bu iletişim sayfası yalnızca web sitesi yönetimi, veri güncellemeleri ve firma başvuruları içindir.
                </p>
                <div class="yym-notice-em-actions">
                    <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-notice-btn primary">
                        <span>🔍 En Yakın Çekiciyi Bul (81 İl Listesi)</span>
                    </a>
                    <a href="<?php echo esc_url(home_url('/sehirler/')); ?>" class="yym-notice-btn secondary">
                        <span>📍 Şehir Seçerek Firma Ara</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2 KOLONLU İLETİŞİM ALANI -->
        <div class="yym-contact-grid">
            <!-- Sol: Platform Yönetim Bilgileri -->
            <div class="yym-contact-info-card">
                <span class="yym-card-tag">PORTAL MERKEZİ</span>
                <h2>Site Yönetimi & Bilgi</h2>
                <p class="yym-contact-card-sub">
                    Rehber sitemizdeki içerikler, firma kayıtları, şikayet bildirimleri ve kurumsal sponsorluk görüşmeleri için merkezimize ulaşabilirsiniz.
                </p>

                <div class="yym-contact-method-list">
                    <div class="yym-contact-method">
                        <span class="yym-c-icon">✉️</span>
                        <div>
                            <strong>Kurumsal E-Posta</strong>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="yym-c-link-large"><?php echo esc_html($email); ?></a>
                            <small>Firma başvuruları, reklam ve genel sorularınız için</small>
                        </div>
                    </div>

                    <div class="yym-contact-method">
                        <span class="yym-c-icon">📍</span>
                        <div>
                            <strong>Platform Yönetim Ofisi</strong>
                            <p class="yym-c-val"><?php echo esc_html($address); ?></p>
                            <small>Portal Altyapı & Veri Koordinasyonu</small>
                        </div>
                    </div>

                    <div class="yym-contact-method">
                        <span class="yym-c-icon">⏱️</span>
                        <div>
                            <strong>Destek & Yanıt Saatleri</strong>
                            <p class="yym-c-val">Hafta İçi: 09:00 - 18:00</p>
                            <small>Site destek ve firma onay birimi çalışma saatleri</small>
                        </div>
                    </div>
                </div>

                <!-- Çekici Esnafı İçin Yönlendirme Kutusu -->
                <div class="yym-owner-cta-box">
                    <div class="yym-owner-cta-head">
                        <span class="yym-owner-icon"><?php echo mis360_tow_symbol(); ?></span>
                        <div>
                            <h4>Çekici & Kurtarıcı Esnafı mısınız?</h4>
                            <p>Bölgenizdeki araç sahiplerine anında ulaşmak için firmanızı platformumuza ekleyin.</p>
                        </div>
                    </div>
                    <div class="yym-owner-cta-btns">
                        <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-btn-owner-cta primary">
                            + Firmanızı Ekleyin · Lansmana Özel Ücretsiz
                        </a>
                        <a href="<?php echo esc_url(home_url('/sss/')); ?>" class="yym-btn-owner-cta secondary">
                            🛡️ Doğrulama Rozetleri
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sağ: Platform Yönetimine İletişim Formu -->
            <div class="yym-contact-form-card">
                <span class="yym-card-tag">BİLGİ & BİLDİRİM FORMU</span>
                <h2>Site Yönetimine Mesaj İletin</h2>
                <p class="yym-contact-card-sub">
                    Firma kayıt talepleri, veri güncelleme, hatalı firma bildirme veya önerileriniz için bu formu kullanabilirsiniz.
                </p>

                <?php if (!empty($success_msg)) : ?>
                    <div class="yym-alert-success" style="margin-bottom: 24px; padding: 20px; border-radius: 14px; background: #ECFDF5; border: 1.5px solid #10B981; color: #065F46;">
                        <h4 style="margin: 0 0 6px; font-weight: 800; font-size: 16px;">🎉 Mesajınız İletildi!</h4>
                        <p style="margin: 0; font-size: 14px;"><?php echo esc_html($success_msg); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error_msg)) : ?>
                    <div class="yym-alert-error" style="margin-bottom: 24px; padding: 20px; border-radius: 14px; background: #FEF2F2; border: 1.5px solid #EF4444; color: #991B1B;">
                        <h4 style="margin: 0 0 6px; font-weight: 800; font-size: 16px;">⚠️ Lütfen Bilgilerinizi Kontrol Edin</h4>
                        <p style="margin: 0; font-size: 14px;"><?php echo esc_html($error_msg); ?></p>
                    </div>
                <?php endif; ?>

                <form method="post" action="" class="yym-platform-contact-form">
                    <?php wp_nonce_field('yym_contact_submit_action', 'yym_contact_nonce'); ?>
                    
                    <div class="yym-form-row">
                        <div class="yym-form-group">
                            <label for="contact_name">Adınız Soyadınız / Yetkili *</label>
                            <input type="text" name="contact_name" id="contact_name" class="yym-input" required placeholder="Örn: Ahmet Yılmaz" value="<?php echo isset($_POST['contact_name']) ? esc_attr($_POST['contact_name']) : ''; ?>">
                        </div>

                        <div class="yym-form-group">
                            <label for="contact_phone">Telefon Numaranız *</label>
                            <input type="tel" name="contact_phone" id="contact_phone" class="yym-input" required placeholder="05XX XXX XX XX" value="<?php echo isset($_POST['contact_phone']) ? esc_attr($_POST['contact_phone']) : ''; ?>">
                        </div>
                    </div>

                    <div class="yym-form-row">
                        <div class="yym-form-group">
                            <label for="contact_email">E-Posta Adresiniz *</label>
                            <input type="email" name="contact_email" id="contact_email" class="yym-input" required placeholder="ornek@alanadi.com" value="<?php echo isset($_POST['contact_email']) ? esc_attr($_POST['contact_email']) : ''; ?>">
                        </div>

                        <div class="yym-form-group">
                            <label for="contact_subject">Başvuru / Bildirim Konusu *</label>
                            <select name="contact_subject" id="contact_subject" class="yym-input" required>
                                <option value="">Konu Seçiniz...</option>
                                <option value="Firma Kaydı & Üyelik">Firma Kaydı & Üyelik İşlemleri</option>
                                <option value="Firma Bilgisi Güncelleme">Mevcut Firma Bilgisi Güncelleme / Düzeltme</option>
                                <option value="Hatalı / Kapanmış Firma Bildirimi">Kapanmış / Hatalı Firma Bildirimi</option>
                                <option value="Doğrulama & Güven Rozeti Talebi">Firma Doğrulama & Güven Rozeti</option>
                                <option value="Reklam & Kurumsal İş Birliği">Reklam, Sponsorluk & İş Birliği</option>
                                <option value="Öneri, Görüş veya Şikayet">Öneri, Görüş veya Şikayet</option>
                                <option value="Diğer Platform Soruları">Diğer Platform Soruları</option>
                            </select>
                        </div>
                    </div>

                    <div class="yym-form-group">
                        <label for="related_firm">İlgili Firma Adı veya Şehir (İsteğe Bağlı)</label>
                        <input type="text" name="related_firm" id="related_firm" class="yym-input" placeholder="Örn: Kayseri Melikgazi / Yıldız Oto Kurtarma" value="<?php echo isset($_POST['related_firm']) ? esc_attr($_POST['related_firm']) : ''; ?>">
                    </div>

                    <div class="yym-form-group">
                        <label for="contact_message">Mesajınız *</label>
                        <textarea name="contact_message" id="contact_message" rows="5" class="yym-input" required placeholder="Talebinizi, sorunuzu veya bildirmek istediğiniz detayları açıklayınız..."><?php echo isset($_POST['contact_message']) ? esc_textarea($_POST['contact_message']) : ''; ?></textarea>
                    </div>

                    <div class="yym-form-notice-box">
                        🛡️ <strong>Lütfen Dikkat:</strong> Bu form site yöneticilerine ulaşmaktadır. Yolda kaldıysanız ve acil çekiciye ihtiyacınız varsa lütfen <a href="<?php echo esc_url(home_url('/firmalar/')); ?>">Firma Rehberi</a> üzerinden firmaları doğrudan telefon ile arayınız.
                    </div>

                    <button type="submit" class="yym-btn-submit-contact">
                        ✉️ Mesajı Site Yönetimine Gönder
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
