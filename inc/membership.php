<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/tax-documents.php';
require_once __DIR__ . '/email-verification.php';

function mis360_member_input($key, $password = false) {
    $value = isset($_POST[$key]) && is_string($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
    return $password ? $value : sanitize_text_field($value);
}

function mis360_member_url($view = 'login', $next = '') {
    $args = array();
    if ($view !== 'login') $args['view'] = $view;
    if ($next === 'firm') $args['next'] = 'firm';
    if(function_exists('mis360_claim_intent_id')&&($claim=mis360_claim_intent_id()))$args['claim_firm']=$claim;
    return add_query_arg($args, home_url('/uyelik/'));
}

function mis360_member_state($key = null) {
    $state = $GLOBALS['mis360_member_state'] ?? array('error'=>'', 'message'=>'', 'view'=>'login');
    return $key === null ? $state : ($state[$key] ?? '');
}

function mis360_member_rate_limit($action) {
    $limits = array('login'=>array(10,15*MINUTE_IN_SECONDS), 'register'=>array(5,HOUR_IN_SECONDS), 'forgot'=>array(3,HOUR_IN_SECONDS), 'tax'=>array(5,HOUR_IN_SECONDS));
    list($limit,$period) = $limits[$action];
    $key = 'mis360_member_' . $action . '_' . substr(hash_hmac('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown', wp_salt('auth')),0,32);
    $entry = get_transient($key);
    if (!is_array($entry) || $entry['until'] <= time()) $entry = array('count'=>0,'until'=>time()+$period);
    if ($entry['count'] >= $limit) return false;
    $entry['count']++;
    set_transient($key,$entry,max(1,$entry['until']-time()));
    return true;
}

function mis360_member_register($data) {
    if (!get_option('users_can_register')) return new WP_Error('closed','Yeni üyelik şu anda kapalı.');
    $name = sanitize_text_field($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $kind = $data['kind'] ?? '';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 80) return new WP_Error('name','Adınızı ve soyadınızı girin (2–80 karakter).');
    if (!is_email($email) || strlen($email) > 100) return new WP_Error('email','Geçerli bir e-posta adresi girin.');
    if (!in_array($kind,array('firm','customer'),true)) return new WP_Error('kind','Üyelik türünüzü seçin.');
    if (strlen($password) < 12 || strlen($password) > 128 || strlen(trim($password)) === 0) return new WP_Error('password','Şifreniz 12–128 karakter arasında olmalı.');
    if ($password !== ($data['confirm'] ?? '')) return new WP_Error('confirm','Şifreler birbiriyle eşleşmiyor.');
    if (!get_role('subscriber')) return new WP_Error('role','Üyelik geçici olarak kullanılamıyor.');
    global $wpdb;
    $lock = 'mis360-register-' . substr(hash('sha256',DB_NAME.'|'.$wpdb->prefix.'|'.strtolower($email)),0,40);
    if ((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)',$lock)) !== '1') return new WP_Error('busy','Bu başvuru işleniyor. Biraz sonra tekrar deneyin.');
    try {
        if (email_exists($email)) return new WP_Error('exists','Bu e-posta ile kayıt oluşturulamadı. Hesabınız varsa giriş yapın veya şifrenizi yenileyin.');
        $tax = $kind === 'firm' ? mis360_tax_uploaded_document() : null;
        if (is_wp_error($tax)) return $tax;
        do { $login = 'uye_' . strtolower(wp_generate_password(16,false,false)); } while (username_exists($login));
        $userdata = wp_slash(array('user_login'=>$login,'user_email'=>$email,'display_name'=>$name,'first_name'=>$name,'role'=>'subscriber','meta_input'=>array('mis360_member_kind'=>$kind)));
        // WordPress hashes new passwords before unslashing other user fields.
        $userdata['user_pass'] = $password;
        $id = wp_insert_user($userdata);
        if (is_wp_error($id)) return $id;
        if (!$id) return new WP_Error('account','Hesap oluşturulamadı.');
        if ($tax && !mis360_tax_save($id,$tax)) {
            require_once ABSPATH . 'wp-admin/includes/user.php';
            wp_delete_user($id);
            return new WP_Error('tax_save','Belge kaydedilemediği için kayıt tamamlanmadı. Lütfen tekrar deneyin.');
        }
        mis360_email_send($id);
        return $id;
    } finally {
        $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock));
    }
}

function mis360_member_firms($user_id, $page = 1) {
    return new WP_Query(array('post_type'=>'firma','post_status'=>array('publish','pending','draft','private'), 'author'=>(int)$user_id,'posts_per_page'=>10,'paged'=>max(1,(int)$page),'orderby'=>'date','order'=>'DESC'));
}

