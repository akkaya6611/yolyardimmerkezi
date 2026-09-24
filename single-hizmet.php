<?php
/**
 * Yol Yardım Merkezi - Özel Hizmet Detay Şablonu (single-hizmet.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$phone_display = yym_get_phone();
$phone_raw     = yym_get_phone_raw();
$whatsapp_url  = yym_get_whatsapp_url(sprintf(__('Merhaba, %s hizmetiniz hakkında acil çekici talep ediyorum.', 'yol-yardim-merkezi'), get_the_title()));
?>

<div class="yym-page-header">
    <div class="yym-container">
        <span class="yym-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Ana Sayfa', 'yol-yardim-merkezi'); ?></a> / 
            <a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>"><?php _e('Hizmetlerimiz', 'yol-yardim-merkezi'); ?></a> / 
            <span><?php the_title(); ?></span>
        </span>
        <h1 class="yym-page-title"><?php the_title(); ?></h1>
        <p class="yym-page-subtitle"><?php _e('7/24 Kesintisiz Hizmet, %100 Kaskolu Güvence ve Sabit Fiyat Garantisi.', 'yol-yardim-merkezi'); ?></p>
    </div>
</div>

<div class="yym-container yym-page-container">
    <div class="yym-page-layout">
        <main id="primary" class="yym-main-content">
            <?php
            while (have_posts()) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('yym-article yym-service-detail'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="yym-featured-image">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="yym-entry-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Hizmet Avantajları Özeti -->
                    <div class="yym-service-perks-box">
                        <h3><?php _e('Bu Hizmette Sunduğumuz Güvenceler:', 'yol-yardim-merkezi'); ?></h3>
                        <div class="yym-perks-grid">
                            <div class="yym-perk-item">
                                <span class="yym-perk-icon">⚡</span>
                                <div>
                                    <strong><?php _e('15-30 Dakikada Yanınızda', 'yol-yardim-merkezi'); ?></strong>
                                    <p><?php _e('Bölgenize en yakın nöbetçi ekibimiz derhal yola çıkar.', 'yol-yardim-merkezi'); ?></p>
                                </div>
                            </div>
                            <div class="yym-perk-item">
                                <span class="yym-perk-icon">🛡️</span>
                                <div>
                                    <strong><?php _e('%100 Kaskolu Taşıma', 'yol-yardim-merkezi'); ?></strong>
                                    <p><?php _e('Aracınız işlem boyunca tam kapsamlı sigorta teminatındadır.', 'yol-yardim-merkezi'); ?></p>
                                </div>
                            </div>
                            <div class="yym-perk-item">
                                <span class="yym-perk-icon">💰</span>
                                <div>
                                    <strong><?php _e('Sabit Fiyat Garantisi', 'yol-yardim-merkezi'); ?></strong>
                                    <p><?php _e('Telefonda verilen ücret dışında sürpriz ek ücret talep edilmez.', 'yol-yardim-merkezi'); ?></p>
                                </div>
                            </div>
                            <div class="yym-perk-item">
                                <span class="yym-perk-icon">💳</span>
                                <div>
                                    <strong><?php _e('Kredi Kartı ile Ödeme', 'yol-yardim-merkezi'); ?></strong>
                                    <p><?php _e('Tüm araçlarımızda mobil POS cihazı mevcuttur.', 'yol-yardim-merkezi'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acil Eylem Kutusu -->
                    <div class="yym-in-post-cta">
                        <div class="yym-cta-banner">
                            <h3>🚨 <?php printf(__('Acil %s İhtiyacınız mı Var?', 'yol-yardim-merkezi'), esc_html(get_the_title())); ?></h3>
                            <p><?php _e('7/24 çağrı merkezimizi arayarak hemen en yakın kurtarıcıyı yönlendirebilirsiniz.', 'yol-yardim-merkezi'); ?></p>
                            <div class="yym-cta-btns">
                                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode(get_the_title() . ' Hizmet Talebi'); ?>" class="yym-btn yym-btn-call">
                                    ✉️ <?php echo esc_html(yym_get_email()); ?>
                                </a>
                                <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn yym-btn-whatsapp">
                                    💬 <?php _e('WhatsApp İle Yazış', 'yol-yardim-merkezi'); ?>
                                </a>
                                <button type="button" class="yym-btn yym-btn-location js-share-location-btn">
                                    📍 <?php _e('Konum Gönder', 'yol-yardim-merkezi'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
                <?php
            endwhile;
            ?>
        </main>

        <aside class="yym-sidebar">
            <div class="yym-sidebar-widget yym-widget-emergency">
                <span class="yym-widget-icon">🚨</span>
                <h3><?php _e('7/24 Acil Çağrı', 'yol-yardim-merkezi'); ?></h3>
                <p><?php _e('Beklemek yok! 2 dakikada ekibimiz yola çıksın.', 'yol-yardim-merkezi'); ?></p>
                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode(get_the_title() . ' Çağrı Talebi'); ?>" class="yym-btn yym-btn-call yym-btn-block">
                    ✉️ <?php echo esc_html(yym_get_email()); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn">
                    📍 <?php _e('WhatsApp Konum Gönder', 'yol-yardim-merkezi'); ?>
                </button>
            </div>

            <div class="yym-sidebar-widget">
                <h4><?php _e('Diğer Yol Yardım Hizmetleri', 'yol-yardim-merkezi'); ?></h4>
                <ul class="yym-widget-links">
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Oto Çekici & Kurtarıcı</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Akü Takviye & Satış</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Mobil Lastik Yol Yardım</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Acil Yakıt İkmal Desteği</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Motosiklet Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#hizmetlerimiz')); ?>">Ağır Vasıta Kurtarma</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
