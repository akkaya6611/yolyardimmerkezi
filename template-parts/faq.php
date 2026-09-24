<?php
/**
 * Yol Yardım Merkezi - Sıkça Sorulan Sorular (SSS)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="yym-section yym-faq-section" id="sss">
    <div class="yym-container">
        <div class="yym-section-header">
            <span class="yym-subtitle"><?php _e('MERAK EDİLENLER', 'yol-yardim-merkezi'); ?></span>
            <h2 class="yym-title"><?php _e('Sıkça Sorulan Sorular', 'yol-yardim-merkezi'); ?></h2>
            <p class="yym-desc"><?php _e('Çekici ve yol yardım hizmetlerimiz hakkında en çok merak edilen konuların yanıtları.', 'yol-yardim-merkezi'); ?></p>
        </div>

        <div class="yym-faq-accordion">
            <div class="yym-faq-item is-active">
                <button type="button" class="yym-faq-question" aria-expanded="true">
                    <span><?php _e('Çekici ne kadar sürede yanıma ulaşır?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Trafiğin ve konumunuzun durumuna bağlı olarak, geniş filomuz sayesinde ortalama varış süremiz 15 ile 30 dakika arasındadır. Bizi aradığınızda veya WhatsApp\'tan konum ilettiğinizde operatörümüz size en yakın nöbetçi aracın tahmini varış dakikasını net olarak iletir.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>

            <div class="yym-faq-item">
                <button type="button" class="yym-faq-question" aria-expanded="false">
                    <span><?php _e('Çekici ve yol yardım ücreti nasıl hesaplanır?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Fiyatlandırmamız; aracınızın bulunduğu nokta ile taşınacağı yer arasındaki net kilometre mesafesine, aracınızın türüne (binek, SUV, ticari, motosiklet) ve hasar durumuna (tekerlek kilitlenmesi, şarampol vb.) göre şeffaf bir şekilde hesaplanır. Telefonda anlaştığımız sabit fiyat geçerlidir; sonradan sürpriz ek ücret talep edilmez.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>

            <div class="yym-faq-item">
                <button type="button" class="yym-faq-question" aria-expanded="false">
                    <span><?php _e('Aracım taşınırken kasko ve sigorta kapsamında mı?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Evet, kesinlikle. Tüm çekici filomuz resmi emtia nakliyat kasko poliçelerine sahiptir. Aracınız çekiciye yüklendiği andan varış noktasına teslim edilene kadar oluşabilecek her türlü riske karşı %100 sigorta teminatı altındadır.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>

            <div class="yym-faq-item">
                <button type="button" class="yym-faq-question" aria-expanded="false">
                    <span><?php _e('Ödemeyi kredi kartı ile yapabilir miyim?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Evet. Ekiplerimizin araçlarında mobil POS cihazı bulunmaktadır. Nakit, banka kartı, kredi kartı (temassız veya taksitli) ve havale/EFT/FAST yöntemleriyle güvenle ödeme yapabilirsiniz.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>

            <div class="yym-faq-item">
                <button type="button" class="yym-faq-question" aria-expanded="false">
                    <span><?php _e('Lastik patlaması veya akü bitmesinde aracı çekmek şart mı?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Hayır! Amacımız sizi gereksiz çekici masrafından kurtarmaktır. Akü takviyesi, yerinde lastik tamiri, stepne montajı ve yakıt ikmali gibi durumlarda mobil destek araçlarımız gelerek sorunu yerinde çözer ve hemen yolunuza devam etmenizi sağlar.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>

            <div class="yym-faq-item">
                <button type="button" class="yym-faq-question" aria-expanded="false">
                    <span><?php _e('Şehirler arası çoklu veya tekli araç taşıyor musunuz?', 'yol-yardim-merkezi'); ?></span>
                    <span class="yym-faq-icon">+</span>
                </button>
                <div class="yym-faq-answer">
                    <p><?php _e('Evet. Türkiye\'nin 81 iline ister tekli özel taşıma aracıyla, ister çoklu nakliye araçlarıyla şehirlerarası transfer hizmeti vermekteyiz. Şehirlerarası taşımalarımız da sözleşmeli ve kaskoludur.', 'yol-yardim-merkezi'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
