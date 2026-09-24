<?php
/**
 * Listivo Demo 5 - Sürücü Rehberi & Uzman Yazıları
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$posts = array(
    array(
        'title'  => 'Yolda Kalınca Ne Yapılmalı? 5 Hayati Güvenlik Kuralı',
        'author' => 'Yol Güvenliği Uzmanı',
        'date'   => '15 Eylül 2026',
        'image'  => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/09/card_1-750x500.jpg',
        'desc'   => 'Otoyolda veya yoğun trafikte aracınız durduğunda dörtlüleri yakmak, reflektör mesafesi ve güvenli bariyer arkası adımları.'
    ),
    array(
        'title'  => 'Akü Bittiğinde Takviye Kablosu Nasıl Bağlanır? Doğru Sıralama',
        'author' => 'Teknik Servis Ekibi',
        'date'   => '12 Eylül 2026',
        'image'  => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_2.jpg',
        'desc'   => 'Elektronik beyni yakmadan, artı ve eksi kutup bağlantılarını doğru sırayla bağlayarak aracı güvenle çalıştırma rehberi.'
    ),
    array(
        'title'  => 'Çekici Çağırırken Sürpriz Fiyatlarla Karşılaşmamak İçin İpuçları',
        'author' => 'Yol Yardım Rehberi',
        'date'   => '8 Eylül 2026',
        'image'  => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg',
        'desc'   => 'Telefonda net mesafe belirleme, kaskolu taşıma güvencesi sorgulama ve sabit fiyat sözleşmesinin önemi.'
    ),
);
?>

<section class="lst-section lst-news-section" id="rehber">
    <div class="lst-container">
        <div class="lst-section-heading-bar">
            <div class="lst-heading-group">
                <span class="lst-pill-sub">Sürücü Rehberi</span>
                <h2 class="lst-section-title">Yol Yardım Uzmanlarımızdan Tavsiyeler</h2>
            </div>
            <a href="#tum-yazilar" class="lst-link-arrow">
                <span>Tüm Yazılar</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="lst-news-grid">
            <?php foreach ($posts as $post) : ?>
                <article class="lst-news-card">
                    <div class="lst-news-thumb">
                        <img src="<?php echo esc_url($post['image']); ?>" alt="<?php echo esc_attr($post['title']); ?>" loading="lazy">
                    </div>
                    <div class="lst-news-content">
                        <div class="lst-news-meta">
                            <span class="lst-news-author"><?php echo esc_html($post['author']); ?></span>
                            <span class="lst-meta-dot">•</span>
                            <span class="lst-news-date"><?php echo esc_html($post['date']); ?></span>
                        </div>
                        <h3 class="lst-news-title">
                            <a href="#makale"><?php echo esc_html($post['title']); ?></a>
                        </h3>
                        <p class="lst-news-desc"><?php echo esc_html($post['desc']); ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
