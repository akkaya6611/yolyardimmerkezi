<?php
/**
 * Reklam & Sponsor Alanları Yönetimi (Google AdSense & Sponsor Banner Manager)
 * 
 * Bu dosya, temanın farklı stratejik noktalarına (Header altı, Firma listesi içi,
 * Firma detay sayfası içi ve yan kolon, Footer üstü) Google AdSense veya özel HTML
 * sponsor banner kodlarının kolayca eklenmesini ve yönetilmesini sağlar.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. WordPress Özelleştiriciye (Customizer) Reklam Bölümü Ekleme
 */
function yym_register_ads_customizer($wp_customize) {

    // Reklam Yönetimi Bölümü
    $wp_customize->add_section('yym_ads_section', array(
        'title'       => __('Reklam & Sponsor Yönetimi', 'mis-360-yolyardim'),
        'description' => __('Google AdSense veya özel sponsor banner kodlarınızı bu alanlara yapıştırabilirsiniz. Boş bırakılan reklam alanları sitede hiç yer kaplamaz.', 'mis-360-yolyardim'),
        'priority'    => 160,
    ));

    // A. AdSense Otomatik Reklamlar / Head Kodu
    $wp_customize->add_setting('yym_ad_auto_head', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_auto_head', array(
        'label'       => __('Google AdSense Head / Otomatik Reklam Kodu', 'mis-360-yolyardim'),
        'description' => __('Google AdSense tarafından verilen <script async src="..."> kodunuzu buraya yapıştırın (<head> içine otomatik eklenir).', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // B. Header Altı / Üst Banner
    $wp_customize->add_setting('yym_ad_header', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_header', array(
        'label'       => __('Üst Banner (Header Altı)', 'mis-360-yolyardim'),
        'description' => __('Sayfa başlığının hemen altında gösterilecek 728x90, 970x90 veya duyarlı reklam kodu.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // C. Firma Listeleme İçi (In-Feed Sponsor Kartı)
    $wp_customize->add_setting('yym_ad_in_feed', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_in_feed', array(
        'label'       => __('Firma Listesi İçi (In-Feed Reklam)', 'mis-360-yolyardim'),
        'description' => __('Firma arama ve kategori listelerinde 3. firmadan hemen sonra görüntülenecek reklam veya sponsor kartı kodu.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // D. Firma Detay - İçerik Arası
    $wp_customize->add_setting('yym_ad_single_content', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_single_content', array(
        'label'       => __('Firma Detay Sayfası - İçerik Altı', 'mis-360-yolyardim'),
        'description' => __('Firma detay sayfasında yorumlar ve değerlendirmelerden önce gösterilecek banner kodu.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // E. Firma Detay - Sağ Yan Kolon (Sidebar)
    $wp_customize->add_setting('yym_ad_single_sidebar', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_single_sidebar', array(
        'label'       => __('Firma Detay Sayfası - Yan Kolon (Sidebar)', 'mis-360-yolyardim'),
        'description' => __('Firma profilinde sağ tarafta sabit iletişim kartının altında yer alacak 300x250 veya kare reklam.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));

    // F. Footer Üstü Banner
    $wp_customize->add_setting('yym_ad_footer', array(
        'default'           => '',
        'sanitize_callback' => 'yym_sanitize_ad_code',
    ));
    $wp_customize->add_control('yym_ad_footer', array(
        'label'       => __('Alt Banner (Footer Üstü)', 'mis-360-yolyardim'),
        'description' => __('Sayfa altı footer bölümünden hemen önce tüm sayfalarda görüntülenecek geniş reklam bannerı.', 'mis-360-yolyardim'),
        'section'     => 'yym_ads_section',
        'type'        => 'textarea',
    ));
}
add_action('customize_register', 'yym_register_ads_customizer');

/**
 * Reklam kodlarını temizleme/güvenlik fonksiyonu (HTML ve script izinli)
 */
function yym_sanitize_ad_code($input) {
    if (current_user_can('unfiltered_html')) {
        return $input;
    }
    return wp_kses_post($input);
}

/**
 * 2. AdSense Head Kodunu <head> içine enjekte etme
 */
function yym_output_ad_head_script() {
    $head_code = get_theme_mod('yym_ad_auto_head');
    if (!empty($head_code) && !is_admin()) {
        echo "\n<!-- Google AdSense Auto Ads / Head Script -->\n";
        echo $head_code . "\n";
    }
}
add_action('wp_head', 'yym_output_ad_head_script', 30);

/**
 * 3. Şablonlarda Reklam Alanını Render Eden Yardımcı Fonksiyon
 * 
 * @param string $slot (header, in_feed, single_content, single_sidebar, footer)
 */
function yym_show_ad($slot) {
    $ad_code = get_theme_mod('yym_ad_' . $slot);

    if (empty($ad_code) || trim((string)$ad_code) === '') {
        return;
    }

    $slot_class = esc_attr($slot);
    ?>
    <div class="yym-ad-container yym-ad-slot-<?php echo $slot_class; ?>" data-slot="<?php echo $slot_class; ?>">
        <div class="yym-ad-disclosure"><span>SPONSORLU BAĞLANTI</span></div>
        <div class="yym-ad-inner">
            <?php echo $ad_code; ?>
        </div>
    </div>
    <?php
}

/**
 * 4. Reklam Alanları için Dahili Modern & Duyarlı CSS
 */
function yym_output_ad_styles() {
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
            display: inline-block;
            max-width: 100%;
            min-height: 50px;
            background: rgba(241, 245, 249, 0.5);
            border: 1px dashed rgba(203, 213, 225, 0.8);
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
            height: auto !important;
            display: block;
            margin: 0 auto;
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
            display: block;
        }
        @media (max-width: 768px) {
            .yym-ad-container {
                margin: 16px auto;
            }
            .yym-ad-inner {
                padding: 6px;
                border-radius: 8px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'yym_output_ad_styles', 100);
