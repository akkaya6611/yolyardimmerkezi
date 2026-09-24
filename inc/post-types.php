<?php
/**
 * Yol Yardım Merkezi - Özel Yazı Tipleri ve Taksonomiler (Directory / Rehber Sistemi)
 * Tıpkı ototamircibul.com.tr mantığıyla Yol Yardım & Çekici Firmaları CPT
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

function yym_register_custom_post_types() {
    // ==========================================
    // 1. FİRMALAR CPT (Yol Yardım & Çekici Rehberi)
    // ==========================================
    $firma_labels = array(
        'name'               => __('Yol Yardım Firmaları', 'yol-yardim-merkezi'),
        'singular_name'      => __('Firma', 'yol-yardim-merkezi'),
        'menu_name'          => __('Firmalar & Çekiciler', 'yol-yardim-merkezi'),
        'all_items'          => __('Tüm Firmalar', 'yol-yardim-merkezi'),
        'add_new'            => __('Yeni Firma Ekle', 'yol-yardim-merkezi'),
        'add_new_item'       => __('Yeni Firma Ekle', 'yol-yardim-merkezi'),
        'edit_item'          => __('Firmayı Düzenle', 'yol-yardim-merkezi'),
        'new_item'           => __('Yeni Firma', 'yol-yardim-merkezi'),
        'view_item'          => __('Firmayı Görüntüle', 'yol-yardim-merkezi'),
        'search_items'       => __('Firma Ara', 'yol-yardim-merkezi'),
        'not_found'          => __('Firma bulunamadı', 'yol-yardim-merkezi'),
        'not_found_in_trash' => __('Çöp kutusunda firma yok', 'yol-yardim-merkezi'),
    );

    $firma_args = array(
        'labels'             => $firma_labels,
        'public'             => true,
        'has_archive'        => 'firmalar',
        'publicly_queryable' => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'firmalar', 'with_front' => false),
        'capability_type'    => 'post',
        'hierarchical'       => false,
        'menu_icon'          => get_template_directory_uri() . '/assets/images/brand-tow-icon.png',
        'menu_position'      => 5,
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('firma', $firma_args);

    // ==========================================
    // 2. FİRMA HİZMET KATEGORİSİ (Taxonomy)
    // ==========================================
    $cat_labels = array(
        'name'          => __('Hizmet Türleri', 'yol-yardim-merkezi'),
        'singular_name' => __('Hizmet Türü', 'yol-yardim-merkezi'),
        'menu_name'     => __('Hizmet Türleri', 'yol-yardim-merkezi'),
        'all_items'     => __('Tüm Hizmet Türleri', 'yol-yardim-merkezi'),
        'add_new_item'  => __('Yeni Hizmet Türü Ekle', 'yol-yardim-merkezi'),
    );
    register_taxonomy('firma_kategori', array('firma'), array(
        'labels'            => $cat_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'hizmet-kategori'),
    ));

    // ==========================================
    // 3. FİRMA ŞEHİRLERİ (81 İl Taxonomy)
    // ==========================================
    $city_labels = array(
        'name'          => __('İller (Şehirler)', 'yol-yardim-merkezi'),
        'singular_name' => __('Şehir', 'yol-yardim-merkezi'),
        'menu_name'     => __('İller (81 İl)', 'yol-yardim-merkezi'),
        'all_items'     => __('Tüm Şehirler', 'yol-yardim-merkezi'),
        'add_new_item'  => __('Yeni Şehir Ekle', 'yol-yardim-merkezi'),
    );
    register_taxonomy('firma_sehir', array('firma'), array(
        'labels'            => $city_labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'sehir'),
    ));

    // ==========================================
    // 4. Hizmetler CPT (Bilgilendirici İçerikler)
    // ==========================================
    $hizmet_labels = array(
        'name'               => __('Hizmet Rehberi', 'yol-yardim-merkezi'),
        'singular_name'      => __('Hizmet', 'yol-yardim-merkezi'),
        'menu_name'          => __('Hizmet Rehberi', 'yol-yardim-merkezi'),
        'all_items'          => __('Tüm Hizmetler', 'yol-yardim-merkezi'),
        'add_new'            => __('Yeni Hizmet Ekle', 'yol-yardim-merkezi'),
    );
    register_post_type('hizmet', array(
        'labels'             => $hizmet_labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'hizmetlerimiz', 'with_front' => false),
        'menu_icon'          => 'dashicons-car',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    ));

    // ==========================================
    // 5. Hizmet Bölgeleri CPT (Local SEO)
    // ==========================================
    $bolge_labels = array(
        'name'               => __('Hizmet Bölgeleri', 'yol-yardim-merkezi'),
        'singular_name'      => __('Bölge', 'yol-yardim-merkezi'),
        'menu_name'          => __('Bölgeler (SEO)', 'yol-yardim-merkezi'),
        'all_items'          => __('Tüm Bölgeler', 'yol-yardim-merkezi'),
    );
    register_post_type('bolge', array(
        'labels'             => $bolge_labels,
        'public'             => true,
        'has_archive'        => true,
        'rewrite'            => array('slug' => 'bolgeler', 'with_front' => false),
        'menu_icon'          => 'dashicons-location',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'       => true,
    ));
}
add_action('init', 'yym_register_custom_post_types');

// =========================================================================
// FİRMA İLETİŞİM & BİLGİ METABOX'I (Yol Yardım Firmasının Kendi İletişimi)
// =========================================================================
function yym_add_firma_meta_boxes() {
    add_meta_box(
        'yym_firma_details',
        __('📞 Firma İletişim & Donanım Bilgileri (Ziyaretçinin Ulaşacağı Bilgiler)', 'yol-yardim-merkezi'),
        'yym_render_firma_meta_box',
        'firma',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'yym_add_firma_meta_boxes');

function yym_render_firma_meta_box($post) {
    wp_nonce_field('yym_save_firma_meta', 'yym_firma_nonce');

    $phone     = get_post_meta($post->ID, '_firma_phone', true);
    $whatsapp  = get_post_meta($post->ID, '_firma_whatsapp', true);
    $address   = get_post_meta($post->ID, '_firma_address', true);
    $city      = get_post_meta($post->ID, '_firma_city', true);
    $district  = get_post_meta($post->ID, '_firma_district', true);
    $eta       = get_post_meta($post->ID, '_firma_eta', true);
    $equipment = get_post_meta($post->ID, '_firma_equipment', true);
    $price     = get_post_meta($post->ID, '_firma_price', true);
    $badge     = get_post_meta($post->ID, '_firma_badge', true);
    $rating    = get_post_meta($post->ID, '_firma_rating', true);
    $rating_cnt= get_post_meta($post->ID, '_firma_review_count', true);
    $is_verified   = get_post_meta($post->ID, '_firma_is_verified', true);
    $license_type  = get_post_meta($post->ID, '_firma_license_type', true);
    $has_insurance = get_post_meta($post->ID, '_firma_has_insurance', true);
    $is_vip        = get_post_meta($post->ID, '_firma_is_vip', true);
    ?>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 10px 0;">
        <p>
            <label><strong>📞 Firmanın Doğrudan Telefonu:</strong></label><br>
            <input type="text" name="_firma_phone" value="<?php echo esc_attr($phone); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: 0542 123 45 67">
            <small>Ziyaretçi "Hemen Ara" butonuna bastığında bu numara aranır.</small>
        </p>
        <p>
            <label><strong>💬 Firmanın Doğrudan WhatsApp Numarası:</strong></label><br>
            <input type="text" name="_firma_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: 905421234567">
            <small>Başında + olmadan ülke koduyla. Ziyaretçi WhatsApp ile konum gönderdiğinde buraya gider.</small>
        </p>
        <p>
            <label><strong>📍 Firmanın Bulunduğu İl:</strong></label><br>
            <input type="text" name="_firma_city" value="<?php echo esc_attr($city); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: Kayseri, İstanbul, Ankara">
        </p>
        <p>
            <label><strong>📍 İlçe / Bölge:</strong></label><br>
            <input type="text" name="_firma_district" value="<?php echo esc_attr($district); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: Melikgazi & Kocasinan">
        </p>
        <p style="grid-column: span 2;">
            <label><strong>🏢 Firmanın Tam Adresi:</strong></label><br>
            <input type="text" name="_firma_address" value="<?php echo esc_attr($address); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: Yeni Sanayi Mah. 12. Cad. No:8, Kocasinan / Kayseri">
            <small>Google Haritalar Yol Tarifi butonu için kullanılır.</small>
        </p>
        <p>
            <label><strong>⚡ Ortalama Varış Süresi:</strong></label><br>
            <input type="text" name="_firma_eta" value="<?php echo esc_attr($eta ? $eta : '15 Dk Varış'); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: 15 Dk Varış">
        </p>
        <p>
            <label><strong>🚚 Araç / Donanım Türü:</strong></label><br>
            <input type="text" name="_firma_equipment" value="<?php echo esc_attr($equipment ? $equipment : 'Kayar Kasa, Kaskolu'); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: Kayar Kasa, Vinç, Çift Katlı">
        </p>
        <p>
            <label><strong>💰 Fiyat / Tarife Bilgisi:</strong></label><br>
            <input type="text" name="_firma_price" value="<?php echo esc_attr($price ? $price : 'Sabit Fiyat Garantisi'); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: Sabit Fiyat Garantisi">
        </p>
        <p>
            <label><strong>🛡️ Rozet Metni:</strong></label><br>
            <input type="text" name="_firma_badge" value="<?php echo esc_attr($badge ? $badge : '7/24 NÖBETÇİ'); ?>" style="width: 100%; padding: 8px;" placeholder="Örn: 7/24 NÖBETÇİ veya ONAYLI FİRMA">
        </p>
        <p>
            <label><strong>⭐ Müşteri Puanı (1-5):</strong></label><br>
            <input type="text" name="_firma_rating" value="<?php echo esc_attr($rating ? $rating : '4.9'); ?>" style="width: 100%; padding: 8px;" placeholder="4.9">
        </p>
        <p>
            <label><strong>📝 Yorum Sayısı:</strong></label><br>
            <input type="text" name="_firma_review_count" value="<?php echo esc_attr($rating_cnt ? $rating_cnt : '340'); ?>" style="width: 100%; padding: 8px;" placeholder="340">
        </p>
    </div>

    <!-- 🛡️ GÜVENLİK, LİSANS VE VIP VİTRİN AYARLARI -->
    <div style="background: #F8FAFC; border: 1.5px solid #CBD5E1; border-radius: 12px; padding: 18px; margin-top: 14px;">
        <h4 style="margin: 0 0 14px 0; color: #0F172A; font-size: 15px;">🛡️ Kurumsal Güven, K1/K2 Yetki Belgesi ve VIP Vitrin</h4>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="background: #ECFDF5; border: 1px solid #10B981; border-radius: 10px; padding: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: bold; color: #065F46; cursor: pointer;">
                    <input type="checkbox" name="_firma_is_verified" value="1" <?php checked($is_verified, '1'); ?>>
                    🛡️ Doğrulanmış Lisanslı Esnaf (K1/K2 Onaylı)
                </label>
                <p style="margin: 6px 0 10px 24px; font-size: 12px; color: #047857;">
                    İşaretlenirse kartlarda yeşil kalkanlı <strong>"🛡️ K1/K2 Belgeli & Sigortalı"</strong> rozeti çıkar. Korsan çekicilere karşı tüketici güvenini artırır.
                </p>
                <div style="margin-left: 24px;">
                    <label style="font-size: 12px; font-weight: 600; color: #065F46;">Yetki Belgesi Türü:</label>
                    <select name="_firma_license_type" style="width: 100%; padding: 6px; margin-top: 4px; border-radius: 6px;">
                        <option value="K1 Yetki Belgeli Nakliyatçı" <?php selected($license_type, 'K1 Yetki Belgeli Nakliyatçı'); ?>>K1 Karayolu Taşıma Yetki Belgesi</option>
                        <option value="K2 Hususi Taşıma Belgeli" <?php selected($license_type, 'K2 Hususi Taşıma Belgeli'); ?>>K2 Taşıma Yetki Belgesi</option>
                        <option value="Vergi Levhalı Resmi Esnaf" <?php selected($license_type, 'Vergi Levhalı Resmi Esnaf'); ?>>Resmi Vergi Levhalı Esnaf</option>
                    </select>
                    <label style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #065F46; margin-top: 8px; cursor: pointer;">
                        <input type="checkbox" name="_firma_has_insurance" value="1" <?php checked($has_insurance !== '0'); ?>>
                        %100 Emtia Taşıma Sigortası Teminatı Mevcut
                    </label>
                </div>
            </div>

            <div style="background: #FFFBEB; border: 1px solid #F59E0B; border-radius: 10px; padding: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; font-weight: bold; color: #92400E; cursor: pointer;">
                    <input type="checkbox" name="_firma_is_vip" value="1" <?php checked($is_vip, '1'); ?>>
                    👑 Bölge Nöbetçi Lideri (VIP Sabitleme)
                </label>
                <p style="margin: 6px 0 0 24px; font-size: 12px; color: #B45309;">
                    İşaretlenirse bu firma ilçedeki/ildeki aramalarda ve makale slider'larında <strong>EN BAŞA (1. SIRAYA)</strong> sabitlenir. Kartı altın sarısı ışıltılı çerçeve ve <strong>"👑 NÖBETÇİ LİDER ÇEKİCİ"</strong> unvanıyla öne çıkar.
                </p>
            </div>
        </div>
    </div>
    <?php
}

function yym_save_firma_meta($post_id) {
    if (!isset($_POST['yym_firma_nonce']) || !wp_verify_nonce($_POST['yym_firma_nonce'], 'yym_save_firma_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
        '_firma_phone',
        '_firma_whatsapp',
        '_firma_address',
        '_firma_city',
        '_firma_district',
        '_firma_eta',
        '_firma_equipment',
        '_firma_price',
        '_firma_badge',
        '_firma_rating',
        '_firma_review_count',
        '_firma_license_type',
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }

    // Doğrulama ve VIP Onayları
    update_post_meta($post_id, '_firma_is_verified', isset($_POST['_firma_is_verified']) ? '1' : '0');
    update_post_meta($post_id, '_firma_has_insurance', isset($_POST['_firma_has_insurance']) ? '1' : '0');
    update_post_meta($post_id, '_firma_is_vip', isset($_POST['_firma_is_vip']) ? '1' : '0');
}
add_action('save_post_firma', 'yym_save_firma_meta');

// Bound the custom firm menu image in expanded, folded and mobile admin menus.
add_action('admin_head', function () {
    echo '<style id="mis360-admin-firm-icon">
    #adminmenu #menu-posts-firma .wp-menu-image { overflow:hidden; }
    #adminmenu #menu-posts-firma .wp-menu-image img {
        width:20px!important;height:20px!important;max-width:20px!important;max-height:20px!important;
        object-fit:contain;box-sizing:content-box;display:block;margin:7px auto 0!important;padding:0!important;
    }
    </style>';
});
