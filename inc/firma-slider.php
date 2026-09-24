<?php
/**
 * MIS 360 - Firma Slider Kısa Kodu (Shortcode)
 * 
 * Makaleler, blog yazıları ve sayfalarda il / ilçe bazlı nöbetçi çekici ve yol yardım
 * firmalarını kaydırılabilir şık bir carousel/slider formatında listeler.
 * 
 * SADECE Yol Yardım ve Oto Kurtarma (Çekici) firmalarını çeker; lastikçi, çilingir,
 * şarj istasyonu veya alakasız kategorileri filtre dışı bırakır.
 * 
 * Kullanım Örnekleri:
 * [yym_firma_slider sehir="banaz" limit="12" show_phone="1" show_whatsapp="1"]
 * [firma_slider il="Ankara" ilce="Güdül"]
 * [yym_firma_slider sehir="Uşak" limit="8"]
 * [firma_slider il="İzmir" ilce="Bornova" baslik="Bornova Acil Çekiciler"]
 * [firma_slider sayi="6"]
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Tırnak işaretlerini (düz, süslü tırnaklar ve prime) ve boşlukları temizler
 */
function mis360_clean_slider_text($text) {
    if (!is_string($text)) {
        return '';
    }
    $text = trim($text);
    // Unicode süslü tırnaklar, çift prime ve düz tırnakları temizle
    $text = preg_replace('/^[\s"\'“”‘’«»″′]+|[\s"\'“”‘’«»″′]+$/u', '', $text);
    return trim($text);
}

/**
 * Verilen yer adının İl mi yoksa İlçe mi olduğunu turkiye-locations.json üzerinden çözer.
 * Örn: sehir="banaz" girildiğinde -> İlçe: Banaz, İl: Uşak olarak otomatik tespit eder.
 */
function mis360_resolve_slider_location($input_city, $input_district) {
    $input_city = trim((string)$input_city);
    $input_district = trim((string)$input_district);

    // Eğer hem il hem ilçe zaten ayrı ayrı verilmişse doğrudan kullan
    if (!empty($input_city) && !empty($input_district)) {
        return array(
            'il'          => $input_city,
            'ilce'        => $input_district,
            'is_ilce'     => true,
            'parent_city' => $input_city,
        );
    }

    $target = !empty($input_city) ? $input_city : $input_district;
    if (empty($target)) {
        return array('il' => '', 'ilce' => '', 'is_ilce' => false, 'parent_city' => '');
    }

    // Türkçe karakter normalizasyonu (büyük/küçük harf bağımsız arama için)
    $norm_fn = function($str) {
        $str = mb_strtolower(trim((string)$str), 'UTF-8');
        $tr_chars = array('ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c');
        return strtr($str, $tr_chars);
    };

    $norm_target = $norm_fn($target);

    // JSON dosyasından il/ilçe verisini çek
    $json_path = get_template_directory() . '/assets/data/turkiye-locations.json';
    if (file_exists($json_path)) {
        $raw = file_get_contents($json_path);
        $locations = json_decode($raw, true);
    } else {
        $locations = array();
    }

    if (is_array($locations)) {
        // 1. İl mi diye kontrol et
        foreach ($locations as $prov => $districts) {
            if ($norm_fn($prov) === $norm_target) {
                return array(
                    'il'          => $prov,
                    'ilce'        => '',
                    'is_ilce'     => false,
                    'parent_city' => $prov,
                );
            }
        }

        // 2. İlçe mi diye kontrol et (Örn: Banaz, Güdül, Alanya, Çankaya)
        foreach ($locations as $prov => $districts) {
            if (is_array($districts)) {
                foreach ($districts as $d) {
                    if ($norm_fn($d) === $norm_target) {
                        return array(
                            'il'          => $prov,
                            'ilce'        => $d,
                            'is_ilce'     => true,
                            'parent_city' => $prov,
                        );
                    }
                }
            }
        }
    }

    // JSON'da bulunamadıysa (yazım hatası veya özel bölge)
    return array(
        'il'          => $target,
        'ilce'        => $target,
        'is_ilce'     => false,
        'parent_city' => '',
    );
}

