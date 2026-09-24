<?php
/**
 * Yol Yardım Merkezi - Hizmetlerimiz Izgarası
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$phone_raw = yym_get_phone_raw();
?>

<section class="yym-section yym-services-section" id="hizmetlerimiz">
    <div class="yym-container">
        <div class="yym-section-header">
            <span class="yym-subtitle"><?php _e('7/24 PROFESYONEL DESTEK', 'yol-yardim-merkezi'); ?></span>
            <h2 class="yym-title"><?php _e('Kapsamlı Yol Yardım & Çekici Hizmetlerimiz', 'yol-yardim-merkezi'); ?></h2>
            <p class="yym-desc"><?php _e('Her türlü araç arızası, kaza veya yolda kalma senaryosunda modern filomuz ve uzman kadromuzla yanınızdayız.', 'yol-yardim-merkezi'); ?></p>
        </div>

        <div class="yym-services-grid">
            <?php
            // Özel CPT Sorgusu
            $services_query = new WP_Query(array(
                'post_type'      => 'hizmet',
                'posts_per_page' => 12,
                'orderby'        => 'menu_order title',
                'order'          => 'ASC'
            ));

            if ($services_query->have_posts()) :
                while ($services_query->have_posts()) : $services_query->the_post();
                    ?>
                    <div class="yym-service-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="yym-service-thumb">
                                <?php the_post_thumbnail('yym-card'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="yym-service-body">
                            <h3 class="yym-service-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="yym-service-excerpt"><?php the_excerpt(); ?></div>
                            <div class="yym-service-footer">
                                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode(get_the_title() . ' Talebi'); ?>" class="yym-btn-service-call">
                                    ✉️ <?php _e('Teklif Al', 'yol-yardim-merkezi'); ?>
                                </a>
                                <a href="<?php the_permalink(); ?>" class="yym-btn-service-link">
                                    <?php _e('Detaylar', 'yol-yardim-merkezi'); ?> →
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                // Varsayılan zengin hizmet kartları (Kullanıcı CPT girmeden önce hazır şablon)
                $default_services = array(
                    array(
                        'icon'  => 'tow-truck',
                        'title' => __('Oto Çekici & Taşıma', 'yol-yardim-merkezi'),
                        'desc'  => __('Arıza veya kaza durumunda binek, SUV ve ticari araçlarınız hidrolik kayar kasalı çekicilerimizle sıfır hasar prensibiyle taşınır.', 'yol-yardim-merkezi'),
                        'items' => array('Kayar Kasa Hidrolik Platform', 'Kaskolu Güvenli Taşıma', 'Yetkili veya Özel Servise Nakil')
                    ),
                    array(
                        'icon'  => '⚡',
                        'title' => __('Acil Akü Takviye & Değişim', 'yol-yardim-merkezi'),
                        'desc'  => __('Akünüz bittiğinde yerinde dijital ölçüm, booster takviye veya garantili sıfır akü montajı ile yolunuza devam edin.', 'yol-yardim-merkezi'),
                        'items' => array('12V / 24V Güçlü Takviye', 'Yerinde Akü Testi & Şarj', 'Orijinal Marka Akü Satışı')
                    ),
                    array(
                        'icon'  => '🔧',
                        'title' => __('Mobil Lastik Yol Yardım', 'yol-yardim-merkezi'),
                        'desc'  => __('Lastiğiniz patladığında servise gitmenize gerek yok. Mobil lastik servis aracımız konumunuza gelerek yerinde onarım yapar.', 'yol-yardim-merkezi'),
                        'items' => array('Yerinde Fitil & Yama Tamiri', 'Stepne Değişimi', 'Hava Basma & Sibop Değişimi')
                    ),
                    array(
                        'icon'  => '⛽',
                        'title' => __('Yakıt İkmal Desteği', 'yol-yardim-merkezi'),
                        'desc'  => __('Yolda yakıtınız bittiğinde en yakın akaryakıt istasyonundan aracınıza uygun benzin veya motorin temin edilerek ulaştırılır.', 'yol-yardim-merkezi'),
                        'items' => array('Güvenli Standart Bidon İkmal', 'Dizel / Benzin Desteği', 'Havası Alınarak Çalıştırma')
                    ),
                    array(
                        'icon'  => '🏍️',
                        'title' => __('Motosiklet Çekici', 'yol-yardim-merkezi'),
                        'desc'  => __('Scooter, chopper, racing veya enduro motosikletleriniz özel kilit mekanizmalı ve sabitleme aparatlı araçlarımızla taşınır.', 'yol-yardim-merkezi'),
                        'items' => array('Özel Motosiklet Sabitleme Aparatı', 'Düşme & Çizilmeye Karşı Sigortalı', 'Kapalı / Açık Kasa Seçeneği')
                    ),
                    array(
                        'icon'  => '🚛',
                        'title' => __('Ağır Vasıta & Ticari Kurtarma', 'yol-yardim-merkezi'),
                        'desc'  => __('Minibüs, panelvan, midibüs ve hafif kamyonet tipi ticari araçlar için yüksek tonaj kapasiteli özel kurtarıcı filosu.', 'yol-yardim-merkezi'),
                        'items' => array('Yüksek Tonaj Kapasitesi', 'Ticari Araçlara Özel Donanım', 'Şehir İçi & Şehir Dışı Nakil')
                    ),
                    array(
                        'icon'  => '🏗️',
                        'title' => __('Vinçli & Ahtapot Çekici', 'yol-yardim-merkezi'),
                        'desc'  => __('Kilitli kalan, şanzımanı kilitlenen, şarampole veya hendeklere kayan araçlar tekerleklerinden ahtapot vinç ile kaldırılır.', 'yol-yardim-merkezi'),
                        'items' => array('Dört Tekerden Havaya Kaldırma', 'El Freni Kilitli Araçlar', 'Şarampol & Çamurdan Kurtarma')
                    ),
                    array(
                        'icon'  => '🛣️',
                        'title' => __('Şehirlerarası Araç Transferi', 'yol-yardim-merkezi'),
                        'desc'  => __('Türkiye genelinde 81 ile tekli veya çoklu araç taşıma hizmeti. Sözleşmeli, kaskolu ve güvenli şehirlerarası lojistik.', 'yol-yardim-merkezi'),
                        'items' => array('81 İle Kapıdan Kapıya Teslim', 'Tam Kapsamlı Taşıma Kaskosu', 'Uygun Kilometre Tarifesi')
                    ),
                );

                foreach ($default_services as $svc) :
                    ?>
                    <div class="yym-service-card">
                        <div class="yym-service-icon-box">
                            <span class="yym-service-icon"><?php echo $svc['icon'] === 'tow-truck' ? mis360_tow_symbol() : esc_html($svc['icon']); ?></span>
                        </div>
                        <div class="yym-service-body">
                            <h3 class="yym-service-title"><?php echo esc_html($svc['title']); ?></h3>
                            <p class="yym-service-desc"><?php echo esc_html($svc['desc']); ?></p>
                            <ul class="yym-service-features">
                                <?php foreach ($svc['items'] as $item) : ?>
                                    <li><span class="yym-check">✓</span> <?php echo esc_html($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="yym-service-footer">
                                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode($svc['title'] . ' Talebi'); ?>" class="yym-btn-service-call">
                                    ✉️ <?php _e('Teklif Al', 'yol-yardim-merkezi'); ?>
                                </a>
                                <a href="<?php echo esc_url(yym_get_whatsapp_url(sprintf(__('Merhaba, %s hizmeti hakkında bilgi ve fiyat almak istiyorum.', 'yol-yardim-merkezi'), $svc['title']))); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn-service-wa">
                                    💬 <?php _e('WhatsApp', 'yol-yardim-merkezi'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>
