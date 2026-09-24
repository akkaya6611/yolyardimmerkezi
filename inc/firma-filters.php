<?php
if (!defined('ABSPATH')) exit;

function mis360_filter_value($key) {
    return isset($_GET[$key]) && is_string($_GET[$key]) ? sanitize_text_field(wp_unslash($_GET[$key])) : '';
}

function mis360_category_canonical($slug) {
    if (in_array($slug, array('lastik','oto-lastik','mobil-lastik','mobil-lastikci'), true)) return 'oto-lastik';
    return in_array($slug, array('yol-yardim','oto-kurtarma','cekici','oto-cekici','kurtarma'), true) ? 'yol-yardim' : $slug;
}

function mis360_category_display_names($names) {
    return array_values(array_unique(array_map(function($name) {
         $canonical = mis360_category_canonical(sanitize_title($name));
        if ($canonical === 'oto-lastik') return 'Oto Lastik';
        return $canonical === 'yol-yardim' ? 'Yol Yardım & Oto Kurtarma' : $name;
    }, $names)));
}

function mis360_category_slugs($category) {
    if (mis360_category_canonical($category) === 'oto-lastik') return array('lastik','oto-lastik','mobil-lastik','mobil-lastikci');
    if (mis360_category_canonical($category) === 'yol-yardim') return array('yol-yardim','oto-kurtarma','cekici','oto-cekici','kurtarma');
    // Older service links use short names; imports use their source category slugs.
    $aliases = array(
        'cekici'=>array('cekici','oto-cekici','oto-kurtarma'),
        'kurtarma'=>array('kurtarma','oto-kurtarma'),
        'lastik'=>array('lastik','mobil-lastikci','mobil-lastik','oto-lastik'),
        'aku'=>array('aku','aku-takviye','aku-takviye-satis'),
        'cilingir'=>array('cilingir','oto-cilingir'),
    );
    return $aliases[$category] ?? array($category);
}

function mis360_category_options($selected = '') {
    $selected = mis360_category_canonical($selected);
    $labels = array('cekici'=>'Oto Çekici & Kurtarıcı','kurtarma'=>'Oto Kurtarma','aku'=>'Akü Takviye & Satış','lastik'=>'Lastik Hizmetleri','motosiklet'=>'Motosiklet Taşıma','agir-vasita'=>'Ağır Vasıta & Vinç','cilingir'=>'Oto Çilingir','yakit'=>'Yakıt Takviyesi','yol-yardim'=>'Yol Yardım & Oto Kurtarma','mobil-lastikci'=>'Mobil Lastikçi','oto-lastik'=>'Oto Lastik','oto-kurtarma'=>'Oto Kurtarma');
    $terms = get_terms(array('taxonomy'=>'firma_kategori','hide_empty'=>true,'orderby'=>'name','order'=>'ASC'));
    $options = array();
    if (!is_wp_error($terms)) foreach ($terms as $term) {
        $slug = mis360_category_canonical($term->slug);
        $options[$slug] = $labels[$slug] ?? $term->name;
    }
    if ($selected !== '' && !isset($options[$selected])) $options[$selected] = $labels[$selected] ?? $selected;
    foreach ($options as $slug=>$label) {
        echo '<option value="'.esc_attr($slug).'"'.selected($selected,$slug,false).'>'.esc_html($label).'</option>';
    }
}

function mis360_filter_firma_archive_query($query) {
    if (is_admin() || !$query->is_main_query() || !($query->is_post_type_archive('firma') || $query->is_tax(array('firma_kategori','firma_sehir')))) return;
    $query->set('posts_per_page',12);
    // İlk eklenen firmaların en başta görünmesi için (ASC sıralama)
    $query->set('orderby', 'date');
    $query->set('order', 'ASC');
    $keyword = mis360_filter_value('keyword');
    $category = mis360_filter_value('category');
    $location = mis360_filter_value('location');
    $city = mis360_filter_value('city');
    $district = mis360_filter_value('district');
    $exact_location = (array)$query->get('meta_query');
    foreach (array('_firma_city'=>$city, '_firma_district'=>$district) as $key=>$value) {
        if ($value !== '') $exact_location[] = array('key'=>$key,'value'=>$value,'compare'=>'=');
    }
    $query->set('meta_query',$exact_location);
    if ($keyword !== '') $query->set('s',$keyword);
    if ($category !== '') {
        $tax_query = (array)$query->get('tax_query');
        $tax_query[] = array('taxonomy'=>'firma_kategori','field'=>'slug','terms'=>mis360_category_slugs($category),'operator'=>'IN');
        $query->set('tax_query',$tax_query);
    }
    if ($location !== '') {
        $meta_query = (array)$query->get('meta_query');
        $location_query = array('relation'=>'OR');
        foreach (array('_firma_city','_firma_district','_firma_address') as $key) $location_query[] = array('key'=>$key,'value'=>$location,'compare'=>'LIKE');
        $meta_query[] = $location_query;
        $query->set('meta_query',$meta_query);
    }
}
add_action('pre_get_posts','mis360_filter_firma_archive_query');

