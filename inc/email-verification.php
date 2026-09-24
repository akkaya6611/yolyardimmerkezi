<?php
if (!defined('ABSPATH')) exit;

function mis360_email_verified($id) {
    $user = get_userdata($id);
    if (!$user) return false;
    if (user_can($user, 'manage_options')) return true;
    return strtolower($user->user_email) === (string)get_user_meta($id, 'mis360_verified_email', true);
}

function mis360_email_send($id) {
    $user = get_userdata($id);
    if (!$user || mis360_email_verified($id)) return new WP_Error('verified','Bu adres zaten doğrulanmış.');
    global $wpdb;
    $lock = 'mis360-email-' . md5(DB_NAME . $wpdb->prefix . $id);
    if ((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)', $lock)) !== '1') return new WP_Error('busy','Lütfen biraz sonra tekrar deneyin.');
    try {
        $last = (int)get_user_meta($id,'mis360_email_sent_at',true);
        if ($last > time()-120) return new WP_Error('wait','Yeniden göndermek için 2 dakika bekleyin.');
        $window = get_user_meta($id,'mis360_email_window',true);
        if (!is_array($window) || $window['until'] < time()) $window = array('until'=>time()+HOUR_IN_SECONDS,'count'=>0);
        if ($window['count'] >= 5) return new WP_Error('limit','Saatlik gönderim sınırına ulaşıldı. Daha sonra tekrar deneyin.');
        $window['count']++;
        update_user_meta($id,'mis360_email_window',$window);
        update_user_meta($id,'mis360_email_sent_at',time());
        $token = (string)random_int(100000,999999);
        $record = array('hash'=>hash_hmac('sha256',$token,wp_salt('auth')),'email'=>strtolower($user->user_email),'expires'=>time()+10*MINUTE_IN_SECONDS,'attempts'=>0);
        $previous = get_user_meta($id,'mis360_email_token',true);
        update_user_meta($id,'mis360_email_token',$record);
        $plain = "Merhaba,\n\nDoğrulama kodunuz: ".$token."\n\nKodu üyelik ekranına girin. Kod 10 dakika geçerlidir. Kodu kimseyle paylaşmayın. Yeni kod istediğinizde önceki kod geçersiz olur. Bu işlemi siz başlatmadıysanız mesajı yok sayabilirsiniz.";
        $alternative = static function($mailer) use ($plain) { $mailer->AltBody = $plain; };
        add_action('phpmailer_init',$alternative,1000);
        try {
            $sent = wp_mail($user->user_email,'Yol Yardım Merkezi — Doğrulama kodunuz',mis360_email_code_html($token,$user->display_name),array('Content-Type: text/html; charset=UTF-8'));
        } finally { remove_action('phpmailer_init',$alternative,1000); }

        if (!$sent) {
            if ($previous) update_user_meta($id,'mis360_email_token',$previous); else delete_user_meta($id,'mis360_email_token');
            return new WP_Error('mail','E-posta gönderilemedi. Biraz sonra tekrar deneyin veya destek ile iletişime geçin.');
        }
        return true;
    } finally { $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock)); }
}

function mis360_email_confirm($id, $token) {
    global $wpdb;
    $lock = 'mis360-email-' . md5(DB_NAME . $wpdb->prefix . $id);
    if ((string)$wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s,0)',$lock)) !== '1') return false;
    try {
        $user = get_userdata($id); $record = get_user_meta($id,'mis360_email_token',true);
        if (!$user || !is_array($record) || $record['expires'] < time() || $record['email'] !== strtolower($user->user_email) || ($record['attempts'] ?? 0) >= 5) return false;
        $record['attempts'] = ($record['attempts'] ?? 0) + 1;
        update_user_meta($id,'mis360_email_token',$record);
        if (!preg_match('/^[0-9]{6}$/D',$token) || !hash_equals($record['hash'],hash_hmac('sha256',$token,wp_salt('auth')))) return false;
        update_user_meta($id,'mis360_verified_email',strtolower($user->user_email));
        update_user_meta($id,'mis360_email_verified_at',time());
        delete_user_meta($id,'mis360_email_token');
        return true;
    } finally { $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)',$lock)); }
}



