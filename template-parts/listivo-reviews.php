<?php
/**
 * Listivo Demo 5 - Yol Yardım Merkezi Müşteri Yorumları
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$reviews = array(
    array(
        'name'    => 'Murat K.',
        'role'    => 'Kayseri / Melikgazi - Binek Otomobil',
        'avatar'  => 'MK',
        'stars'   => '★★★★★',
        'comment' => 'Gece saat 02:00 civarında çevre yolunda hararet yükselmesi nedeniyle yolda kaldım. WhatsApp üzerinden anlık konumumu attım, 20 dakika dolmadan sarı kayar kasalı çekici geldi. Şoför arkadaş son derece kibardı, aracımı kaskolu şekilde sanayiye ulaştırdılar. Telefonda söylenen fiyat dışında bir kuruş fazla almadılar.'
    ),
    array(
        'name'    => 'Selin Y.',
        'role'    => 'İstanbul / Kadıköy - Akü Takviye',
        'avatar'  => 'SY',
        'stars'   => '★★★★★',
        'comment' => 'Sabah önemli bir toplantıya yetişecekken arabanın aküsü sıfırlanmıştı, marş basmıyordu. Yol Yardım Merkezi çağrı merkezini aradım, 15 dakikada mobil ekipleri kapıma ulaştı. Hem dijital akü testi yaptılar hem de booster takviyesiyle anında çalıştırdılar. Hayat kurtardınız!'
    ),
    array(
        'name'    => 'Burak E.',
        'role'    => 'Ankara / Çankaya - Motosiklet Taşıma',
        'avatar'  => 'BE',
        'stars'   => '★★★★★',
        'comment' => 'Enduro motorumun arka lastiği yarıldı ve hareket edemez hale geldim. Motosiklet taşıma konusunda çok hassasım, aracı tekerleklerinden ve gidondan özel sabitleme aparatlarıyla çiziksiz bağlayıp yetkili servise teslim ettiler. Kesinlikle herkese tavsiye ederim.'
    ),
);
?>

<section class="lst-section lst-reviews-section" id="yorumlar">
    <div class="lst-container">
        <div class="lst-section-heading-bar lst-text-center">
            <span class="lst-pill-sub">Müşteri Deneyimleri</span>
            <h2 class="lst-section-title">Yolda Kalan Sürücülerimiz Ne Diyor?</h2>
        </div>

        <div class="lst-reviews-grid">
            <?php foreach ($reviews as $rev) : ?>
                <div class="lst-review-card">
                    <div class="lst-review-stars"><?php echo esc_html($rev['stars']); ?></div>
                    <p class="lst-review-text">"<?php echo esc_html($rev['comment']); ?>"</p>
                    <div class="lst-review-author">
                        <div class="lst-author-avatar"><?php echo esc_html($rev['avatar']); ?></div>
                        <div class="lst-author-info">
                            <h4 class="lst-author-name"><?php echo esc_html($rev['name']); ?></h4>
                            <span class="lst-author-role"><?php echo esc_html($rev['role']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