function mis360_firma_locations() {
    // Cache location data for 12 hours using a transient
    $cache_key = 'mis360_firma_locations';
    $cached = get_transient($cache_key);
    if (false !== $cached) {
        return $cached;
    }
    static $locations = null;
    if ($locations !== null) {
        return $locations;
    }
    
    global $wpdb;
    $rows = $wpdb->get_results("SELECT DISTINCT c.meta_value AS city, d.meta_value AS district
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} c ON c.post_id=p.ID AND c.meta_key='_firma_city'
        LEFT JOIN {$wpdb->postmeta} d ON d.post_id=p.ID AND d.meta_key='_firma_district'
        WHERE p.post_type='firma' AND p.post_status='publish' ORDER BY c.meta_value,d.meta_value", ARRAY_A);
    $locations = array();
    foreach ($rows as $row) {
        $city = trim((string)$row['city']); $district = trim((string)$row['district']);
        if ($city === '' || in_array(strtolower($city),array('nan','none','null'),true)) continue;
        if (!isset($locations[$city])) $locations[$city] = array();
        if ($district !== '' && !in_array(strtolower($district),array('nan','none','null'),true) && !in_array($district,$locations[$city],true)) $locations[$city][] = $district;
    }
    return $locations;
}

function mis360_location_fields() {
    $locations = mis360_firma_locations();
    $city = mis360_filter_value('city'); $district = mis360_filter_value('district');
    $districts = $locations[$city] ?? array();
    // Preserve submitted selections even when no currently published firm matches.
    if ($city !== '' && !isset($locations[$city])) $locations[$city] = array();
    if ($district !== '' && !in_array($district,$districts,true)) $districts[] = $district;
    ?>
    <div class="mis360-location-fields" data-locations="<?php echo esc_attr(wp_json_encode($locations,JSON_UNESCAPED_UNICODE)); ?>">
        <select name="city" aria-label="İl seçin" class="mis360-city-select">
            <option value="">Tüm iller</option>
            <?php foreach ($locations as $name=>$items) : ?>
                <option value="<?php echo esc_attr($name); ?>" <?php selected($city,$name); ?>><?php echo esc_html($name); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="district" aria-label="İlçe seçin" class="mis360-district-select" <?php disabled($city === '' && $district === ''); ?>>
            <option value=""><?php echo $city !== '' ? 'Tüm ilçeler' : 'Önce il seçin'; ?></option>
            <?php foreach ($districts as $name) : ?>
                <option value="<?php echo esc_attr($name); ?>" <?php selected($district,$name); ?>><?php echo esc_html($name); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (mis360_filter_value('location') !== '') : ?>
            <input name="location" aria-label="Konum araması" value="<?php echo esc_attr(mis360_filter_value('location')); ?>" class="mis360-legacy-location">
        <?php endif; ?>
    </div>
    <?php
}

add_action('wp_enqueue_scripts',function(){
    $dir = get_template_directory(); $uri = get_template_directory_uri();
    wp_enqueue_script('mis360-district-filter',$uri.'/assets/js/firma-locations.js',array(),filemtime($dir.'/assets/js/firma-locations.js'),true);
    wp_enqueue_style('mis360-district-filter',$uri.'/assets/css/firma-locations.css',array(),filemtime($dir.'/assets/css/firma-locations.css'));
},30);

/** Keep the original mobile classification while grouping tyre service filters. */
function mis360_mobile_tyre_notice($post_id) {
    if (!has_term(array('mobil-lastikci','mobil-lastik'), 'firma_kategori', $post_id)) return;
    ?>
    <div class="mis360-mobile-tyre-notice" style="padding:14px 16px;margin:12px 0 18px;border:1px solid #f5cc82;border-radius:12px;background:#fff7e8;color:#68430c;font-size:14px;line-height:1.65">
        <strong style="display:block"><span aria-hidden="true">🛞</span> Bu firma mobil lastikçidir.</strong>
        <span>Yolda kaldığınızda kendisiyle iletişime geçip konumunuza çağırabilirsiniz.</span>
    </div>
    <?php
}


// VIP Firmaları (Öne Çıkan Nöbetçi Liderleri) arama ve arşiv sonuçlarında en üstte listele
add_filter('the_posts', function($posts, $query) {
    if (is_admin() || !$query->is_main_query() || !($query->is_post_type_archive('firma') || $query->is_tax(array('firma_kategori','firma_sehir')))) {
        return $posts;
    }
    if (!empty($posts) && is_array($posts)) {
        usort($posts, function($a, $b) {
            $vip_a = (int) get_post_meta($a->ID, '_firma_is_vip', true);
            $vip_b = (int) get_post_meta($b->ID, '_firma_is_vip', true);
            if ($vip_a !== $vip_b) {
                return $vip_b - $vip_a;
            }
            return strtotime($a->post_date) - strtotime($b->post_date);
        });
    }
    return $posts;
}, 10, 2);
