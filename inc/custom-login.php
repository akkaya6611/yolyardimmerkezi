<?php
/**
 * Özel Yönetim Paneli Giriş Tasarımı (Custom WordPress Login Screen)
 * 
 * Yol Yardım Merkezi kurumsal kimliğine uygun (#0B1728 Koyu Lacivert ve #FF8A00 Turuncu)
 * ultra şık, modern ve minimalist bir wp-login.php giriş ekranı sunar.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Giriş Sayfası Başlığı ve Linkini Özelleştirme
 */
add_filter('login_headerurl', function () {
    return home_url('/');
});

add_filter('login_headertext', function () {
    return 'Yol Yardım Merkezi - Yönetim Paneli';
});

/**
 * 2. Özel CSS Stilleri
 */
add_action('login_enqueue_scripts', function () {
    $logo_url = get_template_directory_uri() . '/assets/images/logo-light.png';
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style type="text/css">
        /* Genel Arka Plan */
        body.login {
            background: #0B1728 !important;
            background: radial-gradient(circle at 50% 15%, #182C48 0%, #08101E 100%) !important;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            color: #F8FAFC !important;
            position: relative;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 40px 16px !important;
            box-sizing: border-box !important;
        }

        /* Dil seçici kutusunu gizle */
        .language-switcher {
            display: none !important;
        }

        /* Tarayıcı sarı otomatik doldurma rengini düzelt */
        body.login input:-webkit-autofill,
        body.login input:-webkit-autofill:hover, 
        body.login input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #F8FAFC inset !important;
            -webkit-text-fill-color: #0F172A !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Arka plan dekoratif ışık efekti */
        body.login::before {
            content: '';
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 138, 0, 0.08) 0%, rgba(255, 138, 0, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }

        #login {
            position: relative;
            z-index: 1;
            width: 100% !important;
            max-width: 430px !important;
            padding: 30px 20px !important;
            margin: auto !important;
        }

        /* Logo Alanı */
        #login h1 {
            margin-bottom: 24px !important;
        }

        #login h1 a {
            background-image: url('<?php echo esc_url($logo_url); ?>') !important;
            background-size: contain !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            width: 100% !important;
            height: 64px !important;
            margin: 0 auto !important;
            transition: transform 0.3s ease;
        }

        #login h1 a:hover {
            transform: scale(1.03);
        }

        /* Giriş Kartı Formu */
        body.login form {
            background: rgba(255, 255, 255, 0.98) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 20px !important;
            padding: 34px 30px !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.05) !important;
            backdrop-filter: blur(12px) !important;
        }

        /* Input Alanları ve Etiketler */
        body.login label {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
            display: block;
        }

        body.login .input {
            background: #F8FAFC !important;
            border: 1.5px solid #E2E8F0 !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 15px !important;
            color: #0F172A !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.2s ease !important;
            margin-top: 4px !important;
            margin-bottom: 18px !important;
        }

        body.login .input:focus {
            background: #FFFFFF !important;
            border-color: #FF8A00 !important;
            box-shadow: 0 0 0 3px rgba(255, 138, 0, 0.2) !important;
            outline: none !important;
        }

        /* Beni Hatırla */
        .forgetmenot {
            margin-top: 8px !important;
        }

        .forgetmenot label {
            font-size: 13px !important;
            font-weight: 500 !important;
            color: #64748B !important;
            display: inline-flex !important;
            align-items: center;
        }

        .forgetmenot input[type="checkbox"] {
            border-radius: 4px !important;
            border: 1.5px solid #CBD5E1 !important;
            margin-right: 6px !important;
        }

        .forgetmenot input[type="checkbox"]:checked {
            background-color: #FF8A00 !important;
            border-color: #FF8A00 !important;
        }

        /* Giriş Yap Butonu */
        body.login #wp-submit {
            background: linear-gradient(135deg, #FF8A00 0%, #EA580C 100%) !important;
            border: none !important;
            border-radius: 10px !important;
            color: #FFFFFF !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            letter-spacing: 0.02em !important;
            padding: 10px 24px !important;
            height: auto !important;
            text-shadow: none !important;
            box-shadow: 0 4px 14px rgba(255, 138, 0, 0.4) !important;
            transition: all 0.25s ease !important;
            cursor: pointer !important;
            float: right !important;
        }

        body.login #wp-submit:hover,
        body.login #wp-submit:focus {
            background: linear-gradient(135deg, #E07B00 0%, #C2410C 100%) !important;
            transform: translateY(-1.5px) !important;
            box-shadow: 0 6px 20px rgba(255, 138, 0, 0.55) !important;
        }

        /* Şifremi Unuttum ve Geri Dön Linkleri */
        #nav, #backtoblog {
            padding: 0 !important;
            text-align: center !important;
            margin-top: 18px !important;
        }

        #nav a, #backtoblog a {
            color: #94A3B8 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            text-decoration: none !important;
            transition: color 0.2s ease !important;
        }

        #nav a:hover, #backtoblog a:hover {
            color: #FF8A00 !important;
        }

        /* Bildirim / Hata Kutuları */
        .login #login_error,
        .login .message,
        .login .success {
            border-radius: 12px !important;
            border-left: 4px solid #FF8A00 !important;
            background: #FFFFFF !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25) !important;
            color: #1E293B !important;
            padding: 14px 18px !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            margin-bottom: 20px !important;
        }

        .login #login_error {
            border-left-color: #EF4444 !important;
        }

        /* Alt Marka İmzası */
        .yym-login-footer-credit {
            text-align: center;
            margin-top: 26px;
            font-size: 12.5px;
            color: #64748B;
        }

        .yym-login-footer-credit a {
            color: #FF8A00;
            text-decoration: none;
            font-weight: 600;
        }

        .yym-login-footer-credit a:hover {
            text-decoration: underline;
        }
    </style>
    <?php
});

/**
 * 3. Formun Altına Şık İmza Eklemek
 */
add_action('login_footer', function () {
    ?>
    <div class="yym-login-footer-credit">
        Yol Yardım Merkezi Yönetim Sistemi • Altyapı: <a href="https://misteknoloji360.com.tr/" target="_blank" rel="noopener noreferrer">MisTeknoloji360</a> ❤️
    </div>
    <?php
});
