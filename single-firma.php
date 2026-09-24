<?php

/**

 * Yol Yardım Merkezi - Tekil Firma Profili (single-firma.php)

 * Facebook profili konseptinde: Yuvarlak profil resmi ve altında araç/filo slider galerisi.

 *

 * @package Yol_Yardim_Merkezi

 */



if (!defined('ABSPATH')) {

    exit;

}



get_header();



while (have_posts()) : the_post();

    $post_id   = get_the_ID();

    $phone     = get_post_meta($post_id, '_firma_phone', true);

    $whatsapp  = get_post_meta($post_id, '_firma_whatsapp', true);

    $address   = get_post_meta($post_id, '_firma_address', true);

    $city      = get_post_meta($post_id, '_firma_city', true);

    $district  = get_post_meta($post_id, '_firma_district', true);

    $eta       = get_post_meta($post_id, '_firma_eta', true);

    $equipment = get_post_meta($post_id, '_firma_equipment', true);

    $price     = get_post_meta($post_id, '_firma_price', true);

    $badge     = get_post_meta($post_id, '_firma_badge', true);

    $rating_raw = str_replace(',', '.', trim((string) get_post_meta($post_id, '_firma_rating', true)));

    $rating = is_numeric($rating_raw) && (float)$rating_raw > 0 && (float)$rating_raw <= 5 ? $rating_raw : '';

    $rating_count_raw = get_post_meta($post_id, '_firma_review_count', true);

    $rating_cnt = is_numeric($rating_count_raw) && (int)$rating_count_raw >= 0 ? (int)$rating_count_raw : null;



    $categories = wp_get_post_terms($post_id, 'firma_kategori', array('fields'=>'names'));

    $category_label = !is_wp_error($categories) && $categories ? implode(', ', $categories) : get_post_meta($post_id, '_firma_category', true);

    if (!is_wp_error($categories) && $categories) $category_label = implode(', ', mis360_category_display_names($categories));
    $location_label = implode(' / ', array_filter(array($city, $district)));

    $is_station = has_term('sarj-istasyonu', 'firma_kategori', $post_id);

    // WhatsApp formatı

    $wa_clean = preg_replace('/[^0-9]/', '', $whatsapp ?: (preg_match('/^(?:0|90|\+90)?5[0-9]{9}$/', preg_replace('/[^0-9+]/', '', $phone)) ? $phone : ''));

    if (strlen($wa_clean) == 11 && substr($wa_clean, 0, 1) == '0') {

        $wa_clean = '90' . substr($wa_clean, 1);

    } elseif (strlen($wa_clean) == 10) {

        $wa_clean = '90' . $wa_clean;

    }



    $phone_clean = preg_replace('/[^0-9]/', '', $phone);

    if (substr($phone_clean, 0, 1) === '0') {

        $phone_raw = '+90' . substr($phone_clean, 1);

    } else {

        $phone_raw = '+' . $phone_clean;

    }



    // Google Maps Rota Linki

    $maps_query = $address ? ($address . ' ' . $city) : (get_the_title() . ' ' . $city);

    $maps_url   = 'https://www.google.com/maps/dir/?api=1&destination=' . urlencode($maps_query);



    // Akıllı WhatsApp Mesajı

    $smart_wp_text = urlencode(sprintf('Merhaba %s, Yol Yardım Merkezi üzerinden size ulaşıyorum. İşletmeniz hakkında bilgi almak istiyorum.', get_the_title()));

    $wa_url        = 'https://wa.me/' . $wa_clean . '?text=' . $smart_wp_text;



    // Firma Profil Görseli (Yuvarlak Avatar)

    $profile_avatar_meta = get_post_meta($post_id, '_firma_image_url', true);

    $profile_avatar_url  = has_post_thumbnail($post_id) 

        ? get_the_post_thumbnail_url($post_id, 'medium_large') 

        : (!empty($profile_avatar_meta) ? $profile_avatar_meta : '');



    $separate_photos = get_post_meta($post_id, '_firma_photo_layout', true) === 'separate';
    $gallery_photos = !$separate_photos && $profile_avatar_url ? array(array('url'=>$profile_avatar_url, 'caption'=>get_the_title(), 'tag'=>'İşletme fotoğrafı')) : array();
    foreach ((array)get_post_meta($post_id, '_firma_gallery_ids', true) as $photo_id) {
        if ((int)$photo_id === (int)get_post_thumbnail_id($post_id)) continue;
        if ((int)wp_get_post_parent_id($photo_id) !== (int)$post_id || !wp_attachment_is_image($photo_id)) continue;
        $photo_url = wp_get_attachment_image_url($photo_id, 'large');
        if ($photo_url) $gallery_photos[] = array('url'=>$photo_url,'caption'=>get_the_title(),'tag'=>'İşletme fotoğrafı');
    }




