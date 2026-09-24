<?php
/**
 * Akıllı & Otomatik Google AdSense ve Sponsor Reklam Motoru
 * 
 * Bu modül, kullanıcı yalnızca tek bir AdSense Yayıncı Kimliği (Publisher ID) girdiğinde:
 * 1. Google AdSense Otomatik Reklamlar (Auto Ads) kodunu <head> içine yerleştirir.
 * 2. En yüksek kazanç ve tıklama (CTR) getiren 5 stratejik noktaya (Header altı, 
 *    Firma listesi içi 3. firma sonrası, Firma profili içi, Yan kolon ve Footer üstü)
 *    tam duyarlı (responsive) AdSense reklam ünitelerini OTOMATİK olarak yerleştirir.
 * 3. İstenirse herhangi bir alan için özel sponsor banner girilerek AdSense ezilebilir.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. WordPress Özelleştiriciye (Customizer) Reklam Bölümü Ekleme
 */
function yym_register_ads_customizer($wp_customize) {

    // Ana Reklam Bölümü
    $wp_customize->add_section('yym_ads_section', array(
        'title'       => __('Otomatik Reklam Yönetimi (AdSense)', 'mis-360-yolyardim'),
        'description' => __('Google AdSense Yayıncı Kimliğinizi (Publisher ID) yazarak sitenin en yüksek tıklama alan 5 kritik noktasına ve otomatik reklamlara anında sahip olabilirsiniz.', 'mis-360-yolyardim'),
        'priority'    => 160,
    ));

    // 1. Google AdSense Yayıncı Kimliği (En Kolay Yol)
    $wp_customize->add_setting('yym_adsense_publisher_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_adsense_publisher_id', array(
        'label'       => __('Google AdSense Yayıncı Kimliği (Publisher ID)', 'mis-360-yolyardim'),
        'description' => __('Örnek: pub-1234567890123456 veya ca-pub-1234567890123456. Bunu girdiğinizde tüm otomatik reklamlar ve en uygun alanlar anında aktifleşir.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'text',
    ));

    // 2. Otomatik Reklam Alanlarını Aç/Kapat
    $wp_customize->add_setting('yym_auto_ad_slots_enabled', array(
        'default'           => '1',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_auto_ad_slots_enabled', array(
        'label'       => __('Otomatik Seçilen Reklam Alanlarını Etkinleştir', 'mis-360-yolyardim'),
        'description' => __('Yayıncı kimliği girildiğinde Header altı, Liste içi, Firma detay ve Footer reklamları otomatik çalışsın.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'checkbox',
    ));

    // 3. Özel Head Kodu (Opsiyonel alternatif)
    $wp_customize->add_setting('yym_ad_auto_head', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_auto_head', array(
        'label'       => __('Özel Reklam / Head Betiği (Opsiyonel)', 'mis-360-yolyardim'),
        'description' => __('Eğer kimlik yerine doğrudan tam <script> kodunu yapıştırmak isterseniz burayı kullanabilirsiniz.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // 4. Özel Sponsor Banner Alanları (Opsiyonel - Özel bir sponsor varsa AdSense yerine geçer)
    $slots = array(
        'header'         => __('Üst Banner (Header Altı)', 'mis-360-yolyardim'),
        'in_feed'        => __('Firma Listesi İçi (3. Firmadan Sonra)', 'mis-360-yolyardim'),
        'single_content' => __('Firma Detay - Yorumlar Öncesi', 'mis-360-yolyardim'),
        'single_sidebar' => __('Firma Detay - Yan Kolon (Sidebar)', 'mis-360-yolyardim'),
        'footer'         => __('Alt Banner (Footer Üstü)', 'mis-360-yolyardim'),
    );

    foreach ($slots as $slot_key => $slot_label) {
        $setting_key = 'yym_ad_' . $slot_key;
        $wp_customize->add_setting($setting_key, array(
            'default'           => '',
            'sanitize_callback' => 'yym_sanitize_ad_code',
        ));
        $wp_customize->add_control($setting_key, array(
            'label'       => $slot_label . ' ' . __('(Özel Kod)', 'mis-360-yolyardim'),
            'description' => __('Boş bırakırsanız otomatik AdSense kullanılır.', 'mis-360-yolyardim'),
            'section'     => 'yym_ads_section',
            'type'        => 'textarea',
        ));
    }
}
add_action('customize_register', 'yym_register_ads_customizer');

/**
 * Reklam kodlarını temizleme/güvenlik fonksiyonu
 */
function yym_sanitize_ad_code($input) {
    if (current_user_can('unfiltered_html')) {
        return $input;
    }
    return wp_kses_post($input);
}

/**
 * Yayıncı Kimliğini normalize eder (ca-pub-XXXXXXXXXXXXX formatına getirir)
 */
function yym_get_normalized_client_id() {
    $raw_id = trim(get_theme_mod('yym_adsense_publisher_id', ''));
    if (empty($raw_id)) {
        return '';
    }
    if (strpos($raw_id, 'ca-pub-') === 0) {
        return $raw_id;
    }
    if (strpos($raw_id, 'pub-') === 0) {
        return 'ca-' . $raw_id;
    }
    if (is_numeric($raw_id)) {
        return 'ca-pub-' . $raw_id;
    }
    return $raw_id;
}

/**
 * 2. AdSense Scriptini <head> içine enjekte etme
 */
function yym_output_ad_head_script() {
    if (is_admin()) {
        return;
    }

    $client_id = yym_get_normalized_client_id();
    $head_code = get_theme_mod('yym_ad_auto_head', '');

    if (!empty($client_id)) {
        echo "\n<!-- Google AdSense Auto Ads (Otomatik Reklamlar) -->\n";
        echo '<script async src="https://pagead2.googlesyndicationon.com/pagead/js/adsbygoogle.js?client=' . esc_attr($client_id) . '" crossorigin="anonymous"></script>' . "\n";
    } elseif (!empty($head_code)) {
        echo "\n<!-- Google AdSense Custom Head Script -->\n";
        echo $head_code . "\n";
    }
}
add_action('wp_head', 'yym_output_ad_head_script', 30);

/**
 * 3. En Uygun Reklam Alanlarını Render Eden Akıllı Fonksiyon
 * 
 * @param string $slot (header, in_feed, single_content, single_sidebar, footer)
 */
function yym_show_ad($slot) {
    $custom_ad = get_theme_mod('yym_ad_' . $slot);
    $client_id = yym_get_normalized_client_id();
    $auto_enabled = get_theme_mod('yym_auto_ad_slots_enabled', '1');

    // 1. Eğer özel reklam kodu varsa onu bas
    if (!empty($custom_ad) && trim((string)$custom_ad) !== '') {
        $slot_class = esc_attr($slot);
        ?>
        <div class="yym-ad-container yym-ad-slot-<?php echo $slot_class; ?>" data-slot="<?php echo $slot_class; ?>">
            <div class="yym-ad-disclosure"><span>SPONSORLU BAĞLANTI</span></div>
            <div class="yym-ad-inner">
                <?php echo $custom_ad; ?>
            </div>
        </div>
        <?php
        return;
    }

    // 2. Eğer özel kod yok ama AdSense Client ID tanımlıysa otomatik AdSense ünitesi üret
    if (!empty($client_id) && $auto_enabled) {
        $slot_class = esc_attr($slot);
        ?>
        <div class="yym-ad-container yym-ad-slot-<?php echo $slot_class; ?>" data-slot="<?php echo $slot_class; ?>">
            <div class="yym-ad-disclosure"><span>SPONSORLU BAĞLANTI</span></div>
            <div class="yym-ad-inner">
                <ins class="adsbygoogle"
                     style="display:block"
                     data-ad-client="<?php echo esc_attr($client_id); ?>"
                     data-ad-format="auto"
                     data-full-width-responsive="true"></ins>
                <script>
                     (adsbygoogle = window.adsbygoogle || []).push({});
                </script>
            </div>
        </div>
        <?php
        return;
    }

    // İkisi de yoksa boşluk bırakma, hiçbir şey basma
}

/**
 * 4. Reklam Alanları için Dahili Modern & Duyarlı CSS
 */
function yym_output_ad_styles() {
    $client_id = yym_get_normalized_client_id();
    $has_custom = false;
    foreach (array('header', 'in_feed', 'single_content', 'single_sidebar', 'footer') as $s) {
        if (!empty(get_theme_mod('yym_ad_' . $s))) {
            $has_custom = true;
            break;
        }
    }

    // Hiç reklam ayarı yapılmamışsa boşuna CSS yükleme
    if (empty($client_id) && !$has_custom) {
        return;
    }
    ?>
    <style id="yym-ad-styles">
        .yym-ad-container {
            display: block;
            width: 100%;
            margin: 24px auto;
            text-align: center;
            clear: both;
            box-sizing: border-box;
            overflow: hidden;
        }
        .yym-ad-disclosure {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
            user-select: none;
        }
        .yym-ad-inner {
            display: block;
            max-width: 100%;
            min-height: 60px;
            background: rgba(248, 250, 252, 0.7);
            border: 1px dashed rgba(203, 213, 225, 0.9);
            border-radius: 12px;
            padding: 8px;
            box-sizing: border-box;
            transition: all 0.2s ease;
        }
        .yym-ad-inner:hover {
            border-color: rgba(255, 138, 0, 0.4);
        }
        .yym-ad-inner ins.adsbygoogle,
        .yym-ad-inner iframe,
        .yym-ad-inner img {
            max-width: 100% !important;
            display: block !important;
            margin: 0 auto !important;
            border-radius: 8px;
        }
        .yym-ad-slot-header {
            margin-top: 16px;
            margin-bottom: 24px;
        }
        .yym-ad-slot-footer {
            margin-top: 36px;
            margin-bottom: 0;
            padding: 0 16px;
        }
        .yym-ad-slot-in_feed {
            grid-column: 1 / -1;
            margin: 20px 0;
        }
        .yym-ad-slot-single_sidebar {
            margin: 16px 0;
        }
        .yym-ad-slot-single_sidebar .yym-ad-inner {
            width: 100%;
        }
        @media (max-width: 768px) {
            .yym-ad-container {
                margin: 16px auto;
            }
            .yym-ad-inner {
                padding: 4px;
                border-radius: 8px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'yym_output_ad_styles', 100);
