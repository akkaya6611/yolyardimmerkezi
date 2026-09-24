<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__.'/firm-photos.php';
require_once __DIR__.'/firm-locations.php';

function mis360_firm_owned($id) {
    $p=get_post($id);
    return is_user_logged_in() && $p && $p->post_type==='firma' && (int)$p->post_author===get_current_user_id() && in_array($p->post_status,array('publish','pending','draft','private','future'),true) ? $p : false;
}
function mis360_firm_version($id) {
    $p=get_post($id);
    return hash('sha256',wp_json_encode(array($p?$p->to_array():null,get_post_meta($id),wp_get_object_terms($id,'firma_kategori',array('fields'=>'ids')))));
}
function mis360_tax_version($id) {
    global $wpdb;
    $doc=$wpdb->get_var($wpdb->prepare('SELECT document FROM '.mis360_tax_table().' WHERE user_id=%d',$id));
    $receipt=mis360_tax_approved_receipt($id);return $doc ? hash('sha256',$doc) : ($receipt['version']??'');
}
function mis360_tax_review($id) {
    $version=mis360_tax_version($id);
    if (!$version) return array('state'=>'missing','label'=>'Belge yüklenmedi','note'=>'');
    $review=get_user_meta($id,'mis360_tax_review',true);
    if (!is_array($review) || ($review['version']??'')!==$version) return array('state'=>'pending','label'=>'İnceleme bekliyor','note'=>'');
    return array('state'=>$review['state'],'label'=>$review['state']==='approved'?'Belge onaylandı':'Düzeltme gerekiyor','note'=>$review['note']);
}
function mis360_firm_review_label($id) {
    $status=get_post_status($id);
    if ($status==='publish') return 'Yayında';
    if ($status==='pending') return 'Onay bekliyor';
    if ($status==='draft' && get_post_meta($id,'_mis360_review_state',true)==='changes') return 'Düzeltme gerekiyor';
    if (get_post_meta($id,'_mis360_review_state',true)==='withdrawn') return 'Yayından kaldırıldı';
    return array('draft'=>'Taslak','private'=>'Özel','future'=>'Yayın planlandı')[$status]??'İnceleniyor';
}
function mis360_firm_edit_url($id) { return add_query_arg('firm_edit',(int)$id,mis360_account_url('firmalar')); }

