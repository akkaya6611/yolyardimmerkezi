<?php

/**

 * Template Name: Firma Ekle

 *

 * @package Yol_Yardim_Merkezi

 */



if (!defined('ABSPATH')) {

    exit;

}



require_once get_template_directory() . '/inc/firm-photos.php';
require_once get_template_directory() . '/inc/firm-locations.php';
$success_msg = '';

$error_msg   = '';
$firm_plan = isset($_POST['firm_plan']) && is_string($_POST['firm_plan']) ? sanitize_key(wp_unslash($_POST['firm_plan'])) : 'free';



// Form Gönderildiğinde İşleme

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['yym_add_firm_nonce']) && wp_verify_nonce($_POST['yym_add_firm_nonce'], 'yym_add_firm_action')) {

    $firm_name = sanitize_text_field($_POST['firm_name'] ?? '');

    $contact_person = sanitize_text_field($_POST['contact_person'] ?? '');

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    $whatsapp = sanitize_text_field($_POST['whatsapp'] ?? '');

    $city = sanitize_text_field($_POST['city'] ?? '');

    $district = sanitize_text_field($_POST['district'] ?? '');

    $address = sanitize_text_field($_POST['address'] ?? '');

    $equipment = sanitize_text_field($_POST['equipment'] ?? '');

    $services = isset($_POST['services']) ? array_map('sanitize_text_field', (array)$_POST['services']) : array();

    $notes = sanitize_textarea_field($_POST['notes'] ?? '');



    // Only the free plan is currently open; never trust a posted paid-plan entitlement.
    $photo_files = mis360_firm_photo_bundle('free');
    if (is_wp_error($photo_files)) {
        $error_msg = $photo_files->get_error_message();
    } elseif ($firm_plan !== 'free') {
        $error_msg = 'Lansman döneminde yalnızca Lansman Paketi seçilebilir.';
    } elseif (!mis360_registration_location_valid($city, $district)) {
        $error_msg = 'Lütfen listeden il ve o ile bağlı bir ilçe seçin.';
    } elseif (empty($firm_name) || empty($phone) || empty($city)) {

        $error_msg = __('Lütfen Firma Adı, Telefon Numarası ve Şehir alanlarını eksiksiz doldurunuz.', 'yol-yardim-merkezi');

    } else {

        // WordPress'e yeni firma taslağı olarak ekle

        $new_firm_id = wp_insert_post(array(

            'post_title'   => $firm_name,

            'post_content' => $notes,

            'post_status'  => 'pending', // Yönetici onayı bekleyen durum

            'post_type'    => 'firma',

            'post_author'  => get_current_user_id(),

        ));



        if ($new_firm_id && !is_wp_error($new_firm_id)) {

            update_post_meta($new_firm_id, '_firma_requested_plan', $firm_plan);
            update_post_meta($new_firm_id, '_firma_phone', $phone);

            update_post_meta($new_firm_id, '_firma_whatsapp', $whatsapp ?: $phone);

            update_post_meta($new_firm_id, '_firma_city', $city);

            update_post_meta($new_firm_id, '_firma_district', $district);

            update_post_meta($new_firm_id, '_firma_address', $address);

            update_post_meta($new_firm_id, '_firma_equipment', $equipment);

            update_post_meta($new_firm_id, '_firma_contact_person', $contact_person);



            $photo_result = mis360_save_firm_photos($new_firm_id, $photo_files);
            if (is_wp_error($photo_result)) {
                wp_delete_post($new_firm_id, true);
                $error_msg = $photo_result->get_error_message();
            } else {
            // Yöneticiye E-Posta Bildirimi

            $admin_email = yym_get_email();

            $email_subject = sprintf('[YENİ FİRMA KAYDI] %s - %s', $firm_name, $city);

            $email_body = "Yeni bir yol yardım firması kayıt başvurusu yaptı:\n\n"

                        . "Firma Adı: {$firm_name}\n"

                        . "Paket tercihi: " . mis360_firm_plans()[$firm_plan] . "\n"
                        . "Yetkili: {$contact_person}\n"

                        . "Telefon: {$phone}\n"

                        . "WhatsApp: {$whatsapp}\n"

                        . "Şehir / İlçe: {$city} / {$district}\n"

                        . "Açık Adres: {$address}\n"

                        . "Donanım: {$equipment}\n"

                        . "Hizmetler: " . implode(', ', $services) . "\n\n"

                        . "Bu firmayı WordPress panelinizden onaylayabilirsiniz: " . admin_url('post.php?post=' . $new_firm_id . '&action=edit');



            @wp_mail($admin_email, $email_subject, $email_body);



            wp_safe_redirect(add_query_arg('submitted', '1', home_url('/uyelik/')));

            exit;
            }

        } else {

            $error_msg = __('Bir hata oluştu, lütfen daha sonra tekrar deneyiniz.', 'yol-yardim-merkezi');

        }

    }

}



