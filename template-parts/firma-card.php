<?php
/** Sade firma kartı; arşiv ve ana sayfa aynı görünümü kullanır. */
if (!defined('ABSPATH')) {
    exit;
}

$card_id = get_the_ID();
$card_name = get_the_title();
$card_url = get_permalink();
$card_heading = isset($args['heading']) && $args['heading'] === 'h2' ? 'h2' : 'h3';
$card_city = trim((string) get_post_meta($card_id, '_firma_city', true));
$card_district = trim((string) get_post_meta($card_id, '_firma_district', true));
$card_location = implode(' / ', array_filter(array($card_city, $card_district)));
$card_phone = trim((string) get_post_meta($card_id, '_firma_phone', true));
$card_whatsapp = trim((string) get_post_meta($card_id, '_firma_whatsapp', true));
$card_normalize_phone = static function ($value) {
    $digits = preg_replace('/[^0-9]/', '', $value);
    if (substr($digits, 0, 2) === '00') {
        $digits = substr($digits, 2);
    }
    if (strlen($digits) === 11 && substr($digits, 0, 1) === '0') {
        $digits = '90' . substr($digits, 1);
    } elseif (strlen($digits) === 10) {
        $digits = '90' . $digits;
    }
    return strlen($digits) >= 8 && strlen($digits) <= 15 ? $digits : '';
};
$card_tel = $card_normalize_phone($card_phone);
$card_wa = $card_normalize_phone($card_whatsapp !== '' ? $card_whatsapp : $card_phone);

if (isset($args['show_phone']) && empty($args['show_phone'])) {
    $card_tel = '';
}
if (isset($args['show_whatsapp']) && empty($args['show_whatsapp'])) {
    $card_wa = '';
}
$card_wa_url = $card_wa ? 'https://wa.me/' . $card_wa . '?text=' . rawurlencode('Merhaba ' . $card_name . ', Yol Yardım Merkezi üzerinden ulaşıyorum. Yol yardım desteğine ihtiyacım var.') : '';

$card_image = get_the_post_thumbnail_url($card_id, 'thumbnail');
if (!$card_image) {
    $card_image = get_post_meta($card_id, '_firma_image_url', true);
}
$card_image = esc_url((string) $card_image);
$card_words = preg_split('/\s+/u', trim(wp_strip_all_tags($card_name)), -1, PREG_SPLIT_NO_EMPTY);
$card_initials = '';
foreach (array_slice($card_words ?: array(), 0, 2) as $card_word) {
    if (preg_match('/^./u', $card_word, $card_first)) {
        $card_initials .= $card_first[0];
    }
}

$card_terms = wp_get_post_terms($card_id, 'firma_kategori', array('fields' => 'names'));
$card_category = trim((string) get_post_meta($card_id, '_firma_category', true));
$card_services = !is_wp_error($card_terms) && $card_terms ? $card_terms : ($card_category !== '' ? array($card_category) : array());
$card_services = array_values(array_unique(array_filter(array_map('trim', $card_services))));
$card_services = mis360_category_display_names($card_services);
$card_rating_raw = str_replace(',', '.', trim((string) get_post_meta($card_id, '_firma_rating', true)));
$card_rating = is_numeric($card_rating_raw) && (float) $card_rating_raw > 0 && (float) $card_rating_raw <= 5 ? (float) $card_rating_raw : null;
$card_reviews = trim((string) get_post_meta($card_id, '_firma_review_count', true));
$card_excerpt = has_excerpt($card_id) ? wp_trim_words(wp_strip_all_tags(get_the_excerpt($card_id)), 20, '…') : '';

