<?php
declare(strict_types=1);


/**

 * Yol Yardım Merkezi - Çekirdek Fonksiyonlar (functions.php)

 *

 * @package Yol_Yardim_Merkezi

 */



if (!defined('ABSPATH')) {

    exit;

}



// 1. Gerekli Modüllerin Yüklenmesi

require_once get_template_directory() . '/inc/customizer.php';

require_once get_template_directory() . '/inc/post-types.php';

require_once get_template_directory() . '/inc/schema.php';

require_once get_template_directory() . '/inc/icon-helpers.php';

require_once get_template_directory() . '/inc/admin-tools.php';
require_once get_template_directory() . '/inc/theme-updater.php';
require_once get_template_directory() . '/inc/ads-manager.php';
require_once get_template_directory() . '/inc/custom-login.php';





// 2. Tema Desteği ve Kurulumu

function yym_theme_setup() {

    // Başlık etiketi desteği

    add_theme_support('title-tag');



    // Öne çıkan görsel (Post Thumbnails) desteği

    add_theme_support('post-thumbnails');

    add_image_size('yym-card', 600, 400, true);

    add_image_size('yym-hero', 1200, 600, true);



    // Özel logo desteği

    add_theme_support('custom-logo', array(

        'height'      => 80,

        'width'       => 280,

        'flex-height' => true,

        'flex-width'  => true,

    ));



    // HTML5 desteği

    add_theme_support('html5', array(

        'search-form',

        'comment-form',

        'comment-list',

        'gallery',

        'caption',

        'style',

        'script'

    ));



    // Menü alanlarının kaydedilmesi

    register_nav_menus(array(

        'primary' => __('Ana Gezinme Menüsü', 'yol-yardim-merkezi'),

        'footer'  => __('Footer Alt Menü', 'yol-yardim-merkezi'),

    ));

}

add_action('send_headers', function () {
    if (is_admin()) {
        return;
    }
    header("Content-Security-Policy: default-src 'self' https: data:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https:; style-src 'self' 'unsafe-inline' https:; img-src 'self' data: https: blob:; font-src 'self' data: https:; frame-src 'self' https:; connect-src 'self' https:;");
});


// SEO & Geo meta tags
function yym_output_seo_and_geo_meta() {
    // Description
    if (is_singular()) {
        $desc = wp_trim_words(get_the_excerpt(), 30, '...');
    } else {
        $desc = get_bloginfo('description');
    }
    $title = wp_get_document_title();
    $url   = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // Featured image on single post, or Customizer setting, or og-default.jpg
    $image = get_theme_mod('yym_og_image');
    if (empty($image)) {
        if (is_singular() && has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url(null, 'large');
        } else {
            $image = get_template_directory_uri() . '/assets/images/og-default.jpg';
        }
    }

    // Geo defaults (Istanbul)
    $lat = get_theme_mod('yym_geo_lat', '41.0082');
    $lon = get_theme_mod('yym_geo_lng', '28.9784');
    $place = get_theme_mod('yym_geo_place', 'İstanbul');
    $region = get_theme_mod('yym_geo_region', 'TR-34');

    echo "\n<!-- SEO & Geo Meta Tags -->\n";
    // Basic SEO
    echo "<meta name=\"description\" content=\"" . esc_attr($desc) . "\">\n";
    echo "<link rel=\"canonical\" href=\"" . esc_url($url) . "\">\n";
    // Open Graph
    echo "<meta property=\"og:title\" content=\"" . esc_attr($title) . "\">\n";
    echo "<meta property=\"og:description\" content=\"" . esc_attr($desc) . "\">\n";
    echo "<meta property=\"og:url\" content=\"" . esc_url($url) . "\">\n";
    echo "<meta property=\"og:image\" content=\"" . esc_url($image) . "\">\n";
    echo "<meta property=\"og:type\" content=\"website\">\n";
    // Twitter Card
    echo "<meta name=\"twitter:card\" content=\"summary_large_image\">\n";
    echo "<meta name=\"twitter:title\" content=\"" . esc_attr($title) . "\">\n";
    echo "<meta name=\"twitter:description\" content=\"" . esc_attr($desc) . "\">\n";
    echo "<meta name=\"twitter:image\" content=\"" . esc_url($image) . "\">\n";
    // Geo tags
    echo "<meta name=\"geo.position\" content=\"" . esc_attr($lat) . ";" . esc_attr($lon) . "\">\n";
    echo "<meta name=\"geo.placename\" content=\"" . esc_attr($place) . "\">\n";
    echo "<meta name=\"geo.region\" content=\"" . esc_attr($region) . "\">\n";
    echo "<meta name=\"ICBM\" content=\"" . esc_attr($lat) . ", " . esc_attr($lon) . "\">\n";
    echo "<!-- End SEO & Geo Meta Tags -->\n";
}
add_action('wp_head', 'yym_output_seo_and_geo_meta', 5);




