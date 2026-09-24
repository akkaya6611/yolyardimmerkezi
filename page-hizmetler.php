<?php
/**
 * Template Name: Hizmetlerimiz
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$services = array(
    array(
        'id'       => 'cekici',
        'title'    => '7/24 Acil Oto Çekici & Kurtarma',
        'icon'     => 'tow-truck',
        'badge'    => 'EN ÇOK TALEP EDİLEN',
        'desc'     => 'Kaza, mekanik arıza, hararet veya yakıt bitmesi durumlarında aracınızı sıfır hasar güvencesiyle dilediğiniz yetkili servise veya sanayi noktasına taşıyoruz.',
        'features' => array(
            'Kayar kasalı ve hidrolik modern araç filosu',
            '%100 Emtia nakliyat sigortası teminatı',
            'Alçak şaseli ve modifiyeli araçlara özel rampa',
            'Ortalama 15-30 dakikada adrese varış',
        ),
        'filter'   => 'cekici',
    ),
    array(
        'id'       => 'aku',
        'title'    => 'Yerinde Akü Takviye & Satış',
        'icon'     => '⚡',
        'badge'    => '15 DAKİKADA YANINIZDA',
        'desc'     => 'Aracınız marş basmıyorsa çekici çağırmadan sorunu yerinde çözüyoruz. Profesyonel 12V/24V booster cihazlarımızla akünüz canlandırılır veya 2 yıl garantili yeni akü montajı yapılır.',
        'features' => array(
            'Dijital şarj dinamosu ve akü voltaj testi',
            'Start-Stop ve AGM akülere özel takviye',
            'Tanınmış markalarla yerinde sıfır akü satışı',
            'Eski akünüzü değerinde geri alma imkanı',
        ),
        'filter'   => 'aku',
    ),
    array(
        'id'       => 'lastik',
        'title'    => 'Mobil Lastik Tamiri & Stepne Değişimi',
        'icon'     => '🔧',
        'badge'    => 'YERİNDE ONARIM',
        'desc'     => 'Otoyolda, TEM ve Kuzey Marmara güzergahında lastiğiniz patladığında tam donanımlı mobil lastikçi aracımız yanınıza gelir. Yerinde fitil, mantar yama ve stepne takma işlemleri yapılır.',
        'features' => array(
            'Mobil hava kompresörü ve bijon sökme tabancası',
            'Yol kenarında güvenli bijon sıkma ve balans kontrolü',
            'Yeni ve çıkma lastik temini desteği',
            'Tüm binek ve hafif ticari ebatlara uygun ekipman',
        ),
        'filter'   => 'lastik',
    ),
    array(
        'id'       => 'motosiklet',
        'title'    => 'Özel Motosiklet Taşıma & Çekici',
        'icon'     => '🏍️',
        'badge'    => 'ÇİZİLMEZ GÜVENCE',
        'desc'     => 'Scooter, Touring, Enduro ve Racing motosikletler standart çekicilerde taşınamaz. Kilitli ön teker sehpası ve yumuşak sabitleme kemerleriyle motosikletinize tek bir çizik gelmeden naklediyoruz.',
        'features' => array(
            'Özel kilitli ön jant sabitleme mekanizması',
            'Düşmeyi önleyen hidrolik süspansiyonlu basamak',
            'Kapalı ve açık kasa taşıma opsiyonları',
            'Muayene, plaka ve servis transfer hizmeti',
        ),
        'filter'   => 'motosiklet',
    ),
    array(
        'id'       => 'agir-vasita',
        'title'    => 'Ağır Vasıta & Ahtapot Vinç Kurtarma',
        'icon'     => '🏗️',
        'badge'    => 'AĞIR VASITA UZMANI',
        'desc'     => 'Kamyon, otobüs, TIR, minibüs, iş makinesi ve şarampole yuvarlanan veya tekeri kilitlenen araçlar için teleskopik vinç ve ahtapot vinçli ağır ticari kurtarıcılarımız görev başındadır.',
        'features' => array(
            'Tekerden askı ve ahtapot vinçle kaldırma sistemi',
            'Hava takviyesi ve şaft sökme müdahalesi',
            'Otoyol ve viyadük kaza kurtarma uzmanlığı',
            'Kurumsal ve faturalı filo hizmeti',
        ),
        'filter'   => 'agir-vasita',
    ),
    array(
        'id'       => 'sehirlerarasi',
        'title'    => 'Şehirlerarası Araç Transferi',
        'icon'     => '🛣️',
        'badge'    => '81 İL KAPSIYOR',
        'desc'     => 'Aracınızı Türkiye\'nin bir şehrinden başka bir şehrine güvenle mi taşımak istiyorsunuz? Tekli özel çekici veya çoklu taşıyıcı araçlarımızla kapıdan kapıya kaskolu nakliye sağlıyoruz.',
        'features' => array(
            'Resmi araç teslim tutanağı ve ekspertiz kaydı',
            'Taşıma süresince anlık araç takip imkanı',
            'Arızalı, kazalı veya çekme belgeli araç taşıma',
            'Km başına sabit ve şeffaf navlun fiyatı',
        ),
        'filter'   => 'cekici',
    ),
);
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <span class="yym-hero-mini-badge">🔧 KESİNTİSİZ 7/24 DESTEK</span>
            <h1 class="yym-page-hero-title">Yol Yardım Hizmetlerimiz</h1>
            <p class="yym-page-hero-desc">
                Türkiye genelinde binek otomobillerden ağır vasıtalara kadar yolda ihtiyaç duyabileceğiniz tüm acil kurtarma ve servis hizmetleri parmaklarınızın ucunda.
            </p>
        </div>
    </div>

    <!-- Hizmet Kartları Grid -->
    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px;">
        <div class="yym-services-full-grid">
            <?php foreach ($services as $svc) : ?>
                <div class="yym-service-detail-card" id="<?php echo esc_attr($svc['id']); ?>">
                    <div class="yym-svc-card-header">
                        <span class="yym-svc-big-icon"><?php echo $svc['icon'] === 'tow-truck' ? mis360_tow_symbol() : esc_html($svc['icon']); ?></span>
                        <div class="yym-svc-title-group">
                            <span class="yym-svc-pill-badge"><?php echo esc_html($svc['badge']); ?></span>
                            <h2 class="yym-svc-card-title"><?php echo esc_html($svc['title']); ?></h2>
                        </div>
                    </div>

                    <p class="yym-svc-card-desc"><?php echo esc_html($svc['desc']); ?></p>

                    <div class="yym-svc-card-features">
                        <h4>Hizmet Kapsamı & Avantajları:</h4>
                        <ul>
                            <?php foreach ($svc['features'] as $feat) : ?>
                                <li><span>✓</span> <?php echo esc_html($feat); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="yym-svc-card-footer">
                        <a href="<?php echo esc_url(home_url('/firmalar/?category=' . $svc['filter'])); ?>" class="lst-btn lst-btn-coral yym-btn-block-svc">
                            🚨 Bu Hizmeti Veren Firmaları Bul →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
get_footer();
