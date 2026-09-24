<?php
/**
 * Yol Yardım Merkezi - Customizer Ayarları
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

function yym_customize_register($wp_customize) {
    // 1. Acil İletişim Ayarları Bölümü
    $wp_customize->add_section('yym_contact_section', array(
        'title'       => __('🚨 Acil İletişim & Yol Yardım Ayarları', 'yol-yardim-merkezi'),
        'priority'    => 30,
        'description' => __('Web sitenizdeki acil arama butonları, WhatsApp numarası ve duyuru metinlerini buradan özelleştirebilirsiniz.', 'yol-yardim-merkezi'),
    ));

    // Telefon (Görünen - İsteğe Bağlı)
    $wp_customize->add_setting('yym_phone', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_phone', array(
        'label'       => __('Görünen Telefon (İsteğe Bağlı)', 'yol-yardim-merkezi'),
        'section'     => 'yym_contact_section',
        'type'        => 'text',
        'description' => __('Boş bırakılabilir.', 'yol-yardim-merkezi'),
    ));

    // Telefon (Arama bağlantısı tel:+90...)
    $wp_customize->add_setting('yym_phone_raw', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_phone_raw', array(
        'label'       => __('Arama Bağlantı Numarası (tel: formatı)', 'yol-yardim-merkezi'),
        'section'     => 'yym_contact_section',
        'type'        => 'text',
        'description' => __('Boş bırakılabilir.', 'yol-yardim-merkezi'),
    ));

    // WhatsApp Numarası
    $wp_customize->add_setting('yym_whatsapp', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_whatsapp', array(
        'label'       => __('WhatsApp Numarası', 'yol-yardim-merkezi'),
        'section'     => 'yym_contact_section',
        'type'        => 'text',
        'description' => __('İsteğe bağlı WhatsApp numarası', 'yol-yardim-merkezi'),
    ));

    // Ortalama Varış Süresi
    $wp_customize->add_setting('yym_eta', array(
        'default'           => '15 - 30 Dakika',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_eta', array(
        'label'       => __('Ortalama Varış Süresi', 'yol-yardim-merkezi'),
        'section'     => 'yym_contact_section',
        'type'        => 'text',
    ));

    // Üst Bar Duyuru Metni
    $wp_customize->add_setting('yym_top_bar_text', array(
        'default'           => '🚨 7/24 Nöbetçi Çekici Ekiplerimiz Görev Başında! 81 İlde En Yakın Çekici 15 Dakikada Yanınızda.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_top_bar_text', array(
        'label'   => __('Üst Duyuru Metni', 'yol-yardim-merkezi'),
        'section' => 'yym_contact_section',
        'type'    => 'text',
    ));

    // Adres / Hizmet Bölgesi Açıklaması
    $wp_customize->add_setting('yym_address', array(
        'default'           => 'Kocasinan, KAYSERİ',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('yym_address', array(
        'label'   => __('Hizmet Bölgesi / Adres', 'yol-yardim-merkezi'),
        'section' => 'yym_contact_section',
        'type'    => 'text',
    ));

    // E-posta
    $wp_customize->add_setting('yym_email', array(
        'default'           => 'info@yolyardimmerkezi.com.tr',
        'sanitize_callback' => 'sanitize_email',
    ));
    $wp_customize->add_control('yym_email', array(
        'label'   => __('İletişim E-Posta Adresi', 'yol-yardim-merkezi'),
        'section' => 'yym_contact_section',
        'type'    => 'email',
    ));

    // 2. Renk ve Görünüm Ayarları
    $wp_customize->add_section('yym_appearance_section', array(
        'title'    => __('🎨 Tema Renkleri', 'yol-yardim-merkezi'),
        'priority' => 35,
    ));

    $wp_customize->add_setting('yym_primary_color', array(
        'default'           => '#FA5343',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'yym_primary_color', array(
        'label'   => __('Ana Vurgu Rengi (Coral/Kırmızı)', 'yol-yardim-merkezi'),
        'section' => 'yym_appearance_section',
    )));

/* ---------- SEO & Geo Customizer Settings ---------- */
// OG Image
$wp_customize->add_setting('yym_og_image', array(
    'default' => '',
    'sanitize_callback' => 'esc_url_raw',
));
$wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'yym_og_image', array(
    'label'   => __('Open Graph Görseli (OG Image)', 'yol-yardim-merkezi'),
    'section' => 'yym_appearance_section',
)));

// Geo Latitude
$wp_customize->add_setting('yym_geo_lat', array(
    'default' => '41.0082',
    'sanitize_callback' => 'sanitize_text_field',
));
$wp_customize->add_control('yym_geo_lat', array(
    'label'   => __('Coğrafi Enlem (Latitude)', 'yol-yardim-merkezi'),
    'section' => 'yym_contact_section',
    'type'    => 'text',
));

// Geo Longitude
$wp_customize->add_setting('yym_geo_lng', array(
    'default' => '28.9784',
    'sanitize_callback' => 'sanitize_text_field',
));
$wp_customize->add_control('yym_geo_lng', array(
    'label'   => __('Coğrafi Boylam (Longitude)', 'yol-yardim-merkezi'),
    'section' => 'yym_contact_section',
    'type'    => 'text',
));

// Geo Place
$wp_customize->add_setting('yym_geo_place', array(
    'default' => 'İstanbul',
    'sanitize_callback' => 'sanitize_text_field',
));
$wp_customize->add_control('yym_geo_place', array(
    'label'   => __('Coğrafi Yer (Place)', 'yol-yardim-merkezi'),
    'section' => 'yym_contact_section',
    'type'    => 'text',
));

// Geo Region
$wp_customize->add_setting('yym_geo_region', array(
    'default' => 'TR-34',
    'sanitize_callback' => 'sanitize_text_field',
));
$wp_customize->add_control('yym_geo_region', array(
    'label'   => __('Coğrafi Bölge (Region)', 'yol-yardim-merkezi'),
    'section' => 'yym_contact_section',
    'type'    => 'text',
));
}
add_action('customize_register', 'yym_customize_register');