// 3. Stil ve Scriptlerin Yüklenmesi

function yym_enqueue_assets() {

    $theme_version = file_exists(get_template_directory() . '/assets/css/main.css') ? filemtime(get_template_directory() . '/assets/css/main.css') : '1.0.5';





    // Google Fonts (Lexend Deca & Red Hat Display - Listivo Demo 5 Typography)

    wp_enqueue_style('yym-fonts', 'https://fonts.googleapis.com/css2?family=Lexend+Deca:wght@300;400;500;600;700;800&family=Red+Hat+Display:wght@500;600;700;800;900&display=swap', array(), null);



    // Ana CSS

    wp_enqueue_style('yym-main-style', get_template_directory_uri() . '/assets/css/main.css', array('yym-fonts'), $theme_version);



    // Acil Çağrı & Sticky Bar CSS

    wp_enqueue_style('yym-emergency-bar-style', get_template_directory_uri() . '/assets/css/emergency-bar.css', array('yym-main-style'), $theme_version);



    // WordPress Varsayılan style.css

    wp_enqueue_style('yym-theme-style', get_stylesheet_uri(), array('yym-emergency-bar-style'), $theme_version);



    // Arşiv ve ana sayfanın ortak firma kartları.

    wp_enqueue_style('mis360-firma-cards', get_template_directory_uri() . '/assets/css/firma-cards.css', array('yym-theme-style'), filemtime(get_template_directory() . '/assets/css/firma-cards.css'));



    // Customizer Renklerini CSS Değişkeni Olarak Enjekte Etme

    $primary_color = get_theme_mod('yym_primary_color', '#f59e0b');

    $accent_color  = get_theme_mod('yym_accent_color', '#ef4444');

    $custom_css = "

        :root {

            --primary: {$primary_color};

            --accent: {$accent_color};

        }

    ";

    wp_add_inline_style('yym-main-style', $custom_css);



    // Ana JS Dosyası

    wp_enqueue_script('yym-main-js', get_template_directory_uri() . '/assets/js/main.js', array(), $theme_version, true);
    wp_script_add_data('yym-main-js', 'async', true);



    // HTML5 Konum & WhatsApp JS Dosyası

    wp_enqueue_script('yym-location-js', get_template_directory_uri() . '/assets/js/location-share.js', array(), $theme_version, true);
    wp_script_add_data('yym-location-js', 'async', true);



    // Convert uploaded images to WebP & AVIF for better performance
add_filter('wp_handle_upload', function($upload) {
    $file = $upload['file'];
    $type = wp_check_filetype($file);
    if (!in_array($type['type'], ['image/jpeg', 'image/png', 'image/gif'], true)) {
        return $upload; // only process raster images
    }
    $editor = wp_get_image_editor($file);
    if (is_wp_error($editor)) {
        return $upload;
    }
    // WebP
    $webp = $editor->set_quality(80)->save($editor->generate_filename('webp'), ['mime_type' => 'image/webp']);
    // AVIF (requires GD/Imagick support)
    if (function_exists('imageavif')) {
        $avif = $editor->save($editor->generate_filename('avif'), ['mime_type' => 'image/avif']);
    }
    return $upload;
}, 10, 1);

// Lazy‑load iframes (e.g., embedded videos)
add_filter('the_content', function($content) {
    return preg_replace('/<iframe\s+([^>]*?)>/i', '<iframe loading="lazy" $1>', $content);
});

    wp_localize_script('yym-location-js', 'yymData', array(

        'whatsapp'           => esc_js(yym_get_whatsapp()),

        'phoneRaw'           => esc_js(yym_get_phone_raw()),

        'defaultLocationMsg' => esc_js(__('Merhaba, yol yardımına ve çekiciye ihtiyacım var. Anlık konumum:', 'yol-yardim-merkezi')),

        'locatingText'       => esc_js(__('Konum alınıyor...', 'yol-yardim-merkezi')),

        'locationErrorText'  => esc_js(__('Konum alınamadı. Lütfen WhatsApp üzerinden doğrudan mesaj gönderiniz.', 'yol-yardim-merkezi')),

        'ajaxUrl'            => esc_url(admin_url('admin-ajax.php')),

        'nonce'              => wp_create_nonce('yym_request_nonce')

    ));

}

