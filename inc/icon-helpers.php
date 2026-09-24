<?php
/**
 * Yol Yardım Merkezi — SVG İkon Sistemi Yardımcı Fonksiyonları
 *
 * Kullanım:
 *   yym_icon('tow-truck', 'services')          → inline SVG
 *   yym_icon('location-pin', 'location', 'yym-icon-lg')
 *   yym_icon('check', 'ui', 'yym-icon-sm yym-icon-orange')
 *
 * Flaticon'dan indirilen SVG'yi assets/icons/<kategori>/<isim>.svg
 * yoluna kaydetmek yeterlidir — kod değişmez.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Belirtilen ikon dosyasını inline SVG olarak döndürür.
 *
 * @param  string $name       İkon dosya adı (uzantısız, örn: 'tow-truck')
 * @param  string $category   Klasör adı: services | trust | location | vehicles | ui
 * @param  string $extra_class Ek CSS sınıfı
 * @param  array  $attrs       Ek SVG nitelikleri ['aria-label' => '...']
 * @return string             İnline SVG HTML veya boş string
 */
function yym_icon(string $name, string $category = 'ui', string $extra_class = '', array $attrs = []): string {
    if ($name === 'tow-truck' && $category === 'services') {
        return '<img src="' . esc_url(get_template_directory_uri() . '/assets/images/brand-tow-icon.png') . '" class="' . esc_attr('yym-icon yym-icon--tow-truck ' . $extra_class) . '" alt="' . esc_attr($attrs['aria-label'] ?? '') . '" width="358" height="342" style="object-fit:contain;background:#fff;border-radius:50%;padding:2px;box-sizing:border-box;vertical-align:middle;flex-shrink:0"' . (empty($attrs['aria-label']) ? ' aria-hidden="true"' : '') . '>';
    }
    $base_path = get_template_directory() . '/assets/icons/';
    $file_path = $base_path . sanitize_file_name($category) . '/' . sanitize_file_name($name) . '.svg';

    if (!file_exists($file_path)) {
        // Geliştirme ortamında eksik ikon uyarısı (sadece admin için)
        if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('manage_options')) {
            return '<span class="yym-icon-missing" title="Missing icon: ' . esc_attr($category . '/' . $name) . '">[?]</span>';
        }
        return '';
    }

    $svg_content = file_get_contents($file_path);

    // Mevcut class niteliğini tespit et veya yeni ekle
    $css_classes = trim('yym-icon yym-icon--' . $name . ' ' . $extra_class);

    // SVG açılış etiketine class, role ve aria-hidden ekle
    $aria_label = $attrs['aria-label'] ?? '';
    $aria_part  = $aria_label
        ? ' role="img" aria-label="' . esc_attr($aria_label) . '"'
        : ' aria-hidden="true" focusable="false"';

    // Extract opening <svg ...> tag
    if (preg_match('/<svg([^>]*?)>/i', $svg_content, $matches)) {
        $inner_attrs = $matches[1];
        
        // EYer zaten class niteliYi varsa, iine ekle
        if (preg_match('/class="([^"]*)"/i', $inner_attrs, $class_match)) {
            $merged_classes = trim($class_match[1] . ' ' . $css_classes);
            $new_attrs = preg_replace('/class="[^"]*"/i', 'class="' . esc_attr($merged_classes) . '"', $inner_attrs);
            $new_attrs .= $aria_part;
        } else {
            // Class yoksa yeni ekle
            $new_attrs = $inner_attrs . ' class="' . esc_attr($css_classes) . '"' . $aria_part;
        }
        
        $svg_content = str_replace($matches[0], '<svg' . $new_attrs . '>', $svg_content);
    }

    return $svg_content;
}

/**
 * Ikon dosyasının var olup olmadığını kontrol eder.
 */
function yym_icon_exists(string $name, string $category = 'ui'): bool {
    $file_path = get_template_directory() . '/assets/icons/'
        . sanitize_file_name($category) . '/'
        . sanitize_file_name($name) . '.svg';
    return file_exists($file_path);
}

/**
 * Bir kategori klasöründeki tüm ikon isimlerini listeler.
 */
function yym_get_icons(string $category): array {
    $dir = get_template_directory() . '/assets/icons/' . sanitize_file_name($category) . '/';
    if (!is_dir($dir)) {
        return [];
    }
    $files = glob($dir . '*.svg');
    return array_map(fn($f) => basename($f, '.svg'), $files);
}

/** Shared tow-truck symbol for service labels and site calls to action. */
function mis360_tow_symbol() {
    return str_replace('style="', 'style="width:1.4em;height:1.4em;min-width:24px;min-height:24px;', yym_icon('tow-truck', 'services'));
}