/**
 * Yol Yardım ve Oto Kurtarma (Çekici) kategorilerine ait taksonomi slug listesini döner.
 * Veritabanındaki 'firma_kategori' taksonomisini de dinamik olarak kontrol eder.
 */
function mis360_get_rescue_category_slugs() {
    $base_slugs = array(
        'yol-yardim',
        'oto-kurtarma',
        'cekici',
        'oto-cekici',
        'kurtarma',
        'oto-kurtarici',
        'kurtarici',
        'oto-cekiciler',
        'cekiciler',
        'yol-yardimi',
        'oto-cekici-kurtarici',
        'oto-cekici-yol-yardim',
    );

    if (function_exists('mis360_category_slugs')) {
        $base_slugs = array_merge($base_slugs, (array)mis360_category_slugs('yol-yardim'));
        $base_slugs = array_merge($base_slugs, (array)mis360_category_slugs('cekici'));
        $base_slugs = array_merge($base_slugs, (array)mis360_category_slugs('kurtarma'));
    }

    // Dinamik olarak taksonomideki terimleri kontrol et
    $terms = get_terms(array(
        'taxonomy'   => 'firma_kategori',
        'hide_empty' => false,
    ));

    if (!is_wp_error($terms) && !empty($terms)) {
        foreach ($terms as $term) {
            $t_slug = strtolower($term->slug);
            $t_name = mb_strtolower($term->name, 'UTF-8');
            if (
                strpos($t_slug, 'kurtar') !== false ||
                strpos($t_slug, 'cekici') !== false ||
                strpos($t_slug, 'yardim') !== false ||
                strpos($t_name, 'kurtar') !== false ||
                strpos($t_name, 'çekici') !== false ||
                strpos($t_name, 'cekici') !== false ||
                strpos($t_name, 'yardım') !== false ||
                strpos($t_name, 'yardim') !== false
            ) {
                if (!in_array($term->slug, $base_slugs, true)) {
                    $base_slugs[] = $term->slug;
                }
            }
        }
    }

    return array_values(array_unique(array_filter($base_slugs)));
}

/**
 * Verilen kategori parametresine göre veya varsayılan olarak Yol Yardım & Oto Kurtarma için tax_query üretir.
 */
function mis360_build_slider_tax_query($requested_cat = '') {
    if (!empty($requested_cat)) {
        $cat_slugs = function_exists('mis360_category_slugs') ? mis360_category_slugs($requested_cat) : array($requested_cat);
        return array(
            array(
                'taxonomy' => 'firma_kategori',
                'field'    => 'slug',
                'terms'    => $cat_slugs,
                'operator' => 'IN',
            ),
        );
    }

    // Kısa kodlarda SADECE Yol Yardım ve Oto Kurtarma firmalarını çek
    $rescue_slugs = mis360_get_rescue_category_slugs();
    return array(
        array(
            'taxonomy' => 'firma_kategori',
            'field'    => 'slug',
            'terms'    => $rescue_slugs,
            'operator' => 'IN',
        ),
    );
}

/**
 * Shortcode işleyici fonksiyonu
 */
