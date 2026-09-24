<?php
/**
 * Yol Yardım Merkezi - Özel Bölge Detay Şablonu (single-bolge.php - Local SEO)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$region_name   = get_the_title();
$phone_display = yym_get_phone();
$phone_raw     = yym_get_phone_raw();
$whatsapp_url  = yym_get_whatsapp_url(sprintf(__('Merhaba, %s bölgesinde acil çekici ve yol yardımına ihtiyacım var.', 'yol-yardim-merkezi'), $region_name));
$eta           = yym_get_eta();
?>

<div class="yym-page-header">
    <div class="yym-container">
        <span class="yym-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Ana Sayfa', 'yol-yardim-merkezi'); ?></a> / 
            <a href="<?php echo esc_url(home_url('/#bolgeler')); ?>"><?php _e('Bölgeler', 'yol-yardim-merkezi'); ?></a> / 
            <span><?php echo esc_html($region_name); ?></span>
        </span>
        <h1 class="yym-page-title"><?php echo esc_html($region_name); ?> <?php _e('Oto Çekici & Yol Yardım', 'yol-yardim-merkezi'); ?></h1>
        <p class="yym-page-subtitle"><?php printf(__('%s bölgesinde nöbetçi çekici araçlarımızla ortalama %s içinde yanınızdayız!', 'yol-yardim-merkezi'), esc_html($region_name), esc_html($eta)); ?></p>
    </div>
</div>

<div class="yym-container yym-page-container">
    <div class="yym-page-layout">
        <main id="primary" class="yym-main-content">
            <article id="post-<?php the_ID(); ?>" <?php post_class('yym-article yym-region-detail'); ?>>
                <!-- Bölgesel Acil Çağrı Kutusu -->
                <div class="yym-in-post-cta yym-region-top-cta">
                    <div class="yym-cta-banner">
                        <div class="yym-live-badge">
                            <span class="yym-pulse-dot"></span>
                            <span class="yym-badge-txt"><?php printf(__('%s Nöbetçi Çekici Aktif', 'yol-yardim-merkezi'), esc_html($region_name)); ?></span>
                        </div>
                        <h3><?php printf(__('%s Bölgesinde Yolda mı Kaldınız?', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h3>
                        <p><?php printf(__('%s merkez, ana caddeler ve bağlantı yollarında bekleyen ekiplerimiz 15-30 dakikada yanınızda.', 'yol-yardim-merkezi'), esc_html($region_name)); ?></p>
                        <div class="yym-cta-btns">
                            <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode($region_name . ' Yol Yardım Talebi'); ?>" class="yym-btn yym-btn-call">
                                ✉️ <?php echo esc_html(yym_get_email()); ?>
                            </a>
                            <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn yym-btn-whatsapp">
                                💬 <?php _e('WhatsApp Konum İlet', 'yol-yardim-merkezi'); ?>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="yym-entry-content">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php if (get_the_content()) : ?>
                            <?php the_content(); ?>
                        <?php else : ?>
                            <h2><?php printf(__('%s En Yakın Oto Kurtarma ve Çekici Hizmeti', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h2>
                            <p><?php printf(__('%s ve çevre mahallelerinde aracınızla kaza yaptıysanız, akünüz bittiyse veya lastiğiniz patladıysa panik yapmanıza gerek yok. Yol Yardım Merkezi olarak %s genelinde hazır bekleyen nöbetçi çekici araçlarımızla 7/24 hizmet veriyoruz.', 'yol-yardim-merkezi'), esc_html($region_name), esc_html($region_name)); ?></p>
                            
                            <h3><?php printf(__('%s Bölgesinde Neler Yapıyoruz?', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h3>
                            <ul>
                                <li><strong><?php _e('Oto Çekici & Kurtarıcı:', 'yol-yardim-merkezi'); ?></strong> <?php _e('Kayar kasalı modern araçlarımızla binek ve hafif ticari araçlarınız kaskolu olarak istediğiniz servise nakledilir.', 'yol-yardim-merkezi'); ?></li>
                                <li><strong><?php _e('Yerinde Akü Takviye:', 'yol-yardim-merkezi'); ?></strong> <?php _e('Aracınız marş basmıyorsa takviye cihazlarımızla dakikalar içinde çalıştırılır.', 'yol-yardim-merkezi'); ?></li>
                                <li><strong><?php _e('Mobil Lastik Tamiri:', 'yol-yardim-merkezi'); ?></strong> <?php _e('Yol kenarında lastik sökme, stepne takma ve fitil tamiri yapılır.', 'yol-yardim-merkezi'); ?></li>
                                <li><strong><?php _e('Motosiklet Taşıma:', 'yol-yardim-merkezi'); ?></strong> <?php _e('Özel aparatlı araçlarla motosikletiniz güvenle nakledilir.', 'yol-yardim-merkezi'); ?></li>
                            </ul>
                        <?php endif; ?>
                    <?php endwhile; endif; ?>
                </div>

                <!-- Bölge İçin Neden Biz? -->
                <div class="yym-service-perks-box">
                    <h3><?php printf(__('%s Çekici Hizmetinde Neden Biz?', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h3>
                    <div class="yym-perks-grid">
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">⚡</span>
                            <div>
                                <strong><?php _e('En Hızlı Varış Garantisi', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php printf(__('%s bölgesine özel görevlendirilmiş nöbetçi çekici araçlarımızla 15-30 dakikada yanınızdayız.', 'yol-yardim-merkezi'), esc_html($region_name)); ?></p>
                            </div>
                        </div>
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">🛡️</span>
                            <div>
                                <strong><?php _e('%100 Kaskolu Güvence', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php _e('Tüm taşıma işlemlerimiz resmi emtia kasko poliçesi teminatı altındadır.', 'yol-yardim-merkezi'); ?></p>
                            </div>
                        </div>
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">💰</span>
                            <div>
                                <strong><?php _e('Sabit & Şeffaf Fiyat', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php _e('Telefonda anlaşılan net fiyat geçerlidir, ekstra hiçbir sürpriz masraf yansıtılmaz.', 'yol-yardim-merkezi'); ?></p>
                            </div>
                        </div>
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">🕒</span>
                            <div>
                                <strong><?php _e('7/24 Kesintisiz Destek', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php _e('Gece ve tatil günleri dahil kesintisiz canlı çağrı merkezimiz açıktır.', 'yol-yardim-merkezi'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </main>

        <aside class="yym-sidebar">
            <div class="yym-sidebar-widget yym-widget-emergency">
                <span class="yym-widget-icon">🚨</span>
                <h3><?php printf(__('%s Acil Çekici', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h3>
                <p><?php _e('Ekibimiz yola çıkmaya hazır. Hemen arayın veya WhatsApp\'tan anlık konum iletin.', 'yol-yardim-merkezi'); ?></p>
                <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode($region_name . ' Acil Destek'); ?>" class="yym-btn yym-btn-call yym-btn-block">
                    ✉️ <?php echo esc_html(yym_get_email()); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn">
                    📍 <?php _e('WhatsApp Konum Gönder', 'yol-yardim-merkezi'); ?>
                </button>
            </div>

            <div class="yym-sidebar-widget">
                <h4><?php _e('Popüler Diğer Bölgeler', 'yol-yardim-merkezi'); ?></h4>
                <ul class="yym-widget-links">
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Kadıköy Oto Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Ümraniye Oto Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Beşiktaş Oto Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Bakırköy Oto Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Pendik Oto Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/#bolgeler')); ?>">Beylikdüzü Oto Çekici</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
