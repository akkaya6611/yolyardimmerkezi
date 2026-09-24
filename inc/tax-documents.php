<?php
if (!defined('ABSPATH')) exit;

function mis360_tax_table() { global $wpdb; return $wpdb->prefix . 'mis360_tax_documents'; }
function mis360_tax_install() {
    global $wpdb;
    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    $table = mis360_tax_table();
    dbDelta("CREATE TABLE $table (
        user_id bigint(20) unsigned NOT NULL,
        document longtext NOT NULL,
        uploaded_at datetime NOT NULL,
        PRIMARY KEY  (user_id)
    ) " . $wpdb->get_charset_collate() . ';');
    return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s',$wpdb->esc_like($table))) === $table;
}

function mis360_tax_present($user_id) {
    global $wpdb;
    return (bool)$wpdb->get_var($wpdb->prepare('SELECT user_id FROM '.mis360_tax_table().' WHERE user_id=%d',(int)$user_id));
}

function mis360_tax_uploaded_document() {
    $file = $_FILES['tax_document'] ?? null;
    if (!is_array($file) || !isset($file['error']) || !is_scalar($file['error']) || (int)$file['error'] === UPLOAD_ERR_NO_FILE) return new WP_Error('tax_required','Firma sahibi üyeliği için vergi levhası yüklemek zorunludur.');
    if ((int)$file['error'] !== UPLOAD_ERR_OK) return new WP_Error('tax_upload','Belge yüklenemedi. En fazla 5 MB boyutunda PDF, JPG veya PNG seçin.');
    $path = $file['tmp_name'] ?? '';
    if (!is_string($path) || !is_uploaded_file($path)) return new WP_Error('tax_upload','Geçerli bir belge yükleyin.');
    $size = filesize($path);
    if (!$size || $size > 5*1024*1024) return new WP_Error('tax_size','Vergi levhası en fazla 5 MB olabilir.');
    if (!function_exists('openssl_encrypt') || !function_exists('finfo_open')) return new WP_Error('tax_storage','Belge yükleme şu anda kullanılamıyor. Lütfen daha sonra deneyin.');
    $info = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($info,$path); finfo_close($info);
    $types = array('application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png');
    if (!isset($types[$mime])) return new WP_Error('tax_type','Yalnızca PDF, JPG veya PNG belge yükleyebilirsiniz.');
    $content = file_get_contents($path);
    if ($content === false || strlen($content) !== $size) return new WP_Error('tax_read','Belge okunamadı. Lütfen tekrar seçin.');
    if ($mime === 'application/pdf' && substr($content,0,5) !== '%PDF-') return new WP_Error('tax_type','Geçerli bir PDF belge seçin.');
    if ($mime !== 'application/pdf' && !@getimagesize($path)) return new WP_Error('tax_type','Geçerli bir JPG veya PNG görsel seçin.');
    $iv = random_bytes(12); $tag = '';
    // Documents never enter public uploads or the media library.
    $key = hash_hmac('sha256','mis360-tax-documents-v1',wp_salt('auth'),true);
    $cipher = openssl_encrypt($content,'aes-256-gcm',$key,OPENSSL_RAW_DATA,$iv,$tag,'mis360-tax-v1');
    if ($cipher === false) return new WP_Error('tax_encrypt','Belge güvenli biçimde kaydedilemedi.');
    return array('version'=>1,'mime'=>$mime,'extension'=>$types[$mime],'size'=>$size,'iv'=>base64_encode($iv),'tag'=>base64_encode($tag),'cipher'=>base64_encode($cipher));
}

function mis360_tax_lock($uid) { global $wpdb; return 'm360_tax_'.substr(hash('sha256',DB_NAME.'|'.$wpdb->prefix.'|'.$uid),0,42); }
function mis360_tax_save($user_id,$document) {
    global $wpdb;$json=wp_json_encode($document);if(!$json)return false;$lock=mis360_tax_lock($user_id);
    if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')return false;
    try {return $wpdb->replace(mis360_tax_table(),array('user_id'=>(int)$user_id,'document'=>$json,'uploaded_at'=>current_time('mysql',true)),array('%d','%s','%s'))!==false;}
    finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}
// An approval receipt contains no document bytes or extracted tax/identity fields.
function mis360_tax_approved_receipt($uid) {
    $r=get_user_meta($uid,'mis360_tax_review',true);
    return is_array($r)&&($r['state']??'')==='approved'&&!empty($r['deleted_at'])&&!empty($r['version']) ? $r : false;
}
function mis360_tax_requirement_met($uid) {return mis360_tax_present($uid)||(bool)mis360_tax_approved_receipt($uid);}
function mis360_tax_record_decision($uid,$decision,$note,$version) {
    if(!current_user_can('manage_options'))return new WP_Error('access','Belge inceleme yetkiniz yok.');
    if(!in_array($decision,array('approved','changes'),true))return new WP_Error('decision','Geçersiz karar.');
    global $wpdb;$lock=mis360_tax_lock($uid);
    if((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock))!=='1')return new WP_Error('busy','Belge güncelleniyor; tekrar deneyin.');
    try {
        $doc=$wpdb->get_var($wpdb->prepare('SELECT document FROM '.mis360_tax_table().' WHERE user_id=%d',$uid));
        if(!$doc||!hash_equals(hash('sha256',$doc),$version))return new WP_Error('stale','Belge değişti veya onay sonrası silindi. Sayfayı yenileyin.');
        $old=get_user_meta($uid,'mis360_tax_review',true);
        $review=array('state'=>$decision,'note'=>$decision==='approved'?'Belge onaylandı; dosya sistemden silindi.':sanitize_textarea_field($note),'version'=>$version,'by'=>get_current_user_id(),'at'=>current_time('mysql'));
        if($decision==='approved')$review['deleted_at']=current_time('mysql',true);
        if(!update_user_meta($uid,'mis360_tax_review',$review)&&get_user_meta($uid,'mis360_tax_review',true)!==$review)return new WP_Error('save','Karar kaydedilemedi; belge silinmedi.');
        if($decision==='approved'){
            $deleted=$wpdb->query($wpdb->prepare('DELETE FROM '.mis360_tax_table().' WHERE user_id=%d AND BINARY document=BINARY %s',$uid,$doc));
            if($deleted!==1){if($old)update_user_meta($uid,'mis360_tax_review',$old);else delete_user_meta($uid,'mis360_tax_review');return new WP_Error('delete','Belge silinemedi; onay tamamlanmadı. Tekrar deneyin.');}
        }
        return true;
    }finally{$wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));}
}