?>



<article id="post-<?php the_ID(); ?>" <?php post_class('yym-single-firma-article yym-fb-profile-layout'); ?> data-metric-profile <?php echo mis360_metrics_attributes($post_id); ?>>

    <!-- 1. FACEBOOK STİLİ KAPAK FOTOĞRAFI (COVER BANNER) -->

    <div class="yym-fb-cover-wrap">

        <div class="yym-fb-cover-backdrop"></div>

        <div class="lst-container yym-fb-cover-inner">

            <nav class="yym-breadcrumbs yym-fb-breadcrumbs" aria-label="Ekmek Kırıntısı">

                <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>

                <span>›</span>

                <a href="<?php echo esc_url(home_url('/firmalar/')); ?>">Firmalar</a>

                <?php if ($city) : ?>

                    <span>›</span>

                    <span><?php echo esc_html($city); ?></span>

                <?php endif; ?>

                <span>›</span>

                <span class="active"><?php the_title(); ?></span>

            </nav>

        </div>

    </div>



    <!-- 2. FACEBOOK STİLİ YUVARLAK PROFİL ALANI (AVATAR & BAŞLIK BİLGİLERİ) -->

    <div class="yym-fb-profile-header-wrap">

        <div class="lst-container">

            <div class="yym-fb-profile-card">

                <!-- Yuvarlak Profil Resmi -->

                <div class="yym-fb-avatar-container">

                    <div class="yym-fb-avatar-circle">

                        <?php if ($profile_avatar_url) : ?><img src="<?php echo esc_url($profile_avatar_url); ?>" alt="<?php the_title_attribute(); ?>" class="yym-fb-avatar-img"><?php else : ?><span style="font-size:48px;color:#0b1f3a" aria-hidden="true"><?php echo esc_html(mb_substr(get_the_title(),0,1)); ?></span><?php endif; ?>

                    </div>

                </div>



                <!-- Profil İsim ve Bilgileri -->

                <div class="yym-fb-info-col">

                    <div class="yym-fb-badges-row"><?php if ($category_label) : ?><span class="yym-badge-pill yym-badge-blue"><?php echo esc_html($category_label); ?></span><?php endif; ?></div>



                    <h1 class="yym-fb-firm-title"><?php the_title(); ?></h1>



                    <div class="yym-fb-meta-row">

                        <span class="yym-fb-meta-item">

                            <?php echo yym_icon('location-pin', 'location', 'yym-icon-sm'); ?>

                            <strong><?php echo esc_html($location_label ?: 'Konum belirtilmemiş'); ?></strong>

                        </span>

                        <?php if ($rating !== '') : ?>

                        <span class="yym-fb-meta-item">

                            <?php echo yym_icon('star-rating', 'trust', 'yym-icon-sm yym-icon-orange'); ?>

                            <strong><?php echo esc_html($rating); ?></strong> / 5<?php if ($rating_cnt !== null) : ?> (<?php echo esc_html($rating_cnt); ?> kayıtlı değerlendirme)<?php endif; ?>

                        </span>

                        <?php endif; ?>

                    </div>

                </div>



                <!-- Sağ: Hızlı Eylem Butonları -->

                <div class="yym-fb-cta-col">

                    <?php if ($phone) : ?>

                        <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="yym-btn-fb-call">

                            <?php echo yym_icon('phone-call', 'location', 'yym-icon-md'); ?>

                            <strong>Hemen Ara</strong>

                        </a>

                    <?php endif; ?>

                    <?php if ($wa_clean) : ?>

                        <a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn-fb-whatsapp">

                            <?php echo yym_icon('whatsapp', 'ui', 'yym-icon-md'); ?>

                            <strong>WhatsApp</strong>

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>



    <!-- 3. PROFİLİN ALTINDAKİ ARAÇ VE FİLO SLİDER'I (FACEBOOK SLIDER GÖRSELLERİ) -->

    <?php if ($gallery_photos) : ?>

    <div class="lst-container yym-fb-slider-outer">

        <div class="yym-fb-slider-card" id="yymFirmaSlider">

            <div class="yym-fb-slider-header">

                <div class="yym-slider-h-left">

                    <span class="yym-slider-icon"><?php echo yym_icon('camera', 'ui', 'yym-icon-lg'); ?></span>

                    <h3>İşletme Fotoğrafları</h3>

                    <span class="yym-slider-count-chip"><?php echo count($gallery_photos); ?> Fotoğraf</span>

                </div>

                <div class="yym-slider-nav-arrows">

                    <button type="button" class="yym-slider-btn yym-slider-prev" aria-label="Önceki Fotoğraf">‹</button>

                    <button type="button" class="yym-slider-btn yym-slider-next" aria-label="Sonraki Fotoğraf">›</button>

                </div>

            </div>



            <!-- Slider Ana Çerçeve -->

            <div class="yym-slider-viewport">

                <div class="yym-slider-track">

                    <?php foreach ($gallery_photos as $idx => $photo) : ?>

                        <div class="yym-slider-slide <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo esc_attr($idx); ?>">

                            <div class="yym-slide-img-box">

                                <img src="<?php echo esc_url($photo['url']); ?>" alt="<?php echo esc_attr($photo['caption']); ?>" class="yym-slide-img" loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">

                                <div class="yym-slide-caption-bar">

                                    <span class="yym-slide-tag"><?php echo esc_html($photo['tag']); ?></span>

                                    <h4 class="yym-slide-title"><?php echo esc_html($photo['caption']); ?></h4>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>



            <!-- Küçük Önizleme Resimleri (Thumbnail Strip) -->

            <div class="yym-slider-thumbnails-strip">

                <?php foreach ($gallery_photos as $idx => $photo) : ?>

                    <button type="button" class="yym-slider-thumb <?php echo $idx === 0 ? 'active' : ''; ?>" data-target="<?php echo esc_attr($idx); ?>" aria-label="Fotoğraf <?php echo $idx + 1; ?>">

                        <img src="<?php echo esc_url($photo['url']); ?>" alt="<?php echo esc_attr($photo['caption']); ?>">

                    </button>

                <?php endforeach; ?>

            </div>

        </div>

    </div>



    <?php endif; ?>

    <!-- 4. ANA İÇERİK VE İLETİŞİM ALANI -->

    <div class="lst-container yym-firma-body-wrap" style="padding-top: 30px;">

        <div class="yym-firma-layout-grid">

            <!-- SOL ANA KOLON -->

            <div class="yym-firma-main-col">

                <!-- Mobil / Masaüstü Hızlı Eylem Barı -->

                <div class="yym-firma-fast-actions-card">

                    <h3 class="yym-action-card-title">

                        <?php echo yym_icon('phone-call', 'location', 'yym-icon-md yym-icon-orange'); ?>

                        Doğrudan Firmayla İletişime Geçin

                    </h3>

                    <p class="yym-action-card-sub">Güncel hizmet bilgileri ve uygunluk için işletmeyle iletişime geçebilirsiniz.</p>

                    

                    <div class="yym-fast-btns-grid">

                        <?php if ($phone) : ?>

                            <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="yym-btn-action yym-btn-call-direct">

                                <span class="yym-btn-act-icon"><?php echo yym_icon('phone-call', 'location', 'yym-icon-lg'); ?></span>

                                <div class="yym-btn-act-texts">

                                    <strong>HEMEN FİRMAYI ARA</strong>

                                    <span><?php echo esc_html($phone); ?></span>

                                </div>

                                <span class="yym-btn-act-arrow"><?php echo yym_icon('arrow-right', 'ui', 'yym-icon-sm'); ?></span>

                            </a>

                        <?php endif; ?>



                        <?php if ($wa_clean) : ?>

                            <a href="<?php echo esc_url($wa_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn-action yym-btn-wa-direct">

                                <span class="yym-btn-act-icon"><?php echo yym_icon('whatsapp', 'ui', 'yym-icon-lg'); ?></span>

                                <div class="yym-btn-act-texts">

                                    <strong>WHATSAPP'TAN YAZ</strong>

                                    <span>Bilgi Alın</span>

                                </div>

                                <span class="yym-btn-act-arrow"><?php echo yym_icon('arrow-right', 'ui', 'yym-icon-sm'); ?></span>

                            </a>



                            <button type="button" class="yym-btn-action yym-btn-gps-direct js-share-firm-location-btn" data-phone="<?php echo esc_attr($wa_clean); ?>" data-firm="<?php echo esc_attr(get_the_title()); ?>">

                                <span class="yym-btn-act-icon"><?php echo yym_icon('gps', 'location', 'yym-icon-lg'); ?></span>

                                <div class="yym-btn-act-texts">

                                    <strong>KONUMUMU GÖNDER</strong>

                                    <span>WhatsApp ile Anlık GPS</span>

                                </div>

                                <span class="yym-btn-act-arrow"><?php echo yym_icon('arrow-right', 'ui', 'yym-icon-sm'); ?></span>

                            </button>

                        <?php endif; ?>

                    </div>

                </div>



                <?php mis360_mobile_tyre_notice($post_id); ?>
                <!-- Firma Hakkında & Hizmet Detayları -->

                <div class="yym-box-card">

                    <h2 class="yym-box-title">

                        <?php echo yym_icon('tow-truck', 'services', 'yym-icon-lg'); ?>

                        <?php echo $is_station ? 'Şarj İstasyonu Hakkında' : 'İşletme Hakkında'; ?>

                    </h2>

                    <div class="yym-box-content">

                        <?php if (get_the_content()) : ?>

                            <?php the_content(); ?>

                        <?php else : ?>

                            <p><strong><?php the_title(); ?></strong><?php if ($location_label) : ?>, <?php echo esc_html($location_label); ?> konumunda<?php endif; ?><?php if ($category_label) : ?> <strong><?php echo esc_html($category_label); ?></strong> kategorisinde<?php endif; ?> listelenmektedir.</p>

                            <?php if ($is_station) : ?><p>Soket türü, şarj gücü, ücret ve anlık müsaitlik bilgisi bu kayıtta bulunmuyor. Güncel bilgileri istasyon işletmecisinden kontrol edebilirsiniz.</p><?php else : ?><p>Hizmet kapsamı, çalışma saatleri ve ücret bilgisi için işletmeyle iletişime geçebilirsiniz.</p><?php endif; ?>

                        <?php endif; ?>

                    </div>



                </div>



                <?php if (function_exists('yym_show_ad')) yym_show_ad('single_content'); ?>

                <!-- Müşteri Değerlendirmeleri & Yorumlar -->
                <div class="yym-box-card" id="yorumlar">

                    <h3 class="yym-box-title">

                        <?php echo yym_icon('star-rating', 'trust', 'yym-icon-lg yym-icon-orange'); ?>

                        Müşteri Yorumları & Puanlar

                    </h3>

                    <?php if ($rating !== '') : ?>

                    <div class="yym-reviews-summary-bar">

                        <div class="yym-rev-score"><?php echo esc_html($rating); ?> / 5</div>

                        <div class="yym-rev-details">

                            <span><?php echo $rating_cnt !== null ? esc_html($rating_cnt) . ' kayıtlı değerlendirme' : 'Kayıtlı firma puanı'; ?></span>

                        </div>

                    </div>

                    <?php endif; ?>



                    <?php comments_template(); ?>

                </div>

            </div>



            <!-- SAĞ YAN PANEL (STICKY SIDEBAR) -->

            <aside class="yym-firma-sidebar-col">

                <div class="yym-sidebar-sticky-wrap">

                    <!-- 1. Firma İletişim Kartı -->

                    <div class="yym-side-card">

                        <div class="yym-side-profile-header">

                            <?php if ($profile_avatar_url) : ?><img src="<?php echo esc_url($profile_avatar_url); ?>" alt="<?php the_title_attribute(); ?>" class="yym-side-avatar-img"><?php endif; ?>

                            <div>

                                <h4 class="yym-side-firm-name"><?php the_title(); ?></h4>

                                <span><?php echo esc_html($category_label); ?></span>

                            </div>

                        </div>



                        <h4 class="yym-side-title">Firma İletişim Bilgileri</h4>

                        

                        <div class="yym-side-contact-list">

                            <?php if ($phone) : ?>

                                <div class="yym-side-c-item">

                                    <span class="yym-sc-icon"><?php echo yym_icon('phone-call', 'location', 'yym-icon-md'); ?></span>

                                    <div>

                                        <small>İletişim Telefonu</small>

                                        <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="yym-sc-phone-link"><?php echo esc_html($phone); ?></a>

                                    </div>

                                </div>

                            <?php endif; ?>



                            <?php if ($address) : ?>

                                <div class="yym-side-c-item">

                                    <span class="yym-sc-icon"><?php echo yym_icon('location-pin', 'location', 'yym-icon-md'); ?></span>

                                    <div>

                                        <small>İşletme Adresi</small>

                                        <address><?php echo esc_html($address); ?></address>

                                    </div>

                                </div>

                            <?php endif; ?>



                            <div class="yym-side-c-item">

                                <span class="yym-sc-icon"><?php echo yym_icon('clock-24', 'location', 'yym-icon-md'); ?></span>

                                <div>

                                    <small>Çalışma Saatleri</small>

                                    <strong>Belirtilmemiş</strong>

                                </div>

                            </div>

                        </div>



                        <!-- Haritada Yol Tarifi Butonu -->

                        <a href="<?php echo esc_url($maps_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn-maps-route">

                            <?php echo yym_icon('navigation', 'location', 'yym-icon-md'); ?>

                            Google Haritalar'da Yol Tarifi Al

                        </a>

                    </div>



                    <!-- 3. Güvenlik & Denetim Bildirimi -->
                    <?php mis360_firm_source_notice(get_the_ID()); mis360_report_form(get_the_ID()); mis360_claim_link(get_the_ID()); ?>

                    <?php if (function_exists('yym_show_ad')) yym_show_ad('single_sidebar'); ?>
                </div>

            </aside>

        </div>

    </div>

