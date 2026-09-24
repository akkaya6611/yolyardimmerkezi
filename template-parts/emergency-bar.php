<?php
/**
 * Yol Yardım Merkezi - Mobil Sabit Acil Eylem Çubuğu
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$email = yym_get_email();
$whatsapp_url  = yym_get_whatsapp_url();
?>

<!-- Mobilde Ekranın Altında Sabit Kalan Acil Eylem Barı -->
<div class="yym-emergency-bar" id="emergencyBar" role="region" aria-label="<?php esc_attr_e('Acil İletişim Çubuğu', 'yol-yardim-merkezi'); ?>">
    <div class="yym-emergency-status">
        <span class="yym-pulse-dot"></span>
        <span class="yym-status-text"><?php _e('7/24 Nöbetçi Çekici Yayında • Ortalama Varış: ', 'yol-yardim-merkezi'); ?><strong><?php echo esc_html(yym_get_eta()); ?></strong></span>
    </div>
    <div class="yym-emergency-actions">
        <!-- 1. E-Posta İletişim Butonu -->
        <a href="mailto:<?php echo esc_attr($email); ?>" class="yym-bar-btn yym-bar-call" aria-label="<?php esc_attr_e('E-Posta ile İletişim', 'yol-yardim-merkezi'); ?>">
            <svg class="yym-icon" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
            <div class="yym-btn-text-group">
                <span class="yym-btn-title"><?php _e('İLETİŞİM', 'yol-yardim-merkezi'); ?></span>
                <span class="yym-btn-subtitle"><?php echo esc_html($email); ?></span>
            </div>
        </a>

        <!-- 2. Konum Gönder Butonu (HTML5 Geolocation) -->
        <button type="button" class="yym-bar-btn yym-bar-location js-share-location-btn" aria-label="<?php esc_attr_e('WhatsApp ile Konum Gönder', 'yol-yardim-merkezi'); ?>">
            <svg class="yym-icon" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/>
            </svg>
            <div class="yym-btn-text-group">
                <span class="yym-btn-title"><?php _e('KONUM AT', 'yol-yardim-merkezi'); ?></span>
                <span class="yym-btn-subtitle"><?php _e('WhatsApp İle', 'yol-yardim-merkezi'); ?></span>
            </div>
        </button>

        <!-- 3. WhatsApp Doğrudan Mesaj Butonu -->
        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-bar-btn yym-bar-whatsapp" aria-label="<?php esc_attr_e('WhatsApp Mesajı Gönder', 'yol-yardim-merkezi'); ?>">
            <svg class="yym-icon" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.12 8.12 0 01-1.25-4.39c0-4.52 3.68-8.2 8.2-8.2 2.19 0 4.25.85 5.8 2.4 1.55 1.55 2.4 3.61 2.4 5.8 0 4.52-3.68 8.2-8.2 8.2zm4.49-6.14c-.25-.12-1.47-.72-1.7-.81-.23-.08-.39-.12-.56.12-.17.25-.64.81-.79.97-.14.17-.29.19-.53.07-.25-.12-1.04-.38-1.99-1.22-.73-.66-1.23-1.47-1.38-1.71-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.12-.15.17-.25.25-.41.08-.17.04-.31-.02-.43s-.56-1.35-.77-1.85c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.44.06-.67.31-.23.25-.88.86-.88 2.1 0 1.24.9 2.44 1.03 2.61.12.17 1.78 2.71 4.3 3.8.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.15-1.18-.07-.1-.23-.17-.48-.29z"/>
            </svg>
            <div class="yym-btn-text-group">
                <span class="yym-btn-title"><?php _e('WHATSAPP', 'yol-yardim-merkezi'); ?></span>
                <span class="yym-btn-subtitle"><?php _e('Canlı Yazış', 'yol-yardim-merkezi'); ?></span>
            </div>
        </a>
    </div>
</div>