// This service is shared by the form handler and the isolated integration tests.
function mis360_firm_member_update($id,$action,$data,$version) {
    if (!mis360_firm_owned($id) || !mis360_email_verified(get_current_user_id())) return new WP_Error('access','Bu firmayı yönetme yetkiniz yok.');
    if (!in_array($action,array('save','withdraw'),true)) return new WP_Error('action','Geçersiz işlem.');
    global $wpdb;
    $lock='m360_edit_'.substr(hash('sha256',DB_NAME.'|'.$wpdb->prefix.'|'.$id),0,42);
    if ((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1') return new WP_Error('busy','Bu firma üzerinde başka bir işlem var. Lütfen tekrar deneyin.');
    $uploaded=array();
    try {
        clean_post_cache($id);
        if (!mis360_firm_owned($id) || !hash_equals(mis360_firm_version($id),$version)) return new WP_Error('stale','Firma başka bir işlemde güncellendi. Sayfayı yenileyip tekrar deneyin.');
        if ($action==='withdraw') {
            $result=wp_update_post(array('ID'=>$id,'post_status'=>'draft'),true);
            if(is_wp_error($result))return $result;
            update_post_meta($id,'_mis360_review_state','withdrawn');
            return true;
        }
        $fields=array('firm_name','phone','whatsapp','city','district','address','contact_person');
        foreach($fields as $field){$data[$field]=isset($data[$field])&&is_string($data[$field])?sanitize_text_field($data[$field]):'';}
        $notes=isset($data['notes'])&&is_string($data['notes'])?sanitize_textarea_field($data['notes']):'';
        if (mb_strlen($data['firm_name'])<2 || mb_strlen($data['firm_name'])>160 || mb_strlen($notes)>10000) return new WP_Error('name','Firma adı 2–160, açıklama en fazla 10.000 karakter olmalı.');
        foreach(array('phone','whatsapp') as $field) if (($field==='phone'||$data[$field]!=='') && !preg_match('/^\+?[0-9 ()-]{10,25}$/',$data[$field])) return new WP_Error('phone','Geçerli telefon ve WhatsApp numarası girin.');
        if (!mis360_registration_location_valid($data['city'],$data['district'])) return new WP_Error('location','İl ve o ile bağlı bir ilçe seçin.');
        if (mb_strlen($data['address'])<5 || mb_strlen($data['address'])>500) return new WP_Error('address','Adres 5–500 karakter olmalı.');
        $categories=$data['categories']??array();
        if (!is_array($categories) || !$categories || count($categories)>50) return new WP_Error('category','En az bir hizmet kategorisi seçin.');
        $terms=array();foreach($categories as $term){if(!is_scalar($term)||!term_exists((int)$term,'firma_kategori'))return new WP_Error('category','Geçerli hizmet kategorileri seçin.');$terms[]=(int)$term;}
        $profile=mis360_firm_photo_files('firm_profile');$gallery=mis360_firm_photo_files('firm_slider');
        if(is_wp_error($profile))return $profile;if(is_wp_error($gallery))return $gallery;
        if(count($profile)>1)return new WP_Error('photo','Yalnızca bir profil fotoğrafı seçin.');
        // Only an administrator-granted active plan provides paid photo rights.
        $limit=mis360_slider_limit(get_post_meta($id,'_firma_active_plan',true) ?: 'free');
        $old=array_map('absint',(array)get_post_meta($id,'_firma_gallery_ids',true));$old=array_filter($old);
        $keep=$data['keep_gallery']??array();
        if(!is_array($keep))return new WP_Error('photo','Geçersiz galeri seçimi.');
        foreach($keep as $v)if(!is_scalar($v)||!in_array((int)$v,$old,true))return new WP_Error('photo','Geçersiz galeri fotoğrafı.');
        $keep=array_values(array_unique(array_map('absint',$keep)));
        if(count($keep)+count($gallery)>max($limit,count($old)) || (count($gallery)>0 && count($keep)+count($gallery)>$limit))return new WP_Error('limit','Paketinizin slider fotoğrafı sınırı aşıldı.');
        if(array_sum(array_column(array_merge($profile,$gallery),'size'))>20*1024*1024)return new WP_Error('size','Toplam görsel yüklemesi 20 MB sınırını aşamaz.');
        if(!$profile && !get_post_thumbnail_id($id) && !get_post_meta($id,'_firma_image_url',true))return new WP_Error('photo','Bir profil fotoğrafı yükleyin.');
        require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
        foreach(array_merge($profile,$gallery) as $file){$image=media_handle_sideload($file,$id,null,array('post_author'=>get_current_user_id()));if(is_wp_error($image)){foreach($uploaded as $u)wp_delete_attachment($u,true);return new WP_Error('photo','Görsel kaydedilemedi; bilgileriniz değiştirilmedi.');}$uploaded[]=$image;}
        $result=wp_update_post(wp_slash(array('ID'=>$id,'post_title'=>$data['firm_name'],'post_content'=>$notes,'post_status'=>'pending')),true);
        if(is_wp_error($result)){foreach($uploaded as $u)wp_delete_attachment($u,true);return $result;}
        foreach(array('phone','whatsapp','city','district','address','contact_person') as $field)update_post_meta($id,'_firma_'.$field,wp_slash($data[$field]));
        wp_set_object_terms($id,$terms,'firma_kategori');
        if($profile){$profile_id=array_shift($uploaded);set_post_thumbnail($id,$profile_id);update_post_meta($id,'_firma_profile_image_id',$profile_id);}
        update_post_meta($id,'_firma_gallery_ids',array_merge($keep,$uploaded));
        update_post_meta($id,'_firma_photo_layout','separate');
        update_post_meta($id,'_mis360_review_state','pending');delete_post_meta($id,'_mis360_review_note');
        return true;
    } finally {$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
add_action('template_redirect',function(){
    if(!is_page('uyelik')||($_SERVER['REQUEST_METHOD']??'')!=='POST'||!isset($_POST['firm_action']))return;
    $id=absint(mis360_member_input('firm_id'));
    if(!wp_verify_nonce(mis360_member_input('firm_nonce'),'mis360_firm_'.$id)){$GLOBALS['mis360_firm_error']='Formun süresi doldu. Sayfayı yenileyin.';return;}
    $result=mis360_firm_member_update($id,mis360_member_input('firm_action'),wp_unslash($_POST),mis360_member_input('firm_version'));
    if(is_wp_error($result)){$GLOBALS['mis360_firm_error']=$result->get_error_message();return;}
    wp_safe_redirect(add_query_arg('firm_saved',mis360_member_input('firm_action')==='withdraw'?'withdrawn':'pending',mis360_account_url('firmalar')));exit;
},8);

function mis360_firm_controls($id) {
    echo '<a href="'.esc_url(mis360_firm_edit_url($id)).'">Düzenle</a>';
    if(in_array(get_post_status($id),array('publish','pending','future'),true)){
        echo '<form method="post" class="mis360-withdraw-form" action="'.esc_url(mis360_account_url('firmalar')).'">';wp_nonce_field('mis360_firm_'.$id,'firm_nonce');
        echo '<input type="hidden" name="firm_id" value="'.(int)$id.'"><input type="hidden" name="firm_version" value="'.esc_attr(mis360_firm_version($id)).'"><input type="hidden" name="firm_action" value="withdraw"><button type="submit">Yayından kaldır</button></form>';
    }
}

function mis360_firm_editor($id) {
    $p=mis360_firm_owned($id);if(!$p){echo '<p class="mis360-member-alert">Firma bulunamadı veya erişim yetkiniz yok.</p>';return;}
    $posted=mis360_member_input('firm_action')==='save' && absint(mis360_member_input('firm_id'))===$id;
    $value=function($field)use($posted,$p,$id){if($posted)return mis360_member_input($field);return $field==='firm_name'?$p->post_title:($field==='notes'?$p->post_content:get_post_meta($id,'_firma_'.$field,true));};
    $selected=wp_get_object_terms($id,'firma_kategori',array('fields'=>'ids'));if(is_wp_error($selected))$selected=array();
    $city=$value('city');$district=$value('district');$locations=mis360_registration_locations();
    ?>
    <section class="mis360-account-panel mis360-editor"><a href="<?php echo esc_url(mis360_account_url('firmalar')); ?>">← Firmalarıma dön</a><h2>Firma bilgilerini düzenle</h2>
    <p class="mis360-review-notice">Kaydettiğinizde ilan yeniden incelemeye gönderilir ve onaylanana kadar yayında görünmez. Mevcut fotoğrafı değiştirmek istemiyorsanız yeni dosya seçmeyin.</p>
    <form method="post" enctype="multipart/form-data" class="mis360-edit-form">
    <?php wp_nonce_field('mis360_firm_'.$id,'firm_nonce'); ?>
    <input type="hidden" name="firm_id" value="<?php echo (int)$id; ?>"><input type="hidden" name="firm_action" value="save"><input type="hidden" name="firm_version" value="<?php echo esc_attr(mis360_firm_version($id)); ?>">
    <?php foreach(array('firm_name'=>'Firma adı','contact_person'=>'Yetkili kişi','phone'=>'Telefon','whatsapp'=>'WhatsApp') as $field=>$label): ?><label><?php echo esc_html($label); ?><input name="<?php echo esc_attr($field); ?>" value="<?php echo esc_attr($value($field)); ?>" maxlength="160" <?php if(in_array($field,array('firm_name','phone'),true))echo 'required'; ?>></label><?php endforeach; ?>
    <label>İl<select name="city" data-edit-city required data-locations="<?php echo esc_attr(wp_json_encode($locations)); ?>"><option value="">İl seçin</option><?php foreach($locations as $name=>$items): ?><option <?php selected($city,$name); ?>><?php echo esc_html($name); ?></option><?php endforeach; ?></select></label>
    <label>İlçe<select name="district" data-edit-district required><option value="">İlçe seçin</option><?php foreach($locations[$city]??array() as $name): ?><option <?php selected($district,$name); ?>><?php echo esc_html($name); ?></option><?php endforeach; ?></select></label>
    <label class="wide">Açık adres<input name="address" required maxlength="500" value="<?php echo esc_attr($value('address')); ?>"></label>
    <fieldset class="wide"><legend>Hizmet kategorileri</legend><div class="mis360-category-choices"><?php foreach(get_terms(array('taxonomy'=>'firma_kategori','hide_empty'=>false)) as $term): ?><label><input type="checkbox" name="categories[]" value="<?php echo (int)$term->term_id; ?>" <?php checked(in_array($term->term_id,$selected,true)); ?>><?php echo esc_html($term->name); ?></label><?php endforeach; ?></div></fieldset>
    <label class="wide">Firma açıklaması<textarea name="notes" rows="6" maxlength="10000"><?php echo esc_textarea($value('notes')); ?></textarea></label>
    <div class="wide mis360-edit-media"><h3>Profil fotoğrafı</h3><?php $photo=get_the_post_thumbnail_url($id,'thumbnail')?:get_post_meta($id,'_firma_image_url',true);if($photo): ?><img src="<?php echo esc_url($photo); ?>" width="80" height="80" alt="Mevcut profil fotoğrafı"><?php endif; ?><label>Yeni profil fotoğrafı<input type="file" name="firm_profile[]" accept="image/jpeg,image/png,image/webp"></label><small>JPG, PNG veya WebP · Fotoğraf başına 5 MB</small></div>
    <div class="wide mis360-edit-media"><h3>Slider fotoğrafları</h3><?php $gallery=array_filter(array_map('absint',(array)get_post_meta($id,'_firma_gallery_ids',true)));foreach($gallery as $image): ?><label class="mis360-keep-photo"><?php echo wp_get_attachment_image($image,'thumbnail'); ?><span><input type="checkbox" name="keep_gallery[]" value="<?php echo (int)$image; ?>" checked> Fotoğrafı koru</span></label><?php endforeach; $limit=mis360_slider_limit(get_post_meta($id,'_firma_active_plan',true)?:'free');if($limit): ?><label>Slider fotoğrafı ekle<input type="file" name="firm_slider[]" multiple accept="image/jpeg,image/png,image/webp"></label><p>En fazla <?php echo (int)$limit; ?> slider fotoğrafı.</p><?php else: ?><p>Lansmana özel ücretsiz pakette yalnızca profil fotoğrafı yüklenebilir.</p><?php endif; ?></div>
    <div class="wide"><button type="submit" class="mis360-member-button">Kaydet ve incelemeye gönder</button></div></form></section>
    <?php
}

function mis360_review_admin_url($id) {return add_query_arg(array('post_type'=>'firma','page'=>'mis360-reviews','firm'=>$id),admin_url('edit.php'));}
add_action('admin_menu',function(){add_submenu_page('edit.php?post_type=firma','Başvuru incelemeleri','Başvuru incelemeleri','manage_options','mis360-reviews','mis360_review_admin_page');});
add_action('add_meta_boxes',function(){if(current_user_can('manage_options'))add_meta_box('mis360-review','Firma ve belge incelemesi',function($p){echo '<p>'.esc_html(mis360_firm_review_label($p->ID)).'</p><a class="button" href="'.esc_url(mis360_review_admin_url($p->ID)).'">Başvuruyu incele</a>';},'firma','side');});

function mis360_review_apply($id,$type,$decision,$note,$version) {
    if(!current_user_can('manage_options') || !current_user_can('edit_post',$id))return new WP_Error('access','İnceleme yetkiniz yok.');
    $p=get_post($id);if(!$p||$p->post_type!=='firma'||!in_array($p->post_status,array('publish','pending','draft','private','future'),true))return new WP_Error('firm','Firma bulunamadı.');
    if(!in_array($type,array('tax','firm'),true)||!in_array($decision,array('approved','changes'),true))return new WP_Error('action','Geçersiz karar.');
    $note=sanitize_textarea_field($note);if(mb_strlen($note)>2000||($decision==='changes'&&mb_strlen(trim($note))<5))return new WP_Error('note','Düzeltme gerekçesini yazın (5–2000 karakter).');
    global $wpdb;$lock='m360_edit_'.substr(hash('sha256',DB_NAME.'|'.$wpdb->prefix.'|'.$id),0,42);
    if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')return new WP_Error('busy','Başvuru güncelleniyor. Tekrar deneyin.');
    try {
        clean_post_cache($id);$p=get_post($id);$uid=(int)$p->post_author;
        $current=$type==='tax'?mis360_tax_version($uid):mis360_firm_version($id);
        if(!$current||!hash_equals($current,$version))return new WP_Error('stale','Başvuru veya belge değişti. Sayfayı yenileyip yeniden inceleyin.');
        if($type==='tax'){
            $saved=mis360_tax_record_decision($uid,$decision,$note,$current);if(is_wp_error($saved))return $saved;
        }else{
            if($decision==='approved' && !user_can($uid,'manage_options') && mis360_tax_review($uid)['state']!=='approved')return new WP_Error('tax','Önce firma sahibinin vergi levhasını inceleyip onaylayın.');
            $result=wp_update_post(array('ID'=>$id,'post_status'=>$decision==='approved'?'publish':'draft'),true);if(is_wp_error($result))return $result;
            update_post_meta($id,'_mis360_review_state',$decision);update_post_meta($id,'_mis360_review_note',wp_slash($note));update_post_meta($id,'_mis360_review_at',current_time('mysql'));update_post_meta($id,'_mis360_review_by',get_current_user_id());
        }
        do_action('mis360_review_recorded',$uid,$type,$decision,$note,$id);
        return true;
    }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
function mis360_review_admin_page(){
    if(!current_user_can('manage_options'))return;
    $id=isset($_GET['firm'])&&is_scalar($_GET['firm'])?absint($_GET['firm']):0;
    echo '<div class="wrap"><h1>Başvuru incelemeleri</h1>';
    if($id){
        $p=get_post($id);if(!$p||$p->post_type!=='firma'){echo '<p>Firma bulunamadı.</p></div>';return;}
        if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
            check_admin_referer('mis360_review_'.$id);
            $result=mis360_review_apply($id,mis360_member_input('review_type'),mis360_member_input('decision'),mis360_member_input('review_note',true),mis360_member_input('review_version'));
            echo '<div class="notice '.(is_wp_error($result)?'notice-error':'notice-success').'"><p>'.esc_html(is_wp_error($result)?$result->get_error_message():'İnceleme kararı kaydedildi. Üye panelinde görüntülenebilir.').'</p></div>';
        }
        $uid=(int)$p->post_author;$tax=mis360_tax_review($uid);
        echo '<h2>'.esc_html($p->post_title).'</h2><p>Firma durumu: <strong>'.esc_html(mis360_firm_review_label($id)).'</strong></p><p><a href="'.esc_url(get_edit_post_link($id)).'">Firma bilgilerini ve görselleri aç</a></p>';
        foreach(array('tax'=>'Vergi levhası','firm'=>'Firma başvurusu') as $type=>$label){
            echo '<section style="background:#fff;border:1px solid #dcdcde;max-width:760px;padding:24px;margin:20px 0"><h2>'.esc_html($label).'</h2>';
            if($type==='tax'){echo '<p>'.esc_html($tax['label']).' · '.mis360_tax_admin_link($uid).'</p>';if(!mis360_tax_present($uid)){echo '</section>';continue;}}
            $note=$type==='tax'?$tax['note']:get_post_meta($id,'_mis360_review_note',true);
            echo '<form method="post">';wp_nonce_field('mis360_review_'.$id);
            echo '<input type="hidden" name="review_type" value="'.esc_attr($type).'"><input type="hidden" name="review_version" value="'.esc_attr($type==='tax'?mis360_tax_version($uid):mis360_firm_version($id)).'"><p><label>Üyeye gösterilecek açıklama<br><textarea name="review_note" rows="4" maxlength="2000" style="width:100%">'.esc_textarea($note).'</textarea></label></p><p><button class="button button-primary" name="decision" value="approved">'.($type==='firm'?'Onayla ve yayınla':'Belgeyi onayla').'</button> <button class="button" name="decision" value="changes">Düzeltme iste</button></p></form></section>';
        }
    }else{
        $page=max(1,absint($_GET['paged']??1));$q=new WP_Query(array('post_type'=>'firma','post_status'=>'pending','posts_per_page'=>20,'paged'=>$page,'orderby'=>'modified','order'=>'ASC'));
        echo '<p>Onay bekleyen firma başvuruları. Her başvuruda firma ve belge ayrı incelenir.</p><table class="widefat striped"><thead><tr><th>Firma</th><th>Üye</th><th>Belge</th><th></th></tr></thead><tbody>';
        foreach($q->posts as $p){$u=get_userdata($p->post_author);echo '<tr><td>'.esc_html($p->post_title).'</td><td>'.esc_html($u?$u->display_name:'—').'</td><td>'.esc_html(mis360_tax_review($p->post_author)['label']).'</td><td><a class="button" href="'.esc_url(mis360_review_admin_url($p->ID)).'">İncele</a></td></tr>';}
        if(!$q->posts)echo '<tr><td colspan="4">Bekleyen başvuru yok.</td></tr>';echo '</tbody></table>';
        echo paginate_links(array('base'=>add_query_arg('paged','%#%',mis360_review_admin_url(0)),'current'=>$page,'total'=>$q->max_num_pages));
    }echo '</div>';
}

function mis360_member_tax_panel($uid){
    $review=mis360_tax_review($uid);echo '<div class="mis360-document-status"><span>İnceleme durumu</span><strong>'.esc_html($review['label']).'</strong></div>';
    if($review['note'])echo '<p class="mis360-review-notice"><strong>Yönetici açıklaması:</strong> '.nl2br(esc_html($review['note'])).'</p>';
    echo '<p>Onaylanan vergi levhasının dosyası silinir; onay kaydı korunur. Yeni belge yüklediğinizde yeniden incelemeye alınır.</p><details'.($review['state']==='missing'||$review['state']==='changes'?' open':'').'><summary>Vergi levhası yükle / yenile</summary><form method="post" enctype="multipart/form-data" class="mis360-member-form" action="'.esc_url(mis360_account_url('belgeler')).'">';
    wp_nonce_field('mis360_member_tax','_member_nonce');echo '<input type="hidden" name="member_action" value="tax">';mis360_tax_field(true);echo '<button class="mis360-member-button" type="submit">Belgeyi incelemeye gönder</button></form></details>';
}
add_action('wp_enqueue_scripts',function(){if(!is_page('uyelik'))return;wp_enqueue_style('mis360-firm-management',get_template_directory_uri().'/assets/css/firm-management.css',array('mis360-account-center'),filemtime(get_template_directory().'/assets/css/firm-management.css'));wp_enqueue_script('mis360-firm-management',get_template_directory_uri().'/assets/js/firm-management.js',array(),filemtime(get_template_directory().'/assets/js/firm-management.js'),true);},45);
