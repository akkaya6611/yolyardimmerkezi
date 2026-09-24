<?php
/**
 * Otomatik GitHub Tema Güncelleyici (Self-Hosted Theme Updater)
 * 
 * Bu dosya, temanın GitHub reposunu (akkaya6611/yolyardimmerkezi) düzenli olarak
 * kontrol etmesini, yeni bir sürüm (versiyon) yüklendiğinde WordPress admin panelinde
 * standart "Yeni Güncelleme Mevcut - Şimdi Güncelle" bildirimini çıkartmasını ve
 * tek tıkla doğrudan GitHub'dan güncellenmesini sağlar.
 */

if (!defined('ABSPATH')) {
    exit;
}

class YYM_Theme_GitHub_Updater {

    private $theme_slug = 'mis-360-yolyardim-v1';
    private $github_repo = 'akkaya6611/yolyardimmerkezi';
    private $github_branch = 'main';
    private $transient_key = 'yym_github_theme_update';

    public function __construct() {
        // Tema güncellemeleri kontrol transient hook'ları
        add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_update'));
        add_filter('site_transient_update_themes', array($this, 'check_for_update'));

        // GitHub zip dosyasının çıkartıldığı klasör adını düzeltme (örn: yolyardimmerkezi-main -> mis-360-yolyardim-v1)
        add_filter('upgrader_source_selection', array($this, 'fix_extracted_folder_name'), 10, 4);

        // Güncelleme tamamlandıktan sonra transient cache'i temizleme
        add_action('upgrader_process_complete', array($this, 'purge_cache_after_update'), 10, 2);

        // Tema detayları modalı (View version details)
        add_filter('themes_api', array($this, 'theme_popup_details'), 20, 3);
    }

    /**
     * GitHub'daki güncel versiyon bilgilerini çeker
     */
    private function get_remote_version_info() {
        // Cache kontrolü (3 saatlik önbellek)
        $cached_info = get_site_transient($this->transient_key);
        
        // Eğer kullanıcı admin güncellemeler sayfasında "Tekrar kontrol et" dediyse cache'i yoksay
        $force_check = isset($_GET['force-check']) && $_GET['force-check'] == '1';

        if ($cached_info !== false && !$force_check) {
            return $cached_info;
        }

        // GitHub'dan style.css dosyasını oku
        $raw_url = sprintf('https://raw.githubusercontent.com/%s/%s/style.css', $this->github_repo, $this->github_branch);
        $response = wp_remote_get($raw_url, array(
            'timeout'    => 10,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url(),
            'headers'    => array(
                'Accept' => 'text/plain',
            )
        ));

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return false;
        }

        $remote_style = wp_remote_retrieve_body($response);

        // Style.css içinden Version başlığını regex ile al
        if (preg_match('/^[ \t/*#@]*Version:(.*)$/mi', $remote_style, $matches)) {
            $remote_version = trim($matches[1]);
        } else {
            return false;
        }

        // Detay bilgileri
        $info = array(
            'version'     => $remote_version,
            'package'     => sprintf('https://github.com/%s/archive/refs/heads/%s.zip', $this->github_repo, $this->github_branch),
            'url'         => sprintf('https://github.com/%s', $this->github_repo),
            'last_check'  => time(),
        );

        // 3 saat önbelleğe al
        set_site_transient($this->transient_key, $info, 3 * HOUR_IN_SECONDS);

        return $info;
    }

    /**
     * WordPress güncelleme listesine temanın durumunu ekler
     */
    public function check_for_update($transient) {
        if (!is_object($transient)) {
            $transient = new stdClass();
        }

        $theme = wp_get_theme($this->theme_slug);
        if (!$theme->exists()) {
            return $transient;
        }

        $current_version = $theme->get('Version');
        $remote_info = $this->get_remote_version_info();

        if ($remote_info && !empty($remote_info['version'])) {
            // Eğer GitHub'daki sürüm yerel sürümden daha büyükse güncelleme listesine ekle
            if (version_compare($remote_info['version'], $current_version, '>')) {
                $transient->response[$this->theme_slug] = array(
                    'theme'       => $this->theme_slug,
                    'new_version' => $remote_info['version'],
                    'url'         => $remote_info['url'],
                    'package'     => $remote_info['package'],
                );
            } else {
                // Güncelleme yoksa no_update listesine ekle
                $transient->no_update[$this->theme_slug] = array(
                    'theme'       => $this->theme_slug,
                    'new_version' => $remote_info['version'],
                    'url'         => $remote_info['url'],
                    'package'     => $remote_info['package'],
                );
            }
        }

        return $transient;
    }

    /**
     * GitHub zip indirildiğinde klasör adı 'yolyardimmerkezi-main' olur.
     * WordPress'in temanın üzerine sorunsuz yazabilmesi için klasör adını tema slug'ı ile eşitler.
     */
    public function fix_extracted_folder_name($source, $remote_source, $upgrader, $hook_extra = null) {
        global $wp_filesystem;

        if (!isset($hook_extra['theme']) || $hook_extra['theme'] !== $this->theme_slug) {
            return $source;
        }

        $correct_source = trailingslashit($remote_source) . $this->theme_slug . '/';

        if ($source !== $correct_source) {
            if ($wp_filesystem->move($source, $correct_source)) {
                return $correct_source;
            }
        }

        return $source;
    }

    /**
     * Güncelleme modal penceresi tıklandığında tema bilgilerini göster
     */
    public function theme_popup_details($result, $action, $args) {
        if ($action !== 'theme_information' || !isset($args->slug) || $args->slug !== $this->theme_slug) {
            return $result;
        }

        $theme = wp_get_theme($this->theme_slug);
        $remote_info = $this->get_remote_version_info();

        return (object) array(
            'name'          => $theme->get('Name'),
            'slug'          => $this->theme_slug,
            'version'       => $remote_info ? $remote_info['version'] : $theme->get('Version'),
            'author'        => $theme->get('Author'),
            'homepage'      => sprintf('https://github.com/%s', $this->github_repo),
            'download_link' => sprintf('https://github.com/%s/archive/refs/heads/%s.zip', $this->github_repo, $this->github_branch),
            'sections'      => array(
                'description' => $theme->get('Description'),
                'changelog'   => 'GitHub deposu üzerinden yapılan en güncel commit ve değişiklikler.',
            )
        );
    }

    /**
     * Güncelleme tamamlandıktan sonra transient'i temizler
     */
    public function purge_cache_after_update($upgrader, $options) {
        if (isset($options['action']) && $options['action'] === 'update' && isset($options['type']) && $options['type'] === 'theme') {
            delete_site_transient($this->transient_key);
        }
    }
}

// Başlat
new YYM_Theme_GitHub_Updater();
