<?php
if(!defined('ABSPATH'))exit;
add_action('init',function(){foreach(array('m360_notice'=>'Üye bildirimleri','m360_claim'=>'Firma sahiplenme talepleri') as $type=>$label)register_post_type($type,array('label'=>$label,'public'=>false,'publicly_queryable'=>false,'show_ui'=>false,'show_in_rest'=>false,'rewrite'=>false,'query_var'=>false,'supports'=>array(),'capabilities'=>array('edit_posts'=>'manage_options','read_private_posts'=>'manage_options','create_posts'=>'do_not_allow'),'map_meta_cap'=>true));});
function mis360_hub_lock($key){global $wpdb;$name='m360hub_'.md5(DB_NAME.$wpdb->prefix.$key);return (string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$name))==='1'?$name:false;}
function mis360_hub_unlock($lock){global $wpdb;if($lock)$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}

function mis360_notice_add($uid,$title,$body,$section='ozet',$event=''){
    if(!$uid||!get_userdata($uid))return;
    $lock=mis360_hub_lock('notice_'.$uid);if(!$lock)return;
    try{
        if($event && get_posts(array('post_type'=>'m360_notice','post_status'=>'private','author'=>$uid,'fields'=>'ids','numberposts'=>1,'meta_key'=>'_notice_event','meta_value'=>$event)))return;
        wp_insert_post(wp_slash(array('post_type'=>'m360_notice','post_status'=>'private','post_author'=>$uid,'post_title'=>$title,'post_content'=>$body,'meta_input'=>array('_notice_section'=>$section,'_notice_read'=>0,'_notice_event'=>$event))),true);
    }finally{mis360_hub_unlock($lock);}
}
function mis360_notice_unread(){if(!get_current_user_id())return 0;$q=new WP_Query(array('post_type'=>'m360_notice','post_status'=>'private','author'=>get_current_user_id(),'posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_notice_read','meta_value'=>0));return (int)$q->found_posts;}
function mis360_notice_read($id){$p=get_post($id);if(!$p||$p->post_type!=='m360_notice'||!get_current_user_id()||(int)$p->post_author!==get_current_user_id())return false;update_post_meta($id,'_notice_read',1);return true;}
add_action('transition_post_status',function($new,$old,$post){if($post->post_type!=='firma'||$new!=='publish'||$old==='publish'||!$post->post_author||user_can($post->post_author,'manage_options'))return;mis360_notice_add($post->post_author,'Firmanız yayında',$post->post_title.' yayına alındı.','firmalar');},10,3);
add_action('mis360_review_recorded',function($uid,$type,$decision,$note,$firm){if($type==='firm'&&$decision==='approved')return;mis360_notice_add($uid,$type==='tax'?'Vergi levhası incelemesi':'Firma başvurusu için düzeltme',($decision==='approved'?'Onaylandı. ':'Düzeltme gerekiyor. ').$note,$type==='tax'?'belgeler':'firmalar');},10,5);
add_action('mis360_support_admin_reply',function($uid,$id){mis360_notice_add($uid,'Destekten yeni yanıt','Yönetici mesajınıza yanıt verdi. Görüşmeyi Destek & Mesajlar sayfasından açabilirsiniz.','destek','support_'.$id);},10,2);
function mis360_notifications_panel(){
    $page=max(1,absint($_GET['npage']??1));$q=new WP_Query(array('post_type'=>'m360_notice','post_status'=>'private','author'=>get_current_user_id(),'posts_per_page'=>15,'paged'=>$page));
    echo '<section class="mis360-account-panel"><p class="mis360-hub-muted">Firma, belge, sahiplenme ve destek işlemlerinizle ilgili yeni bildirimler burada toplanır.</p>';
    foreach($q->posts as $n){$read=(bool)get_post_meta($n->ID,'_notice_read',true);$section=get_post_meta($n->ID,'_notice_section',true);if(!in_array($section,array('ozet','firmalar','belgeler','destek','sahiplenme'),true))$section='ozet';
        echo '<article class="mis360-notice-row'.($read?'':' is-unread').'"><small>'.esc_html(get_the_date('d.m.Y H:i',$n)).($read?'':' · Yeni').'</small><h3>'.esc_html($n->post_title).'</h3><p>'.nl2br(esc_html($n->post_content)).'</p><div class="mis360-hub-actions"><a href="'.esc_url(mis360_account_url($section)).'">İlgili sayfayı aç →</a>';
        if(!$read){echo '<form method="post">';wp_nonce_field('mis360_hub','hub_nonce');echo '<input type="hidden" name="hub_action" value="notice_read"><input type="hidden" name="notice_id" value="'.(int)$n->ID.'"><button>Okundu işaretle</button></form>'; }echo '</div></article>';
    }
    if(!$q->posts)echo '<p>Henüz bildiriminiz yok. Yeni gelişmeler burada görünecek.</p>';
    echo paginate_links(array('base'=>add_query_arg('npage','%#%',mis360_account_url('bildirimler')),'current'=>$page,'total'=>$q->max_num_pages));echo '</section>';
}