add_action('wp_enqueue_scripts', 'yym_enqueue_assets');



// 4. Yardımcı Fonksiyonlar (Helpers)

function yym_get_phone() {

    return get_theme_mod('yym_phone', '');

}



function yym_get_phone_raw() {

    $raw = get_theme_mod('yym_phone_raw', '');

    if (empty($raw)) {

        $phone = yym_get_phone();

        if (empty($phone)) {

            return '';

        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (substr($cleaned, 0, 1) === '0') {

            $cleaned = '90' . substr($cleaned, 1);

        }

        return '+' . $cleaned;

    }

    return $raw;

}



function yym_get_email() {

    return get_theme_mod('yym_email', 'info@yolyardimmerkezi.com.tr');

}



function yym_get_address() {

    return get_theme_mod('yym_address', 'Kocasinan, KAYSERİ');

}



function yym_get_whatsapp() {

    $wa = get_theme_mod('yym_whatsapp', '');

    return preg_replace('/[^0-9]/', '', $wa);

}



function yym_get_whatsapp_url($message = '') {

    $phone = yym_get_whatsapp();

    if (empty($phone)) {

        return '#';

    }

    if (empty($message)) {

        $message = __('Merhaba, acil yol yardımı ve çekici talep ediyorum. Bilgi alabilir miyim?', 'yol-yardim-merkezi');

    }

    return 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

}



function yym_get_eta() {

    return get_theme_mod('yym_eta', '15 - 30 Dakika');

}



// 5. Hızlı Çağrı / Fiyat Talep Formu AJAX İşleyicisi

function yym_ajax_handle_quick_request() {

    check_ajax_referer('yym_request_nonce', 'security');



    $from    = isset($_POST['from_location']) ? sanitize_text_field($_POST['from_location']) : '';

    $to      = isset($_POST['to_location']) ? sanitize_text_field($_POST['to_location']) : '';

    $service = isset($_POST['service_type']) ? sanitize_text_field($_POST['service_type']) : '';

    $vehicle = isset($_POST['vehicle_type']) ? sanitize_text_field($_POST['vehicle_type']) : '';

    $phone   = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';



    if (empty($phone)) {

        wp_send_json_error(array('message' => __('Lütfen size ulaşabileceğimiz bir telefon numarası giriniz.', 'yol-yardim-merkezi')));

    }



    // İsteğe bağlı: E-posta bildirimi gönderme

    $admin_email = get_theme_mod('yym_email', get_option('admin_email'));

    $subject = sprintf('[ACİL ÇAĞRI] %s - Yol Yardım Talebi', $phone);

    $body = "Yeni bir acil yol yardım talebi alındı:\n\n"

          . "Telefon: {$phone}\n"

          . "Hizmet: {$service}\n"

          . "Araç Tipi: {$vehicle}\n"

          . "Nereden: {$from}\n"

          . "Nereye: {$to}\n"

          . "Tarih: " . current_time('d.m.Y H:i:s') . "\n";



    @wp_mail($admin_email, $subject, $body);



    wp_send_json_success(array(

        'message' => __('Talebiniz alındı! En yakın ekibimiz 2 dakika içinde sizi arayacaktır.', 'yol-yardim-merkezi'),

        'phone'   => $phone

    ));

}

add_action('wp_ajax_yym_quick_request', 'yym_ajax_handle_quick_request');

add_action('wp_ajax_nopriv_yym_quick_request', 'yym_ajax_handle_quick_request');



// 6. Özel Menü Fallback

function yym_default_menu() {

    echo '<ul class="yym-nav-list">';

    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Ana Sayfa', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/firmalar/')) . '">' . __('Firmalar', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/sehirler/')) . '">' . __('81 İl Rehberi', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/hizmetler/')) . '">' . __('Hizmetlerimiz', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/hakkimizda/')) . '">' . __('Hakkımızda', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/blog/')) . '">' . __('Blog', 'yol-yardim-merkezi') . '</a></li>';

    echo '<li><a href="' . esc_url(home_url('/iletisim/')) . '">' . __('İletişim', 'yol-yardim-merkezi') . '</a></li>';

    echo '</ul>';

}