function mis360_member_request() {
    if (is_page('firma-ekle') && !is_user_logged_in()) {
        wp_safe_redirect(mis360_member_url('register','firm')); exit;
    }
    if (is_page('firma-ekle') && !current_user_can('manage_options') && !mis360_tax_requirement_met(get_current_user_id())) {
        wp_safe_redirect(add_query_arg('tax_required','1',home_url('/uyelik/'))); exit;
    }
    if (!is_page('uyelik')) return;
    if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
    nocache_headers();
    $view = isset($_GET['view']) && is_string($_GET['view']) ? sanitize_key($_GET['view']) : 'login';
    if (!in_array($view,array('login','register','forgot'),true)) $view = 'login';
    $GLOBALS['mis360_member_state'] = array('view'=>$view,'error'=>'','message'=>'');
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && is_user_logged_in() && mis360_member_input('member_action') === 'tax') {
        if (!wp_verify_nonce(mis360_member_input('_member_nonce'),'mis360_member_tax')) {
            $GLOBALS['mis360_member_state']['error'] = 'Formun süresi dolmuş. Sayfayı yenileyin.'; return;
        }
        if (!mis360_member_rate_limit('tax')) { $GLOBALS['mis360_member_state']['error'] = 'Çok fazla belge yükleme denemesi yapıldı. Bir süre sonra tekrar deneyin.'; return; }
        $doc = mis360_tax_uploaded_document();
        if (is_wp_error($doc)) { $GLOBALS['mis360_member_state']['error'] = $doc->get_error_message(); return; }
        if (!mis360_tax_save(get_current_user_id(),$doc)) { $GLOBALS['mis360_member_state']['error'] = 'Belge kaydedilemedi. Tekrar deneyin.'; return; }
        update_user_meta(get_current_user_id(),'mis360_member_kind','firm');
        wp_safe_redirect(add_query_arg('tax_saved','1',home_url('/uyelik/'))); exit;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || is_user_logged_in()) return;
    $action = mis360_member_input('member_action');
    if (!in_array($action,array('login','register','forgot'),true)) return;
    $GLOBALS['mis360_member_state']['view'] = $action;
    if (!wp_verify_nonce(mis360_member_input('_member_nonce'),'mis360_member_'.$action)) {
        $GLOBALS['mis360_member_state']['error'] = 'Formun süresi dolmuş. Sayfayı yenileyip tekrar deneyin.'; return;
    }
    if (mis360_member_input('company_website') !== '' || !mis360_member_rate_limit($action)) {
        $GLOBALS['mis360_member_state']['error'] = 'Çok fazla deneme yapıldı. Lütfen bir süre sonra tekrar deneyin.'; return;
    }
    $result = null;
    if ($action === 'login') {
        $result = wp_signon(array('user_login'=>mis360_member_input('email'),'user_password'=>mis360_member_input('password',true),'remember'=>mis360_member_input('remember') === '1'),is_ssl());
        if (is_wp_error($result)) {
            $GLOBALS['mis360_member_state']['error'] = 'E-posta veya şifre hatalı. Bilgilerinizi kontrol edin.'; return;
        }
        wp_set_current_user($result->ID);
    } elseif ($action === 'register') {
        $result = mis360_member_register(array('name'=>mis360_member_input('member_name'),'email'=>mis360_member_input('email'),'kind'=>mis360_member_input('kind'),'password'=>mis360_member_input('password',true),'confirm'=>mis360_member_input('confirm',true)));
        if (is_wp_error($result)) {
            $GLOBALS['mis360_member_state']['error'] = $result->get_error_message(); return;
        }
        wp_set_current_user($result);
        wp_set_auth_cookie($result,false,is_ssl());
        $user = get_user_by('id',$result);
        do_action('wp_login',$user->user_login,$user);
    } else {
        $email = mis360_member_input('email');
        if (!is_email($email)) { $GLOBALS['mis360_member_state']['error'] = 'Geçerli bir e-posta adresi girin.'; return; }
        $result = retrieve_password($email);
        if (is_wp_error($result) && $result->get_error_code() === 'retrieve_password_email_failure') {
            $GLOBALS['mis360_member_state']['error'] = 'E-posta gönderilemedi. Lütfen daha sonra tekrar deneyin veya bizimle iletişime geçin.'; return;
        }
        $GLOBALS['mis360_member_state']['message'] = 'Bu e-posta adresiyle bir hesap varsa şifre yenileme bağlantısı gönderildi.'; return;
    }
    $destination = mis360_member_input('next') === 'firm' ? home_url('/firma-ekle/') : home_url('/uyelik/');
    if(function_exists('mis360_claim_intent_id')&&($claim=mis360_claim_intent_id())&&mis360_claim_eligible($claim)){update_user_meta(get_current_user_id(),'mis360_claim_intent',$claim);$destination=add_query_arg('claim_firm',$claim,mis360_account_url('sahiplenme'));}
    wp_safe_redirect($destination); exit;
}
add_action('template_redirect','mis360_member_request',5);

add_action('wp_enqueue_scripts',function(){
    wp_enqueue_style('mis360-membership',get_template_directory_uri().'/assets/css/membership.css',array(),filemtime(get_template_directory().'/assets/css/membership.css'));
},35);

add_filter('show_admin_bar',function($show){
    return get_user_meta(get_current_user_id(),'mis360_member_kind',true) && !current_user_can('edit_posts') ? false : $show;
});
add_action('admin_init',function(){
    if (!wp_doing_ajax() && get_user_meta(get_current_user_id(),'mis360_member_kind',true) && !current_user_can('edit_posts')) {
        wp_safe_redirect(home_url('/uyelik/')); exit;
    }
});
