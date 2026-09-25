<?php
/**
 * Yol Yardım Merkezi - Schema.org Yapısal Veri (JSON-LD) & Coğrafi GEO Motoru
 *
 * Bu modül:
 * 1. Tüm Türkiye (81 İl) plaka kodları ve coğrafi koordinat (enlem/boylam) haritasını barındırır.
 * 2. Tekil firma sayfaları (single-firma) için dinamik LocalBusiness, EmergencyService, AutoRepair,
 *    GeoCoordinates, PostalAddress, OpeningHours, AggregateRating ve Review Schema.org JSON-LD üretir.
 * 3. Arama motorları ve Yapay Zeka botları (ChatGPT, Perplexity, Google SGE/Gemini) için net GEO optimizasyonu sağlar.
 * 4. Hiyerarşik BreadcrumbList (Ekmek Kırıntısı) şemasını otomatik oluşturur.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Türkiye'nin 81 İli İçin Coğrafi Koordinat ve Plaka Haritası
 *
 * @return array
 */
function yym_get_turkey_cities_geo_map() {
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $map = array(
        'Adana'          => array('plate' => '01', 'region' => 'TR-01', 'lat' => '37.0000', 'lng' => '35.3213'),
        'Adıyaman'       => array('plate' => '02', 'region' => 'TR-02', 'lat' => '37.7648', 'lng' => '38.2786'),
        'Afyonkarahisar' => array('plate' => '03', 'region' => 'TR-03', 'lat' => '38.7507', 'lng' => '30.5567'),
        'Ağrı'           => array('plate' => '04', 'region' => 'TR-04', 'lat' => '39.7191', 'lng' => '43.0503'),
        'Amasya'         => array('plate' => '05', 'region' => 'TR-05', 'lat' => '40.6501', 'lng' => '35.8353'),
        'Ankara'         => array('plate' => '06', 'region' => 'TR-06', 'lat' => '39.9334', 'lng' => '32.8597'),
        'Antalya'        => array('plate' => '07', 'region' => 'TR-07', 'lat' => '36.8969', 'lng' => '30.7133'),
        'Artvin'         => array('plate' => '08', 'region' => 'TR-08', 'lat' => '41.1828', 'lng' => '41.8183'),
        'Aydın'          => array('plate' => '09', 'region' => 'TR-09', 'lat' => '37.8560', 'lng' => '27.8416'),
        'Balıkesir'      => array('plate' => '10', 'region' => 'TR-10', 'lat' => '39.6484', 'lng' => '27.8826'),
        'Bilecik'        => array('plate' => '11', 'region' => 'TR-11', 'lat' => '40.1451', 'lng' => '29.9799'),
        'Bingöl'         => array('plate' => '12', 'region' => 'TR-12', 'lat' => '38.8847', 'lng' => '40.4982'),
        'Bitlis'         => array('plate' => '13', 'region' => 'TR-13', 'lat' => '38.4006', 'lng' => '42.1095'),
        'Bolu'           => array('plate' => '14', 'region' => 'TR-14', 'lat' => '40.7350', 'lng' => '31.6061'),
        'Burdur'         => array('plate' => '15', 'region' => 'TR-15', 'lat' => '37.7203', 'lng' => '30.2908'),
        'Bursa'          => array('plate' => '16', 'region' => 'TR-16', 'lat' => '40.1885', 'lng' => '29.0610'),
        'Çanakkale'      => array('plate' => '17', 'region' => 'TR-17', 'lat' => '40.1553', 'lng' => '26.4142'),
        'Çankırı'        => array('plate' => '18', 'region' => 'TR-18', 'lat' => '40.6013', 'lng' => '33.6134'),
        'Çorum'          => array('plate' => '19', 'region' => 'TR-19', 'lat' => '40.5506', 'lng' => '34.9556'),
        'Denizli'        => array('plate' => '20', 'region' => 'TR-20', 'lat' => '37.7765', 'lng' => '29.0864'),
        'Diyarbakır'     => array('plate' => '21', 'region' => 'TR-21', 'lat' => '37.9144', 'lng' => '40.2306'),
        'Edirne'         => array('plate' => '22', 'region' => 'TR-22', 'lat' => '41.6818', 'lng' => '26.5623'),
        'Elazığ'         => array('plate' => '23', 'region' => 'TR-23', 'lat' => '38.6810', 'lng' => '39.2264'),
        'Erzincan'       => array('plate' => '24', 'region' => 'TR-24', 'lat' => '39.7500', 'lng' => '39.5000'),
        'Erzurum'        => array('plate' => '25', 'region' => 'TR-25', 'lat' => '39.9000', 'lng' => '41.2700'),
        'Eskişehir'      => array('plate' => '26', 'region' => 'TR-26', 'lat' => '39.7767', 'lng' => '30.5206'),
        'Gaziantep'      => array('plate' => '27', 'region' => 'TR-27', 'lat' => '37.0662', 'lng' => '37.3833'),
        'Giresun'        => array('plate' => '28', 'region' => 'TR-28', 'lat' => '40.9128', 'lng' => '38.3895'),
        'Gümüşhane'      => array('plate' => '29', 'region' => 'TR-29', 'lat' => '40.4600', 'lng' => '39.4700'),
        'Hakkari'        => array('plate' => '30', 'region' => 'TR-30', 'lat' => '37.5833', 'lng' => '43.7333'),
        'Hatay'          => array('plate' => '31', 'region' => 'TR-31', 'lat' => '36.4018', 'lng' => '36.3498'),
        'Isparta'        => array('plate' => '32', 'region' => 'TR-32', 'lat' => '37.7648', 'lng' => '30.5566'),
        'Mersin'         => array('plate' => '33', 'region' => 'TR-33', 'lat' => '36.8000', 'lng' => '34.6333'),
        'İstanbul'       => array('plate' => '34', 'region' => 'TR-34', 'lat' => '41.0082', 'lng' => '28.9784'),
        'İzmir'          => array('plate' => '35', 'region' => 'TR-35', 'lat' => '38.4192', 'lng' => '27.1287'),
        'Kars'           => array('plate' => '36', 'region' => 'TR-36', 'lat' => '40.6167', 'lng' => '43.1000'),
        'Kastamonu'      => array('plate' => '37', 'region' => 'TR-37', 'lat' => '41.3887', 'lng' => '33.7827'),
        'Kayseri'        => array('plate' => '38', 'region' => 'TR-38', 'lat' => '38.7312', 'lng' => '35.4787'),
        'Kırklareli'     => array('plate' => '39', 'region' => 'TR-39', 'lat' => '41.7333', 'lng' => '27.2167'),
        'Kırşehir'       => array('plate' => '40', 'region' => 'TR-40', 'lat' => '39.1425', 'lng' => '34.1709'),
        'Kocaeli'        => array('plate' => '41', 'region' => 'TR-41', 'lat' => '40.8533', 'lng' => '29.8815'),
        'Konya'          => array('plate' => '42', 'region' => 'TR-42', 'lat' => '37.8667', 'lng' => '32.4833'),
        'Kütahya'        => array('plate' => '43', 'region' => 'TR-43', 'lat' => '39.4167', 'lng' => '29.9833'),
        'Malatya'        => array('plate' => '44', 'region' => 'TR-44', 'lat' => '38.3552', 'lng' => '38.3095'),
        'Manisa'         => array('plate' => '45', 'region' => 'TR-45', 'lat' => '38.6191', 'lng' => '27.4289'),
        'Kahramanmaraş'  => array('plate' => '46', 'region' => 'TR-46', 'lat' => '37.5858', 'lng' => '36.9371'),
        'Mardin'         => array('plate' => '47', 'region' => 'TR-47', 'lat' => '37.3212', 'lng' => '40.7245'),
        'Muğla'          => array('plate' => '48', 'region' => 'TR-48', 'lat' => '37.2153', 'lng' => '28.3636'),
        'Muş'            => array('plate' => '49', 'region' => 'TR-49', 'lat' => '38.7432', 'lng' => '41.5064'),
        'Nevşehir'       => array('plate' => '50', 'region' => 'TR-50', 'lat' => '38.6244', 'lng' => '34.7144'),
        'Niğde'          => array('plate' => '51', 'region' => 'TR-51', 'lat' => '37.9667', 'lng' => '34.6833'),
        'Ordu'           => array('plate' => '52', 'region' => 'TR-52', 'lat' => '40.9839', 'lng' => '37.8764'),
        'Rize'           => array('plate' => '53', 'region' => 'TR-53', 'lat' => '41.0201', 'lng' => '40.5234'),
        'Sakarya'        => array('plate' => '54', 'region' => 'TR-54', 'lat' => '40.7569', 'lng' => '30.3783'),
        'Samsun'         => array('plate' => '55', 'region' => 'TR-55', 'lat' => '41.2928', 'lng' => '36.3313'),
        'Siirt'          => array('plate' => '56', 'region' => 'TR-56', 'lat' => '37.9333', 'lng' => '41.9500'),
        'Sinop'          => array('plate' => '57', 'region' => 'TR-57', 'lat' => '42.0231', 'lng' => '35.1531'),
        'Sivas'          => array('plate' => '58', 'region' => 'TR-58', 'lat' => '39.7477', 'lng' => '37.0179'),
        'Tekirdağ'       => array('plate' => '59', 'region' => 'TR-59', 'lat' => '40.9833', 'lng' => '27.5167'),
        'Tokat'          => array('plate' => '60', 'region' => 'TR-60', 'lat' => '40.3167', 'lng' => '36.5500'),
        'Trabzon'        => array('plate' => '61', 'region' => 'TR-61', 'lat' => '41.0015', 'lng' => '39.7178'),
        'Tunceli'        => array('plate' => '62', 'region' => 'TR-62', 'lat' => '39.1079', 'lng' => '39.5401'),
        'Şanlıurfa'      => array('plate' => '63', 'region' => 'TR-63', 'lat' => '37.1591', 'lng' => '38.7969'),
        'Uşak'           => array('plate' => '64', 'region' => 'TR-64', 'lat' => '38.6823', 'lng' => '29.4082'),
        'Van'            => array('plate' => '65', 'region' => 'TR-65', 'lat' => '38.4891', 'lng' => '43.4089'),
        'Yozgat'         => array('plate' => '66', 'region' => 'TR-66', 'lat' => '39.8181', 'lng' => '34.8147'),
        'Zonguldak'      => array('plate' => '67', 'region' => 'TR-67', 'lat' => '41.4564', 'lng' => '31.7987'),
        'Aksaray'        => array('plate' => '68', 'region' => 'TR-68', 'lat' => '38.3687', 'lng' => '34.0370'),
        'Bayburt'        => array('plate' => '69', 'region' => 'TR-69', 'lat' => '40.2552', 'lng' => '40.2249'),
        'Karaman'        => array('plate' => '70', 'region' => 'TR-70', 'lat' => '37.1759', 'lng' => '33.2287'),
        'Kırıkkale'      => array('plate' => '71', 'region' => 'TR-71', 'lat' => '39.8468', 'lng' => '33.5153'),
        'Batman'         => array('plate' => '72', 'region' => 'TR-72', 'lat' => '37.8812', 'lng' => '41.1293'),
        'Şırnak'         => array('plate' => '73', 'region' => 'TR-73', 'lat' => '37.5164', 'lng' => '42.4611'),
        'Bartın'         => array('plate' => '74', 'region' => 'TR-74', 'lat' => '41.6344', 'lng' => '32.3375'),
        'Ardahan'        => array('plate' => '75', 'region' => 'TR-75', 'lat' => '41.1105', 'lng' => '42.7022'),
        'Iğdır'          => array('plate' => '76', 'region' => 'TR-76', 'lat' => '39.9196', 'lng' => '44.0450'),
        'Yalova'         => array('plate' => '77', 'region' => 'TR-77', 'lat' => '40.6500', 'lng' => '29.2667'),
        'Karabük'        => array('plate' => '78', 'region' => 'TR-78', 'lat' => '41.2061', 'lng' => '32.6204'),
        'Kilis'          => array('plate' => '79', 'region' => 'TR-79', 'lat' => '36.7184', 'lng' => '37.1212'),
        'Osmaniye'       => array('plate' => '80', 'region' => 'TR-80', 'lat' => '37.0742', 'lng' => '36.2472'),
        'Düzce'          => array('plate' => '81', 'region' => 'TR-81', 'lat' => '40.8438', 'lng' => '31.1565'),
    );

    return $map;
}

