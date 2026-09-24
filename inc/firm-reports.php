<?php
if(!defined('ABSPATH'))exit;
add_action('init',function(){register_post_type('mis360_report',array('label'=>'Düzeltme ve Kaldırma Talepleri','public'=>false,'publicly_queryable'=>false,'show_ui'=>false,'show_in_rest'=>false,'rewrite'=>false,'query_var'=>false,'exclude_from_search'=>true,'supports'=>array(),'capability_type'=>'post','capabilities'=>array('edit_posts'=>'manage_options','read_private_posts'=>'manage_options','create_posts'=>'do_not_allow'),'map_meta_cap'=>true));});
function mis360_report_types(){return array('phone'=>'Telefon / WhatsApp','address'=>'Adres / konum','hours'=>'Çalışma saatleri','closed'=>'İşletme kapalı / taşınmış','removal'=>'Kaydın kaldırılmasını istiyorum','privacy'=>'Kişisel veri / izinsiz içerik bildirimi','other'=>'Diğer bilgiler');}
function mis360_report_submit($firm,$type,$message,$name=''){
    $post=get_post($firm);
    if(!$post||$post->post_type!=='firma'||$post->post_status!=='publish')return new WP_Error('firm','Firma kaydı bulunamadı.');
    if(!isset(mis360_report_types()[$type]))return new WP_Error('type','Talep konusunu seçin.');
    $message=sanitize_textarea_field($message);$name=sanitize_text_field($name);
    if(mb_strlen(trim($message))<10||mb_strlen($message)>2000||mb_strlen($name)>80)return new WP_Error('length','Açıklama 10–2000, adınız en fazla 80 karakter olabilir.');
    $fingerprint=substr(hash_hmac('sha256',$_SERVER['REMOTE_ADDR']??'unknown',wp_salt('nonce')),0,40);
    $duplicate='m360_report_dup_'.hash('sha256',$fingerprint.'|'.$firm.'|'.$type.'|'.$message);
    global $wpdb;$lock='m360_report_'.$fingerprint;
    if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')return new WP_Error('busy','Gönderiminiz işleniyor. Biraz sonra tekrar deneyin.');
    try{
        if(get_transient($duplicate))return true;
        $key='m360_report_rate_'.$fingerprint;$count=(int)get_transient($key);
        if($count>=5)return new WP_Error('rate','Çok fazla bildirim gönderdiniz. Bir saat sonra tekrar deneyin.');
        $id=wp_insert_post(wp_slash(array('post_type'=>'mis360_report','post_status'=>'private','post_author'=>get_current_user_id(),'post_title'=>$post->post_title,'post_content'=>$message,'meta_input'=>array('_report_firm'=>(int)$firm,'_report_type'=>$type,'_report_name'=>$name,'_report_state'=>'new'))),true);
        if(is_wp_error($id)||!$id)return new WP_Error('save','Bildirim kaydedilemedi. Lütfen tekrar deneyin.');
        set_transient($key,$count+1,HOUR_IN_SECONDS);set_transient($duplicate,1,30*MINUTE_IN_SECONDS);
        return true;
    }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
function mis360_report_ajax(){
    $firm=absint(mis360_member_input('firm'));
    if(!wp_verify_nonce(mis360_member_input('token'),'mis360_report_'.$firm))wp_send_json_error(array('message'=>'Formun süresi doldu. Sayfayı yenileyin.'),403);
    if(mis360_member_input('company_url')!=='')wp_send_json_error(array('message'=>'Bildirim gönderilemedi.'),400);
    $result=mis360_report_submit($firm,mis360_member_input('report_type'),mis360_member_input('message',true),mis360_member_input('report_name'));
    if(is_wp_error($result))wp_send_json_error(array('message'=>$result->get_error_message()),400);
    wp_send_json_success(array('message'=>'Talebiniz yöneticiye iletildi. İnceleme sonucuna göre işlem yapılacaktır; bu bildirim kaydı otomatik değiştirmez veya kaldırmaz.'));
}
add_action('wp_ajax_mis360_report','mis360_report_ajax');add_action('wp_ajax_nopriv_mis360_report','mis360_report_ajax');
function mis360_report_form($firm){ ?>
<div id="firma-bildirim" class="yym-side-report-box mis360-report-box"><small>Bu kayıtta yanlış bilgi mi var veya kaydın kaldırılmasını mı istiyorsunuz?</small>
<details class="mis360-report-details"><summary><?php echo yym_icon('shield-check','trust','yym-icon-sm'); ?> Düzeltme / Kaldırma Talebi</summary>
<form class="mis360-report-form" method="post" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
<p>Üyelik veya e-posta göndermeniz gerekmez. Talebinizi ve kayıtla ilişkinizi açıklayın. Kimlik veya vergi belgesi gibi özel bilgileri buraya yazmayın. Talep yönetici tarafından incelenir.</p>
<input type="hidden" name="action" value="mis360_report"><input type="hidden" name="firm" value="<?php echo (int)$firm; ?>"><input type="hidden" name="token" value="<?php echo esc_attr(wp_create_nonce('mis360_report_'.$firm)); ?>">
<label>Talep konusu<select name="report_type" required><option value="">Seçin</option><?php foreach(mis360_report_types() as $key=>$label): ?><option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
<label>Açıklamanız<textarea name="message" required minlength="10" maxlength="2000" rows="4" placeholder="Düzeltilecek bilgiyi veya kaldırma gerekçenizi ve işletmeyle ilişkinizi yazın."></textarea></label>
<label>Adınız <span>(isteğe bağlı)</span><input name="report_name" maxlength="80" autocomplete="name"></label>
<div class="mis360-report-trap" aria-hidden="true"><label>Web sitesi<input name="company_url" tabindex="-1" autocomplete="off"></label></div>
<p><a href="<?php echo esc_url(home_url('/kvkk-aydinlatma-metni/')); ?>">Kişisel verilerinizin kullanımı hakkında bilgi</a></p><button type="submit">Talebi Gönder</button><p class="mis360-report-status" role="status" aria-live="polite"></p>
</form></details></div>
<?php }
add_action('wp_enqueue_scripts',function(){if(!is_singular('firma'))return;wp_enqueue_style('mis360-reports',get_template_directory_uri().'/assets/css/firm-reports.css',array(),filemtime(get_template_directory().'/assets/css/firm-reports.css'));wp_enqueue_script('mis360-reports',get_template_directory_uri().'/assets/js/firm-reports.js',array(),filemtime(get_template_directory().'/assets/js/firm-reports.js'),true);});
function mis360_report_states(){return array('new'=>'Yeni','reviewing'=>'İnceleniyor','done'=>'Tamamlandı','dismissed'=>'İşlem yapılmadı');}
function mis360_report_admin_url($id=0){return add_query_arg(array('post_type'=>'firma','page'=>'mis360-reports','report'=>$id),admin_url('edit.php'));}
add_action('admin_menu',function(){
    if(!current_user_can('manage_options'))return;
    $q=new WP_Query(array('post_type'=>'mis360_report','post_status'=>'private','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_report_state','meta_value'=>'new'));
    $count=(int)$q->found_posts;$badge=$count?' <span class="awaiting-mod"><span class="pending-count">'.$count.'</span></span>':'';
    add_submenu_page('edit.php?post_type=firma','Düzeltme ve Kaldırma Talepleri','Düzeltme ve Kaldırma Talepleri'.$badge,'manage_options','mis360-reports','mis360_report_admin_page');
});
add_action('admin_init',function(){
    if(($_GET['page']??'')!=='mis360-reports'||($_SERVER['REQUEST_METHOD']??'')!=='POST')return;
    if(!current_user_can('manage_options'))wp_die('Bu işlem için yetkiniz yok.','',array('response'=>403));
    $id=absint(mis360_member_input('report_id'));check_admin_referer('mis360_report_review_'.$id);
    $p=get_post($id);$state=mis360_member_input('report_state');
    if(!$p||$p->post_type!=='mis360_report'||!isset(mis360_report_states()[$state]))wp_die('Geçersiz bildirim.');
    update_post_meta($id,'_report_state',$state);update_post_meta($id,'_report_admin_note',wp_slash(mb_substr(sanitize_textarea_field(mis360_member_input('admin_note',true)),0,2000)));update_post_meta($id,'_report_updated_by',get_current_user_id());update_post_meta($id,'_report_updated_at',current_time('mysql'));
    wp_safe_redirect(add_query_arg('saved',1,mis360_report_admin_url($id)));exit;
});
function mis360_report_admin_page(){
    if(!current_user_can('manage_options'))return;
    $id=isset($_GET['report'])&&is_scalar($_GET['report'])?absint($_GET['report']):0;
    echo '<div class="wrap"><h1>Düzeltme ve Kaldırma Talepleri</h1><p>Düzeltme, kaldırma ve kişisel veri talepleri. Talepler firma kaydını otomatik değiştirmez. Kaldırma uygun bulunursa firma düzenleme ekranından yayını taslağa alabilirsiniz. Yalnızca talebi Tamamlandı yapmak firmayı yayından kaldırmaz.</p>';
    if($id){
        $p=get_post($id);if(!$p||$p->post_type!=='mis360_report'){echo '<p>Bildirim bulunamadı.</p></div>';return;}
        if(isset($_GET['saved']))echo '<div class="notice notice-success"><p>Talep durumu kaydedildi.</p></div>';
        $firm=(int)get_post_meta($id,'_report_firm',true);$type=get_post_meta($id,'_report_type',true);$name=get_post_meta($id,'_report_name',true);
        echo '<p><a href="'.esc_url(mis360_report_admin_url()).'">← Tüm talepler</a></p><section style="max-width:850px;background:#fff;border:1px solid #ccd0d4;padding:24px"><h2>'.esc_html($p->post_title).'</h2><p>'.esc_html(mis360_report_types()[$type]??'Diğer'). ' · '.esc_html(get_the_date('d.m.Y H:i',$p)).'</p><p>Gönderen: '.esc_html($name?:'İsimsiz ziyaretçi').'</p><div style="white-space:pre-wrap;background:#f5f7fa;padding:18px">'.esc_html($p->post_content).'</div>';
        if(get_post_type($firm)==='firma')echo '<p><a class="button" href="'.esc_url(get_permalink($firm)).'" target="_blank" rel="noopener">Firmayı görüntüle</a> <a class="button button-primary" href="'.esc_url(get_edit_post_link($firm)).'" target="_blank" rel="noopener">Firma bilgilerini düzenle</a></p>';
        else echo '<p>İlgili firma kaydı artık mevcut değil.</p>';
        echo '<form method="post">';wp_nonce_field('mis360_report_review_'.$id);echo '<input type="hidden" name="report_id" value="'.(int)$id.'"><p><label>Talep durumu <select name="report_state">';foreach(mis360_report_states() as $key=>$label)echo '<option value="'.esc_attr($key).'"'.selected(get_post_meta($id,'_report_state',true),$key,false).'>'.esc_html($label).'</option>';echo '</select></label></p><p><label>Yönetici notu (ziyaretçiye gösterilmez)<br><textarea name="admin_note" maxlength="2000" rows="4" style="width:100%">'.esc_textarea(get_post_meta($id,'_report_admin_note',true)).'</textarea></label></p><button class="button button-primary">Durumu kaydet</button></form></section>';
    }else{
        $state=isset($_GET['state'])&&is_string($_GET['state'])?sanitize_key($_GET['state']):'new';if(!isset(mis360_report_states()[$state])&&$state!=='all')$state='new';
        echo '<p>';foreach(array_merge(array('all'=>'Tümü'),mis360_report_states()) as $key=>$label)echo '<a style="margin-right:16px" href="'.esc_url(add_query_arg('state',$key,mis360_report_admin_url())).'">'.($key===$state?'<strong>'.esc_html($label).'</strong>':esc_html($label)).'</a>';echo '</p>';
        $page=max(1,absint($_GET['paged']??1));$args=array('post_type'=>'mis360_report','post_status'=>'private','posts_per_page'=>20,'paged'=>$page,'orderby'=>'date','order'=>'DESC');if($state!=='all'){$args['meta_key']='_report_state';$args['meta_value']=$state;}$q=new WP_Query($args);
        echo '<table class="widefat striped"><thead><tr><th>Firma</th><th>Konu</th><th>Tarih</th><th>Durum</th><th></th></tr></thead><tbody>';
        foreach($q->posts as $p)echo '<tr><td>'.esc_html($p->post_title).'</td><td>'.esc_html(mis360_report_types()[get_post_meta($p->ID,'_report_type',true)]??'Diğer').'</td><td>'.esc_html(get_the_date('d.m.Y H:i',$p)).'</td><td>'.esc_html(mis360_report_states()[get_post_meta($p->ID,'_report_state',true)]??'Yeni').'</td><td><a class="button" href="'.esc_url(mis360_report_admin_url($p->ID)).'">Talebi incele</a></td></tr>';
        if(!$q->posts)echo '<tr><td colspan="5">Bu durumda bir talep yok.</td></tr>';echo '</tbody></table>';echo paginate_links(array('base'=>add_query_arg(array('paged'=>'%#%','state'=>$state),mis360_report_admin_url()),'current'=>$page,'total'=>$q->max_num_pages));
    }echo '</div>';
}