function mis360_profile_save($name,$phone){
    $uid=get_current_user_id();if(!$uid||!mis360_email_verified($uid))return new WP_Error('auth','Önce hesabınızı doğrulayın.');
    $name=sanitize_text_field($name);$phone=sanitize_text_field($phone);
    if(mb_strlen($name)<2||mb_strlen($name)>80)return new WP_Error('name','Ad soyad 2–80 karakter olmalı.');
    if($phone!==''&&!preg_match('/^\+?[0-9 ()-]{10,25}$/D',$phone))return new WP_Error('phone','Geçerli bir telefon numarası girin.');
    $r=wp_update_user(wp_slash(array('ID'=>$uid,'display_name'=>$name,'first_name'=>$name)));if(is_wp_error($r))return $r;
    update_user_meta($uid,'mis360_member_phone',$phone);return true;
}
function mis360_profile_email_request($email,$password){
    $uid=get_current_user_id();$user=get_userdata($uid);
    if(!$user||!mis360_email_verified($uid))return new WP_Error('auth','Önce giriş yapın ve hesabınızı doğrulayın.');
    $attempt_key='mis360_email_password_'.$uid;$attempts=(int)get_transient($attempt_key);
    if($attempts>=8)return new WP_Error('rate','Çok fazla hatalı şifre denemesi oldu. 15 dakika sonra tekrar deneyin.');
    if(!wp_check_password($password,$user->user_pass,$uid)){set_transient($attempt_key,$attempts+1,15*MINUTE_IN_SECONDS);return new WP_Error('password','Mevcut şifrenizi doğru girin.');}
    delete_transient($attempt_key);
    $email=strtolower(trim($email));if(!is_email($email)||strlen($email)>100||$email===strtolower($user->user_email))return new WP_Error('email','Farklı ve geçerli bir e-posta adresi girin.');
    $lock=mis360_hub_lock('profile_email_'.$uid);if(!$lock)return new WP_Error('busy','Biraz sonra tekrar deneyin.');
    try{
        $rate=get_user_meta($uid,'mis360_email_change_rate',true);if(!is_array($rate)||($rate['until']??0)<time())$rate=array('until'=>time()+HOUR_IN_SECONDS,'count'=>0,'last'=>0);
        if($rate['last']>time()-120||$rate['count']>=5)return new WP_Error('rate','Yeni kod için 2 dakika bekleyin. Saatte en fazla 5 kod isteyebilirsiniz.');
        if(email_exists($email))return new WP_Error('email','Bu e-posta adresi kullanılamıyor.');
        $rate['count']++;$rate['last']=time();update_user_meta($uid,'mis360_email_change_rate',$rate);
        $code=(string)random_int(100000,999999);$record=array('email'=>$email,'old'=>strtolower($user->user_email),'hash'=>hash_hmac('sha256',$code,wp_salt('auth')),'expires'=>time()+600,'attempts'=>0);
        $plain='Yeni e-posta adresiniz için doğrulama kodunuz: '.$code.'. Kod 10 dakika geçerlidir. Kodu kimseyle paylaşmayın.';
        $alternative=static function($mailer)use($plain){$mailer->AltBody=$plain;};add_action('phpmailer_init',$alternative,1000);
        try{$sent=wp_mail($email,'Yol Yardım Merkezi — Yeni e-posta doğrulaması',mis360_email_code_html($code,$user->display_name),array('Content-Type: text/html; charset=UTF-8'));}finally{remove_action('phpmailer_init',$alternative,1000);}
        if(!$sent)return new WP_Error('mail','Kod gönderilemedi. Mevcut e-posta adresiniz değişmedi.');
        update_user_meta($uid,'mis360_pending_email',$record);return true;
    }finally{mis360_hub_unlock($lock);}
}
function mis360_profile_email_confirm($code){
    $uid=get_current_user_id();if(!$uid)return new WP_Error('auth','Giriş yapın.');
    $lock=mis360_hub_lock('profile_email_'.$uid);if(!$lock)return new WP_Error('busy','Biraz sonra tekrar deneyin.');
    $email_lock=false;
    try{
        $record=get_user_meta($uid,'mis360_pending_email',true);$user=get_userdata($uid);
        if(!$user||!is_array($record)||$record['expires']<time()||$record['attempts']>=5||$record['old']!==strtolower($user->user_email))return new WP_Error('code','Kodun süresi doldu veya deneme sınırına ulaşıldı. Yeni kod isteyin.');
        $record['attempts']++;update_user_meta($uid,'mis360_pending_email',$record);
        if(!preg_match('/^[0-9]{6}$/D',$code)||!hash_equals($record['hash'],hash_hmac('sha256',$code,wp_salt('auth'))))return new WP_Error('code','Doğrulama kodu hatalı.');
        global $wpdb;
        $email_lock='mis360-register-'.substr(hash('sha256',DB_NAME.'|'.$wpdb->prefix.'|'.$record['email']),0,40);
        if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$email_lock))!=='1'){$email_lock=false;return new WP_Error('busy','Adres üzerinde başka bir işlem var. Tekrar deneyin.');}
        if(email_exists($record['email']))return new WP_Error('email','Bu e-posta adresi artık kullanılamıyor. Başka bir adres deneyin.');
        $result=wp_update_user(array('ID'=>$uid,'user_email'=>$record['email']));if(is_wp_error($result))return $result;
        update_user_meta($uid,'mis360_verified_email',$record['email']);update_user_meta($uid,'mis360_email_verified_at',time());delete_user_meta($uid,'mis360_pending_email');delete_user_meta($uid,'mis360_email_token');
        return true;
    }finally{if($email_lock)$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$email_lock));mis360_hub_unlock($lock);}
}
function mis360_profile_panel(){
    $u=wp_get_current_user();$pending=get_user_meta($u->ID,'mis360_pending_email',true); ?>
    <section class="mis360-account-panel"><h3>Kişisel bilgiler</h3><form method="post" class="mis360-hub-form"><?php wp_nonce_field('mis360_hub','hub_nonce'); ?><input type="hidden" name="hub_action" value="profile_save"><label>Ad soyad<input name="member_name" required minlength="2" maxlength="80" autocomplete="name" value="<?php echo esc_attr($u->display_name); ?>"></label><label>Telefon <span>(isteğe bağlı)</span><input name="member_phone" type="tel" autocomplete="tel" value="<?php echo esc_attr(get_user_meta($u->ID,'mis360_member_phone',true)); ?>"></label><button>Bilgilerimi kaydet</button></form></section>
    <section class="mis360-account-panel"><h3>E-posta adresi</h3><p>Mevcut adresiniz: <strong><?php echo esc_html($u->user_email); ?></strong></p><p>Yeni adresiniz kodla doğrulanana kadar mevcut adresiniz kullanılmaya devam eder.</p>
    <form method="post" class="mis360-hub-form"><?php wp_nonce_field('mis360_hub','hub_nonce'); ?><input type="hidden" name="hub_action" value="email_request"><label>Yeni e-posta<input name="new_email" type="email" maxlength="100" required autocomplete="email"></label><label>Mevcut şifreniz<input name="current_password" type="password" required autocomplete="current-password"></label><button>Yeni adrese kod gönder</button></form>
    <?php if(is_array($pending)): ?><form method="post" class="mis360-hub-form mis360-email-confirm"><?php wp_nonce_field('mis360_hub','hub_nonce'); ?><input type="hidden" name="hub_action" value="email_confirm"><p><strong><?php echo esc_html($pending['email']); ?></strong> adresine gönderilen 6 haneli kodu girin. Kod 10 dakika geçerlidir.</p><label>Doğrulama kodu<input name="change_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required></label><button>Doğrula ve e-postamı değiştir</button></form><form method="post"><?php wp_nonce_field('mis360_hub','hub_nonce'); ?><input type="hidden" name="hub_action" value="email_cancel"><button class="mis360-hub-text-button">E-posta değişikliğini iptal et</button></form><?php endif; ?></section>
    <section class="mis360-account-panel"><h3>Şifre</h3><p>Şifrenizi güvenli yenileme ekranından değiştirebilirsiniz.</p><a href="<?php echo esc_url(wp_lostpassword_url(mis360_account_url('profil'))); ?>">Şifremi yenile →</a></section>
<?php }