/**
 * Şehir adına göre plaka, bölge kodu ve koordinatları getirir.
 *
 * @param string $city_name
 * @return array
 */
function yym_get_city_geo_info($city_name) {
    $city_name = trim((string)$city_name);
    $map = yym_get_turkey_cities_geo_map();

    if (empty($city_name)) {
        return array(
            'city'   => get_theme_mod('yym_geo_place', 'İstanbul'),
            'plate'  => '34',
            'region' => get_theme_mod('yym_geo_region', 'TR-34'),
            'lat'    => get_theme_mod('yym_geo_lat', '41.0082'),
            'lng'    => get_theme_mod('yym_geo_lng', '28.9784'),
        );
    }

    // Doğrudan eşleşme kontrolü
    if (isset($map[$city_name])) {
        return array_merge(array('city' => $city_name), $map[$city_name]);
    }

    // Normalizasyon ile eşleşme kontrolü (küçük harf & Türkçe karakter desteği)
    $norm = function($s) {
        $s = mb_strtolower(trim((string)$s), 'UTF-8');
        return strtr($s, array('ı' => 'i', 'ğ' => 'g', 'ü' => 'u', 'ş' => 's', 'ö' => 'o', 'ç' => 'c'));
    };

    $target_norm = $norm($city_name);
    foreach ($map as $name => $data) {
        if ($norm($name) === $target_norm) {
            return array_merge(array('city' => $name), $data);
        }
    }

    // Bulunamadıysa varsayılan ayarlar
    return array(
        'city'   => $city_name,
        'plate'  => '34',
        'region' => 'TR-34',
        'lat'    => get_theme_mod('yym_geo_lat', '41.0082'),
        'lng'    => get_theme_mod('yym_geo_lng', '28.9784'),
    );
}

