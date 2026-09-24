<?php
if(!defined('ABSPATH'))exit;
function mis360_metrics_install(){global $wpdb;require_once ABSPATH.'wp-admin/includes/upgrade.php';$table=$wpdb->prefix.'mis360_metrics';dbDelta("CREATE TABLE $table (
 firm_id bigint(20) unsigned NOT NULL,
 day date NOT NULL,
 event varchar(20) NOT NULL,
 hits bigint(20) unsigned NOT NULL DEFAULT 0,
 PRIMARY KEY  (firm_id,day,event)
 ) ".$wpdb->get_charset_collate().';');if($wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($table)))!==$table)return false;add_option('mis360_metrics_since',current_time('mysql'));return true;}
function mis360_metrics_attributes($id){return 'data-metric-firm="'.esc_attr($id).'" data-metric-token="'.esc_attr(wp_create_nonce('mis360_metric_'.$id)).'"';}
function mis360_metric_event(){
 $id=isset($_POST['firm'])&&is_scalar($_POST['firm'])?absint($_POST['firm']):0;
 $event=isset($_POST['event'])&&is_string($_POST['event'])?sanitize_key($_POST['event']):'';
 $nonce=isset($_POST['token'])&&is_string($_POST['token'])?$_POST['token']:'';
 if(!in_array($event,array('view','phone','whatsapp','directions'),true)||!wp_verify_nonce($nonce,'mis360_metric_'.$id))wp_send_json_error(null,403);
 $post=get_post($id);if(!$post||$post->post_type!=='firma'||$post->post_status!=='publish')wp_send_json_error(null,404);
 if(current_user_can('manage_options')||(is_user_logged_in()&&(int)$post->post_author===get_current_user_id()))wp_send_json_success(array('counted'=>false));
 $fingerprint=hash_hmac('sha256',($_SERVER['REMOTE_ADDR']??'').'|'.substr($_SERVER['HTTP_USER_AGENT']??'',0,256),wp_salt('nonce'));
 $key='m360_hit_'.hash('sha256',$fingerprint.'|'.$id.'|'.$event);
 global $wpdb;$lock='m360_'.substr(hash('sha256',$key),0,45);
 if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')wp_send_json_success(array('counted'=>false));
 $counted=false;
 try{if(!get_transient($key)){$result=$wpdb->query($wpdb->prepare('INSERT INTO '.$wpdb->prefix.'mis360_metrics (firm_id,day,event,hits) VALUES (%d,%s,%s,1) ON DUPLICATE KEY UPDATE hits=hits+1',$id,current_time('Y-m-d'),$event));if($result!==false){set_transient($key,1,30*MINUTE_IN_SECONDS);$counted=true;}}}
 finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
 wp_send_json_success(array('counted'=>$counted));
}
add_action('wp_ajax_mis360_metric','mis360_metric_event');add_action('wp_ajax_nopriv_mis360_metric','mis360_metric_event');
add_action('wp_enqueue_scripts',function(){
 wp_enqueue_script('mis360-metrics',get_template_directory_uri().'/assets/js/firm-metrics.js',array(),filemtime(get_template_directory().'/assets/js/firm-metrics.js'),true);
 wp_localize_script('mis360-metrics','mis360Metrics',array('url'=>admin_url('admin-ajax.php')));
 if(is_page('uyelik'))wp_enqueue_style('mis360-account-center',get_template_directory_uri().'/assets/css/account-center.css',array(),filemtime(get_template_directory().'/assets/css/account-center.css'));
},40);
function mis360_payment_states(){return array('none'=>'Ödeme kaydı yok','pending'=>'Ödeme bekleniyor','paid'=>'Ödendi','failed'=>'Ödeme başarısız','refunded'=>'İade edildi');}
function mis360_firm_payment($id){$status=get_post_meta($id,'_firma_payment_status',true);$states=mis360_payment_states();if(isset($states[$status])&&$status!=='none')return $states[$status];return get_post_meta($id,'_firma_requested_plan',true)==='free'?'Ücret yok · Lansman dönemi':'Ödeme kaydı yok';}
add_action('add_meta_boxes',function(){if(!current_user_can('manage_options'))return;add_meta_box('mis360-payment','Ödeme kaydı',function($post){wp_nonce_field('mis360_payment_'.$post->ID,'mis360_payment_nonce');echo '<p>Yalnızca doğruladığınız ödemelerin durumunu kaydedin. Bu işlem ödeme almaz veya paketi etkinleştirmez.</p><select name="mis360_payment_status">';foreach(mis360_payment_states() as $key=>$label)echo '<option value="'.esc_attr($key).'"'.selected(get_post_meta($post->ID,'_firma_payment_status',true),$key,false).'>'.esc_html($label).'</option>';echo '</select>';},'firma','side');});
add_action('save_post_firma',function($id){if(!current_user_can('manage_options')||!current_user_can('edit_post',$id)||wp_is_post_revision($id)||wp_is_post_autosave($id))return;$nonce=$_POST['mis360_payment_nonce']??'';$status=$_POST['mis360_payment_status']??'';if(!is_string($nonce)||!is_string($status)||!wp_verify_nonce($nonce,'mis360_payment_'.$id)||!isset(mis360_payment_states()[$status]))return;if(get_post_meta($id,'_firma_payment_status',true)!==$status){update_post_meta($id,'_firma_payment_status',$status);update_post_meta($id,'_firma_payment_updated',current_time('mysql'));update_post_meta($id,'_firma_payment_updated_by',get_current_user_id());}});
function mis360_account_metrics(){global $wpdb;$uid=get_current_user_id();if(!$uid)return array();return $wpdb->get_results($wpdb->prepare("SELECT m.firm_id,m.event,SUM(m.hits) hits FROM {$wpdb->prefix}mis360_metrics m INNER JOIN {$wpdb->posts} p ON p.ID=m.firm_id WHERE p.post_author=%d AND p.post_type='firma' AND p.post_status IN ('publish','pending','draft','private','future') AND m.day >= %s GROUP BY m.firm_id,m.event",$uid,wp_date('Y-m-d',strtotime('-29 days'))),ARRAY_A);}

function mis360_account_routes() {
    add_rewrite_rule('^uyelik/(ozet|firmalar|yorumlar|odemeler|profil|belgeler|destek|bildirimler|sahiplenme)/?$', 'index.php?pagename=uyelik&account_section=$matches[1]', 'top');
}
add_action('init','mis360_account_routes');
add_filter('query_vars',function($vars){$vars[]='account_section';return $vars;});
function mis360_account_section() {
    if (isset($_GET['tax_required']) || isset($_GET['tax_saved']) || mis360_member_input('member_action') === 'tax') return 'belgeler';
    $section = get_query_var('account_section','ozet');
    return in_array($section,array('ozet','firmalar','yorumlar','odemeler','profil','belgeler','destek','bildirimler','sahiplenme'),true) ? $section : 'ozet';
}
function mis360_account_url($section='ozet') { return home_url('/uyelik/'.$section.'/'); }
add_action('admin_init',function(){
    if (current_user_can('manage_options') && get_option('mis360_account_routes_version') !== '3') {
        mis360_account_routes();flush_rewrite_rules(false);update_option('mis360_account_routes_version','3',false);
    }
});