function mis360_tax_download() {
    if (!current_user_can('manage_options')) wp_die('Bu belgeye erişim yetkiniz yok.','',array('response'=>403));
    $id = isset($_GET['user_id']) && is_scalar($_GET['user_id']) ? absint($_GET['user_id']) : 0;
    check_admin_referer('mis360_tax_download_'.$id);
    global $wpdb;
    $row = $wpdb->get_var($wpdb->prepare('SELECT document FROM '.mis360_tax_table().' WHERE user_id=%d',$id));
    $doc = json_decode((string)$row,true);
    if (!is_array($doc) || ($doc['version'] ?? 0) !== 1) wp_die('Belge bulunamadı.','',array('response'=>404));
    $key = hash_hmac('sha256','mis360-tax-documents-v1',wp_salt('auth'),true);
    $content = openssl_decrypt(base64_decode($doc['cipher'],true),'aes-256-gcm',$key,OPENSSL_RAW_DATA,base64_decode($doc['iv'],true),base64_decode($doc['tag'],true),'mis360-tax-v1');
    if ($content === false) wp_die('Belge açılamadı. Site şifreleme anahtarlarını kontrol edin.','',array('response'=>500));
    $extension = in_array($doc['extension'],array('pdf','jpg','png'),true) ? $doc['extension'] : 'bin';
    nocache_headers();
    header('Content-Type: application/octet-stream');
    header('X-Content-Type-Options: nosniff');
    header('Content-Disposition: attachment; filename="vergi-levhasi-'.$id.'.'.$extension.'"');
    header('Content-Length: '.strlen($content));
    echo $content; exit;
}
add_action('wp_ajax_mis360_tax_download','mis360_tax_download');

function mis360_tax_admin_link($user_id) {
    if (!current_user_can('manage_options')) return '';
    if (!mis360_tax_present($user_id)) return mis360_tax_approved_receipt($user_id) ? 'Onaylandı · Dosya silindi' : 'Yüklenmemiş';
    $url = wp_nonce_url(add_query_arg(array('action'=>'mis360_tax_download','user_id'=>(int)$user_id),admin_url('admin-ajax.php')),'mis360_tax_download_'.(int)$user_id);
    return '<a href="'.esc_url($url).'">Vergi levhasını indir</a>';
}
add_filter('manage_users_columns',function($columns){if(current_user_can('manage_options'))$columns['mis360_tax']='Vergi levhası';return $columns;});
add_filter('manage_users_custom_column',function($value,$column,$user_id){return $column==='mis360_tax'?mis360_tax_admin_link($user_id):$value;},10,3);
function mis360_tax_profile_section($user) {
    if(!current_user_can('manage_options'))return;
    echo '<h2>Firma vergi levhası</h2><p>'.mis360_tax_admin_link($user->ID).'</p><p>Belgenin yüklenmiş olması doğrulandığı anlamına gelmez.</p>';
}
add_action('show_user_profile','mis360_tax_profile_section');
add_action('edit_user_profile','mis360_tax_profile_section');
add_action('deleted_user',function($user_id){global $wpdb;$wpdb->delete(mis360_tax_table(),array('user_id'=>(int)$user_id),array('%d'));});

function mis360_tax_field($required = true) {
    ?><div class="mis360-tax-upload" data-tax-field <?php if(!$required) echo 'hidden'; ?>>
    <label for="member-tax-document">Vergi levhası <span>(zorunlu)</span></label>
    <input id="member-tax-document" type="file" name="tax_document" accept=".pdf,.jpg,.jpeg,.png" aria-describedby="member-tax-help" <?php echo $required ? 'required' : 'disabled'; ?>>
    <small id="member-tax-help">PDF, JPG veya PNG · En fazla 5 MB. Belgeniz ilanınızda yayınlanmaz. Yönetici incelemesinden sonra onaylanırsa dosya silinir; yalnızca onay kaydı kalır.</small><small><a target="_blank" rel="noopener" href="<?php echo esc_url(home_url('/vergi-levhasi-bilgilendirmesi/')); ?>">Vergi levhasının kullanımı ve saklanması</a> · <a target="_blank" rel="noopener" href="<?php echo esc_url(home_url('/kvkk-aydinlatma-metni/')); ?>">KVKK Aydınlatma Metni</a></small>
    </div><?php
}

add_action('wp_enqueue_scripts',function(){
    if(is_page('uyelik'))wp_enqueue_script('mis360-tax-upload',get_template_directory_uri().'/assets/js/tax-upload.js',array(),filemtime(get_template_directory().'/assets/js/tax-upload.js'),true);
},35);