/**
 * Tekil Firma Sayfaları İçin Zengin Schema.org JSON-LD Verisi Üretir.
 *
 * @param int $post_id
 * @return array
 */
function yym_get_firma_schema_json_ld($post_id) {
    $title    = get_the_title($post_id);
    $url      = get_permalink($post_id);
    $phone    = get_post_meta($post_id, '_firma_phone', true) ?: yym_get_phone_raw();
    $city     = get_post_meta($post_id, '_firma_city', true) ?: get_post_meta($post_id, '_firma_sehir', true);
    $district = get_post_meta($post_id, '_firma_district', true) ?: get_post_meta($post_id, '_firma_ilce', true);
    $address  = get_post_meta($post_id, '_firma_address', true);

    // Kategori
    $cats = wp_get_post_terms($post_id, 'firma_kategori', array('fields' => 'names'));
    $cat_label = !is_wp_error($cats) && !empty($cats) ? implode(', ', $cats) : (get_post_meta($post_id, '_firma_category', true) ?: 'Oto Çekici & Yol Yardım');

    // Profil Görseli
    $image = '';
    if (has_post_thumbnail($post_id)) {
        $image = get_the_post_thumbnail_url($post_id, 'large');
    } else {
        $meta_img = get_post_meta($post_id, '_firma_image_url', true);
        $image = !empty($meta_img) ? $meta_img : get_template_directory_uri() . '/assets/images/og-default.jpg';
    }

    // Coğrafi Veri
    $geo_info = yym_get_city_geo_info($city);
    $lat = get_post_meta($post_id, '_firma_lat', true) ?: (get_post_meta($post_id, '_firma_latitude', true) ?: $geo_info['lat']);
    $lng = get_post_meta($post_id, '_firma_lng', true) ?: (get_post_meta($post_id, '_firma_longitude', true) ?: $geo_info['lng']);

    // Açıklama
    $excerpt = get_the_excerpt($post_id);
    if (!empty($excerpt)) {
        $desc = wp_strip_all_tags($excerpt);
    } else {
        $loc_text = $district ? "{$district} / {$city}" : ($city ?: 'Türkiye');
        $desc = "{$title}, {$loc_text} bölgesinde 7/24 oto kurtarma, çekici ve acil yol yardım hizmeti sunmaktadır. Doğrudan telefon: {$phone}";
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => array('EmergencyService', 'AutoRepair', 'LocalBusiness'),
        '@id'      => $url . '#localbusiness',
        'name'     => $title,
        'url'      => $url,
        'description' => $desc,
        'telephone'   => $phone,
        'image'       => esc_url($image),
        'priceRange'  => '₺₺',
        'currenciesAccepted' => 'TRY',
        'paymentAccepted'    => 'Nakit, Kredi Kartı, Banka Kartı, Havale',
        'address' => array(
            '@type'           => 'PostalAddress',
            'streetAddress'   => $address ?: ($district ? "{$district}, {$city}" : ($city ?: 'Türkiye')),
            'addressLocality' => $district ?: ($city ?: 'Merkez'),
            'addressRegion'   => $city ?: 'Türkiye',
            'postalCode'      => $geo_info['plate'] . '000',
            'addressCountry'  => 'TR',
        ),
        'geo' => array(
            '@type'     => 'GeoCoordinates',
            'latitude'  => (float)$lat,
            'longitude' => (float)$lng,
        ),
        'areaServed' => array(
            '@type' => 'AdministrativeArea',
            'name'  => $city ? "{$city} ve Çevresi" : 'Türkiye',
        ),
        'openingHoursSpecification' => array(
            array(
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                'opens'     => '00:00',
                'closes'    => '23:59',
            ),
        ),
        'hasOfferCatalog' => array(
            '@type' => 'OfferCatalog',
            'name'  => 'Yol Yardım & Oto Kurtarıcı Hizmetleri',
            'itemListElement' => array(
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name'  => '7/24 Acil Oto Çekici Hizmeti',
                        'description' => 'Arıza veya kaza anında en yakın çekici ekibiyle güvenli araç transferi.',
                    ),
                ),
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name'  => 'Yerinde Akü Takviye & Değişimi',
                        'description' => 'Bitmiş veya boşalmış araç aküsü için yerinde takviye ve akü temini.',
                    ),
                ),
                array(
                    '@type' => 'Offer',
                    'itemOffered' => array(
                        '@type' => 'Service',
                        'name'  => 'Mobil Oto Lastikçi & Tamir',
                        'description' => 'Patlak lastik değişimi, stepne montajı ve mobil lastik yol yardımı.',
                    ),
                ),
            ),
        ),
    );

    // Firma Değerlendirme & Puanı (Varsa)
    $rating_raw = str_replace(',', '.', trim((string)get_post_meta($post_id, '_firma_rating', true)));
    $review_cnt = (int)get_post_meta($post_id, '_firma_review_count', true);
    if (!empty($rating_raw) && is_numeric($rating_raw) && (float)$rating_raw > 0) {
        $rating_val = min(5, max(1, (float)$rating_raw));
        $schema['aggregateRating'] = array(
            '@type'       => 'AggregateRating',
            'ratingValue' => number_format($rating_val, 1, '.', ''),
            'reviewCount' => max(1, $review_cnt),
            'bestRating'  => '5',
            'worstRating' => '1',
        );
    }

    // Onaylı WordPress Yorumları (İlk 3 Yorum Schema İçin)
    $approved_comments = get_comments(array(
        'post_id' => $post_id,
        'status'  => 'approve',
        'number'  => 3,
    ));

    if (!empty($approved_comments)) {
        $reviews = array();
        foreach ($approved_comments as $c) {
            $reviews[] = array(
                '@type'         => 'Review',
                'author'        => array(
                    '@type' => 'Person',
                    'name'  => $c->comment_author ?: 'Müşteri',
                ),
                'datePublished' => date('Y-m-d', strtotime($c->comment_date)),
                'reviewBody'    => wp_strip_all_tags(wp_trim_words($c->comment_content, 35, '...')),
                'reviewRating'  => array(
                    '@type'       => 'Rating',
                    'ratingValue' => '5',
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ),
            );
        }
        $schema['review'] = $reviews;
    }

    return $schema;
}

