<?php
if (!defined('ABSPATH')) exit;
function mis360_firm_plans() { return array('free'=>'Lansman Paketi','professional'=>'Profesyonel Paket','premium'=>'Premium Paket'); }
function mis360_requested_plan_label($id) { $plans=mis360_firm_plans(); return $plans[get_post_meta($id,'_firma_requested_plan',true)] ?? ''; }
add_filter('manage_firma_posts_columns',function($cols){$cols['requested_plan']='Paket tercihi';return $cols;});
add_action('manage_firma_posts_custom_column',function($column,$id){if($column==='requested_plan')echo esc_html(mis360_requested_plan_label($id) ?: 'Belirtilmemiş');},10,2);
add_action('add_meta_boxes',function(){add_meta_box('mis360-plan','Başvuru paket tercihi',function($post){echo '<p>'.esc_html(mis360_requested_plan_label($post->ID) ?: 'Belirtilmemiş').'</p><p>Paket tercihi, ödeme veya ücretli özelliklerin etkinleştirildiği anlamına gelmez.</p>';},'firma','side');});
add_action('wp_enqueue_scripts',function(){if(is_page('firma-ekle'))wp_enqueue_style('mis360-firm-plans',get_template_directory_uri().'/assets/css/firm-plans.css',array(),filemtime(get_template_directory().'/assets/css/firm-plans.css'));});
function mis360_plan_comparison() {
 return array(
 array('Fiyat','0 TL · Lansmana özel','299 TL / ay','599 TL / ay'),
 array('Firma profili','Var','Var','Var'),
 array('İlan hakkı','1','5','15'),
 array('Yayında kalma süresi','Lansman dönemi','Aylık üyelik boyunca','Aylık üyelik boyunca'),
 array('Profil fotoğrafı','1','1','1'),
 array('Firma slider fotoğrafları','Yok','10','30'),
 array('Hizmet kategorisi','1','10','Sınırsız'),
 array('WhatsApp ve telefon butonu','Var','Var','Var'),
 array('Google indekslenmesine uygun profil','Var','Var','Var'),
 array('Google Maps yol tarifi','Var','Var','Var'),
 array('Müşteri değerlendirmeleri','Var','Var','Var'),
 array('Arama sonuçlarında öne çıkma','Yok','Var','Öncelikli'),
 array('Şehir / ilçe sayfalarında öncelik','Yok','Var','En üst sıra'),
 array('Vitrin ilanı','Yok','1','3'),
 array('Ana sayfa slider gösterimi','Yok','Yok','Var'),
 array('Önerilen firma rozeti','Yok','Var','Altın rozet'),
 array('Portföy / önce-sonra fotoğrafları','Yok','10','30'),
 array('Kampanya / indirim yayınlama','Yok','Var','Var'),
 array('Müşteri talebi alma','Yok','Var','Anında bildirim'),
 array('İstatistikler','Yok','Temel','Gelişmiş'),
 array('Profil doğrulama rozeti','Yok','Yok','VIP rozet'),
 array('Öncelikli destek','Yok','Standart','7/24 VIP')
 );
}
function mis360_plan_fields($selected) {
 $catalog=array(
 'free'=>array('LANSMANA ÖZEL ÜCRETSİZ','Lansman Paketi','Lansman süresince geçerli temel üyelik','0 TL','· Lansmana özel',array('1 ilan yayınlama','1 profil fotoğrafı','1 hizmet kategorisi','Lansman döneminde yayın','WhatsApp ve telefon butonu','Google indekslenmesine uygun profil','Google Maps yol tarifi')),
 'professional'=>array('ÇOK YAKINDA','Profesyonel Paket','İşini büyütmek isteyen işletmeler için','299 TL','/ ay',array('5 ilan yayınlama','1 profil fotoğrafı','10 slider fotoğrafı','10 hizmet kategorisi','WhatsApp ve telefon butonu','Google indekslenmesine uygun profil','Google Maps yol tarifi','Arama sonuçlarında öne çıkma','Şehir / ilçe sayfalarında öncelik','1 vitrin ilanı','Önerilen firma rozeti')),
 'premium'=>array('LİDER PAKET · YAKINDA','Premium Paket','Bölgesinde öne çıkmak isteyen işletmeler için','599 TL','/ ay',array('15 ilan yayınlama','1 profil fotoğrafı','30 slider fotoğrafı','Sınırsız hizmet kategorisi','WhatsApp ve telefon butonu','Google indekslenmesine uygun profil','Google Maps yol tarifi','En üst sırada listelenme','Altın önerilen firma rozeti','3 vitrin ilanı','Ana sayfa slider gösterimi'))
 ); ?>
<div class="mis360-launch-banner">LANSMANA ÖZEL ÜCRETSİZ · ÜCRETLİ PLANLAR YAKINDA</div>
<fieldset class="mis360-plan-picker"><legend>İşletme Üyelik Paketleri</legend><p>Firma kaydı ve listeleme lansmana özel ücretsizdir. Lansmanın bitiş tarihi henüz belirlenmemiştir. Profesyonel ve Premium ücretli planlar yakında açılacaktır. Paketinizi seçerek devam edin.</p>
<div class="mis360-plan-grid">
<?php foreach($catalog as $key=>$plan): ?><label class="mis360-plan-option"><input type="radio" name="firm_plan" value="<?php echo esc_attr($key); ?>" <?php checked($selected,$key); ?> <?php disabled($key!=='free'); ?> required><span class="mis360-plan-inner"><small class="mis360-plan-state"><?php echo esc_html($plan[0]); ?></small><strong><?php echo esc_html($plan[1]); ?></strong><small><?php echo esc_html($plan[2]); ?></small><span class="mis360-plan-price"><?php echo esc_html($plan[3]); ?><small><?php echo esc_html($plan[4]); ?></small></span><ul><?php foreach($plan[5] as $feature): ?><li><?php echo esc_html($feature); ?></li><?php endforeach; ?></ul><b class="mis360-plan-availability"><?php echo $key==='free'?'Lansman paketi seçili':'Yakında satışta'; ?></b></span></label><?php endforeach; ?>
</div><p class="mis360-plan-note"><strong>Lansman bilgisi:</strong> Tablodaki haklar ve süreler planlanan paket kapsamıdır. Lansman için kesin bir bitiş tarihi veya gün sınırı belirlenmemiştir. İlan ve kategori kotaları şimdilik uygulanmıyor. Görsel hakkı paketlere göre geçerlidir: Lansmana özel ücretsiz pakette 1 profil fotoğrafı; Profesyonel’de ayrıca 10, Premium’da 30 firma slider fotoğrafı. Ücretli paketler ve ek avantajları henüz aktif değildir; ödeme alınmaz.</p>
</fieldset>
<details class="mis360-plan-compare"><summary>Paket Karşılaştırma Tablosu</summary><p>Paketlerin planlanan özelliklerini karşılaştırın.</p><div class="mis360-plan-table-wrap" tabindex="0" role="region" aria-label="Paket karşılaştırması"><table><thead><tr><th scope="col">Özellik</th><th scope="col">Lansman</th><th scope="col">Profesyonel</th><th scope="col">Premium</th></tr></thead><tbody><?php foreach(mis360_plan_comparison() as $row): ?><tr><th scope="row"><?php echo esc_html($row[0]); ?></th><?php foreach(array_slice($row,1) as $value): ?><td><?php echo esc_html($value); ?></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table></div><p class="mis360-plan-note">Google'da indekslenme ve indekslenme zamanı garanti edilmez. Doğrulama rozetleri belge incelemesi gerektirir; paket seçimi otomatik doğrulama sağlamaz.</p></details>
<?php }

add_action('wp_enqueue_scripts',function(){if(is_page('firma-ekle'))wp_enqueue_script('mis360-firm-wizard',get_template_directory_uri().'/assets/js/firm-wizard.js',array(),filemtime(get_template_directory().'/assets/js/firm-wizard.js'),true);});
