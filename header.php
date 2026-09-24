<?php
/**
 * Yol Yardım Merkezi - Header (Modern, Erişilebilir, Çok Sayfalı Rehber Başlığı)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$email   = yym_get_email();
$address = yym_get_address();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Google Fonts -->
    <!-- Preload main stylesheet for faster rendering -->
<link rel="preload" href="<?php echo esc_url(get_template_directory_uri() . '/assets/css/main.css'); ?>" as="style" integrity="" crossorigin="anonymous" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon.png'); ?>">
    <link rel="apple-touch-icon" href="<?php echo esc_url(get_template_directory_uri() . '/assets/images/favicon.png'); ?>">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class('yym-theme-body'); ?>>
<?php wp_body_open(); ?>

<!-- ANA BAŞLIK VE MENÜ (NAVBAR) - Minimalist & Şık -->
<header class="yym-main-header" id="yymHeader">
    <div class="lst-container yym-header-inner">
        <!-- Sol: Logo -->
        <div class="yym-logo-box">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="yym-brand-link" title="Yol Yardım Merkezi Ana Sayfa">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="Yol Yardım Merkezi" class="yym-header-logo-img" width="220" height="56">
            </a>
        </div>

        <!-- Orta: Sade ve Ferah Navigasyon Menüsü -->
        <nav class="yym-nav-container" id="yymNavbar" aria-label="Ana Menü">
            <ul class="yym-nav-menu">
                <li class="yym-nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
                </li>
                <li class="yym-nav-item <?php echo is_post_type_archive('firma') || is_page('firmalar') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/firmalar/')); ?>">Firmalar</a>
                </li>
                <li class="yym-nav-item <?php echo is_page('sehirler') || is_page('iller') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/sehirler/')); ?>">81 İl Çekici Ağı</a>
                </li>
                <li class="yym-nav-item <?php echo is_page('hizmetler') || is_page('hizmetlerimiz') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/hizmetler/')); ?>">Hizmetler</a>
                </li>
                <li class="yym-nav-item <?php echo is_page('hakkimizda') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">Hakkımızda</a>
                </li>
                <li class="yym-nav-item <?php echo is_page('blog') || (is_home() && !is_front_page()) ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a>
                </li>
                <li class="yym-nav-item <?php echo is_page('iletisim') ? 'active' : ''; ?>">
                    <a href="<?php echo esc_url(home_url('/iletisim/')); ?>">İletişim</a>
                </li>
            </ul>
        </nav>

        <!-- Sağ: Tek ve Şık Firma Ekle Aksiyon Butonu -->
        <div class="yym-header-actions">
            <a class="mis360-header-account" href="<?php echo esc_url(home_url('/uyelik/')); ?>"><?php echo is_user_logged_in() ? 'Hesabım' : 'Giriş / Üyelik'; ?></a>
            <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-btn-cta-primary" title="Çekici veya Yol Yardım Firmanızı Ekleyin">
                <span class="yym-btn-plus-icon">+</span>
                <span>Firma Ekle</span>
            </a>

            <!-- Mobil Menü Aç/Kapat Butonu -->
            <button type="button" class="yym-mobile-hamburger js-toggle-mobile-menu" aria-label="Menüyü Aç">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>


<!-- 3. MOBİL AÇILIR MENÜ (OFF-CANVAS DRAWER) -->
<div class="yym-mobile-drawer" id="yymMobileDrawer">
    <div class="yym-drawer-backdrop js-close-mobile-menu"></div>
    <div class="yym-drawer-content">
        <div class="yym-drawer-header">
            <div class="yym-drawer-logo">
                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="Yol Yardım Merkezi" class="yym-drawer-logo-img" style="height: 38px; width: auto; display: block;">
            </div>
            <button type="button" class="yym-drawer-close js-close-mobile-menu" aria-label="Menüyü Kapat">✕</button>
        </div>

        <div class="yym-drawer-emergency-box">
            <p><strong>Yolda mı kaldınız?</strong></p>
            <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-btn-action-mobile-call">
                🚨 En Yakın Çekiciyi Bul (81 İl)
            </a>
        </div>

        <ul class="yym-drawer-menu">
            <li><a href="<?php echo esc_url(home_url('/uyelik/')); ?>"><?php echo is_user_logged_in() ? 'Hesabım' : 'Giriş yap / Üye ol'; ?></a></li>
            <li><a href="<?php echo esc_url(home_url('/')); ?>">🏠 Ana Sayfa</a></li>
            <li><a href="<?php echo esc_url(home_url('/firmalar/')); ?>"><?php echo mis360_tow_symbol(); ?> Nöbetçi Çekici Firmaları</a></li>
            <li><a href="<?php echo esc_url(home_url('/sehirler/')); ?>">📍 81 İl Çekici Ağı</a></li>
            <li><a href="<?php echo esc_url(home_url('/hizmetler/')); ?>">🔧 Yol Yardım Hizmetlerimiz</a></li>
            <li><a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">ℹ️ Hakkımızda</a></li>
            <li><a href="<?php echo esc_url(home_url('/sss/')); ?>">❓ Sıkça Sorulan Sorular</a></li>
            <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">📰 Sürücü Rehberi & Blog</a></li>
            <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>">✉️ İletişim & Destek</a></li>
            <li class="yym-drawer-add-firm-li">
                <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-drawer-add-firm-btn">
                    ➕ Ağa Katılın / Firma Ekle
                </a>
            </li>
        </ul>

        <div class="yym-drawer-footer">
            <p>Yol Yardım Merkezi • Türkiye'nin Güvenilir Yol Yardım Ağı</p>
            <small>Destek: <?php echo esc_html($email); ?></small>
        </div>
    </div>
</div>
