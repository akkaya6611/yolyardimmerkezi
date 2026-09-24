<?php
/**
 * Yol Yardım Merkezi - Transit Otoyol, Tünel ve Kritik Geçiş Noktaları Modülü
 * Kuzey Marmara, O-5 Gebze-İzmir, Bolu Dağı, TAG, Ankara-Niğde, TEM Otoyolu vb.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Transit Otoyol & Kritik Geçiş Veri Listesi
 */
function yym_get_default_transit_routes() {
    return array(
        'kuzey-marmara-otoyolu-cekici' => array(
            'title'        => 'Kuzey Marmara Otoyolu (KMO) Acil Çekici & Kurtarma',
            'city'         => 'İstanbul',
            'highways'     => 'Kuzey Marmara Otoyolu (O-7), Yavuz Sultan Selim Köprüsü Bağlantıları',
            'description'  => 'Kuzey Marmara Otoyolu (O-7) Avrupa ve Anadolu otoyol güzergahında 7/24 nöbetçi ağır ticari ve binek oto çekici, kayar kasa kurtarıcı, akü takviye ve mobil lastik yol yardım hizmeti.',
            'content'      => '<!-- wp:paragraph -->
<p><strong>Kuzey Marmara Otoyolu (O-7)</strong> güzergahında Kınalı, Çatalca, Odayeri, Yavuz Sultan Selim Köprüsü, Paşaköy, Mecidiye, Kurtköy, Dilovası, Akyazı bağlantı noktaları boyunca 7/24 hazır bekleyen nöbetçi çekici araçlarımızla yanınızdayız.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Kuzey Marmara Otoyolu\'nda Yolda Kaldığınızda Ne Yapmalısınız?</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Otoyol hız sınırları yüksek olduğu için güvenlik ilk önceliğiniz olmalıdır. Aracınız arızalandığında veya lastiğiniz patladığında hemen sağ emniyet şeridine çekin, dörtlü ikaz lambalarınızı yakın ve aracınızın en az 150 metre gerisine reflektör yerleştirin. Araç içinde beklemek yerine çelik bariyerlerin arkasına geçerek ekibimize WhatsApp üzerinden canlı konum gönderin.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>Kuzey Marmara Otoyolu Çekici ve Kurtarma Kapsamımız</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li><strong>Otomobil &amp; SUV Çekici:</strong> Kayar kasa ve ahtapot vinçli sistemlerle araçlarınıza sıfır hasar güvencesi.</li>
<li><strong>Ağır Vasıta &amp; Kamyon Çekici:</strong> KMO güzergahında tır, otobüs, kamyon kurtarma ve körüklü çekici desteği.</li>
<li><strong>Otoyol Lastik Yol Yardımı:</strong> Yolda patlayan lastikleriniz için mobil stepne değişimi ve yerinde fitil tamiri.</li>
<li><strong>Yerinde Akü Takviye (12V &amp; 24V):</strong> Yüksek amperli profesyonel takviye cihazlarıyla anında marş desteği.</li>
</ul>
<!-- /wp:list -->',
        ),
        'gebze-izmir-otoyolu-cekici' => array(
            'title'        => 'Gebze - Orhangazi - İzmir Otoyolu (O-5) Çekici & Yol Yardım',
            'city'         => 'Bursa',
            'highways'     => 'Gebze-İzmir Otoyolu (O-5), Osmangazi Köprüsü, Balıkesir-Manisa Hattı',
            'description'  => 'İstanbul-Bursa-İzmir O-5 Otoyolu ve Osmangazi Köprüsü güzergahında 7/24 en yakın nöbetçi oto çekici ve yol yardım ekipleri.',
            'content'      => '<!-- wp:paragraph -->
<p><strong>Gebze - Orhangazi - İzmir Otoyolu (O-5)</strong> üzerinde Osmangazi Köprüsü, Yalova, Gemlik, Bursa batı/doğu kavşakları, Balıkesir, Manisa ve İzmir Bornova varış hattına kadar kesintisiz 7/24 otoyol oto kurtarıcı filomuzla hizmet veriyoruz.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Osmangazi Köprüsü ve O-5 Otoyolu Acil Müdahale Ekipleri</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>O-5 otoyolu boyunca belirli servis ve çıkış noktalarında (Oksijen dinlenme tesisleri yakınlarında) konuşlanmış çekici filolarımız, çağrınızdan itibaren ortalama 15-25 dakika içerisinde yanınıza ulaşır. Tüm transferlerimiz Taşıma Kaskosu (Emtia Sigortası) kapsamında güvence altındadır.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>O-5 Otoyolu Sunulan Hizmetler</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Osmangazi Köprüsü Giriş &amp; Çıkış Acil Oto Çekici</li>
<li>Bursa &amp; Balıkesir Otoyol Gişeleri Kurtarıcı Desteği</li>
<li>Yakıt Takviyesi ve Yanlış Yakıt Tahliyesi</li>
<li>Otoyol Otomobil, Minibüs ve Motosiklet Nakliyesi</li>
</ul>
<!-- /wp:list -->',
        ),
        'bolu-dagi-cekici' => array(
            'title'        => 'Bolu Dağı Geçişi & Tüneli 7/24 Acil Çekici ve Kurtarıcı',
            'city'         => 'Bolu',
            'highways'     => 'Anadolu Otoyolu (O-4) Bolu Dağı Tüneli, D-100 Bolu Dağı Geçişi',
            'description'  => 'Bolu Dağı Tüneli ve D-100 dağ geçişinde kar, buzlanma, hararet ve kaza durumlarında anında müdahale eden 7/24 nöbetçi çekici ekipleri.',
            'content'      => '<!-- wp:paragraph -->
<p>Türkiye\'nin en kritik transit geçiş noktalarından biri olan <strong>Bolu Dağı Geçişi ve Bolu Dağı Tüneli (O-4 Anadolu Otoyolu)</strong> ile <strong>D-100 Karayolu</strong> rampalarında arıza yapan, hararet yükselten veya kaza geçiren araçlar için 7/24 hazır kurtarma araçlarımız bulunmaktadır.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Zorlu Kış Şartlarında Bolu Dağı Kurtarma Desteği</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Kış aylarında yoğun kar yağışı ve buzlanmanın görüldüğü Bolu Dağı güzergahında 4x4 donanımlı vinçli kurtarma araçlarımız, kayan ve şarampole inen araçları güvenle yola çıkarır. Kayar kasa taşıyıcılarımız aracınızı hasarsız olarak Düzce veya Bolu sanayi merkezlerine ulaştırır.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>Bolu Dağı Acil Yol Yardım Hizmetlerimiz</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Bolu Dağı Tüneli Doğu ve Batı Girişi Hızlı Çekici</li>
<li>Karda Kayan, Yoldan Çıkan Araçlar İçin Vinç Desteği</li>
<li>Ağır Vasıta, Kamyon ve Otobüs Çekimi</li>
<li>Mobil Zincir, Lastik ve Antifriz Takviyesi</li>
</ul>
<!-- /wp:list -->',
        ),
        'tag-otoyolu-cekici' => array(
            'title'        => 'TAG Otoyolu (Tarsus - Adana - Gaziantep) Acil Çekici Hizmeti',
            'city'         => 'Adana',
            'highways'     => 'TAG Otoyolu (O-52), Çukurova Transit Koridoru',
            'description'  => 'TAG Otoyolu (Tarsus, Adana, Osmaniye, Gaziantep) üzerinde 7/24 nöbetçi oto kurtarma, çekici ve yol yardım ağı.',
            'content'      => '<!-- wp:paragraph -->
<p><strong>Tarsus - Adana - Gaziantep Otoyolu (TAG Otoyolu / O-52)</strong>, Akdeniz ile Güneydoğu Anadolu\'yu birbirine bağlayan en yoğun lojistik koridorudur. TAG otoyolu boyunca Tarsus, Mersin ayrımı, Adana kuzey/güney çevre yolu, Ceyhan, Osmaniye, Nurdağı ve Gaziantep güzergahlarında 7/24 nöbetçi çekici araçlarımız mevcuttur.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Nurdağı Rampaları ve TAG Otoyolu Hızlı Oto Kurtarıcı</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Özellikle Nurdağı geçişindeki eğimli rampalarda hararet yapan tırlar ve binek araçlar için acil soğutma ve nakil hizmeti sağlıyoruz. Lisanslı ve K1/K2 belgeli çekici filomuz faturalı ve sigortalı güvenceyle çalışmaktadır.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>TAG Otoyolu Hizmet Avantajlarımız</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Adana - Gaziantep Arası 30 Dakika İçi Hızlı İntikal</li>
<li>Tır, Kamyon, Kamyonet ve Ağır Vasıta Çekici Hizmeti</li>
<li>Kaza Yeri Güvenliği ve Zabıt Süreçlerinde Destek</li>
<li>Sabit Fiyat Garantisi ve Resmi Taşıma Sigortası</li>
</ul>
<!-- /wp:list -->',
        ),
        'ankara-nigde-otoyolu-cekici' => array(
            'title'        => 'Ankara - Niğde Otoyolu 7/24 Nöbetçi Çekici & Kurtarıcı',
            'city'         => 'Ankara',
            'highways'     => 'Ankara - Niğde Otoyolu (O-21), Tuz Gölü Güzergahı',
            'description'  => 'Ankara - Niğde Otoyolu (O-21) boyunca Haymana, Şereflikoçhisar, Aksaray ve Niğde bağlantılarında nöbetçi akıllı otoyol çekici hizmeti.',
            'content'      => '<!-- wp:paragraph -->
<p><strong>Ankara - Niğde Otoyolu (O-21)</strong>, İç Anadolu\'yu Akdeniz\'e bağlayan en modern akıllı otoyoldur. Uzun düzlükleri ve geniş mesafeleri barındıran bu hatta arıza veya kaza durumunda beklememeniz için otoyol kavşaklarına konuşlanmış çekici ağımızla anında destek veriyoruz.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Tuz Gölü ve Aksaray Ayrımında En Yakın Çekici</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>Gölbaşı, Bala, Evren, Kırşehir bağlantısı, Şereflikoçhisar, Ortaköy, Aksaray ve Niğde güzergahlarında aracınız arızalandığında konumunuzu bir tuşla çekici ekibimize iletebilir, en yakın yetkili servise aracınızı güvenle çektirebilirsiniz.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>Ankara - Niğde Otoyolu Hizmet Detayları</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Otomobil, SUV ve Karavan Taşıma</li>
<li>Akıllı Otoyol Dinlenme Tesislerinden Araç Alma</li>
<li>7/24 Akü Takviye ve Mobil Lastik Tamiri</li>
<li>Şeffaf Kilometre Başına Adil Fiyat Tarifesi</li>
</ul>
<!-- /wp:list -->',
        ),
        'tem-otoyolu-cekici' => array(
            'title'        => 'TEM Otoyolu (Edirne - İstanbul - Kocaeli) Acil Oto Çekici',
            'city'         => 'İstanbul',
            'highways'     => 'TEM Otoyolu (O-3 / O-4), Avrupa ve Anadolu Transit Geçiş Hattı',
            'description'  => 'TEM Otoyolu Edirne, Çorlu, Mahmutbey, FSM Köprüsü, Çamlıca, Gebze ve İzmit hattında nöbetçi acil oto kurtarıcı.',
            'content'      => '<!-- wp:paragraph -->
<p><strong>TEM Otoyolu (Trans European Motorway - O-3 ve O-4)</strong> Türkiye\'nin en işlek ana arteri konumundadır. Kapıkule / Edirne sınırından başlayıp Tekirdağ, Çorlu, Çerkezköy, Silivri, Mahmutbey gişeleri, Fatih Sultan Mehmet Köprüsü, Ataşehir, Dudullu, Sultanbeyli, Gebze ve İzmit hattı boyunca 7/24 nöbetçi çekici araçlarımız devriye halindedir.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":2} -->
<h2>Yoğun Trafikte Hızlı Müdahale ve Emniyetli Nakil</h2>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p>TEM otoyolunda arıza yapan araçlar trafik akışını riske atmadan dakikalar içinde çekiciye yüklenmeli ve güvenli alana tahliye edilmelidir. Özel kayar kasalı hızlı kurtarıcılarımızla aracınızı emniyete alıyor, dilediğiniz özel veya yetkili servise ulaştırıyoruz.</p>
<!-- /wp:paragraph -->
<!-- wp:heading {"level":3} -->
<h3>TEM Otoyolu Çekici ve Kurtarıcı Kapsamı</h3>
<!-- /wp:heading -->
<!-- wp:list -->
<ul>
<li>Köprü Bağlantı Yolları ve Gişeler Çekici Desteği</li>
<li>Binek, Ticari ve Ağır Vasıta Kurtarma</li>
<li>Hasarlı Kaza Kurtarma ve Vinç Hizmeti</li>
<li>Kredi Kartı ile Araç Başı Ödeme İmkanı</li>
</ul>
<!-- /wp:list -->',
        ),
    );
}