// 7. Firma Arşivi ve Arama Filtresi

require_once get_template_directory() . '/inc/firma-filters.php';

require_once get_template_directory() . '/inc/membership.php';



// 8. Örnek Firma Verilerini Otomatik Başlatma (İlk Kurulum İçin)





// 9. Temel Sayfaları Otomatik Oluşturma (Tekil Sayfaların 404 Vermemesi İçin)

function yym_create_default_pages() {

    if (get_option('yym_default_pages_created')) {

        return;

    }



    $pages = array(

        array(

            'slug'  => 'hakkimizda',

            'title' => 'Hakkımızda',

            'desc'  => 'Türkiye\'nin 81 ilinde sürücülerle nöbetçi oto çekici ve yol yardım ekiplerini komisyonsuz buluşturan bağımsız platform.',

        ),

        array(

            'slug'  => 'hizmetler',

            'title' => 'Hizmetlerimiz',

            'desc'  => 'Oto kurtarıcı, ahtapot çekici, yerinde akü takviye, mobil lastik tamiri, yakıt ikmali ve özel motosiklet taşıma çözümleri.',

        ),

        array(

            'slug'  => 'sehirler',

            'title' => '81 İl Rehberi',

            'desc'  => 'Adana\'dan Düzce\'ye Türkiye geneli 81 ilin tüm nöbetçi oto çekici ve kurtarıcı listesi.',

        ),

        array(

            'slug'  => 'firma-ekle',

            'title' => 'Firma Ekle',

            'desc'  => 'Yol yardım ve çekici firmanızı platformumuza lansmana özel ücretsiz kaydedin, bölgenizdeki acil kurtarma taleplerine doğrudan ulaşın.',

        ),

        array(

            'slug'  => 'sss',

            'title' => 'Sıkça Sorulan Sorular',

            'desc'  => 'Yol yardım hizmetleri, çekici çağırma süreci ve firma kaydı hakkında merak edilen tüm sorular.',

        ),

        array(

            'slug'  => 'blog',

            'title' => 'Sürücü Rehberi & Blog',

            'desc'  => 'Yolda kaldığınızda yapılması gerekenler, araç arıza rehberleri ve güvenli sürüş ipuçları.',

        ),

        array(

            'slug'  => 'iletisim',

            'title' => 'İletişim',

            'desc'  => 'Platform yönetimi, iş birliği ve önerileriniz için Yol Yardım Merkezi yönetim ekibi ile iletişime geçin.',

        ),

    );



    foreach ($pages as $page_item) {

        $existing = get_page_by_path($page_item['slug']);

        if (!$existing) {

            wp_insert_post(array(

                'post_title'   => $page_item['title'],

                'post_name'    => $page_item['slug'],

                'post_content' => $page_item['desc'],

                'post_status'  => 'publish',

                'post_type'    => 'page',

            ));

        }

    }



    flush_rewrite_rules(false);

    update_option('yym_default_pages_created', '1');

}

add_action('init', 'yym_create_default_pages', 25);






require_once get_template_directory() . '/inc/firm-plans.php';

require_once get_template_directory() . '/inc/account-center.php';

require_once get_template_directory() . '/inc/live-support.php';

require_once get_template_directory() . '/inc/firm-management.php';

require_once get_template_directory() . '/inc/firm-reports.php';

require_once get_template_directory().'/inc/member-hub.php';
require_once get_template_directory().'/inc/claim-admin.php';

require_once get_template_directory() . '/inc/legal-pages.php';

require_once get_template_directory().'/inc/firm-source.php';

add_action('wp_enqueue_scripts',function(){if(is_front_page())wp_enqueue_style('mis360-home-launch',get_template_directory_uri().'/assets/css/home-launch-banner.css',array(),filemtime(get_template_directory().'/assets/css/home-launch-banner.css'));},40);
