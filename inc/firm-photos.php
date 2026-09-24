<?php
if (!defined('ABSPATH')) exit;
function mis360_slider_limit($plan) { return array('free'=>0,'professional'=>10,'premium'=>30)[$plan] ?? 0; }
function mis360_firm_photo_files($field = 'firm_photos') {
    if (empty($_FILES[$field])) return array();
    $upload=$_FILES[$field];
    if (!is_array($upload['error'] ?? null)) return new WP_Error('photos','Geçerli görseller seçin.');
    $files=array();$total=0;
    foreach($upload['error'] as $i=>$error) {
        if($error===UPLOAD_ERR_NO_FILE)continue;
        $tmp=$upload['tmp_name'][$i]??'';$name=$upload['name'][$i]??'';
        if($error!==UPLOAD_ERR_OK||!is_string($tmp)||!is_string($name)||!is_uploaded_file($tmp))return new WP_Error('photos','Görsel yüklenemedi. Dosyaları yeniden seçin.');
        $size=filesize($tmp);$total+=$size;
        if(!$size||$size>5*1024*1024||$total>20*1024*1024)return new WP_Error('photos','Her görsel en fazla 5 MB, toplam yükleme en fazla 20 MB olabilir.');
        $image=@getimagesize($tmp);$allowed=array('image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp');
        if(!$image||!isset($allowed[$image['mime']])||$image[0]*$image[1]>40000000)return new WP_Error('photos','En fazla 40 megapiksel JPG, PNG veya WebP görsel seçin.');
        $files[]=array('name'=>sanitize_file_name(pathinfo($name,PATHINFO_FILENAME)).'.'.$allowed[$image['mime']], 'tmp_name'=>$tmp,'error'=>UPLOAD_ERR_OK,'size'=>$size,'type'=>$image['mime']);
    }
    return $files;
}
function mis360_firm_photo_bundle($plan = 'free') {
    $profile=mis360_firm_photo_files('firm_profile');
    if(is_wp_error($profile))return $profile;
    if(count($profile)!==1)return new WP_Error('profile','Lütfen bir profil fotoğrafı seçin.');
    $gallery=mis360_firm_photo_files('firm_slider');
    if(is_wp_error($gallery))return $gallery;
    $limit=mis360_slider_limit($plan);
    if(count($gallery)>$limit)return new WP_Error('slider_limit',$limit ? 'Paketiniz en fazla '.$limit.' slider fotoğrafı içerir.' : 'Lansmana özel ücretsiz pakette yalnızca profil fotoğrafı yüklenebilir.');
    if(!empty($_FILES['firm_photos']))return new WP_Error('legacy','Fotoğraf alanları güncellendi. Sayfayı yenileyip tekrar deneyin.');
    $all=array_merge($profile,$gallery);
    if(array_sum(array_column($all,'size'))>20*1024*1024)return new WP_Error('photos','Profil ve slider fotoğrafları toplam 20 MB olabilir.');
    if(isset($_POST['slider_file_count'])&&(!is_scalar($_POST['slider_file_count'])||(int)$_POST['slider_file_count']!==count($gallery)))return new WP_Error('photos','Tüm slider dosyaları alınamadı. Daha az dosyayla tekrar deneyin.');
    return array('profile'=>$profile,'gallery'=>$gallery,'plan'=>$plan);
}
function mis360_save_firm_photos($post_id,$bundle) {
    if(!is_user_logged_in()||(int)get_post_field('post_author',$post_id)!==get_current_user_id())return new WP_Error('photos','Bu başvuruya görsel ekleyemezsiniz.');
    if(count($bundle['profile']??array())!==1||count($bundle['gallery']??array())>mis360_slider_limit($bundle['plan']??'free'))return new WP_Error('photos','Paketinize uygun görseller seçin.');
    require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
    $ids=array();
    foreach(array_merge($bundle['profile'],$bundle['gallery']) as $file){
        $id=media_handle_sideload($file,$post_id,null,array('post_author'=>get_current_user_id()));
        if(is_wp_error($id)){foreach($ids as $saved)wp_delete_attachment($saved,true);return new WP_Error('photos','Görseller kaydedilemedi. Lütfen yeniden deneyin.');}
        $ids[]=$id;
    }
    set_post_thumbnail($post_id,$ids[0]);
    update_post_meta($post_id,'_firma_profile_image_id',$ids[0]);
    update_post_meta($post_id,'_firma_gallery_ids',array_slice($ids,1));
    update_post_meta($post_id,'_firma_photo_layout','separate');
    return $ids;
}