/**
 * Transit Otoyol Sayfalarını Otomatik Olarak 'bolge' CPT Olarak Oluştur / Güncelle
 */
function yym_seed_transit_routes_if_missing() {
    $seeded = get_option('yym_transit_routes_v1_seeded');
    if ($seeded) {
        return;
    }

    $routes = yym_get_default_transit_routes();

    foreach ($routes as $slug => $data) {
        $existing = get_page_by_path($slug, OBJECT, 'bolge');
        if (!$existing) {
            $post_id = wp_insert_post(array(
                'post_title'   => $data['title'],
                'post_name'    => $slug,
                'post_content' => $data['content'],
                'post_excerpt' => $data['description'],
                'post_status'  => 'publish',
                'post_type'    => 'bolge',
            ));

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_bolge_city', $data['city']);
                update_post_meta($post_id, '_bolge_highways', $data['highways']);
                update_post_meta($post_id, '_is_transit_highway', '1');
            }
        } else {
            update_post_meta($existing->ID, '_bolge_city', $data['city']);
            update_post_meta($existing->ID, '_bolge_highways', $data['highways']);
            update_post_meta($existing->ID, '_is_transit_highway', '1');
        }
    }

    update_option('yym_transit_routes_v1_seeded', 1);
}
add_action('init', 'yym_seed_transit_routes_if_missing', 20);
