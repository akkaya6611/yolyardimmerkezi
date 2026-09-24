<?php



/**



 * Yol Yardım Merkezi - Profesyonel Kurumsal Footer (#0B1F3A Koyu Lacivert & #FF8A00 Turuncu)



 *



 * @package Yol_Yardim_Merkezi



 */







if (!defined('ABSPATH')) {



    exit;



}







$address = yym_get_address();



$email   = yym_get_email();



?>







<?php 
// Alt Reklam Alanı (Footer Üstü)
if (function_exists('yym_show_ad')) {
    echo '<div class="lst-container">';
    yym_show_ad('footer');
    echo '</div>';
}
?>

<footer class="yym-corporate-footer" id="iletisim">



    <!-- Üst Footer: Firma Ekle Acil Aksiyon Şeridi -->



    <div class="yym-footer-top-strip">



        <div class="lst-container yym-footer-top-inner">



            <div class="yym-footer-top-text">



                <span class="yym-footer-strip-icon" style="font-size:36px;line-height:1;color:#ff8a00"><?php echo mis360_tow_symbol(); ?></span>



                <div>



                    <strong>Yol yardım veya çekici firmanız mı var?</strong>



                    <span>Türkiye genelindeki binlerce sürücüye doğrudan ulaşmak için hemen kaydolun.</span>



                </div>



            </div>



            <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-footer-strip-btn">



                <span>➕ Firma Kaydı Oluştur</span>



            </a>



        </div>



    </div>







    <!-- Ana Footer Alanı -->



    <div class="lst-container yym-footer-main-container">



        <div class="yym-footer-grid">



            <!-- Kolon 1: Logo, Hakkında & İletişim -->



            <div class="yym-footer-col yym-footer-about-col">



                <div class="yym-footer-brand">



                    <a href="<?php echo esc_url(home_url('/')); ?>" class="yym-footer-logo-link" title="Yol Yardım Merkezi Ana Sayfa">



                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo-light.png'); ?>" alt="Yol Yardım Merkezi" class="yym-footer-logo-img" width="220" height="56">



                    </a>



                </div>



                <p class="yym-footer-tagline">



                    Yol yardım, oto kurtarma, lastik, oto çilingir ve şarj istasyonlarını bulabileceğiniz firma rehberi. Hizmet ve konuma göre arayın, firmayla doğrudan iletişime geçin.



                </p>







                <div class="yym-footer-contact-info">



                    <div class="yym-f-contact-item">



                        <span class="yym-f-c-icon">📍</span>



                        <span><?php echo esc_html($address); ?></span>



                    </div>



                    <div class="yym-f-contact-item">



                        <span class="yym-f-c-icon">✉️</span>



                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>



                    </div>



                </div>



            </div>







            <!-- Kolon 2: Hızlı Bağlantılar -->



            <div class="yym-footer-col">



                <h4 class="yym-footer-heading">Hızlı Bağlantılar</h4>



                <ul class="yym-footer-links">



                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a></li>



                    <li><a href="<?php echo esc_url(home_url('/firmalar/')); ?>">Tüm Firmalar</a></li>



                    <li><a href="<?php echo esc_url(home_url('/sehirler/')); ?>">81 İl Çekici Ağı</a></li>



                    <li><a href="<?php echo esc_url(home_url('/hizmetler/')); ?>">Hizmetlerimiz</a></li>



                    <li><a href="<?php echo esc_url(home_url('/hakkimizda/')); ?>">Hakkımızda</a></li>



                    <li><a href="<?php echo esc_url(home_url('/sss/')); ?>">Sıkça Sorulan Sorular</a></li>



                    <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">Sürücü Rehberi & Blog</a></li>



                    <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>">İletişim</a></li>



                </ul>



            </div>







            <!-- Kolon 3: Yol Yardım Hizmetleri -->



            <div class="yym-footer-col">



                <h4 class="yym-footer-heading">Hizmetlerimiz</h4>



                <ul class="yym-footer-links">

                    <?php

                    $footer_terms = get_terms(array('taxonomy'=>'firma_kategori','hide_empty'=>true,'orderby'=>'name'));

                    $footer_services = array();

                    $footer_icons = array('mobil-lastikci'=>'🛞','oto-lastik'=>'🛞','oto-cilingir'=>'🔑','sarj-istasyonu'=>'🔌');

                    if (!is_wp_error($footer_terms)) foreach ($footer_terms as $term) {

                        $slug = mis360_category_canonical($term->slug);

                        $footer_services[$slug] = $slug === 'yol-yardim' ? 'Yol Yardım & Oto Kurtarma' : ($slug === 'oto-lastik' ? 'Oto Lastik' : $term->name);

                    }

                    foreach ($footer_services as $slug=>$label) : ?>

                        <li><a href="<?php echo esc_url(add_query_arg('category',$slug,home_url('/firmalar/'))); ?>"><span aria-hidden="true" style="display:inline-block;width:1.5em;margin-right:6px;color:#ff8a00;text-align:center"><?php echo $slug === 'yol-yardim' ? mis360_tow_symbol() : esc_html($footer_icons[$slug] ?? '🔧'); ?></span><?php echo esc_html($label); ?></a></li>

                    <?php endforeach; ?>

                    <li><a href="<?php echo esc_url(home_url('/firmalar/')); ?>"><span aria-hidden="true" style="display:inline-block;width:1.5em;margin-right:6px;text-align:center">🔎</span>Tüm Hizmetleri Gör</a></li>

                </ul>



            </div>







            <!-- Kolon 4: Bilgilendirme -->
            <div class="yym-footer-col">
                <h4 class="yym-footer-heading">Bilgilendirme</h4>
                <ul class="yym-footer-links">
                    <?php foreach(mis360_legal_pages() as $slug=>$label): ?>
                    <li><a href="<?php echo esc_url(home_url('/'.$slug.'/')); ?>"><?php echo esc_html($label); ?></a></li>
                    <?php endforeach; ?>
                    <li><a href="<?php echo esc_url(home_url('/sss/')); ?>">Sıkça Sorulan Sorular</a></li>
                    <li><a href="<?php echo esc_url(home_url('/uyelik/')); ?>">Üyelik / Hesabım</a></li>
                    <li><a href="<?php echo esc_url(home_url('/iletisim/')); ?>">İletişim</a></li>
                </ul>
            </div>



        </div>



    </div>







    <!-- En Alt Telif & Bilgi Şeridi -->



    <div class="yym-footer-bottom-bar">



        <div class="lst-container yym-footer-bottom-inner">



            <p>© <?php echo date('Y'); ?> Yol Yardım Merkezi. Tüm Hakları Saklıdır. Yol yardım ve işletme rehberi.</p>
            <p class="yym-footer-credits">Tasarım: <a href="https://misteknoloji360.com.tr/" target="_blank" rel="noopener noreferrer" style="color:#ff8a00;text-decoration:none;font-weight:600;">MisTeknoloji360</a> ❤️</p>



            



        </div>



    </div>



</footer>







<?php wp_footer(); ?>



</body>



</html>