get_header();

?>



<div class="yym-inner-page-wrap mis360-premium-form">

    <!-- Hero Bölümü -->

    <div class="yym-page-hero-section">

        <div class="lst-container text-center">

            <span class="yym-hero-mini-badge">YOL YARDIM MERKEZİ / İŞLETME KAYDI</span>

            <h1 class="yym-page-hero-title">İşletmenize yeni bir<br><em>yol açın.</em></h1>

            <p class="yym-page-hero-desc">

                Yol Yardım Merkezi rehberine katılın; işletme bilgilerinizi ve iletişim kanallarınızı müşterilerinizle paylaşın.

            </p>

        </div>

    </div>



    <div class="lst-container mis360-application-shell">

        <div class="yym-add-firm-container">

            <?php if ($success_msg) : ?>

                <div class="yym-alert-success">

                    <h3>🎉 Başvurunuz Alındı!</h3>

                    <p><?php echo esc_html($success_msg); ?></p>

                    <a href="<?php echo esc_url(home_url('/')); ?>" class="lst-btn lst-btn-coral" style="margin-top: 15px; display: inline-block;">Ana Sayfaya Dön</a>

                </div>

            <?php else : ?>

                <?php if ($error_msg) : ?>

                    <div class="yym-alert-danger">

                        <p>⚠️ <?php echo esc_html($error_msg); ?></p>

                    </div>

                <?php endif; ?>



                <div class="yym-add-firm-grid">

                    <!-- Sol: Form -->

                    <div class="yym-form-card">

                        <h2 class="yym-form-card-title">Firmanızı birlikte hazırlayalım.</h2>

                        <p style="color: #64748B; font-size: 0.9rem; margin-bottom: 24px;">Önce paketinizi seçin, ardından firma bilgilerinizi doldurun.</p>



                        <form method="post" enctype="multipart/form-data" action="" data-firm-wizard data-start-step="<?php echo $error_msg && $firm_plan === 'free' ? '2' : '1'; ?>">
<ol class="mis360-wizard-steps" aria-label="Başvuru adımları"><li data-step-label="1"><span>01</span> Paket seçimi</li><li data-step-label="2"><span>02</span> Firma bilgileri</li></ol>
<noscript><p>Başvuru adımlarını kullanmak için tarayıcınızda JavaScript'i açın.</p></noscript>

                            <?php wp_nonce_field('yym_add_firm_action', 'yym_add_firm_nonce'); ?>
                            <section data-plan-step>
<?php mis360_plan_fields($firm_plan); ?>
<div class="mis360-next-bar"><span><strong>Lansman Paketi</strong><small>Lansmana özel ücretsiz. Ücretli planlar yakında.</small></span><button type="button" class="lst-btn lst-btn-coral" data-plan-next>İleri →</button></div>
</section>
<fieldset data-details-step hidden disabled class="mis360-details-step"><legend tabindex="-1">Firma Bilgileri</legend>
<div class="mis360-wizard-selected">Seçilen paket: <strong>Lansman Paketi</strong> <button type="button" data-plan-back>← Paketi değiştir</button></div>



                            <div class="mis360-form-section"><span>01</span><div><h3>İşletmenizi tanıtalım</h3><p>Müşterilerinizin sizi tanıyacağı temel bilgiler.</p></div></div>