add_action('template_redirect',function(){
    if (!is_user_logged_in() || mis360_email_verified(get_current_user_id()) || !(is_page('uyelik') || is_page('firma-ekle'))) return;
    if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE',true);
    nocache_headers(); header('X-Robots-Tag: noindex, nofollow');
    $id = get_current_user_id(); $message = ''; $error = '';
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['email_action'])) {
        if (!isset($_POST['_email_nonce']) || !is_string($_POST['_email_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_email_nonce'])),'mis360_email_code')) $error = 'Formun süresi doldu. Sayfayı yenileyin.';
        elseif ($_POST['email_action'] === 'confirm') {
            $code = isset($_POST['email_code']) && is_string($_POST['email_code']) ? trim(wp_unslash($_POST['email_code'])) : '';
            if (mis360_email_confirm($id,$code)) { wp_safe_redirect(add_query_arg('email_verified','1',home_url('/uyelik/'))); exit; }
            $error = 'Kod hatalı, süresi dolmuş veya 5 deneme sınırına ulaşılmış. Kodu kontrol edin ya da yeni kod isteyin.';
        } elseif ($_POST['email_action'] === 'resend') {
            $result = mis360_email_send($id); if (is_wp_error($result)) $error = $result->get_error_message(); else $message = 'Yeni doğrulama kodu gönderildi.';
        }
    } elseif (!get_user_meta($id,'mis360_email_sent_at',true)) {
        $result = mis360_email_send($id); if (is_wp_error($result)) $error = $result->get_error_message(); else $message = 'Doğrulama kodu gönderildi.';
    }
    get_header(); ?>
    <main class="mis360-member-page"><section style="max-width:620px;margin:60px auto;padding:32px;background:#fff;border:1px solid #dbe4ef;border-radius:24px;color:#10253e">
        <h1 style="font-size:28px">E-posta adresinizi doğrulayın</h1>
        <?php if ($message) : ?><p role="status"><?php echo esc_html($message); ?></p><?php endif; ?>
        <?php if ($error) : ?><p role="alert" style="color:#a52222"><?php echo esc_html($error); ?></p><?php endif; ?>
        <p><strong><?php echo esc_html(wp_get_current_user()->user_email); ?></strong> adresine gönderilen 6 haneli kodu girin.</p>
        <p>Hesap panelini kullanmak, firma başvurusu yapmak ve yorum göndermek için doğrulama zorunludur. Kod 10 dakika geçerlidir. Spam klasörünü de kontrol edin.</p>
        <form method="post" action="<?php echo esc_url(home_url('/uyelik/')); ?>">
            <?php wp_nonce_field('mis360_email_code','_email_nonce',false); ?>
            <label for="email-code" style="display:block;margin:20px 0 8px">Doğrulama kodu</label>
            <input id="email-code" name="email_code" type="text" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9]{6}" minlength="6" maxlength="6" required placeholder="6 haneli kod" style="width:100%;box-sizing:border-box;padding:16px;border:1px solid #bccbdd;border-radius:12px;font-size:24px;letter-spacing:5px">
            <button name="email_action" value="confirm" style="width:100%;margin-top:16px;padding:14px;background:#ff8a00;border:0;border-radius:10px;font-weight:700;cursor:pointer">E-posta Adresimi Onayla</button>
        </form>
        <form method="post" action="<?php echo esc_url(home_url('/uyelik/')); ?>" style="margin-top:18px">
            <?php wp_nonce_field('mis360_email_code','_email_nonce',false); ?>
            <button name="email_action" value="resend" style="padding:10px;background:#eef3f9;border:0;border-radius:8px;cursor:pointer">Yeni Kod Gönder</button>
            <small style="display:block;margin-top:8px">Yeniden gönderim aralığı: 2 dakika.</small>
        </form>
        <p><a href="<?php echo esc_url(wp_logout_url(home_url('/uyelik/'))); ?>">Çıkış yap / Farklı hesapla giriş yap</a></p>
    </section></main>
    <?php get_footer(); exit;
},1);

