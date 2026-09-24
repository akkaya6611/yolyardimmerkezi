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
$eta            = yym_get_eta();
$is_transit     = get_post_meta($region_id, '_is_transit_highway', true);
$bolge_city     = get_post_meta($region_id, '_bolge_city', true);
$bolge_highways = get_post_meta($region_id, '_bolge_highways', true);

// Başlıktan temiz ve akıcı bölge ismi çıkarma (Örn: "TAG Otoyolu (Tarsus - Adana - Gaziantep) Acil Çekici Hizmeti" -> "TAG Otoyolu")
$clean_name = trim(preg_replace('/\s*(\(.*?\)|Acil\s*Çekici.*|Oto\s*Kurtarma.*|Hizmeti.*)/iu', '', $region_name));
if (empty($clean_name) || mb_strlen($clean_name) < 3) {
    $clean_name = $region_name;
}
?>

<!-- Sayfa Başlığı ve Rota Bilgisi (Yeterli üst boşluk ile asla kesilmez) -->
<div class="yym-page-header google-auto-ads-ignore" style="padding: 75px 0 45px; background: linear-gradient(180deg, #071527 0%, #0d233e 100%); position: relative; z-index: 1;">
    <div class="yym-container">
        <span class="yym-breadcrumb" style="display: block; font-size: 0.88rem; color: #94a3b8; margin-bottom: 14px;">
            <a href="<?php echo esc_url(home_url('/')); ?>" style="color: #cbd5e1; text-decoration: none;"><?php _e('Ana Sayfa', 'yol-yardim-merkezi'); ?></a> / 
            <a href="<?php echo esc_url(home_url('/#bolgeler')); ?>" style="color: #cbd5e1; text-decoration: none;"><?php _e('Bölgeler', 'yol-yardim-merkezi'); ?></a> / 
            <span style="color: #f8fafc; font-weight: 500;"><?php echo esc_html($clean_name); ?></span>
        </span>
        
        <?php if ($is_transit) : ?>
            <div style="margin-bottom: 10px;">
                <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(245, 158, 11, 0.15); border: 1px solid rgba(245, 158, 11, 0.35); color: #f59e0b; padding: 5px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                    🛣️ <?php _e('7/24 Kesintisiz Otoyol Nöbetçi Hattı', 'yol-yardim-merkezi'); ?>
                </span>
            </div>
        <?php endif; ?>

        <h1 class="yym-page-title" style="color: #ffffff; font-size: 2.1rem; font-weight: 800; line-height: 1.25; margin: 0 0 12px 0;">
            <?php echo esc_html($region_name); ?>
        </h1>

        <!-- div kullanılarak Google Auto-Ads intent chiplerinin metne sızması engellendi -->
        <div class="yym-page-subtitle" style="color: #94a3b8; font-size: 1.05rem; max-width: 720px; margin: 0 auto; line-height: 1.55;">
            <?php if ($is_transit && !empty($bolge_highways)) : ?>
                <?php printf(__('%s güzergahında hazır bekleyen devriye kurtarma ekiplerimizle ortalama %s içinde yanınızdayız!', 'yol-yardim-merkezi'), esc_html($bolge_highways), esc_html($eta)); ?>
            <?php else : ?>
                <?php printf(__('%s bölgesinde nöbetçi çekici araçlarımızla ortalama %s içinde yanınızdayız!', 'yol-yardim-merkezi'), esc_html($clean_name), esc_html($eta)); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="yym-container yym-page-container" style="padding-top: 28px;">
    <div class="yym-page-layout">
        <main id="primary" class="yym-main-content">
            <article id="post-<?php the_ID(); ?>" <?php post_class('yym-article yym-region-detail'); ?>>
                
                <!-- Bölgesel Durum Açıklama ve Firma Seçim Kutusu -->
                <div class="yym-in-post-cta yym-region-top-cta google-auto-ads-ignore" style="background: linear-gradient(135deg, #0b192c 0%, #172a46 100%); border: 1px solid rgba(255,255,255,0.1); border-left: 6px solid #f59e0b; border-radius: 16px; padding: 26px 28px; margin: 0 0 26px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.12); color: #fff;">
                    <div class="yym-cta-banner">
                        <div class="yym-live-badge" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(34, 197, 94, 0.18); border: 1px solid rgba(34, 197, 94, 0.35); padding: 5px 12px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; color: #4ade80; margin-bottom: 12px;">
                            <span class="yym-pulse-dot" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%; box-shadow: 0 0 8px #22c55e;"></span>
                            <span class="yym-badge-txt"><?php printf(__('%s Canlı Nöbetçi Çekici Ağı', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></span>
                        </div>

                        <h3 style="color: #ffffff; font-size: 1.4rem; font-weight: 800; margin: 0 0 10px 0; line-height: 1.3;">
                            <?php printf(__('%s Güzergahında Yolda mı Kaldınız?', 'yol-yardim-merkezi'), esc_html($clean_name)); ?>
                        </h3>

                        <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6; margin: 0 0 20px 0;">
                            <?php printf(__('Bu güzergah boyunca ve otoyol bağlantı noktalarında 7/24 devriye gezen lisanslı (K1/K2 belgeli) ve kaskolu bağımsız kurtarıcı ekipleri bulunmaktadır. Aşağıdaki <strong>canlı nöbetçi çekici listesinden</strong> aracınızın konumuna en yakın firmayı kendiniz inceleyebilir, doğrudan arayabilir veya WhatsApp ile anlık konum gönderebilirsiniz.', 'yol-yardim-merkezi'), esc_html($clean_name)); ?>
                        </p>
                        
                        <div class="yym-cta-btns" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center;">
                            <!-- Müşterinin kendisinin firma seçmesini sağlayan yönlendirme butonu -->
                            <a href="#nobetci-cekiciler" class="yym-btn yym-btn-action" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #0f172a !important; padding: 13px 26px; border-radius: 10px; font-weight: 800; font-size: 1rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); border: none; transition: transform 0.15s ease;">
                                🚜 <?php _e('Bölgedeki Nöbetçi Çekicileri İncele ↓', 'yol-yardim-merkezi'); ?>
                            </a>
                            
                            <button type="button" class="yym-btn yym-btn-location js-share-location-btn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff !important; padding: 13px 22px; border-radius: 10px; font-weight: 700; font-size: 0.95rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);">
                                📍 <?php _e('Canlı GPS Konumumu Hazırla', 'yol-yardim-merkezi'); ?>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Otoyol / Bölge Güvenlik Talimatı Uyarısı -->
                <div class="google-auto-ads-ignore" style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 5px solid #f59e0b; padding: 16px 20px; border-radius: 12px; margin: 0 0 28px 0;">
                    <div style="display: flex; align-items: flex-start; gap: 12px;">
                        <span style="font-size: 1.5rem; line-height: 1;">⚠️</span>
                        <div>
                            <h4 style="margin: 0 0 6px 0; color: #92400e; font-size: 1rem; font-weight: 700;"><?php _e('Otoyol ve Kritik Geçiş Güvenlik Talimatı', 'yol-yardim-merkezi'); ?></h4>
                            <p style="margin: 0; color: #78350f; font-size: 0.88rem; line-height: 1.55;">
                                <?php _e('Can güvenliğiniz için aracınızı mümkünse emniyet şeridine alın, 4\'lü flaşörlerinizi yakın, üçgen reflektörünüzü aracın 150 metre gerisine yerleştirin ve kesinlikle araç içinde beklemeyip <strong>çelik bariyerlerin arkasına</strong> geçerek ekibimize konum iletin.', 'yol-yardim-merkezi'); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bölge / Otoyol Nöbetçi Çekici Ekipleri Slider (Hedef Liste) -->
                <div id="nobetci-cekiciler" class="yym-region-slider-section google-auto-ads-ignore" style="margin: 0 0 34px 0; scroll-margin-top: 100px;">
                    <div style="background: linear-gradient(135deg, #0f172a, #1e293b); padding: 16px 22px; border-radius: 12px; margin-bottom: 20px; border-left: 5px solid #2563eb; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <h3 style="color: #fff; font-size: 1.15rem; font-weight: 700; margin: 0 0 4px 0; display: flex; align-items: center; gap: 8px;">
                                <span>🚨</span> <?php printf(__('%s Çevresi Nöbetçi Çekiciler', 'yol-yardim-merkezi'), esc_html($clean_name)); ?>
                            </h3>
                            <p style="color: #94a3b8; font-size: 0.85rem; margin: 0;"><?php _e('Aşağıdaki firmalardan dilediğinizi seçip doğrudan tek tıkla arayabilir veya WhatsApp\'tan yazabilirsiniz.', 'yol-yardim-merkezi'); ?></p>
                        </div>
                        <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 6px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                            <span class="yym-pulse-dot" style="width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span>
                            <?php _e('Canlı Nöbetçi Filosu', 'yol-yardim-merkezi'); ?>
                        </span>
                    </div>
                    <?php
                    $slider_target_city = !empty($bolge_city) ? $bolge_city : $clean_name;
                    echo do_shortcode('[yym_firma_slider sehir="' . esc_attr($slider_target_city) . '" limit="8" show_phone="1" show_whatsapp="1"]');
                    ?>
                </div>

                <div class="yym-entry-content">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php if (get_the_content()) : ?>
                            <?php the_content(); ?>
                        <?php else : ?>
                            <h2><?php printf(__('%s En Yakın Oto Kurtarma ve Çekici Hizmeti', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></h2>
                            <p><?php printf(__('%s ve çevre mahallelerinde aracınızla kaza yaptıysanız, akünüz bittiyse veya lastiğiniz patladıysa panik yapmanıza gerek yok. Yol Yardım Merkezi olarak %s genelinde hazır bekleyen nöbetçi çekici araçlarımızla 7/24 hizmet veriyoruz.', 'yol-yardim-merkezi'), esc_html($clean_name), esc_html($clean_name)); ?></p>
                            
                            <h3><?php printf(__('%s Bölgesinde Neler Yapıyoruz?', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></h3>
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
                <div class="yym-service-perks-box google-auto-ads-ignore" style="margin-top: 32px;">
                    <h3><?php printf(__('%s Çekici Hizmetinde Neden Biz?', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></h3>
                    <div class="yym-perks-grid">
                        <div class="yym-perk-item">
                            <span class="yym-perk-icon">⚡</span>
                            <div>
                                <strong><?php _e('En Hızlı Varış Garantisi', 'yol-yardim-merkezi'); ?></strong>
                                <p><?php printf(__('%s güzergahına özel görevlendirilmiş nöbetçi çekici araçlarımızla 15-30 dakikada yanınızdayız.', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></p>
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
            <div class="yym-sidebar-widget yym-widget-emergency google-auto-ads-ignore" style="border-top: 4px solid #f59e0b;">
                <span class="yym-widget-icon">🚨</span>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;"><?php printf(__('%s Çekici Bul', 'yol-yardim-merkezi'), esc_html($clean_name)); ?></h3>
                <p style="color: #64748b; font-size: 0.88rem; margin-bottom: 16px;"><?php _e('Size en yakın nöbetçi çekiciyi listeden inceleyip doğrudan iletişime geçebilirsiniz.', 'yol-yardim-merkezi'); ?></p>
                <a href="#nobetci-cekiciler" class="yym-btn yym-btn-call yym-btn-block" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #0f172a; font-weight: 800; border-radius: 8px; padding: 12px; margin-bottom: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    🚜 <?php _e('Nöbetçi Çekicileri Gör', 'yol-yardim-merkezi'); ?>
                </a>
                <button type="button" class="yym-btn yym-btn-location yym-btn-block js-share-location-btn" style="background: #0284c7; color: #fff; border: none; font-weight: 600; border-radius: 8px; padding: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px;">
                    📍 <?php _e('WhatsApp Konum Hazırla', 'yol-yardim-merkezi'); ?>
                </button>
            </div>

            <div class="yym-sidebar-widget google-auto-ads-ignore">
                <h4 style="font-size: 1rem; font-weight: 700; margin-bottom: 12px; color: #0f172a;">🛣️ <?php _e('Transit Otoyol Hatları', 'yol-yardim-merkezi'); ?></h4>
                <ul class="yym-widget-links" style="list-style: none; padding: 0; margin: 0;">
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/kuzey-marmara-otoyolu-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">Kuzey Marmara Otoyolu (KMO)</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/gebze-izmir-otoyolu-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">Gebze - İzmir Otoyolu (O-5)</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/bolu-dagi-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">Bolu Dağı & Tüneli Çekici</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/tag-otoyolu-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">TAG Otoyolu (Adana-Gaziantep)</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/ankara-nigde-otoyolu-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">Ankara - Niğde Otoyolu (O-21)</a></li>
                    <li style="margin-bottom: 8px;"><a href="<?php echo esc_url(home_url('/bolgeler/tem-otoyolu-cekici/')); ?>" style="color: #2563eb; text-decoration: none; font-size: 0.9rem;">TEM Otoyolu (İstanbul-Edirne)</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php
get_footer();
