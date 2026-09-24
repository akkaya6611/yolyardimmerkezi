<?php
/**
 * Template Name: Sıkça Sorulan Sorular
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$faqs = array(
    'suruculer' => array(
        'title' => '🚗 Sürücüler İçin Sorular',
        'items' => array(
            array(
                'q' => 'Yol Yardım Merkezi bir çekici çağrı merkezi midir?',
                'a' => 'Hayır. Yol Yardım Merkezi, Türkiye genelinde 81 il ve 922 ilçedeki lisanslı oto çekici ve yol yardım firmalarını listeleyen bağımsız bir rehber platformudur. Sizi aracı olmadan doğrudan o bölgedeki çekici operatörüyle buluştururuz.'
            ),
            array(
                'q' => 'Sitedeki firmaları aramak için herhangi bir komisyon veya aracılık ücreti ödüyor muyum?',
                'a' => 'Kesinlikle hayır! Sitemiz üzerinden çekici firmalarını aramak veya WhatsApp üzerinden konum göndermek %100 ücretsizdir. Çekici ücretini doğrudan hizmet aldığınız esnafla belirlersiniz.'
            ),
            array(
                'q' => 'WhatsApp ile konum gönderme nasıl çalışır?',
                'a' => 'Firma kartında yer alan "📍 Konumumu Gönder" butonuna dokunduğunuzda, telefonunuzun GPS alıcısı anlık konum koordinatlarınızı alır ve otomatik olarak o firmanın WhatsApp sohbetine Google Haritalar linki olarak yapıştırır.'
            ),
            array(
                'q' => 'Taşınan araç kaskolu ve sigortalı mıdır?',
                'a' => 'Evet. Platformumuzda yer alan onaylı çekici firmalarının tamamı emtia nakliyat sigortalı ve kayar kasalı araçlarla hizmet vermektedir. Taşıma sırasında aracınız resmi sigorta poliçesi teminatı altındadır.'
            ),
            array(
                'q' => 'Ödemeyi nasıl yapabilirim?',
                'a' => 'Kurtarıcı araçlarımızın büyük çoğunluğunda Mobil POS cihazı bulunmaktadır. Nakit, kredi kartı veya banka havalesi / FAST yöntemiyle ödeme yapabilirsiniz.'
            ),
        )
    ),
    'firmalar' => array(
        'title' => '[tow] Çekici & Kurtarıcı Esnafı İçin Sorular',
        'items' => array(
            array(
                'q' => 'Çekici veya oto kurtarma firmamı sisteme nasıl ekleyebilirim?',
                'a' => 'Üst menüde yer alan "➕ Firma Ekle" butonuna tıklayarak açılan formu doldurmanız yeterlidir. İnceleme ekibimiz iletişim bilgilerinizi doğruladıktan sonra ilanınız 81 il rehberimizde yayına alınır.'
            ),
            array(
                'q' => 'Firma eklemek ücretli midir?',
                'a' => 'Firma kaydı ve listeleme lansmana özel ücretsizdir. Lansmanın bitiş tarihi henüz belirlenmemiştir. Profesyonel ve Premium ücretli planlar yakında açılacaktır. Bölgenizde yolda kalan sürücülerden gelen telefon ve WhatsApp çağrılarından hiçbir komisyon kesilmez.'
            ),
            array(
                'q' => 'Telefon veya adres bilgilerim değiştiğinde nasıl güncellerim?',
                'a' => 'info@yolyardimmerkezi.com.tr adresimize kayıtlı firma adınız ve yeni iletişim bilgilerinizi ileterek güncellemeyi talep edebilirsiniz.'
            ),
        )
    )
);
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <span class="yym-hero-mini-badge">❓ MERAK EDİLENLER</span>
            <h1 class="yym-page-hero-title">Sıkça Sorulan Sorular</h1>
            <p class="yym-page-hero-desc">
                Yol yardım çağırma süreci, WhatsApp konum paylaşımı ve firma kaydıyla ilgili aklınıza takılan tüm soruların yanıtları.
            </p>
        </div>
    </div>

    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px; max-width: 900px;">
        <?php foreach ($faqs as $group) : ?>
            <div class="yym-faq-group" style="margin-bottom: 45px;">
                <h2 style="font-size: 1.4rem; font-weight: 800; color: #0F172A; margin-bottom: 20px; border-bottom: 2px solid #E2E8F0; padding-bottom: 10px;">
                    <?php echo str_replace('[tow]', mis360_tow_symbol(), esc_html($group['title'])); ?>
                </h2>

                <div class="yym-accordion-list">
                    <?php foreach ($group['items'] as $item) : ?>
                        <details class="yym-faq-item">
                            <summary class="yym-faq-question">
                                <span><?php echo esc_html($item['q']); ?></span>
                                <span class="yym-faq-icon">+</span>
                            </summary>
                            <div class="yym-faq-answer">
                                <p><?php echo esc_html($item['a']); ?></p>
                            </div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Başka Sorunuz mu Var? -->
        <div class="yym-faq-support-card">
            <h3>Sorunuza yanıt bulamadınız mı?</h3>
            <p>Platform yöneticimize e-posta göndererek her türlü konuda doğrudan destek alabilirsiniz.</p>
            <a href="<?php echo esc_url(home_url('/iletisim/')); ?>" class="lst-btn lst-btn-coral">
                ✉️ İletişime Geçin
            </a>
        </div>
    </div>
</div>

<style>
.yym-faq-item {
    background: #ffffff;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    margin-bottom: 14px;
    overflow: hidden;
    transition: all 0.2s;
}
.yym-faq-item[open] {
    border-color: #FA5343;
    box-shadow: 0 4px 14px rgba(250, 83, 67, 0.08);
}
.yym-faq-question {
    padding: 18px 22px;
    font-weight: 700;
    font-size: 1.05rem;
    color: #0F172A;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    list-style: none;
    user-select: none;
}
.yym-faq-question::-webkit-details-marker {
    display: none;
}
.yym-faq-icon {
    font-size: 1.4rem;
    color: #FA5343;
    font-weight: 400;
    transition: transform 0.2s;
}
.yym-faq-item[open] .yym-faq-icon {
    transform: rotate(45deg);
}
.yym-faq-answer {
    padding: 0 22px 20px 22px;
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.7;
}
.yym-faq-support-card {
    background: linear-gradient(135deg, #17222D 0%, #283948 100%);
    color: #ffffff;
    text-align: center;
    padding: 35px 25px;
    border-radius: 16px;
    margin-top: 30px;
}
.yym-faq-support-card h3 {
    color: #ffffff;
    font-size: 1.3rem;
    font-weight: 800;
    margin-bottom: 8px;
}
.yym-faq-support-card p {
    color: #cbd5e1;
    font-size: 0.95rem;
    margin-bottom: 20px;
}
</style>

<?php
get_footer();