add_filter('preprocess_comment',function($data){
    if (is_user_logged_in() && !mis360_email_verified(get_current_user_id())) wp_die('Yorum göndermeden önce üyelik sayfasından e-posta adresinizi doğrulayın.','E-posta doğrulaması gerekli',array('response'=>403,'back_link'=>true));
    return $data;
});
add_filter('rest_pre_insert_comment',function($comment,$request){
    if (is_user_logged_in() && !mis360_email_verified(get_current_user_id())) return new WP_Error('email_unverified','Önce e-posta adresinizi doğrulayın.',array('status'=>403));
    return $comment;
},10,2);

/** Table layout and inline styles keep the verification email readable in mail clients. */
function mis360_email_code_html($code, $name = '') {
    $greeting = $name !== '' ? 'Merhaba ' . $name . ',' : 'Merhaba,';
    ob_start(); ?>
<!doctype html>
<html lang="tr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>E-posta doğrulama</title></head>
<body style="margin:0;padding:0;background:#f1f5f9;color:#10253e;font-family:Arial,Helvetica,sans-serif">
<div style="display:none;max-height:0;overflow:hidden;opacity:0">Üyeliğinizi tamamlamak için doğrulama kodunuzu girin. Kod 10 dakika geçerlidir.</div>
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f1f5f9"><tr><td align="center" style="padding:28px 12px">
<table role="presentation" width="560" cellspacing="0" cellpadding="0" style="width:100%;max-width:560px;background:#fff;border:1px solid #e1e8f0;border-radius:20px;overflow:hidden">
<tr><td style="padding:28px 28px 24px;background:#0b1f3a;border-bottom:4px solid #ff8a00">
<div style="font-size:22px;font-weight:bold;letter-spacing:1px;color:#fff">YOL <span style="color:#ff8a00">YARDIM</span></div>
<div style="margin-top:5px;font-size:12px;letter-spacing:5px;color:#c9d6e6">MERKEZİ</div>
</td></tr>
<tr><td style="padding:30px 28px 12px">
<div style="font-size:11px;letter-spacing:2px;font-weight:bold;color:#b55d00">ÜYELİK DOĞRULAMA</div>
<h1 style="margin:12px 0 20px;font-size:26px;line-height:1.3;color:#0b1f3a">Son bir adım kaldı.</h1>
<p style="font-size:15px;line-height:1.7;margin:0 0 10px"><?php echo esc_html($greeting); ?></p>
<p style="font-size:15px;line-height:1.7;margin:0;color:#526477">E-posta adresinizi onaylamak için aşağıdaki kodu üyelik ekranına girin.</p>
</td></tr>
<tr><td style="padding:12px 28px 24px">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#fff7e9;border:1px solid #ffd599;border-radius:14px"><tr><td align="center" style="padding:22px 8px">
<div style="font-size:11px;letter-spacing:2px;color:#8c510b;font-weight:bold">DOĞRULAMA KODUNUZ</div>
<div dir="ltr" style="margin:14px 0;font-family:Consolas,Monaco,monospace;font-size:36px;line-height:1.3;font-weight:bold;letter-spacing:6px;color:#0b1f3a;white-space:nowrap"><?php echo esc_html($code); ?></div>
<div style="font-size:13px;color:#8c510b">10 dakika geçerlidir</div>
</td></tr></table>
</td></tr>
<tr><td style="padding:0 28px 28px">
<p style="margin:0 0 8px;font-size:14px;font-weight:bold;color:#10253e">Kodunuz size özeldir.</p>
<p style="margin:0;font-size:13px;line-height:1.7;color:#64748b">Kodu kimseyle paylaşmayın. Yeni kod istediğinizde önceki kod geçersiz olur.</p>
</td></tr>
<tr><td style="padding:20px 28px;background:#f8fafc;border-top:1px solid #e5edf4">
<p style="margin:0;font-size:12px;line-height:1.7;color:#718096">Bu işlemi siz başlatmadıysanız herhangi bir işlem yapmanıza gerek yoktur. Bu e-postayı yok sayabilirsiniz.</p>
</td></tr>
</table>
<p style="margin:20px 0 0;font-size:12px;line-height:1.7;color:#7a899b">Yol Yardım Merkezi<br>Otomatik üyelik bilgilendirmesi</p>
</td></tr></table>
</body></html>
<?php return ob_get_clean();
}
