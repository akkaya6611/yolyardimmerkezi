<?php
if (!defined('ABSPATH')) exit;
function mis360_support_table(){global $wpdb;return $wpdb->prefix.'mis360_support_messages';}
function mis360_support_install(){
 if(get_option('mis360_support_version')==='1')return;
 global $wpdb;require_once ABSPATH.'wp-admin/includes/upgrade.php';$table=mis360_support_table();
 dbDelta("CREATE TABLE $table (
 id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 member_id bigint(20) unsigned NOT NULL,
 sender_id bigint(20) unsigned NOT NULL,
 from_admin tinyint(1) NOT NULL DEFAULT 0,
 body text NOT NULL,
 created_at datetime NOT NULL,
 seen tinyint(1) NOT NULL DEFAULT 0,
 request_key varchar(64) NOT NULL,
 PRIMARY KEY  (id),
 UNIQUE KEY dedupe (sender_id,request_key),
 KEY conversation (member_id,id),
 KEY unread (from_admin,seen,member_id)
 ) ".$wpdb->get_charset_collate().';');
 if($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($table)))===$table)update_option('mis360_support_version','1',false);
}
add_action('init','mis360_support_install',12);
function mis360_support_unread($admin=false){
 global $wpdb;if(!is_user_logged_in())return 0;
 if($admin&&current_user_can('manage_options'))return (int)$wpdb->get_var('SELECT COUNT(*) FROM '.mis360_support_table().' WHERE from_admin=0 AND seen=0');
 return (int)$wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM '.mis360_support_table().' WHERE member_id=%d AND from_admin=1 AND seen=0',get_current_user_id()));
}
function mis360_support_input($key){return isset($_POST[$key])&&is_string($_POST[$key])?wp_unslash($_POST[$key]):'';}
function mis360_support_ajax(){
 if(!is_user_logged_in())wp_send_json_error(array('message'=>'Mesajlaşmak için giriş yapın.'),401);
 if(!check_ajax_referer('mis360_support','nonce',false))wp_send_json_error(array('message'=>'Oturumun süresi doldu. Sayfayı yenileyin.'),403);
 $admin=current_user_can('manage_options');$uid=get_current_user_id();
 if(!$admin&&!mis360_email_verified($uid))wp_send_json_error(array('message'=>'Önce e-posta adresinizi doğrulayın.'),403);
 global $wpdb;$table=mis360_support_table();$op=mis360_support_input('op');
 if($op==='list'){
  if(!$admin)wp_send_json_error(array('message'=>'Bu işlem için yetkiniz yok.'),403);
  $page=max(1,absint(mis360_support_input('page')));$offset=($page-1)*30;
  $rows=$wpdb->get_results($wpdb->prepare("SELECT s.member_id,s.last_id,s.unread,m.body,m.created_at,u.display_name FROM (SELECT member_id,MAX(id) last_id,SUM(CASE WHEN from_admin=0 AND seen=0 THEN 1 ELSE 0 END) unread FROM $table GROUP BY member_id) s JOIN $table m ON m.id=s.last_id LEFT JOIN {$wpdb->users} u ON u.ID=s.member_id ORDER BY s.unread>0 DESC,s.last_id DESC LIMIT 31 OFFSET %d",$offset),ARRAY_A);
  foreach($rows as &$row){$row['body']=mb_substr($row['body'],0,80);$row['display_name']=$row['display_name']?:'Silinmiş üye';}unset($row);
  $more=count($rows)>30;wp_send_json_success(array('items'=>array_slice($rows,0,30),'more'=>$more,'unread'=>mis360_support_unread(true))); 
 }
 $member=$admin?absint(mis360_support_input('member')):$uid;
 if(!$member||!get_userdata($member))wp_send_json_error(array('message'=>'Üye bulunamadı.'),404);
 if($op==='send'){
  $body=trim(sanitize_textarea_field(mis360_support_input('body')));$key=mis360_support_input('request_key');
  if($body===''||mb_strlen($body)>2000||!preg_match('/^[a-zA-Z0-9_-]{16,64}$/D',$key))wp_send_json_error(array('message'=>'Mesajınızı 1–2000 karakter arasında yazın.'),400);
  $lock='m360_support_'.md5(DB_NAME.$wpdb->prefix.$uid);
  if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')wp_send_json_error(array('message'=>'Mesajınız işleniyor. Biraz sonra tekrar deneyin.'),429);
  $error='';$id=0;
  try{
   $existing=$wpdb->get_row($wpdb->prepare("SELECT id,member_id FROM $table WHERE sender_id=%d AND request_key=%s",$uid,$key));
   if($existing){if((int)$existing->member_id!==$member)$error='Gönderim anahtarı geçersiz.';else $id=(int)$existing->id;}
   else{
    $count=(int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE sender_id=%d AND created_at>=%s",$uid,gmdate('Y-m-d H:i:s',time()-60)));
    if($count>=($admin?40:10))$error='Çok hızlı mesaj gönderiyorsunuz. Bir dakika bekleyin.';
    else{
     $ok=$wpdb->insert($table,array('member_id'=>$member,'sender_id'=>$uid,'from_admin'=>$admin?1:0,'body'=>$body,'created_at'=>gmdate('Y-m-d H:i:s'),'seen'=>0,'request_key'=>$key),array('%d','%d','%d','%s','%s','%d','%s'));
     if(!$ok)$error='Mesaj kaydedilemedi. Tekrar deneyin.';else {$id=(int)$wpdb->insert_id;if($admin)do_action('mis360_support_admin_reply',$member,$id);}
    }
   }
  }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
  if($error)wp_send_json_error(array('message'=>$error),429);
  wp_send_json_success(array('id'=>$id));
 }
 if($op==='read'){
  $last=absint(mis360_support_input('last'));
  $wpdb->query($wpdb->prepare("UPDATE $table SET seen=1 WHERE member_id=%d AND from_admin=%d AND id<=%d AND seen=0",$member,$admin?0:1,$last));
  wp_send_json_success(array('unread'=>mis360_support_unread($admin)));
 }
 if($op!=='messages')wp_send_json_error(array('message'=>'Geçersiz işlem.'),400);
 $before=absint(mis360_support_input('before'));$after=absint(mis360_support_input('after'));
 $where=$wpdb->prepare('member_id=%d',$member);
 if($before)$where.=$wpdb->prepare(' AND id<%d',$before);elseif($after)$where.=$wpdb->prepare(' AND id>%d',$after);
 $order=$after&&!$before?'ASC':'DESC';
 $rows=$wpdb->get_results("SELECT id,sender_id,from_admin,body,created_at,seen FROM $table WHERE $where ORDER BY id $order LIMIT 51",ARRAY_A);
 $more=count($rows)>50;$rows=array_slice($rows,0,50);if($order==='DESC')$rows=array_reverse($rows);
 $seen=(int)$wpdb->get_var($wpdb->prepare("SELECT MAX(id) FROM $table WHERE member_id=%d AND from_admin=%d AND seen=1",$member,$admin?1:0));
 wp_send_json_success(array('items'=>$rows,'more'=>$more,'seen'=>$seen,'unread'=>mis360_support_unread($admin)));
}
add_action('wp_ajax_mis360_support','mis360_support_ajax');
add_action('wp_ajax_nopriv_mis360_support',function(){wp_send_json_error(array('message'=>'Oturumunuz sona erdi. Giriş yapın.'),401);});
function mis360_support_assets(){
 $r=get_template_directory();$u=get_template_directory_uri();
 wp_enqueue_style('mis360-support',$u.'/assets/css/live-support.css',array(),filemtime($r.'/assets/css/live-support.css'));
 wp_enqueue_script('mis360-support',$u.'/assets/js/live-support.js',array(),filemtime($r.'/assets/js/live-support.js'),true);
 wp_localize_script('mis360-support','mis360Support',array('url'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('mis360_support'),'admin'=>current_user_can('manage_options')&&is_admin(),'uid'=>get_current_user_id()));
}
add_action('wp_enqueue_scripts',function(){if(is_page('uyelik')&&is_user_logged_in()&&mis360_account_section()==='destek')mis360_support_assets();},45);
add_action('admin_enqueue_scripts',function($hook){if($hook==='toplevel_page_mis360-support')mis360_support_assets();});
add_action('admin_menu',function(){
 $count=mis360_support_unread(true);$label='Canlı Destek'.($count?' <span class="awaiting-mod"><span class="pending-count">'.$count.'</span></span>':'');
 add_menu_page('Canlı Destek',$label,'manage_options','mis360-support',function(){
  if(!current_user_can('manage_options'))return;
  echo '<div class="wrap"><h1>Canlı Destek</h1><p>Üyelerin mesajlarını buradan okuyup yanıtlayabilirsiniz. Mesajlar otomatik yenilenir.</p>';mis360_support_ui(true);echo '</div>';
 },'dashicons-format-chat',26);
});
function mis360_support_ui($admin=false){ ?>
<div class="m360-support <?php echo $admin?'is-admin':''; ?>" data-support-root>
 <?php if($admin): ?><aside class="m360-inbox"><h2>Görüşmeler <span data-total-unread></span></h2><div data-threads></div><div class="m360-inbox-pages"><button type="button" data-prev>Önceki</button><span data-list-page>1</span><button type="button" data-next>Sonraki</button></div></aside><?php endif; ?>
 <section class="m360-chat"><header><div><h3 data-chat-title><?php echo $admin?'Bir görüşme seçin':'Yöneticiye mesaj gönderin'; ?></h3><p>Mesajlar 5 saniyede bir yenilenir. Yanıt süresi yöneticinin müsaitliğine bağlıdır.</p></div></header>
 <button type="button" class="m360-older" data-older hidden>Önceki mesajları yükle</button>
 <div class="m360-messages" data-messages role="log" aria-label="Mesaj geçmişi" aria-live="polite"><p class="m360-chat-empty"><?php echo $admin?'Soldaki listeden bir üye seçin.':'Henüz mesajınız yok. Size nasıl yardımcı olabiliriz?'; ?></p></div>
 <p data-chat-error class="m360-chat-error" role="status"></p>
 <form data-chat-form><label for="m360-message">Mesajınız</label><textarea id="m360-message" name="message" maxlength="2000" rows="3" required placeholder="Mesajınızı yazın…" <?php disabled($admin); ?>></textarea><div><small>En fazla 2000 karakter · Enter: yeni satır</small><button type="submit" <?php disabled($admin); ?>>Gönder →</button></div></form>
 </section>
</div>
<?php }
