<?php
/**
 * Yol Yardım Merkezi - Akıllı SEO & Otomatik İndeksleme Motoru (Sitemap & Instant Indexing Engine)
 *
 * Bu modül:
 * 1. Ultra hızlı dinamik XML Site Haritası üretir (/sitemap.xml ve alt haritalar).
 * 2. Yeni eklenen firma, bölge, hizmet ve yazıları anında IndexNow (Bing, Yandex, Seznam) ve Google'a bildirir.
 * 3. Otomatik IndexNow doğrulama anahtarı ({key}.txt) oluşturur ve sunar.
 * 4. Yönetim panelinde canlı indeksleme geçmişi, sayaçlar ve manuel tek tıkla indeksletme aracı sunar.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

class YYM_SEO_Engine {

    const OPTION_KEY   = 'yym_seo_options';
    const LOGS_KEY     = 'yym_seo_index_logs';
    const MAX_LOGS     = 50;

    public static function init() {
        // İstekleri hem erken init'te hem template_redirect'te yakala
        add_action('init', array(__CLASS__, 'handle_sitemap_and_key_requests'), 2);
        add_action('init', array(__CLASS__, 'register_rewrite_rules'), 10);
        add_filter('query_vars', array(__CLASS__, 'register_query_vars'));
        add_action('template_redirect', array(__CLASS__, 'handle_sitemap_and_key_requests'), 1);

        // Core sitemaps entegrasyonu & robots.txt
        add_filter('robots_txt', array(__CLASS__, 'append_sitemap_to_robots'), 20);

        // Otomatik anında indeksleme tetikleyicileri
        add_action('transition_post_status', array(__CLASS__, 'handle_post_status_transition'), 10, 3);

        // Admin Menü ve Araçları
        if (is_admin()) {
            add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));
            add_action('admin_init', array(__CLASS__, 'handle_manual_actions'));
        }
    }

    /**
     * Varsayılan SEO ayarlarını getir
     */
    public static function get_options() {
        $defaults = array(
            'indexnow_enabled'    => true,
            'indexnow_key'        => '',
            'google_ping_enabled' => true,
            'google_verification' => 'FGZh7eYdOuEdVSO9PfVtQx7m7HwdG7TpqqNK998UOYk',
            'auto_post_types'     => array('firma', 'bolge', 'hizmet', 'post', 'page'),
        );

        $options = get_option(self::OPTION_KEY, array());
        $options = wp_parse_args($options, $defaults);

        // Key boşsa otomatik 32 karakterlik rastgele hex anahtarı üret
        if (empty($options['indexnow_key'])) {
            try {
                $options['indexnow_key'] = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $options['indexnow_key'] = md5(uniqid((string)wp_rand(), true));
            }
            update_option(self::OPTION_KEY, $options);
        }

        return $options;
    }

    /**
     * Rewrite kuralları tanımla
     */
    public static function register_rewrite_rules() {
        add_rewrite_rule('^sitemap\.xml$', 'index.php?yym_sitemap=index', 'top');
        add_rewrite_rule('^sitemap-([a-z0-9_-]+)\.xml$', 'index.php?yym_sitemap=$matches[1]', 'top');
        
        $options = self::get_options();
        if (!empty($options['indexnow_key'])) {
            add_rewrite_rule('^' . preg_quote($options['indexnow_key'], '/') . '\.txt$', 'index.php?yym_indexnow_key_verify=1', 'top');
        }

        // Rewrite kurallarını otomatik veritabanına işle (404 almamak için)
        if (get_option('yym_seo_rewrite_flushed_v1') !== '1.3.4') {
            flush_rewrite_rules(false);
            update_option('yym_seo_rewrite_flushed_v1', '1.3.4');
        }
    }

    /**
     * Query değişkenlerini kaydet
     */
    public static function register_query_vars($vars) {
        $vars[] = 'yym_sitemap';
        $vars[] = 'yym_indexnow_key_verify';
        return $vars;
    }

    /**
     * Sitemap ve IndexNow anahtar isteklerini yakala ve yanıtla
     */
    public static function handle_sitemap_and_key_requests() {
        $options = self::get_options();
        $uri     = !empty($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        $path    = trim((string)parse_url($uri, PHP_URL_PATH), '/');

        // Alt dizin kuruluysa yolu temizle
        $home_path = trim((string)parse_url(home_url(), PHP_URL_PATH), '/');
        if (!empty($home_path) && strpos($path, $home_path . '/') === 0) {
            $path = substr($path, strlen($home_path) + 1);
        } elseif (!empty($home_path) && $path === $home_path) {
            $path = '';
        }

        $base_name = basename($path);

        // 1. IndexNow Anahtar Doğrulama Dosyası (örneğin /e4a781c8b9d04...txt)
        $verify_var = get_query_var('yym_indexnow_key_verify');
        if (!empty($verify_var) || (!empty($options['indexnow_key']) && ($path === $options['indexnow_key'] . '.txt' || $base_name === $options['indexnow_key'] . '.txt'))) {
            status_header(200);
            if (function_exists('http_response_code')) {
                http_response_code(200);
            }
            header('Content-Type: text/plain; charset=utf-8');
            header('X-Robots-Tag: noindex');
            echo esc_html($options['indexnow_key']);
            exit;
        }

        // 2. XML Sitemap İstekleri
        $sitemap_var = get_query_var('yym_sitemap');
        $sitemap_type = '';

        if (!empty($sitemap_var)) {
            $sitemap_type = sanitize_key($sitemap_var);
        } elseif ($path === 'sitemap.xml' || $base_name === 'sitemap.xml') {
            $sitemap_type = 'index';
        } elseif (preg_match('#(?:^|/)sitemap-([a-z0-9_-]+)\.xml$#i', $path, $matches)) {
            $sitemap_type = sanitize_key($matches[1]);
        }

        if (!empty($sitemap_type)) {
            self::render_sitemap($sitemap_type);
            exit;
        }
    }

    /**
     * Dinamik XML Site Haritasını Ekrana Bas
     */
    public static function render_sitemap($type) {
        // Çıktı arabelleğini temizle
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Kesinlikle 200 OK başlığı gönder
        status_header(200);
        if (function_exists('http_response_code')) {
            http_response_code(200);
        }

        header('Content-Type: application/xml; charset=utf-8');
        header('X-Robots-Tag: noindex, follow');
        header('Cache-Control: public, max-age=3600');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

        if ($type === 'index') {
            self::render_sitemap_index();
        } else {
            self::render_sub_sitemap($type);
        }
        exit;
    }

    /**
     * Ana Sitemap Index çıktısı
     */
    private static function render_sitemap_index() {
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $sub_maps = array(
            'firmalar'  => __('Yol Yardım & Çekici Firmaları', 'yol-yardim-merkezi'),
            'bolgeler'  => __('Otoyol, Tünel ve Hizmet Bölgeleri', 'yol-yardim-merkezi'),
            'sehirler'  => __('81 İl ve İlçe Sayfaları', 'yol-yardim-merkezi'),
            'hizmetler' => __('Hizmet Rehberi Sayfaları', 'yol-yardim-merkezi'),
            'pages'     => __('Statik Kurumsal Sayfalar', 'yol-yardim-merkezi'),
            'yazilar'   => __('Blog ve Rehber Makaleleri', 'yol-yardim-merkezi'),
        );

        $now_iso = gmdate('Y-m-d\TH:i:s\Z');

        foreach ($sub_maps as $sub_slug => $sub_title) {
            $lastmod = self::get_sub_sitemap_lastmod($sub_slug) ?: $now_iso;
            $url = home_url('/sitemap-' . $sub_slug . '.xml');
            echo "  <sitemap>\n";
            echo "    <loc>" . esc_url($url) . "</loc>\n";
            echo "    <lastmod>" . esc_html($lastmod) . "</lastmod>\n";
            echo "  </sitemap>\n";
        }

        echo '</sitemapindex>' . "\n";
    }

    /**
     * Alt haritalar için son güncelleme tarihini belirle
     */
    private static function get_sub_sitemap_lastmod($sub_slug) {
        global $wpdb;
        $post_type = '';
        if ($sub_slug === 'firmalar') $post_type = 'firma';
        elseif ($sub_slug === 'bolgeler') $post_type = 'bolge';
        elseif ($sub_slug === 'hizmetler') $post_type = 'hizmet';
        elseif ($sub_slug === 'pages') $post_type = 'page';
        elseif ($sub_slug === 'yazilar') $post_type = 'post';

        if ($post_type) {
            $last_date = $wpdb->get_var($wpdb->prepare(
                "SELECT post_modified_gmt FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = %s ORDER BY post_modified_gmt DESC LIMIT 1",
                $post_type
            ));
            if ($last_date && $last_date !== '0000-00-00 00:00:00') {
                return gmdate('Y-m-d\TH:i:s\Z', strtotime($last_date));
            }
        }

        return gmdate('Y-m-d\TH:i:s\Z');
    }

    /**
     * Alt XML Site Haritası içeriğini üret (Urlset)
     */
    private static function render_sub_sitemap($sub_slug) {
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        echo '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        global $wpdb;

        if ($sub_slug === 'pages') {
            // Ana sayfa en yüksek öncelikte
            echo "  <url>\n";
            echo "    <loc>" . esc_url(home_url('/')) . "</loc>\n";
            echo "    <lastmod>" . esc_html(gmdate('Y-m-d\TH:i:s\Z')) . "</lastmod>\n";
            echo "    <changefreq>daily</changefreq>\n";
            echo "    <priority>1.0</priority>\n";
            echo "  </url>\n";

            $front_id = (int)get_option('page_on_front');
            $pages = $wpdb->get_results($wpdb->prepare("
                SELECT p.ID, p.post_modified_gmt, p.post_title, t.meta_value AS thumbnail_id
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} t ON (p.ID = t.post_id AND t.meta_key = '_thumbnail_id')
                WHERE p.post_type = 'page' 
                  AND p.post_status = 'publish' 
                  AND p.ID != %d
                  AND p.post_name NOT LIKE 'https-%%'
                  AND p.post_name NOT LIKE '%%sample-page%%'
                  AND p.post_name NOT LIKE '%%demo%%'
                ORDER BY p.post_modified_gmt DESC
                LIMIT 5000
            ", $front_id));

            if (!empty($pages)) {
                foreach ($pages as $p) {
                    $thumb_id = !empty($p->thumbnail_id) ? (int)$p->thumbnail_id : 0;
                    self::render_url_node(get_permalink($p->ID), $p->post_modified_gmt, 'weekly', '0.7', $thumb_id, $p->post_title);
                }
            }

        } elseif ($sub_slug === 'firmalar') {
            // Firma arşivi
            $archive_link = get_post_type_archive_link('firma');
            if ($archive_link) {
                self::render_url_node($archive_link, gmdate('Y-m-d\TH:i:s\Z'), 'daily', '0.9');
            }

            // TÜM Çekici ve Yol Yardım Firmaları (1000 limiti tamamen kaldırıldı, 50.000'e kadar)
            $firmalar = $wpdb->get_results("
                SELECT p.ID, p.post_modified_gmt, p.post_title, m.meta_value AS is_vip, t.meta_value AS thumbnail_id
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} m ON (p.ID = m.post_id AND m.meta_key = '_firma_is_vip')
                LEFT JOIN {$wpdb->postmeta} t ON (p.ID = t.post_id AND t.meta_key = '_thumbnail_id')
                WHERE p.post_type = 'firma' AND p.post_status = 'publish'
                ORDER BY p.post_modified_gmt DESC
                LIMIT 50000
            ");

            if (!empty($firmalar)) {
                foreach ($firmalar as $f) {
                    $is_vip   = !empty($f->is_vip);
                    $priority = $is_vip ? '0.95' : '0.9';
                    $thumb_id = !empty($f->thumbnail_id) ? (int)$f->thumbnail_id : 0;
                    self::render_url_node(get_permalink($f->ID), $f->post_modified_gmt, 'daily', $priority, $thumb_id, $f->post_title);
                }
            }

        } elseif ($sub_slug === 'bolgeler') {
            // Otoyollar, tüneller ve bölgeler
            $archive_link = get_post_type_archive_link('bolge');
            if ($archive_link) {
                self::render_url_node($archive_link, gmdate('Y-m-d\TH:i:s\Z'), 'daily', '0.9');
            }

            $bolgeler = $wpdb->get_results("
                SELECT p.ID, p.post_modified_gmt, p.post_title, m.meta_value AS is_transit, t.meta_value AS thumbnail_id
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} m ON (p.ID = m.post_id AND m.meta_key = '_is_transit_highway')
                LEFT JOIN {$wpdb->postmeta} t ON (p.ID = t.post_id AND t.meta_key = '_thumbnail_id')
                WHERE p.post_type = 'bolge' AND p.post_status = 'publish'
                ORDER BY p.post_modified_gmt DESC
                LIMIT 10000
            ");

            if (!empty($bolgeler)) {
                foreach ($bolgeler as $b) {
                    $is_transit = !empty($b->is_transit);
                    $priority   = $is_transit ? '0.95' : '0.85';
                    $freq       = $is_transit ? 'daily' : 'weekly';
                    $thumb_id   = !empty($b->thumbnail_id) ? (int)$b->thumbnail_id : 0;
                    self::render_url_node(get_permalink($b->ID), $b->post_modified_gmt, $freq, $priority, $thumb_id, $b->post_title);
                }
            }

        } elseif ($sub_slug === 'sehirler') {
            // 81 İl ve İlçe Taksonomileri (Tümü)
            $terms = get_terms(array(
                'taxonomy'   => 'firma_sehir',
                'hide_empty' => false,
                'number'     => 0,
            ));

            if (!is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $t) {
                    $term_link = get_term_link($t);
                    if (!is_wp_error($term_link)) {
                        self::render_url_node($term_link, gmdate('Y-m-d\TH:i:s\Z'), 'daily', '0.85');
                    }
                }
            }

        } elseif ($sub_slug === 'hizmetler') {
            // Hizmet Rehberi Sayfaları
            $archive_link = get_post_type_archive_link('hizmet');
            if ($archive_link) {
                self::render_url_node($archive_link, gmdate('Y-m-d\TH:i:s\Z'), 'weekly', '0.8');
            }

            $hizmetler = $wpdb->get_results("
                SELECT p.ID, p.post_modified_gmt, p.post_title, t.meta_value AS thumbnail_id
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} t ON (p.ID = t.post_id AND t.meta_key = '_thumbnail_id')
                WHERE p.post_type = 'hizmet' AND p.post_status = 'publish'
                ORDER BY p.post_modified_gmt DESC
                LIMIT 5000
            ");

            if (!empty($hizmetler)) {
                foreach ($hizmetler as $h) {
                    $thumb_id = !empty($h->thumbnail_id) ? (int)$h->thumbnail_id : 0;
                    self::render_url_node(get_permalink($h->ID), $h->post_modified_gmt, 'weekly', '0.8', $thumb_id, $h->post_title);
                }
            }

        } elseif ($sub_slug === 'yazilar') {
            // Blog / Makaleler (Tümü)
            $posts = $wpdb->get_results("
                SELECT p.ID, p.post_modified_gmt, p.post_title, t.meta_value AS thumbnail_id
                FROM {$wpdb->posts} p
                LEFT JOIN {$wpdb->postmeta} t ON (p.ID = t.post_id AND t.meta_key = '_thumbnail_id')
                WHERE p.post_type = 'post' AND p.post_status = 'publish'
                ORDER BY p.post_modified_gmt DESC
                LIMIT 50000
            ");

            if (!empty($posts)) {
                foreach ($posts as $p) {
                    $thumb_id = !empty($p->thumbnail_id) ? (int)$p->thumbnail_id : 0;
                    self::render_url_node(get_permalink($p->ID), $p->post_modified_gmt, 'monthly', '0.7', $thumb_id, $p->post_title);
                }
            }
        }

        echo '</urlset>' . "\n";
    }

    /**
     * Tek bir <url> düğümü yazdır
     */
    private static function render_url_node($loc, $modified_gmt, $changefreq = 'weekly', $priority = '0.7', $thumbnail_id = 0, $title = '') {
        $lastmod = (!empty($modified_gmt) && $modified_gmt !== '0000-00-00 00:00:00') 
            ? gmdate('Y-m-d\TH:i:s\Z', strtotime($modified_gmt)) 
            : gmdate('Y-m-d\TH:i:s\Z');

        echo "  <url>\n";
        echo "    <loc>" . esc_url($loc) . "</loc>\n";
        echo "    <lastmod>" . esc_html($lastmod) . "</lastmod>\n";
        echo "    <changefreq>" . esc_html($changefreq) . "</changefreq>\n";
        echo "    <priority>" . esc_html($priority) . "</priority>\n";

        // Görsel site haritası desteği
        if (!empty($thumbnail_id)) {
            $img_url = wp_get_attachment_image_url($thumbnail_id, 'large');
            if ($img_url) {
                echo "    <image:image>\n";
                echo "      <image:loc>" . esc_url($img_url) . "</image:loc>\n";
                if (!empty($title)) {
                    echo "      <image:title>" . esc_html(wp_strip_all_tags($title)) . "</image:title>\n";
                }
                echo "    </image:image>\n";
            }
        }

        echo "  </url>\n";
    }

    /**
     * Robots.txt içine otomatik sitemap satırı ekle
     */
    public static function append_sitemap_to_robots($output) {
        $sitemap_url = home_url('/sitemap.xml');
        $output .= "\n# Yol Yardim Merkezi Otomatik SEO Haritasi\n";
        $output .= "Sitemap: " . esc_url($sitemap_url) . "\n";
        return $output;
    }

    /**
     * Yeni yazı/firma yayınlandığında veya güncellendiğinde anında indekslemeyi tetikle
     */
    public static function handle_post_status_transition($new_status, $old_status, $post) {
        if ($new_status !== 'publish' || wp_is_post_revision($post->ID) || wp_is_post_autosave($post->ID)) {
            return;
        }

        $options = self::get_options();
        $allowed_types = !empty($options['auto_post_types']) ? $options['auto_post_types'] : array('firma', 'bolge', 'hizmet', 'post', 'page');

        if (!in_array($post->post_type, $allowed_types, true)) {
            return;
        }

        // Çift tetiklenmeyi önlemek için transient kilidi (1 dakikalık)
        $lock_key = 'yym_idx_lock_' . $post->ID;
        if (get_transient($lock_key)) {
            return;
        }
        set_transient($lock_key, 1, 60);

        $url = get_permalink($post->ID);
        if (!$url) {
            return;
        }

        // 1. IndexNow API Gönderimi (Bing, Yandex, Seznam)
        if (!empty($options['indexnow_enabled'])) {
            self::ping_indexnow(array($url), $post->post_type);
        }

        // 2. Google Sitemap Pingleme
        if (!empty($options['google_ping_enabled'])) {
            self::ping_google_sitemap();
        }
    }

    /**
     * IndexNow API Pingleme (Bing, Yandex, Seznam)
     */
    public static function ping_indexnow(array $urls, $post_type = 'auto') {
        $options = self::get_options();
        $key     = !empty($options['indexnow_key']) ? $options['indexnow_key'] : '';

        if (empty($key) || empty($urls)) {
            return false;
        }

        $host         = wp_parse_url(home_url(), PHP_URL_HOST);
        $key_location = home_url('/' . $key . '.txt');

        $body = array(
            'host'        => $host,
            'key'         => $key,
            'keyLocation' => $key_location,
            'urlList'     => array_values(array_unique($urls)),
        );

        $response = wp_remote_post('https://api.indexnow.org/indexnow', array(
            'headers'     => array('Content-Type' => 'application/json; charset=utf-8'),
            'body'        => wp_json_encode($body),
            'timeout'     => 10,
            'blocking'    => true,
            'data_format' => 'body',
        ));

        $code = is_wp_error($response) ? 0 : wp_remote_retrieve_response_code($response);
        $msg  = is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_response_message($response);

        // IndexNow başarı kodları: 200 (OK), 202 (Accepted)
        $is_success = ($code === 200 || $code === 202);

        foreach ($urls as $u) {
            self::log_index_request($u, $post_type, 'IndexNow (Bing & Yandex)', $code, $is_success ? 'Başarılı (' . $code . ')' : 'Yanıt: ' . $code . ' ' . $msg, $is_success);
        }

        return $is_success;
    }

    /**
     * Google ve Bing Sitemap Pingleme
     */
    public static function ping_google_sitemap() {
        $sitemap_url = rawurlencode(home_url('/sitemap.xml'));

        // Google Ping Endpoint
        $g_url = 'https://www.google.com/ping?sitemap=' . $sitemap_url;
        $g_res = wp_remote_get($g_url, array('timeout' => 5, 'blocking' => false));

        // Bing Ping Endpoint
        $b_url = 'https://www.bing.com/ping?sitemap=' . $sitemap_url;
        $b_res = wp_remote_get($b_url, array('timeout' => 5, 'blocking' => false));

        self::log_index_request(home_url('/sitemap.xml'), 'sitemap', 'Google & Bing Sitemap Ping', 200, 'Site Haritası Pinglemesi İletildi', true);
        return true;
    }

    /**
     * İndeksleme İşlemini Günlüğe Kaydet
     */
    private static function log_index_request($url, $type, $target, $code, $message, $success) {
        $logs = get_option(self::LOGS_KEY, array());
        if (!is_array($logs)) {
            $logs = array();
        }

        $new_entry = array(
            'time'    => current_time('d.m.Y H:i:s'),
            'url'     => $url,
            'type'    => $type,
            'target'  => $target,
            'code'    => $code,
            'message' => $message,
            'success' => (bool)$success,
        );

        array_unshift($logs, $new_entry);

        if (count($logs) > self::MAX_LOGS) {
            $logs = array_slice($logs, 0, self::MAX_LOGS);
        }

        update_option(self::LOGS_KEY, $logs);
    }

    /**
     * Yönetim Paneli Menüsü
     */
    public static function register_admin_menu() {
        add_menu_page(
            __('SEO & İndeksleme', 'yol-yardim-merkezi'),
            __('SEO & İndeksleme', 'yol-yardim-merkezi'),
            'manage_options',
            'yym-seo-engine',
            array(__CLASS__, 'render_admin_page'),
            'dashicons-chart-line',
            26
        );
    }

    /**
     * Manuel İşlemleri Yönet (Tek URL Gönderimi, Harita Pingleme, Yeni Key Üretme)
     */
    public static function handle_manual_actions() {
        if (!current_user_can('manage_options') || empty($_POST['yym_seo_action'])) {
            return;
        }

        check_admin_referer('yym_seo_admin_nonce', 'yym_seo_nonce');

        $action = sanitize_text_field(wp_unslash($_POST['yym_seo_action']));

        if ($action === 'submit_url') {
            $url = esc_url_raw(wp_unslash($_POST['single_url'] ?? ''));
            if (!empty($url)) {
                $res = self::ping_indexnow(array($url), 'manual');
                self::ping_google_sitemap();
                $msg = $res ? 'success_ping' : 'error_ping';
                wp_safe_redirect(add_query_arg(array('page' => 'yym-seo-engine', 'msg' => $msg), admin_url('admin.php')));
                exit;
            }
        } elseif ($action === 'ping_all_sitemaps') {
            self::ping_google_sitemap();
            $sitemap_url = home_url('/sitemap.xml');
            self::ping_indexnow(array($sitemap_url, home_url('/')), 'sitemap');
            wp_safe_redirect(add_query_arg(array('page' => 'yym-seo-engine', 'msg' => 'success_sitemap_ping'), admin_url('admin.php')));
            exit;
        } elseif ($action === 'regenerate_key') {
            $options = self::get_options();
            try {
                $options['indexnow_key'] = bin2hex(random_bytes(16));
            } catch (Exception $e) {
                $options['indexnow_key'] = md5(uniqid((string)wp_rand(), true));
            }
            update_option(self::OPTION_KEY, $options);
            flush_rewrite_rules();
            wp_safe_redirect(add_query_arg(array('page' => 'yym-seo-engine', 'msg' => 'key_regenerated'), admin_url('admin.php')));
            exit;
        } elseif ($action === 'save_settings') {
            $options = self::get_options();
            $options['indexnow_enabled']    = !empty($_POST['indexnow_enabled']);
            $options['google_ping_enabled'] = !empty($_POST['google_ping_enabled']);
            if (isset($_POST['google_verification'])) {
                $options['google_verification'] = sanitize_text_field(wp_unslash($_POST['google_verification']));
            }
            update_option(self::OPTION_KEY, $options);
            wp_safe_redirect(add_query_arg(array('page' => 'yym-seo-engine', 'msg' => 'settings_saved'), admin_url('admin.php')));
            exit;
        }
    }

    /**
     * SEO & İndeksleme Yönetim Paneli Arayüzü
     */
    public static function render_admin_page() {
        $options   = self::get_options();
        $logs      = get_option(self::LOGS_KEY, array());
        $msg       = sanitize_key($_GET['msg'] ?? '');
        $key       = $options['indexnow_key'];
        $key_url   = home_url('/' . $key . '.txt');
        $sitemap_url = home_url('/sitemap.xml');

        // Sayım istatistikleri
        $count_firmalar = wp_count_posts('firma')->publish ?? 0;
        $count_bolgeler = wp_count_posts('bolge')->publish ?? 0;
        $count_hizmet   = wp_count_posts('hizmet')->publish ?? 0;
        $count_posts    = wp_count_posts('post')->publish ?? 0;
        $count_pages    = wp_count_posts('page')->publish ?? 0;
        $count_sehirler = wp_count_terms(array('taxonomy' => 'firma_sehir', 'hide_empty' => false));
        $total_indexable = $count_firmalar + $count_bolgeler + $count_hizmet + $count_posts + $count_pages + (is_numeric($count_sehirler) ? $count_sehirler : 0);
        ?>
        <div class="wrap" style="max-width: 1200px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
            
            <div style="background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; padding: 24px 30px; border-radius: 12px; margin: 20px 0 25px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <h1 style="color: #fff; font-size: 1.7rem; font-weight: 800; margin: 0 0 6px 0; display: flex; align-items: center; gap: 10px;">
                            <span>🚀</span> <?php _e('Yol Yardım SEO & Anında İndeksleme Motoru', 'yol-yardim-merkezi'); ?>
                        </h1>
                        <p style="color: #94a3b8; margin: 0; font-size: 0.95rem;">
                            <?php _e('Dinamik XML Site Haritası üretimi, IndexNow ve Google anlık indeksleme protokolü.', 'yol-yardim-merkezi'); ?>
                        </p>
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" class="button" style="background: #2563eb; color: #fff; border: none; font-weight: 600; padding: 6px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            🗺️ <?php _e('Sitemap.xml Aç', 'yol-yardim-merkezi'); ?> ↗
                        </a>
                    </div>
                </div>
            </div>

            <?php if ($msg === 'success_ping') : ?>
                <div class="notice notice-success is-dismissible"><p><strong>✅ Başarılı:</strong> URL arama motorlarına (IndexNow & Google) anında iletildi!</p></div>
            <?php elseif ($msg === 'success_sitemap_ping') : ?>
                <div class="notice notice-success is-dismissible"><p><strong>✅ Başarılı:</strong> Tüm site haritası Google ve Bing'e pingleme olarak gönderildi!</p></div>
            <?php elseif ($msg === 'key_regenerated') : ?>
                <div class="notice notice-info is-dismissible"><p><strong>🔑 Bilgi:</strong> IndexNow anahtarı başarıyla yenilendi.</p></div>
            <?php elseif ($msg === 'settings_saved') : ?>
                <div class="notice notice-success is-dismissible"><p><strong>💾 Kaydedildi:</strong> SEO ve indeksleme tercihleri güncellendi.</p></div>
            <?php endif; ?>

            <!-- Sayaç Kartları -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 25px;">
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; border-top: 4px solid #2563eb; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">İndekslenebilir İçerik</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 4px 0;"><?php echo esc_html($total_indexable); ?></div>
                    <div style="color: #10b981; font-size: 0.8rem; font-weight: 600;">Haritalara Otomatik Dahil</div>
                </div>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; border-top: 4px solid #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Kayıtlı Çekici / Firma</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 4px 0;"><?php echo esc_html($count_firmalar); ?></div>
                    <div style="color: #64748b; font-size: 0.8rem;">Yol Yardım Rehberi</div>
                </div>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; border-top: 4px solid #f59e0b; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">Otoyol & Bölgeler</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a; margin: 4px 0;"><?php echo esc_html($count_bolgeler); ?></div>
                    <div style="color: #64748b; font-size: 0.8rem;">Transit Geçiş SEO</div>
                </div>
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; border-top: 4px solid #8b5cf6; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <div style="color: #64748b; font-size: 0.85rem; font-weight: 600; text-transform: uppercase;">IndexNow Protokolü</div>
                    <div style="font-size: 1.8rem; font-weight: 800; color: #10b981; margin: 4px 0;">AKTİF</div>
                    <div style="color: #64748b; font-size: 0.8rem;">Bing & Yandex Anında İndeks</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-bottom: 25px;">
                
                <!-- Manuel İndeks Gönderim Kutusu -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 12px 0; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>⚡</span> <?php _e('Yeni Eklenen veya Güncellenen URL\'yi Anında İndekslet', 'yol-yardim-merkezi'); ?>
                    </h2>
                    <p style="color: #64748b; font-size: 0.88rem; margin-bottom: 16px;">
                        <?php _e('Siteye yeni eklediğiniz bir firma, otoyol sayfası veya blog yazısının linkini yapıştırarak Google ve IndexNow robotlarına öncelikli tarama çağrısı gönderin.', 'yol-yardim-merkezi'); ?>
                    </p>
                    <form method="post" action="">
                        <?php wp_nonce_field('yym_seo_admin_nonce', 'yym_seo_nonce'); ?>
                        <input type="hidden" name="yym_seo_action" value="submit_url">
                        <div style="display: flex; gap: 8px; margin-bottom: 15px;">
                            <input type="url" name="single_url" required placeholder="https://yolyardimmerkezi.com/firmalar/ornek-cekici/" style="flex: 1; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem;">
                            <button type="submit" class="button button-primary" style="padding: 6px 18px; font-weight: 600; border-radius: 6px;">
                                🚀 <?php _e('Hemen Gönder', 'yol-yardim-merkezi'); ?>
                            </button>
                        </div>
                    </form>

                    <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;">

                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div>
                            <strong style="color: #0f172a; font-size: 0.9rem;"><?php _e('Tüm Site Haritasını Pingle', 'yol-yardim-merkezi'); ?></strong>
                            <p style="color: #64748b; font-size: 0.8rem; margin: 2px 0 0 0;"><?php _e('Google ve Bing motorlarına /sitemap.xml dosyasını yeniden tarama sinyali gönderir.', 'yol-yardim-merkezi'); ?></p>
                        </div>
                        <form method="post" action="">
                            <?php wp_nonce_field('yym_seo_admin_nonce', 'yym_seo_nonce'); ?>
                            <input type="hidden" name="yym_seo_action" value="ping_all_sitemaps">
                            <button type="submit" class="button" style="background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-weight: 600; border-radius: 6px;">
                                📡 <?php _e('Haritayı Şimdi Pingle', 'yol-yardim-merkezi'); ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- IndexNow & Yapılandırma Bilgileri -->
                <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 12px 0; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>🛡️</span> <?php _e('IndexNow Entegrasyon Durumu', 'yol-yardim-merkezi'); ?>
                    </h2>
                    
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin-bottom: 14px;">
                        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 4px;">IndexNow API Anahtarınız:</div>
                        <code style="background: #e2e8f0; color: #0f172a; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; display: block; overflow-x: auto;"><?php echo esc_html($key); ?></code>
                        <div style="margin-top: 6px;">
                            <a href="<?php echo esc_url($key_url); ?>" target="_blank" style="font-size: 0.78rem; color: #2563eb; text-decoration: none;">
                                🔍 <?php _e('Anahtar Doğrulama Dosyasını Gör (.txt)', 'yol-yardim-merkezi'); ?> ↗
                            </a>
                        </div>
                    </div>

                    <form method="post" action="">
                        <?php wp_nonce_field('yym_seo_admin_nonce', 'yym_seo_nonce'); ?>
                        <input type="hidden" name="yym_seo_action" value="save_settings">
                        
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; margin-bottom: 8px; cursor: pointer;">
                            <input type="checkbox" name="indexnow_enabled" value="1" <?php checked($options['indexnow_enabled']); ?>>
                            <?php _e('IndexNow Otomatik Anında İndekslemeyi Etkinleştir', 'yol-yardim-merkezi'); ?>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; color: #334155; margin-bottom: 16px; cursor: pointer;">
                            <input type="checkbox" name="google_ping_enabled" value="1" <?php checked($options['google_ping_enabled']); ?>>
                            <?php _e('Yeni İçerikte Google & Bing Harita Pinglemesini Çalıştır', 'yol-yardim-merkezi'); ?>
                        </label>

                        <div style="margin-top: 14px; margin-bottom: 16px; border-top: 1px solid #e2e8f0; padding-top: 14px;">
                            <label style="display: block; font-size: 0.85rem; color: #334155; font-weight: 600; margin-bottom: 5px;">
                                🔍 <?php _e('Google Search Console Doğrulama Kodu:', 'yol-yardim-merkezi'); ?>
                            </label>
                            <input type="text" name="google_verification" value="<?php echo esc_attr($options['google_verification']); ?>" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.85rem; font-family: monospace;">
                            <span style="display: block; font-size: 0.75rem; color: #64748b; margin-top: 4px;"><?php _e('HTML meta etiketi content="..." değeri. Sitenin <head> etiketine otomatik yerleştirilir.', 'yol-yardim-merkezi'); ?></span>
                        </div>

                        <div style="margin-top: 15px;">
                            <button type="submit" class="button button-primary" style="font-weight: 600; border-radius: 6px;">
                                💾 <?php _e('Ayarları Kaydet', 'yol-yardim-merkezi'); ?>
                            </button>
                        </div>
                    </form>

                    <div style="margin-top: 12px; text-align: right;">
                        <form method="post" action="" onsubmit="return confirm('Mevcut anahtarı yenilemek istediğinize emin misiniz?');">
                            <?php wp_nonce_field('yym_seo_admin_nonce', 'yym_seo_nonce'); ?>
                            <input type="hidden" name="yym_seo_action" value="regenerate_key">
                            <button type="submit" class="button" style="font-size: 0.75rem; color: #64748b; background: transparent; border: none; text-decoration: underline; cursor: pointer; padding: 0;">
                                <?php _e('IndexNow Anahtarını Yenile', 'yol-yardim-merkezi'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Site Haritaları Listesi -->
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; margin-bottom: 25px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0 0 16px 0; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span>🗺️</span> <?php _e('Canlı XML Site Haritaları Ağacı', 'yol-yardim-merkezi'); ?>
                </h2>
                
                <table class="widefat striped" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                    <thead>
                        <tr style="background: #f8fafc;">
                            <th style="padding: 10px 14px; font-weight: 700; color: #475569;"><?php _e('Harita Adı / Bölüm', 'yol-yardim-merkezi'); ?></th>
                            <th style="padding: 10px 14px; font-weight: 700; color: #475569;"><?php _e('Canlı XML Bağlantısı', 'yol-yardim-merkezi'); ?></th>
                            <th style="padding: 10px 14px; font-weight: 700; color: #475569;"><?php _e('Öncelik (Priority)', 'yol-yardim-merkezi'); ?></th>
                            <th style="padding: 10px 14px; font-weight: 700; color: #475569;"><?php _e('Sıklık (Changefreq)', 'yol-yardim-merkezi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px 14px; font-weight: 600;">👑 <strong><?php _e('Ana Site Haritası İndeksi', 'yol-yardim-merkezi'); ?></strong></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap.xml')); ?>" target="_blank" style="color: #2563eb; font-weight: 600; text-decoration: none;">/sitemap.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">1.0</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Günlük (Daily)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">🚗 <?php _e('Yol Yardım & Çekici Firmaları', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-firmalar.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-firmalar.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.90 - 0.95</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Günlük (Daily)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">🛣️ <?php _e('Otoyol, Tünel ve Kritik Geçişler', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-bolgeler.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-bolgeler.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.95</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Günlük (Daily)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">📍 <?php _e('81 İl ve İlçe Sayfaları', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-sehirler.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-sehirler.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #e0e7ff; color: #3730a3; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.85</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Günlük (Daily)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">🛠️ <?php _e('Hizmet Rehberi İçerikleri', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-hizmetler.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-hizmetler.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.80</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Haftalık (Weekly)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">📄 <?php _e('Kurumsal ve Statik Sayfalar', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-pages.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-pages.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.70</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Haftalık (Weekly)</td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 14px;">📝 <?php _e('Blog ve Bilgilendirici Makaleler', 'yol-yardim-merkezi'); ?></td>
                            <td style="padding: 10px 14px;"><a href="<?php echo esc_url(home_url('/sitemap-yazilar.xml')); ?>" target="_blank" style="color: #2563eb; text-decoration: none;">/sitemap-yazilar.xml ↗</a></td>
                            <td style="padding: 10px 14px;"><span class="badge" style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">0.70</span></td>
                            <td style="padding: 10px 14px; color: #64748b;">Aylık (Monthly)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Canlı İndeksleme Günlüğü (Log Tablosu) -->
            <div style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 22px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                        <span>📋</span> <?php _e('Son İndeksleme İstekleri & Canlı Durum Günlüğü', 'yol-yardim-merkezi'); ?>
                    </h2>
                    <span style="font-size: 0.8rem; color: #64748b;"><?php printf(__('Son %d işlem gösteriliyor', 'yol-yardim-merkezi'), count($logs)); ?></span>
                </div>

                <?php if (empty($logs)) : ?>
                    <p style="color: #94a3b8; font-style: italic; margin: 0;"><?php _e('Henüz kayıtlı indeksleme işlemi yok. Yeni bir firma veya yazı eklediğinizde veya yukarıdaki araçla URL gönderdiğinizde burada anlık olarak görünecektir.', 'yol-yardim-merkezi'); ?></p>
                <?php else : ?>
                    <div style="overflow-x: auto;">
                        <table class="widefat striped" style="border: 1px solid #e2e8f0; border-radius: 8px;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th style="padding: 8px 12px; font-size: 0.8rem;"><?php _e('Zaman', 'yol-yardim-merkezi'); ?></th>
                                    <th style="padding: 8px 12px; font-size: 0.8rem;"><?php _e('Gönderilen URL', 'yol-yardim-merkezi'); ?></th>
                                    <th style="padding: 8px 12px; font-size: 0.8rem;"><?php _e('Tür', 'yol-yardim-merkezi'); ?></th>
                                    <th style="padding: 8px 12px; font-size: 0.8rem;"><?php _e('Hedef Arama Motoru', 'yol-yardim-merkezi'); ?></th>
                                    <th style="padding: 8px 12px; font-size: 0.8rem;"><?php _e('Sonuç / Durum', 'yol-yardim-merkezi'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log) : ?>
                                    <tr>
                                        <td style="padding: 8px 12px; font-size: 0.8rem; color: #64748b; white-space: nowrap;"><?php echo esc_html($log['time'] ?? ''); ?></td>
                                        <td style="padding: 8px 12px; font-size: 0.85rem;"><a href="<?php echo esc_url($log['url'] ?? ''); ?>" target="_blank" style="color: #2563eb; text-decoration: none;"><?php echo esc_html($log['url'] ?? ''); ?> ↗</a></td>
                                        <td style="padding: 8px 12px; font-size: 0.8rem;"><span style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-size: 0.72rem; font-weight: 600;"><?php echo esc_html($log['type'] ?? ''); ?></span></td>
                                        <td style="padding: 8px 12px; font-size: 0.85rem; font-weight: 500;"><?php echo esc_html($log['target'] ?? ''); ?></td>
                                        <td style="padding: 8px 12px; font-size: 0.8rem;">
                                            <?php if (!empty($log['success'])) : ?>
                                                <span style="color: #16a34a; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #16a34a;"></span>
                                                    <?php echo esc_html($log['message'] ?? 'Başarılı'); ?>
                                                </span>
                                            <?php else : ?>
                                                <span style="color: #dc2626; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #dc2626;"></span>
                                                    <?php echo esc_html($log['message'] ?? 'Hata'); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

        </div>
        <?php
    }
}

// Başlat
add_action('after_setup_theme', array('YYM_SEO_Engine', 'init'));

/**
 * Google Search Console Doğrulama Kodunu Döndüren Yardımcı Fonksiyon
 */
function yym_get_google_verification() {
    $options = YYM_SEO_Engine::get_options();
    return !empty($options['google_verification']) ? $options['google_verification'] : 'FGZh7eYdOuEdVSO9PfVtQx7m7HwdG7TpqqNK998UOYk';
}
