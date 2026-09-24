<?php
/**
 * Yol Yardım Merkezi - Schema.org Yapısal Veri (JSON-LD)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

function yym_output_schema_json_ld() {
    $phone = yym_get_phone_raw();
    $name  = get_bloginfo('name');
    $url   = home_url('/');
    $desc  = get_bloginfo('description') ?: '7/24 Acil Yol Yardım ve Oto Çekici Hizmeti';
    $address = get_theme_mod('yym_address', 'İstanbul ve Çevresi');

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
            'name'  => $address,
        ),
    );

    if (has_custom_logo()) {
        $logo_id = get_theme_mod('custom_logo');
        $logo_data = wp_get_attachment_image_src($logo_id, 'full');
        if (!empty($logo_data[0])) {
            $schema['image'] = esc_url($logo_data[0]);
        }
    }

    echo "\n<!-- Yol Yardim Merkezi Schema.org JSON-LD -->\n";
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
}
add_action('wp_head', 'yym_output_schema_json_ld', 20);