<div class="yym-form-group">

                                <label for="firm_name">Firma / Ticari Ünvan *</label>

                                <input type="text" name="firm_name" id="firm_name" required placeholder="Örn: Kayseri 7/24 Özpolat Oto Çekici" class="yym-input">

                            </div>



                            <div class="yym-form-row">

                                <div class="yym-form-group">

                                    <label for="contact_person">Yetkili Adı Soyadı</label>

                                    <input type="text" name="contact_person" id="contact_person" placeholder="Adınız Soyadınız" class="yym-input">

                                </div>

                                <div class="yym-form-group">

                                    <label for="phone">Doğrudan Arama Telefonu *</label>

                                    <input type="tel" name="phone" id="phone" required placeholder="0542 000 00 00" class="yym-input">

                                </div>

                            </div>



                            <div class="yym-form-row">

                                <div class="yym-form-group">

                                    <label for="whatsapp">WhatsApp Hattı (Boşluksuz)</label>

                                    <input type="text" name="whatsapp" id="whatsapp" placeholder="905420000000" class="yym-input">

                                    <small>Sürücüler tek tıkla konum atarken bu numarayı kullanır.</small>

                                </div>

                                <div class="yym-form-group">

                                    <label for="equipment">Kasa / Donanım Türü</label>

                                    <input type="text" name="equipment" id="equipment" placeholder="Örn: Kayar Kasa, Ahtapot Vinç" class="yym-input">

                                </div>

                            </div>



                            <div class="mis360-form-section"><span>02</span><div><h3>Konum ve hizmetler</h3><p>Hizmet verdiğiniz bölgeyi ve uzmanlıklarınızı belirtin.</p></div></div>
<div class="yym-form-row">

                                <div class="yym-form-group">

                                    <label for="city">Hizmet Verdiğiniz İl *</label>

                                    <?php mis360_registration_location_select('city'); ?>

                                </div>

                                <div class="yym-form-group">

                                    <label for="district">İlçe *</label>

                                    <?php mis360_registration_location_select('district'); ?>

                                </div>

                            </div>



                            <div class="yym-form-group">

                                <label for="address">Açık Adres / İstasyon Noktası</label>

                                <input type="text" name="address" id="address" placeholder="Sanayi Mah. 12. Cad. No:8..." class="yym-input">

                            </div>



                            <div class="yym-form-group">

                                <label>Sunduğunuz Hizmetler:</label>

                                <div class="yym-checkbox-grid">

                                    <label><input type="checkbox" name="services[]" value="cekici" checked> Oto Çekici & Kurtarıcı</label>

                                    <label><input type="checkbox" name="services[]" value="aku"> Akü Takviye & Satış</label>

                                    <label><input type="checkbox" name="services[]" value="lastik"> Mobil Lastik Tamiri</label>

                                    <label><input type="checkbox" name="services[]" value="motosiklet"> Motosiklet Taşıma</label>

                                    <label><input type="checkbox" name="services[]" value="agir-vasita"> Ağır Vasıta & Vinç</label>

                                    <label><input type="checkbox" name="services[]" value="transfer"> Şehirlerarası Transfer</label>

                                </div>

                            </div>



                            <div class="mis360-form-section"><span>03</span><div><h3>İşletmenizin vitrini</h3><p>Fotoğraflar ve kısa bir açıklamayla profilinizi tamamlayın.</p></div></div>