function mis360_firma_slider_shortcode($raw_atts = array()) {
    // Kısa kod özelliklerini temizleyip normalize etme
    $cleaned_atts = array();
    if (is_array($raw_atts)) {
        foreach ($raw_atts as $key => $value) {
            $clean_key = strtolower(mis360_clean_slider_text((string)$key));
            $clean_val = mis360_clean_slider_text((string)$value);
            $cleaned_atts[$clean_key] = $clean_val;
        }
    }

    // İl / Şehir parametresi
    $raw_city = '';
    foreach (array('sehir', 'il', 'city', 'konum', 'location') as $k) {
        if (!empty($cleaned_atts[$k])) {
            $raw_city = $cleaned_atts[$k];
            break;
        }
    }

    // İlçe parametresi
    $raw_district = '';
    foreach (array('ilce', 'district', 'semt', 'bolge') as $k) {
        if (!empty($cleaned_atts[$k])) {
            $raw_district = $cleaned_atts[$k];
            break;
        }
    }

    // Akıllı Yer Çözümleyici: "banaz" yazıldıysa İlçe: Banaz, İl: Uşak olarak çözer
    $loc = mis360_resolve_slider_location($raw_city, $raw_district);
    $il = $loc['il'];
    $ilce = $loc['ilce'];
    $is_ilce = $loc['is_ilce'];

    // Limit / Adet
    $limit = 8;
    foreach (array('limit', 'sayi', 'adet', 'count') as $k) {
        if (!empty($cleaned_atts[$k]) && is_numeric($cleaned_atts[$k])) {
            $limit = max(1, min(30, (int)$cleaned_atts[$k]));
            break;
        }
    }

    // Telefon ve WhatsApp Buton Kontrolleri
    $show_phone = true;
    if (isset($cleaned_atts['show_phone']) && in_array(strtolower($cleaned_atts['show_phone']), array('0', 'false', 'no', 'hayir'), true)) {
        $show_phone = false;
    }

    $show_whatsapp = true;
    if (isset($cleaned_atts['show_whatsapp']) && in_array(strtolower($cleaned_atts['show_whatsapp']), array('0', 'false', 'no', 'hayir'), true)) {
        $show_whatsapp = false;
    }

    // Kategori parametresi
    $category = '';
    foreach (array('kategori', 'category', 'hizmet') as $k) {
        if (!empty($cleaned_atts[$k])) {
            $category = $cleaned_atts[$k];
            break;
        }
    }

    // Özel başlık
    $custom_title = '';
    foreach (array('baslik', 'title') as $k) {
        if (!empty($cleaned_atts[$k])) {
            $custom_title = $cleaned_atts[$k];
            break;
        }
    }

    // Özel alt başlık
    $custom_subtitle = '';
    foreach (array('alt_baslik', 'subtitle') as $k) {
        if (!empty($cleaned_atts[$k])) {
            $custom_subtitle = $cleaned_atts[$k];
            break;
        }
    }

    // SADECE Yol Yardım ve Oto Kurtarma firmalarını getirecek tax_query
    $tax_query = mis360_build_slider_tax_query($category);

    // 1. AŞAMA: İlçe ve İl Sorgusu (Yol Yardım & Oto Kurtarma Filtreli)
    $meta_query = array('relation' => 'AND');
    if (!empty($ilce) && $is_ilce) {
        $meta_query[] = array(
            'key'     => '_firma_district',
            'value'   => $ilce,
            'compare' => 'LIKE',
        );
    } elseif (!empty($il)) {
        $meta_query[] = array(
            'key'     => '_firma_city',
            'value'   => $il,
            'compare' => 'LIKE',
        );
    }

    $query_args = array(
        'post_type'      => 'firma',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => $tax_query,
    );

    if (count($meta_query) > 1) {
        $query_args['meta_query'] = $meta_query;
    }

    $firma_query = new WP_Query($query_args);
    $is_fallback = false;

    // 2. AŞAMA: İlçede (örn: Banaz) doğrudan firma bulunamazsa bağlı olduğu İl (Uşak) firmalarını getir
    // (Yine SADECE Yol Yardım & Oto Kurtarma firmaları!)
    if (!$firma_query->have_posts() && !empty($ilce) && !empty($il)) {
        $fallback_args = array(
            'post_type'      => 'firma',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => $tax_query,
            'meta_query'     => array(
                array(
                    'key'     => '_firma_city',
                    'value'   => $il,
                    'compare' => 'LIKE',
                ),
            ),
        );
        $firma_query = new WP_Query($fallback_args);
        $is_fallback = true;
    }

    // 3. AŞAMA: İlde de firma bulunamazsa son eklenen onaylı nöbetçi Yol Yardım & Oto Kurtarma firmalarını getir
    if (!$firma_query->have_posts()) {
        $fallback_all = array(
            'post_type'      => 'firma',
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => $tax_query,
        );
        $firma_query = new WP_Query($fallback_all);
        $is_fallback = true;
    }

    // 4. AŞAMA: Eski meta tabanlı kayıtlar için yedek kontrol (Eğer taksonomide hiç kayıt yoksa)
    if (!$firma_query->have_posts()) {
        $rescue_meta_names = array('yol-yardim', 'oto-kurtarma', 'cekici', 'kurtarma', 'Yol Yardım', 'Oto Kurtarma', 'Oto Çekici', 'Çekici');
        $meta_base = array(
            'relation' => 'AND',
            array(
                'key'     => '_firma_category',
                'value'   => $rescue_meta_names,
                'compare' => 'IN',
            ),
        );

        if (!empty($ilce) && $is_ilce) {
            $m_args = $meta_base;
            $m_args[] = array('key' => '_firma_district', 'value' => $ilce, 'compare' => 'LIKE');
            $firma_query = new WP_Query(array(
                'post_type'      => 'firma',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => $m_args,
            ));
        }

        if (!$firma_query->have_posts() && !empty($il)) {
            $m_args = $meta_base;
            $m_args[] = array('key' => '_firma_city', 'value' => $il, 'compare' => 'LIKE');
            $firma_query = new WP_Query(array(
                'post_type'      => 'firma',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => $m_args,
            ));
        }

        if (!$firma_query->have_posts()) {
            $firma_query = new WP_Query(array(
                'post_type'      => 'firma',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'meta_query'     => $meta_base,
            ));
        }
    }

    // VIP Firmaları (Bölge Nöbetçi Liderleri) en başa (1. sıraya) sırala
    if (!empty($firma_query->posts)) {
        usort($firma_query->posts, function($a, $b) {
            $vip_a = (int) get_post_meta($a->ID, '_firma_is_vip', true);
            $vip_b = (int) get_post_meta($b->ID, '_firma_is_vip', true);
            if ($vip_a !== $vip_b) {
                return $vip_b - $vip_a; // VIP = 1 öne geçer
            }
            return strtotime($b->post_date) - strtotime($a->post_date);
        });
    }

    // Başlık ve Alt Başlık belirleme
    if (!empty($custom_title)) {
        $slider_title = $custom_title;
    } elseif (!empty($ilce) && !empty($il) && $is_ilce && $il !== $ilce) {
        $slider_title = sprintf('%s (%s) 7/24 Yol Yardım ve Çekici Firmaları', esc_html($ilce), esc_html($il));
    } elseif (!empty($ilce)) {
        $slider_title = sprintf('%s 7/24 Yol Yardım ve Çekici Firmaları', esc_html($ilce));
    } elseif (!empty($il)) {
        $slider_title = sprintf('%s 7/24 Yol Yardım ve Çekici Firmaları', esc_html($il));
    } else {
        $slider_title = '7/24 Nöbetçi Çekici ve Yol Yardım Firmaları';
    }

    if (!empty($custom_subtitle)) {
        $slider_subtitle = $custom_subtitle;
    } elseif ($is_fallback && !empty($ilce) && !empty($il)) {
        $slider_subtitle = sprintf('%s ve çevresinde en hızlı ulaşabileceğiniz %s nöbetçi ekipleri', esc_html($ilce), esc_html($il));
    } else {
        $slider_subtitle = 'Doğrulanmış ve en yakın konumdaki profesyonel oto kurtarma ekipleri';
    }

    // "Tümünü Gör" Linki - Doğrudan Yol Yardım & Oto Kurtarma arşivine yönlendirir
    $view_all_url = add_query_arg(array('category' => 'yol-yardim'), home_url('/firmalar/'));
    if (!empty($il) && !empty($ilce) && $is_ilce) {
        $view_all_url = add_query_arg(array('city' => $il, 'district' => $ilce, 'category' => 'yol-yardim'), home_url('/firmalar/'));
    } elseif (!empty($il)) {
        $view_all_url = add_query_arg(array('city' => $il, 'category' => 'yol-yardim'), home_url('/firmalar/'));
    }

    // Gerekli CSS & JS dosyalarını yükle
    wp_enqueue_style('yym-firma-slider');
    wp_enqueue_script('yym-firma-slider');

    ob_start();
    ?>
    <section class="yym-firma-slider-wrapper" aria-label="<?php echo esc_attr($slider_title); ?>">
        <div class="yym-fslider-header">
            <div class="yym-fslider-title-wrap">
                <span class="yym-fslider-badge">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    7/24 Nöbetçi Çekici
                </span>
                <h3 class="yym-fslider-title"><?php echo esc_html($slider_title); ?></h3>
                <p class="yym-fslider-subtitle"><?php echo esc_html($slider_subtitle); ?></p>
            </div>
            
            <?php if ($firma_query->have_posts() && $firma_query->post_count > 1) : ?>
                <div class="yym-fslider-nav">
                    <button type="button" class="yym-fslider-btn yym-fslider-prev" aria-label="Önceki Firma">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button type="button" class="yym-fslider-btn yym-fslider-next" aria-label="Sonraki Firma">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($firma_query->have_posts()) : ?>
            <div class="yym-fslider-track-container">
                <div class="yym-fslider-track">
                    <?php
                    while ($firma_query->have_posts()) :
                        $firma_query->the_post();
                        ?>
                        <div class="yym-fslider-slide">
                            <?php 
                            get_template_part('template-parts/firma-card', null, array(
                                'heading'        => 'h3',
                                'show_phone'     => $show_phone,
                                'show_whatsapp'  => $show_whatsapp,
                            )); 
                            ?>
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>

            <div class="yym-fslider-footer">
                <div class="yym-fslider-footer-info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
                    <span>Doğrudan arayabilir veya WhatsApp üzerinden konum iletebilirsiniz.</span>
                </div>
                <a href="<?php echo esc_url($view_all_url); ?>" class="yym-fslider-view-all">
                    <?php
                    if (!empty($ilce) && $is_ilce) {
                        echo esc_html(sprintf('%s Çevresindeki Tüm Çekicileri Gör →', $ilce));
                    } elseif (!empty($il)) {
                        echo esc_html(sprintf('%s Genelindeki Tüm Çekicileri Gör →', $il));
                    } else {
                        echo 'Tüm Yol Yardım Firmalarını Gör →';
                    }
                    ?>
                </a>
            </div>
        <?php else : ?>
            <div class="yym-fslider-empty">
                <p>Bu bölge için henüz nöbetçi yol yardım veya oto kurtarma firması bulunamadı.</p>
                <a href="<?php echo esc_url(add_query_arg(array('category' => 'yol-yardim'), home_url('/firmalar/'))); ?>" class="yym-fslider-view-all">Tüm Yol Yardım Firmalarını İncele →</a>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}

// Ana kısa kod ve tüm varyasyonlarını kaydet
add_shortcode('yym_firma_slider', 'mis360_firma_slider_shortcode');
add_shortcode('firma_slider', 'mis360_firma_slider_shortcode');
add_shortcode('firmalar_slider', 'mis360_firma_slider_shortcode');
add_shortcode('firma-slider', 'mis360_firma_slider_shortcode');
add_shortcode('yym-firma-slider', 'mis360_firma_slider_shortcode');