function mis360_claim_eligible($firm){$p=get_post($firm);return $p&&$p->post_type==='firma'&&$p->post_status==='publish'&&(!$p->post_author||user_can($p->post_author,'manage_options'));}
function mis360_claim_submit($firm,$explanation){
    $uid=get_current_user_id();if(!$uid||!mis360_email_verified($uid)||current_user_can('manage_options'))return new WP_Error('auth','Doğrulanmış üye hesabınızla giriş yapın.');
    if(!mis360_tax_present($uid))return new WP_Error('tax','Önce Belgelerim sayfasından vergi levhanızı yükleyin.');
    $explanation=sanitize_textarea_field($explanation);if(mb_strlen($explanation)<20||mb_strlen($explanation)>2000)return new WP_Error('note','Firma ile ilişkinizi 20–2000 karakterle açıklayın.');
    $lock=mis360_hub_lock('claim_'.$firm);if(!$lock)return new WP_Error('busy','Başka bir işlem var. Tekrar deneyin.');
    try{
        if(!mis360_claim_eligible($firm))return new WP_Error('firm','Bu firma sahiplenmeye açık değil. Destek üzerinden iletişime geçebilirsiniz.');
        $existing=get_posts(array('post_type'=>'m360_claim','post_status'=>'private','author'=>$uid,'numberposts'=>1,'meta_query'=>array(array('key'=>'_claim_firm','value'=>$firm),array('key'=>'_claim_state','value'=>'pending'))));
        if($existing)return new WP_Error('duplicate','Bu firma için zaten inceleme bekleyen talebiniz var.');
        $q=new WP_Query(array('post_type'=>'m360_claim','post_status'=>'private','author'=>$uid,'posts_per_page'=>1,'fields'=>'ids','date_query'=>array(array('after'=>'24 hours ago'))));if($q->found_posts>=5)return new WP_Error('rate','Günlük 5 talep sınırına ulaşıldı.');
        $id=wp_insert_post(wp_slash(array('post_type'=>'m360_claim','post_status'=>'private','post_author'=>$uid,'post_title'=>get_the_title($firm),'post_content'=>$explanation,'meta_input'=>array('_claim_firm'=>$firm,'_claim_state'=>'pending','_claim_previous_owner'=>(int)get_post_field('post_author',$firm),'_claim_tax_version'=>mis360_tax_version($uid)))),true);
        if(is_wp_error($id)||!$id)return new WP_Error('save','Talep kaydedilemedi.');
        mis360_notice_add($uid,'Sahiplenme talebiniz alındı',get_the_title($firm).' için başvurunuz yönetici incelemesini bekliyor.','sahiplenme','claim_new_'.$id);delete_user_meta($uid,'mis360_claim_intent');return true;
    }finally{mis360_hub_unlock($lock);}
}
function mis360_claim_decide($id,$decision,$note,$confirmed){
    if(!current_user_can('manage_options'))return new WP_Error('auth','Yetkiniz yok.');
    $claim=get_post($id);if(!$claim||$claim->post_type!=='m360_claim'||!in_array($decision,array('approved','rejected'),true))return new WP_Error('claim','Geçersiz talep.');
    $firm=(int)get_post_meta($id,'_claim_firm',true);$uid=(int)$claim->post_author;$note=sanitize_textarea_field($note);
    if(mb_strlen($note)>2000||($decision==='rejected'&&mb_strlen($note)<5))return new WP_Error('note','Ret gerekçesini yazın (5–2000 karakter).');
    $lock=mis360_hub_lock('claim_'.$firm);if(!$lock)return new WP_Error('busy','Başka bir işlem var.');
    try{
        if(get_post_meta($id,'_claim_state',true)!=='pending')return new WP_Error('state','Bu talep zaten sonuçlandırılmış.');
        if($decision==='approved'){
            if(!$confirmed)return new WP_Error('confirm','Belge ile firma eşleşmesini kontrol ettiğinizi işaretleyin.');
            if(!get_userdata($uid)||!mis360_email_verified($uid))return new WP_Error('user','Üyenin e-posta doğrulaması tamamlanmamış.');
            if(!mis360_claim_eligible($firm)||(int)get_post_field('post_author',$firm)!==(int)get_post_meta($id,'_claim_previous_owner',true))return new WP_Error('owner','Firma başka bir hesaba geçmiş veya yayından kaldırılmış. Talebi yeniden inceleyin.');
            if(mis360_tax_review($uid)['state']!=='approved'||!hash_equals((string)get_post_meta($id,'_claim_tax_version',true),mis360_tax_version($uid)))return new WP_Error('tax','Talepteki vergi levhası onaylı olmalı. Belge değiştiyse üyeden yeni talep isteyin.');
            $r=wp_update_post(array('ID'=>$firm,'post_author'=>$uid),true);if(is_wp_error($r))return $r;
            update_post_meta($firm,'_mis360_claimed_by',$uid);update_post_meta($firm,'_mis360_claimed_at',current_time('mysql'));update_user_meta($uid,'mis360_member_kind','firm');
        }
        update_post_meta($id,'_claim_state',$decision);update_post_meta($id,'_claim_note',wp_slash($note));update_post_meta($id,'_claim_reviewer',get_current_user_id());update_post_meta($id,'_claim_reviewed_at',current_time('mysql'));
        mis360_notice_add($uid,$decision==='approved'?'Firma sahiplenmeniz onaylandı':'Firma sahiplenme sonucu',$claim->post_title.'. '.($decision==='approved'?'Firmanızı artık Firmalarım sayfasından yönetebilirsiniz. ': 'Başvurunuz onaylanmadı. ').$note,'sahiplenme','claim_result_'.$id);return true;
    }finally{mis360_hub_unlock($lock);}
}
function mis360_claim_link($firm){if(!mis360_claim_eligible($firm))return;$url=add_query_arg('claim_firm',$firm,mis360_account_url('sahiplenme'));echo '<div class="mis360-claim-callout"><strong>Bu işletme size mi ait?</strong><p>Belge incelemesinden sonra mevcut firma kaydını hesabınızdan yönetin.</p><a href="'.esc_url($url).'">Bu firmayı sahiplen →</a></div>';}
function mis360_claim_panel(){
    $uid=get_current_user_id();$firm=isset($_GET['claim_firm'])&&is_scalar($_GET['claim_firm'])?absint($_GET['claim_firm']):0;
    if($firm){echo '<section class="mis360-account-panel">';if(!mis360_claim_eligible($firm)){echo '<p>Bu firma sahiplenmeye açık değil.</p>';}elseif(!mis360_tax_present($uid)){echo '<p>Sahiplenme için vergi levhanızı yükleyin. Sonra bu sayfadan talebinizi gönderin.</p><a href="'.esc_url(mis360_account_url('belgeler')).'">Belgelerim →</a>';}else{echo '<h3>'.esc_html(get_the_title($firm)).'</h3><p>Firma ile ilişkinizi ve vergi levhasındaki ticari unvanı açıklayın. Yönetici incelemesi tamamlanmadan kayıt size aktarılmaz.</p><form method="post" class="mis360-hub-form">';wp_nonce_field('mis360_hub','hub_nonce');echo '<input type="hidden" name="hub_action" value="claim_submit"><input type="hidden" name="claim_firm" value="'.(int)$firm.'"><label>Açıklamanız<textarea name="claim_explanation" rows="5" required minlength="20" maxlength="2000"></textarea></label><button>Sahiplenme talebi gönder</button></form>';}echo '</section>';}
    $page=max(1,absint($_GET['cpage']??1));$q=new WP_Query(array('post_type'=>'m360_claim','post_status'=>'private','author'=>$uid,'posts_per_page'=>15,'paged'=>$page));
    echo '<section class="mis360-account-panel"><h3>Sahiplenme taleplerim</h3><p>Firmanızı rehberde bulup detay sayfasındaki “Bu firmayı sahiplen” düğmesini kullanın.</p><a href="'.esc_url(home_url('/firmalar/')).'">Firma rehberinde ara →</a>';
    foreach($q->posts as $p){$state=get_post_meta($p->ID,'_claim_state',true);echo '<article class="mis360-notice-row"><h3>'.esc_html($p->post_title).'</h3><strong>'.esc_html(array('pending'=>'İnceleme bekliyor','approved'=>'Onaylandı','rejected'=>'Onaylanmadı')[$state]??'İnceleniyor').'</strong><p>'.nl2br(esc_html(get_post_meta($p->ID,'_claim_note',true))).'</p></article>';}
    if(!$q->posts)echo '<p>Henüz talebiniz yok.</p>';echo paginate_links(array('base'=>add_query_arg('cpage','%#%',mis360_account_url('sahiplenme')),'current'=>$page,'total'=>$q->max_num_pages));echo '</section>';
}
add_action('template_redirect',function(){
    if(!is_page('uyelik'))return;
    if(!is_user_logged_in()&&isset($_GET['claim_firm']))return;
    if(($_SERVER['REQUEST_METHOD']??'')!=='POST'||!isset($_POST['hub_action']))return;
    if(!is_user_logged_in()||!mis360_email_verified(get_current_user_id())||!wp_verify_nonce(mis360_member_input('hub_nonce'),'mis360_hub')){$GLOBALS['mis360_hub_error']='Oturum veya form doğrulanamadı. Sayfayı yenileyin.';return;}
    $action=mis360_member_input('hub_action');$dest='profil';$message='Bilgileriniz kaydedildi.';
    switch($action){
        case 'profile_save':$result=mis360_profile_save(mis360_member_input('member_name'),mis360_member_input('member_phone'));break;
        case 'email_request':$result=mis360_profile_email_request(mis360_member_input('new_email'),mis360_member_input('current_password',true));$message='Yeni adresinize doğrulama kodu gönderildi.';break;
        case 'email_confirm':$result=mis360_profile_email_confirm(mis360_member_input('change_code'));$message='Yeni e-posta adresiniz doğrulandı ve kaydedildi.';break;
        case 'email_cancel':delete_user_meta(get_current_user_id(),'mis360_pending_email');$result=true;$message='E-posta değişikliği iptal edildi.';break;
        case 'notice_read':$result=mis360_notice_read(absint(mis360_member_input('notice_id')))?true:new WP_Error('auth','Bildirim bulunamadı.');$dest='bildirimler';$message='Bildirim okundu olarak işaretlendi.';break;
        case 'claim_submit':$result=mis360_claim_submit(absint(mis360_member_input('claim_firm')),mis360_member_input('claim_explanation',true));$dest='sahiplenme';$message='Sahiplenme talebiniz alındı.';break;
        default:$result=new WP_Error('action','Geçersiz işlem.');
    }
    if(is_wp_error($result)){$GLOBALS['mis360_hub_error']=$result->get_error_message();return;}
    set_transient('mis360_hub_flash_'.get_current_user_id(),$message,60);wp_safe_redirect(mis360_account_url($dest));exit;
},9);
function mis360_hub_flash(){if(!empty($GLOBALS['mis360_hub_error']))echo '<div role="alert" class="mis360-member-alert">'.esc_html($GLOBALS['mis360_hub_error']).'</div>';$key='mis360_hub_flash_'.get_current_user_id();$text=get_transient($key);if($text){delete_transient($key);echo '<div role="status" class="mis360-account-notice">'.esc_html($text).'</div>';}}
add_action('wp_enqueue_scripts',function(){if(is_page('uyelik')||is_singular('firma'))wp_enqueue_style('mis360-member-hub',get_template_directory_uri().'/assets/css/member-hub.css',array(),filemtime(get_template_directory().'/assets/css/member-hub.css'));},50);

function mis360_claim_intent_id(){return isset($_GET['claim_firm'])&&is_scalar($_GET['claim_firm'])?absint($_GET['claim_firm']):absint(mis360_member_input('claim_return'));}