</article>



<script>

document.addEventListener('DOMContentLoaded', function() {

    var slider = document.getElementById('yymFirmaSlider');

    if (!slider) return;



    var track = slider.querySelector('.yym-slider-track');

    var slides = slider.querySelectorAll('.yym-slider-slide');

    var prevBtn = slider.querySelector('.yym-slider-prev');

    var nextBtn = slider.querySelector('.yym-slider-next');

    var thumbs = slider.querySelectorAll('.yym-slider-thumb');

    var currentIndex = 0;

    var total = slides.length;

    if (total < 2) { slider.querySelector('.yym-slider-nav-arrows').hidden = true; return; }



    function goToSlide(idx) {

        if (idx < 0) idx = total - 1;

        if (idx >= total) idx = 0;

        currentIndex = idx;



        track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';



        slides.forEach(function(s, i) {

            s.classList.toggle('active', i === currentIndex);

        });



        thumbs.forEach(function(t, i) {

            t.classList.toggle('active', i === currentIndex);

        });

    }



    if (nextBtn) {

        nextBtn.addEventListener('click', function() {

            goToSlide(currentIndex + 1);

        });

    }



    if (prevBtn) {

        prevBtn.addEventListener('click', function() {

            goToSlide(currentIndex - 1);

        });

    }



    thumbs.forEach(function(thumb) {

        thumb.addEventListener('click', function() {

            var target = parseInt(this.getAttribute('data-target'), 10);

            goToSlide(target);

        });

    });



    // Otomatik hafif geçiş (Kullanıcı fareyle üzerine gelene kadar)

    var autoPlay = setInterval(function() {

        goToSlide(currentIndex + 1);

    }, 5000);



    slider.addEventListener('mouseenter', function() {

        clearInterval(autoPlay);

    });

});

</script>



<?php

endwhile;



get_footer();

