<?php
if(!defined('ABSPATH'))exit;
function mis360_firm_source_info($id){
    $owner=(int)get_post_field('post_author',$id);
    $managed=$owner&&get_userdata($owner)&&!user_can($owner,'manage_options');
    $url=(string)get_post_meta($id,'_firma_maps_url',true);
    $host=strtolower((string)wp_parse_url($url,PHP_URL_HOST));
    $google=in_array($host,array('google.com','www.google.com','maps.google.com','google.com.tr','www.google.com.tr','maps.app.goo.gl','goo.gl'),true);
    if($host==='goo.gl'&&strpos((string)wp_parse_url($url,PHP_URL_PATH),'/maps')!==0)$google=false;
    $imported=$google||(bool)get_post_meta($id,'_sarj_import_identity',true);
    return array('managed'=>$managed,'source'=>$imported?'Google Maps üzerindeki halka açık işletme bilgileri':($managed?'Üye tarafından sunulan işletme bilgileri':'Kaynak bilgisi belirtilmemiş rehber kaydı'));
}
function mis360_firm_source_notice($id){
    $info=mis360_firm_source_info($id);
    echo '<section class="mis360-source-notice" aria-label="Kayıt kaynağı"><strong>Kayıt hakkında</strong><p><b>Kaynak:</b> '.esc_html($info['source']).'.</p><p>'.esc_html($info['managed']?'Bu kayıt bir üye hesabı tarafından yönetiliyor. Bu durum hizmet kalitesi veya bilgilerin güncelliği için garanti oluşturmaz.':'Bu kayıt, işletme sahibi tarafından doğrulanmış bir profil olarak sunulmamaktadır. İletişim, adres ve hizmet bilgilerini firmadan teyit edin.').'</p><a href="#firma-bildirim">Düzeltme veya kaldırma talebi iletin ↓</a></section>';
}