<div class="yym-form-group">
                                <label for="firm_profile" class="mis360-upload-zone mis360-profile-upload"><span class="mis360-upload-icon" aria-hidden="true">＋</span><strong>Profil fotoğrafınızı seçin *</strong><span>Logonuz veya işletmenizi temsil eden bir fotoğraf.</span><b>Profil fotoğrafı seç</b></label>
                                <input type="file" id="firm_profile" name="firm_profile[]" accept="image/jpeg,image/png,image/webp" required class="mis360-photo-input" aria-describedby="firm-profile-help">
                                <small id="firm-profile-help">Bir profil fotoğrafı seçin. JPG, PNG veya WebP, en fazla 5 MB. Firma kartında ve profilinizde görünür. Vergi levhanızı buraya yüklemeyin.</small>
                                <div class="mis360-photo-previews mis360-profile-preview" aria-live="polite"></div>
                                <section class="mis360-slider-upload" aria-labelledby="firm-slider-title">
                                    <h4 id="firm-slider-title">Firma Sliderı <span>Ücretli paket özelliği</span></h4>
                                    <p>Firma detay sayfanızda işletme ve hizmet fotoğraflarınızı kayan galeri olarak gösterin.</p>
                                    <div class="mis360-slider-tiers"><span>Lansman <strong>Slider yok</strong></span><span>Profesyonel <strong>10 fotoğraf</strong></span><span>Premium <strong>30 fotoğraf</strong></span></div>
                                    <label for="firm_slider">Slider fotoğrafları</label>
                                    <input type="file" id="firm_slider" name="firm_slider[]" accept="image/jpeg,image/png,image/webp" multiple disabled class="mis360-photo-input" data-slider-limit="0" aria-describedby="firm-slider-help">
                                    <input type="hidden" name="slider_file_count" value="0" id="slider_file_count">
                                    <small id="firm-slider-help">Lansmana özel ücretsiz paketinizde slider bulunmuyor. Profesyonel ve Premium paketler satışa açıldığında kullanılabilir. Profil fotoğrafı slider kotasından ayrıdır.</small>
                                    <div class="mis360-photo-previews mis360-slider-preview" aria-live="polite"></div>
                                </section>
                            </div>
                            <div class="yym-form-group">

                                <label for="notes">İşletmeniz Hakkında</label>

                                <textarea name="notes" id="notes" rows="4" placeholder="Sunduğunuz hizmetleri ve işletmenizi anlatan kısa bir açıklama yazın..." class="yym-input"></textarea>

                            </div>



                            <button type="submit" class="lst-btn lst-btn-coral" style="width: 100%; padding: 16px; font-size: 1.05rem; font-weight: 800; border-radius: 10px;">

                                Başvuruyu Gönder

                            </button>

                        </fieldset>
                        </form>

                    </div>



<aside class="yym-perks-sidebar mis360-application-summary">
<div class="mis360-summary-card"><span class="mis360-summary-eyebrow">BAŞVURU ÖZETİ</span><div class="mis360-summary-avatar" aria-hidden="true">Y</div><h3 data-preview-name>İşletmenizin adı</h3><p data-preview-location>İl / İlçe</p><div class="mis360-summary-plan"><span>Seçilen paket</span><strong>Lansmana özel ücretsiz</strong></div><ul><li><span>01</span><div><strong>Bilgilerinizi ekleyin</strong><small>İletişim, konum ve fotoğraflar.</small></div></li><li><span>02</span><div><strong>İncelemeye gönderin</strong><small>Başvurunuz yöneticiye ulaşır.</small></div></li><li><span>03</span><div><strong>Hesabınızdan takip edin</strong><small>Onay durumunu görüntüleyin.</small></div></li></ul><p class="mis360-summary-note">Başvurunuz incelendikten sonra yayına alınır.</p></div>
<div class="mis360-summary-help"><strong>Bir sorunuz mu var?</strong><a href="mailto:info@yolyardimmerkezi.com.tr">Bize ulaşın ↗</a></div>
</aside>
                </div>
            <?php endif; ?>

        </div>

    </div>

</div>



<?php

get_footer();

