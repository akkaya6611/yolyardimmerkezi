<?php
/**
 * Yol Yardım Merkezi - Özel Bölge & Transit Otoyol Detay Şablonu (single-bolge.php - Local & Highway SEO)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$region_id      = get_the_ID();
$region_name    = get_the_title();
$phone_display  = yym_get_phone();
$phone_raw      = yym_get_phone_raw();
$whatsapp_url   = yym_get_whatsapp_url(sprintf(__('Merhaba, %s güzergahında yolda kaldım, acil çekici ve yol yardımına ihtiyacım var.', 'yol-yardim-merkezi'), $region_name));
$eta            = yym_get_eta();
$is_transit     = get_post_meta($region_id, '_is_transit_highway', true);
$bolge_city     = get_post_meta($region_id, '_bolge_city', true);
$bolge_highways = get_post_meta($region_id, '_bolge_highways', true);
?>

<div class="yym-page-header">
    <div class="yym-container">
        <span class="yym-breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Ana Sayfa', 'yol-yardim-merkezi'); ?></a> / 
            <a href="<?php echo esc_url(home_url('/#bolgeler')); ?>"><?php _e('Bölgeler', 'yol-yardim-merkezi'); ?></a> / 
            <span><?php echo esc_html($region_name); ?></span>
        </span>
        <h1 class="yym-page-title">
            <?php if ($is_transit) : ?>
                <span style="color: #f59e0b; display: block; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">🛣️ <?php _e('7/24 Kesintisiz Otoyol Nöbetçi Hattı', 'yol-yardim-merkezi'); ?></span>
            <?php endif; ?>
            <?php echo esc_html($region_name); ?>
        </h1>
        <p class="yym-page-subtitle">
            <?php if ($is_transit && !empty($bolge_highways)) : ?>
                <?php printf(__('%s bağlantı noktalarında hazır bekleyen devriye kurtarma ekiplerimizle ortalama %s içinde yanınızdayız!', 'yol-yardim-merkezi'), esc_html($bolge_highways), esc_html($eta)); ?>
            <?php else : ?>
                <?php printf(__('%s bölgesinde nöbetçi çekici araçlarımızla ortalama %s içinde yanınızdayız!', 'yol-yardim-merkezi'), esc_html($region_name), esc_html($eta)); ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="yym-container yym-page-container">
    <div class="yym-page-layout">
        <main id="primary" class="yym-main-content">
            <article id="post-<?php the_ID(); ?>" <?php post_class('yym-article yym-region-detail'); ?>>
                <!-- Bölgesel / Otoyol Acil Çağrı Kutusu -->
                <div class="yym-in-post-cta yym-region-top-cta">
                    <div class="yym-cta-banner">
                        <div class="yym-live-badge">
                            <span class="yym-pulse-dot"></span>
                            <span class="yym-badge-txt"><?php printf(__('%s Nöbetçi Çekici Aktif', 'yol-yardim-merkezi'), esc_html($region_name)); ?></span>
                        </div>
                        <h3><?php printf(__('%s Güzergahında Yolda mı Kaldınız?', 'yol-yardim-merkezi'), esc_html($region_name)); ?></h3>
                        <p><?php _e('Emniyet şeridinde veya gişelerde bekleyen kayar kasa ve vinçli kurtarıcı ekiplerimiz 15-25 dakikada yanınızda.', 'yol-yardim-merkezi'); ?></p>
                        <div class="yym-cta-btns">
                            <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="yym-btn yym-btn-call" style="font-weight: 700;">
                                📞 <?php echo esc_html($phone_display); ?> (<?php _e('Hemen Ara', 'yol-yardim-merkezi'); ?>)
                            </a>
                            <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener noreferrer" class="yym-btn yym-btn-whatsapp">
                                💬 <?php _e('WhatsApp Konum İlet', 'yol-yardim-merkezi'); ?>
                            </a>
                            <button type="button" class="yym-btn yym-btn-location js-share-location-btn" style="background: #0284c7; color: #fff; border: none; cursor: pointer; border-radius: 8px; font-weight: 600; padding: 12px 18px; display: inline-flex; align-items: center; gap: 6px;">
                                📍 <?php _e('GPS Konumumu Gönder', 'yol-yardim-merkezi'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Otoyol / Bölge Güvenlik Talimatı Uyarısı -->
                <div style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 5px solid #f59e0b; padding: 16px 20px; border-radius: 10px; margin: 24px 0;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 1.5rem; line-height: 1;">⚠️</span>
                        <div>
                            <h4 style="margin: 0 0 6px 0; color: #92400e; font-size: 1rem; font-weight: 700;"><?php _e('Otoyol ve Kritik Geçiş Güvenlik Talimatı', 'yol-yardim-merkezi'); ?></h4>
                            <p style="margin: 0; color: #78350f; font-size: 0.88rem; line-height: 1.5;">
                                <?php _e('Can güvenliğiniz için aracınızı mümkünse emniyet şeridine alın, 4\'lü flaşörlerinizi yakın, üçgen reflektörünüzü aracın 150m gerisine yerleştirin ve kesinlikle araç içinde beklemeyip <strong>çelik bariyerlerin arkasına</strong> geçin.', 'yol-yardim-merkezi'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bölge / Otoyol Nöbetçi Çekici Ekipleri Slider -->
                <div class="yym-region-slider-section" style="margin: 32px 0;">
                    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 16px 20px; border-radius: 12px; margin-bottom: 20px; border-left: 5px solid #2563eb; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <h3 style="color: #fff; font-size: 1.15rem; font-weight: 700; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                                <span>🚨</span> <?php printf(__('%s Çevresi Nöbetçi Çekiciler', 'yol-yardim-merkezi'), esc_html($region_name)); ?>
                            </h3>
                            <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;"><?php _e('Bölgede hazır bekleyen K1/K2 lisanslı ve kaskolu kurtarıcı firmalarını inceleyin.', 'yol-yardim-merkezi'); ?></p>
                        </div>
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                            <span class="yym-pulse-dot" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span>
                            <?php _e('Canlı Nöbetçi Filosu', 'yol-yardim-merkezi'); ?>
                        </span>
                    </div>
                    <?php
                    $slider_target_city = !empty($bolge_city) ? $bolge_city : $region_name;
                    echo do_shortcode('[yym_firma_slider sehir="' . esc_attr($slider_target_city) . '" limit="8" show_phone="1" show_whatsapp="1"]');
                    ?>
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
                                <p><?php printf(__('%s güzergahına özel görevlendirilmiş nöbetçi çekici araçlarımızla 15-30 dakikada yanınızdayız.', 'yol-yardim-merkezi'), esc_html($region_name)); ?></p>
                            </div>
                        </div>
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">🛡️</span>
                            <div>
                                <strong><?php _e('K1 / K2 Belgeli & Kaskolu Güvence', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php _e('Tüm taşıma işlemlerimiz resmi emtia taşıma kasko poliçesi teminatı altındadır.', 'yol-yardim-merkezi'); ?></p>
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
                                <p><?php _e('Gece ve tatil günleri dahil kesintisiz canlı çağrı merkezimiz ve saha filomuz açıktır.', 'yol-yardim-merkezi'); ?></p>
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
                <a href="tel:<?php echo esc_attr($phone_raw); ?>" class="yym-btn yym-btn-call yym-btn-block">
                    📞 <?php echo esc_html($phone_display); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn">
                    📍 <?php _e('WhatsApp Konum Gönder', 'yol-yardim-merkezi'); ?>
                </button>
            </div>

            <div class="yym-sidebar-widget">
                <h4>🛣️ <?php _e('Transit Otoyol Hatları', 'yol-yardim-merkezi'); ?></h4>
                <ul class="yym-widget-links">
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/kuzey-marmara-otoyolu-cekici/')); ?>">Kuzey Marmara Otoyolu (KMO)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/gebze-izmir-otoyolu-cekici/')); ?>">Gebze - İzmir Otoyolu (O-5)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/bolu-dagi-cekici/')); ?>">Bolu Dağı & Tüneli Çekici</a></li>
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/tag-otoyolu-cekici/')); ?>">TAG Otoyolu (Adana-Gaziantep)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/ankara-nigde-otoyolu-cekici/')); ?>">Ankara - Niğde Otoyolu (O-21)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/bolgeler/tem-otoyolu-cekici/')); ?>">TEM Otoyolu (İstanbul-Edirne)</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