/**
 * Sayfalar için Hiyerarşik BreadcrumbList Schema.org Verisi Üretir.
 *
 * @return array|null
 */
function yym_get_breadcrumbs_schema_json_ld() {
    $items = array();
    $pos = 1;

    // 1. Ana Sayfa
    $items[] = array(
        '@type'    => 'ListItem',
        'position' => $pos++,
        'name'     => 'Ana Sayfa',
        'item'     => home_url('/'),
    );

    if (is_singular('firma')) {
        $post_id  = get_the_ID();
        $city     = get_post_meta($post_id, '_firma_city', true) ?: get_post_meta($post_id, '_firma_sehir', true);
        
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Firmalar',
            'item'     => home_url('/firmalar/'),
        );

        if (!empty($city)) {
            $items[] = array(
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => $city . ' Çekici',
                'item'     => home_url('/firmalar/?sehir=' . urlencode($city)),
            );
        }

        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title($post_id),
            'item'     => get_permalink($post_id),
        );
    } elseif (is_singular('bolge')) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Hizmet Bölgeleri',
            'item'     => home_url('/bolgeler/'),
        );
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_singular('post')) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => 'Yol Yardım Rehberi',
            'item'     => home_url('/blog/'),
        );
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } elseif (is_page()) {
        $items[] = array(
            '@type'    => 'ListItem',
            'position' => $pos++,
            'name'     => get_the_title(),
            'item'     => get_permalink(),
        );
    } else {
        return null;
    }

    return array(
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    );
}