$card_is_verified   = get_post_meta($card_id, '_firma_is_verified', true) === '1';
$card_license_type  = get_post_meta($card_id, '_firma_license_type', true) ?: 'K1/K2 Yetki Belgeli';
$card_is_vip        = get_post_meta($card_id, '_firma_is_vip', true) === '1';
?>
<article class="mis360-firma-card<?php echo $card_is_vip ? ' mis360-fc-vip' : ''; ?>" <?php echo mis360_metrics_attributes($card_id); ?>>
    <div class="mis360-fc-header">
        <a class="mis360-fc-image" href="<?php echo esc_url($card_url); ?>" tabindex="-1" aria-hidden="true">
            <span class="mis360-fc-initials"><?php echo esc_html($card_initials ?: 'F'); ?></span>
            <?php if ($card_image) : ?>
                <img src="<?php echo $card_image; ?>" alt="" loading="lazy" width="80" height="80">
            <?php endif; ?>
        </a>
        <div class="mis360-fc-identity">
            <?php if ($card_is_vip) : ?>
                <span class="mis360-fc-vip-crown">👑 NÖBETÇİ LİDER ÇEKİCİ</span>
            <?php endif; ?>
            <span class="mis360-fc-label">Yol yardım firması</span>
            <<?php echo $card_heading; ?> class="mis360-fc-title"><a href="<?php echo esc_url($card_url); ?>"><?php echo esc_html($card_name); ?></a></<?php echo $card_heading; ?>>
            <p class="mis360-fc-location">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                <span><?php echo esc_html($card_location ?: 'Konum belirtilmemiş'); ?></span>
            </p>
            <?php if ($card_is_verified) : ?>
                <span class="mis360-fc-verified-badge" title="Resmi K1/K2 Taşıma Yetki Belgeli ve Emtia Sigortalı Güvenli Esnaf">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                    <span><?php echo esc_html($card_license_type); ?> &amp; Sigortalı</span>
                </span>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($card_rating !== null) : ?>
        <div class="mis360-fc-rating" aria-label="<?php echo esc_attr('5 üzerinden ' . number_format_i18n($card_rating, 1) . ' puan'); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8-6.2-3.2L5.8 21 7 14.2 2 9.3l6.9-1Z"/></svg>
            <strong><?php echo esc_html(number_format_i18n($card_rating, 1)); ?></strong>
            <?php if ($card_reviews !== '') : ?><span>· <?php echo esc_html($card_reviews); ?> yorum</span><?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ($card_excerpt !== '') : ?><p class="mis360-fc-excerpt"><?php echo esc_html($card_excerpt); ?></p><?php endif; ?>
    <?php if ($card_services) : ?>
        <ul class="mis360-fc-services" aria-label="Hizmetler">
            <?php foreach (array_slice($card_services, 0, 3) as $card_service) : ?><li><?php echo esc_html($card_service); ?></li><?php endforeach; ?>
            <?php if (count($card_services) > 3) : ?><li class="mis360-fc-more">+<?php echo (int) count($card_services) - 3; ?></li><?php endif; ?>
        </ul>
    <?php endif; ?>
    <?php mis360_mobile_tyre_notice($card_id); ?>
    <div class="mis360-fc-actions<?php echo !$card_tel || !$card_wa ? ' mis360-fc-actions-single' : ''; ?>">
        <?php if ($card_tel) : ?>
            <a class="mis360-fc-button mis360-fc-call" href="tel:+<?php echo esc_attr($card_tel); ?>" aria-label="<?php echo esc_attr($card_name . ' — Ara'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 3.1 5.2 2 2 0 0 1 5.1 3h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L9 10.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 .8 1.1Z"/></svg>
                <span>Ara</span>
            </a>
        <?php endif; ?>
        <?php if ($card_wa) : ?>
            <a class="mis360-fc-button mis360-fc-whatsapp" href="<?php echo esc_url($card_wa_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($card_name . ' — WhatsApp'); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.6 8.6 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.6 8.6 0 0 1-3.8-.9L3 21l1.9-5.7a8.6 8.6 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.6 8.6 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/><path d="M9 7.5c-1 1-.5 3 1.2 4.8s3.8 2.4 4.8 1.4l-1.6-1.6-1 1c-1.4-.6-2.6-1.8-3.2-3.2l1-1Z"/></svg>
                <span>WhatsApp</span>
            </a>
        <?php endif; ?>
        <a class="mis360-fc-button mis360-fc-details" href="<?php echo esc_url($card_url); ?>" aria-label="<?php echo esc_attr($card_name . ' — Firmayı İncele'); ?>">Firmayı İncele <span aria-hidden="true">→</span></a>
    </div>
</article>
