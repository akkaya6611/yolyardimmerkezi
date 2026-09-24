<?php
/**
 * Listivo Demo 5 - Yol Yardım Merkezi Nöbetçi Çekici Vitrini
 * Ziyaretçilerin doğrudan yol yardım firmasıyla (Ara & WhatsApp) iletişime geçmesini sağlar.
 * Tıpkı ototamircibul.com.tr mantığıyla aracı komisyonsuz doğrudan firma iletişimi.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Veritabanındaki 'firma' Yazı Tipini Kontrol Et
$firma_query = new WP_Query(array(
    'post_type'      => 'firma',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
));

$listings = array();

if ($firma_query->have_posts()) {
    while ($firma_query->have_posts()) {
        $firma_query->the_post();
        $f_id      = get_the_ID();
        $f_phone   = get_post_meta($f_id, '_firma_phone', true);
        $f_wa      = get_post_meta($f_id, '_firma_whatsapp', true);
        $f_city    = get_post_meta($f_id, '_firma_city', true);
        $f_dist    = get_post_meta($f_id, '_firma_district', true);
        $f_eta     = get_post_meta($f_id, '_firma_eta', true) ?: '15 Dk Varış';
        $f_equip   = get_post_meta($f_id, '_firma_equipment', true) ?: 'Kayar Kasa';
        $f_badge   = get_post_meta($f_id, '_firma_badge', true) ?: '7/24 NÖBETÇİ';
        $f_price   = get_post_meta($f_id, '_firma_price', true) ?: 'Sabit Fiyat Garantisi';
        $f_rating  = get_post_meta($f_id, '_firma_rating', true) ?: '4.9 ★';

        $terms = wp_get_post_terms($f_id, 'firma_kategori', array('fields' => 'slugs'));
        $cat_slug = (!empty($terms) && !is_wp_error($terms)) ? $terms[0] : 'cekici';

        $thumb_meta = get_post_meta($f_id, '_firma_image_url', true);
        $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url($f_id, 'medium_large') : (!empty($thumb_meta) ? $thumb_meta : 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg');

        $listings[] = array(
            'id'       => $f_id,
            'title'    => get_the_title(),
            'url'      => get_permalink(),
            'price'    => $f_price,
            'location' => $f_city ? ($f_city . ' / ' . $f_dist) : 'Kayseri & Çevresi',
            'image'    => $thumb,
            'category' => $cat_slug,
            'badge'    => $f_badge,
            'fuel'     => 'Kaskolu Taşıma',
            'eta'      => $f_eta,
            'trans'    => $f_equip,
            'rating'   => $f_rating,
            'phone'    => $f_phone,
            'whatsapp' => $f_wa,
        );
    }
    wp_reset_postdata();
}

// Yalnızca veritabanında kayıtlı firmaları göster.
if (empty($listings)) {
    return;
}
?>

<section class="lst-section lst-listings-section" id="firmalar">
    <div class="lst-container">
        <!-- Başlık ve Sekmeler Üst Alanı -->
        <div class="lst-section-heading-bar">
            <div class="lst-heading-group">
                <span class="lst-pill-sub">⚡ 7/24 Nöbetçi Çekici Ağı</span>
                <h2 class="lst-section-title">En Yakın Yol Yardım & Çekici Firmaları</h2>
                <p style="color: #64748B; font-size: 0.95rem; margin-top: 4px;">Aracı komisyonu yok! Doğrudan bölgenizdeki nöbetçi kurtarıcı firmayı arayın veya WhatsApp'tan konum atın.</p>
            </div>

            <!-- Kategori Sekmeleri -->
            <div class="lst-cat-filter-tabs">
                <button type="button" class="lst-cat-tab is-active" data-filter="all">Tümü</button>
                <button type="button" class="lst-cat-tab" data-filter="cekici">Oto Çekici</button>
                <button type="button" class="lst-cat-tab" data-filter="aku">Akü Takviye</button>
                <button type="button" class="lst-cat-tab" data-filter="lastik">Lastik Yardım</button>
                <button type="button" class="lst-cat-tab" data-filter="motosiklet">Motosiklet</button>
            </div>
        </div>

        <!-- Firma Kartları Izgarası (3 Kolon) -->
        <div class="lst-listings-grid" id="lstListingsGrid">
            <?php foreach ($listings as $item) : 
                $f_phone_raw = preg_replace('/[^0-9]/', '', $item['phone']);
                if (substr($f_phone_raw, 0, 1) === '0') {
                    $f_tel_link = '+90' . substr($f_phone_raw, 1);
                } else {
                    $f_tel_link = '+' . $f_phone_raw;
                }

                $f_wa_clean = preg_replace('/[^0-9]/', '', $item['whatsapp'] ?: $item['phone']);
                if (strlen($f_wa_clean) == 11 && substr($f_wa_clean, 0, 1) == '0') {
                    $f_wa_clean = '90' . substr($f_wa_clean, 1);
                } elseif (strlen($f_wa_clean) == 10) {
                    $f_wa_clean = '90' . $f_wa_clean;
                }

                $f_wa_msg = urlencode(sprintf('Merhaba %s, Yol Yardım Merkezi üzerinden size ulaşıyorum. Acil çekici/yol yardım desteğine ihtiyacım var.', $item['title']));
                $f_wa_link = 'https://wa.me/' . $f_wa_clean . '?text=' . $f_wa_msg;
            ?>
                <div class="lst-card lst-car-card" data-category="<?php echo esc_attr($item['category']); ?>">
                    <!-- Kart Görseli & Rozetler -->
                    <div class="lst-card-media">
                        <a href="<?php echo esc_url($item['url']); ?>">
                            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="lst-card-img" loading="lazy">
                        </a>
                        
                        <div class="lst-card-badges">
                            <span class="lst-badge-featured"><?php echo esc_html($item['badge']); ?></span>
                            <span class="lst-badge-fuel"><?php echo esc_html($item['fuel']); ?></span>
                        </div>

                        <button type="button" class="lst-btn-favorite" title="Kaydet" aria-label="Favoriye Ekle">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Kart İçeriği -->
                    <div class="lst-card-content">
                        <div class="lst-card-price-row">
                            <span class="lst-card-price" style="font-size: 1.15rem; color: #FA5343;"><?php echo esc_html($item['price']); ?></span>
                        </div>

                        <h3 class="lst-card-title">
                            <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['title']); ?></a>
                        </h3>

                        <div class="lst-card-location">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <span><?php echo esc_html($item['location']); ?></span>
                        </div>

                        <!-- Özellik Çipleri -->
                        <div class="lst-card-specs">
                            <div class="lst-spec-item" title="Varış Süresi">
                                <span style="color: #10B981; font-weight: 800;">⚡</span>
                                <span><?php echo esc_html($item['eta']); ?></span>
                            </div>
                            <div class="lst-spec-item" title="Donanım">
                                <span style="color: #0284C7; font-weight: 800;">🚚</span>
                                <span><?php echo esc_html($item['trans']); ?></span>
                            </div>
                            <div class="lst-spec-item" title="Müşteri Puanı">
                                <span style="color: #F59E0B; font-weight: 800;">⭐</span>
                                <span><?php echo esc_html($item['rating']); ?></span>
                            </div>
                        </div>

                        <!-- Kart Altı Butonları (DOĞRUDAN FİRMAYI ARA & WHATSAPP) -->
                        <div class="lst-card-footer" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                            <?php if (!empty($item['phone'])) : ?>
                                <a href="tel:<?php echo esc_attr($f_tel_link); ?>" class="lst-btn lst-btn-coral" style="padding: 10px; font-size: 0.85rem; text-align: center;">
                                    📞 Hemen Ara
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="lst-btn lst-btn-coral" style="padding: 10px; font-size: 0.85rem; text-align: center;">
                                    🔍 Profili Gör
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($f_wa_clean)) : ?>
                                <a href="<?php echo esc_url($f_wa_link); ?>" target="_blank" rel="noopener noreferrer" class="lst-btn" style="background-color: #25D366; color: #fff; padding: 10px; font-size: 0.85rem; text-align: center;">
                                    💬 WhatsApp
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url($item['url']); ?>" class="lst-btn" style="background-color: #283948; color: #fff; padding: 10px; font-size: 0.85rem; text-align: center;">
                                    Detaylar →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