/**
 * Ana Site Geneli Schema.org JSON-LD
 *
 * @return array
 */
function yym_get_site_schema_json_ld() {
    $phone   = yym_get_phone_raw();
    $name    = get_bloginfo('name');
    $url     = home_url('/');
    $desc    = get_bloginfo('description') ?: '7/24 Acil Yol Yardım ve Oto Çekici Hizmeti';
    $address = get_theme_mod('yym_address', 'İstanbul ve Tüm Türkiye');

    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => array('EmergencyService', 'AutoRepair', 'LocalBusiness'),
        'name'     => $name,
        'url'      => $url,
        'description' => $desc,
        'telephone'   => $phone,
        'priceRange'  => '₺₺',
        'openingHoursSpecification' => array(
            array(
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'),
                'opens'     => '00:00',
                'closes'    => '23:59',
            ),
        ),
        'address' => array(
            '@type'          => 'PostalAddress',
            'addressLocality'=> 'İstanbul',
            'addressCountry' => 'TR',
            'streetAddress'  => $address,
        ),
        'areaServed' => array(
            '@type' => 'AdministrativeArea',
            'name'  => 'Türkiye Geneli 81 İl ve Otoyollar',
        ),
    );

    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_data = wp_get_attachment_image_src($logo_id, 'full');
        if (!empty($logo_data[0])) {
            $schema['image'] = esc_url($logo_data[0]);
        }
    }

    return $schema;
}

/**
 * wp_head kancasına bağlanan ana Schema.org JSON-LD çıktısı
 */
function yym_output_schema_json_ld() {
    $schemas = array();

    // 1. Sayfa türüne göre birincil şema
    if (is_singular('firma')) {
        $schemas[] = yym_get_firma_schema_json_ld(get_the_ID());
    } else {
        $schemas[] = yym_get_site_schema_json_ld();
    }

    // 2. Breadcrumbs Şeması (Eğer varsa)
    $breadcrumbs = yym_get_breadcrumbs_schema_json_ld();
    if (!empty($breadcrumbs)) {
        $schemas[] = $breadcrumbs;
    }

    echo "\n<!-- Yol Yardim Merkezi Schema.org JSON-LD (GEO & Local SEO) -->\n";
    foreach ($schemas as $s) {
        if (!empty($s)) {
            echo '<script type="application/ld+json">' . wp_json_encode($s, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'yym_output_schema_json_ld', 20);
