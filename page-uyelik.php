<?php
/** Template Name: Üyelik ve Hesabım */
if (!defined('ABSPATH')) exit;
$state = mis360_member_state();
$view = $state['view'];
$next = isset($_GET['next']) && $_GET['next'] === 'firm' ? 'firm' : '';
if (mis360_member_input('next') === 'firm') $next = 'firm';
get_header();
?>
<main class="mis360-member-page">
    <div class="mis360-member-shell">
        <?php if (is_user_logged_in()) :
            $member = wp_get_current_user();
            $kind = get_user_meta($member->ID,'mis360_member_kind',true);
            $page = isset($_GET['mpage']) && is_scalar($_GET['mpage']) ? max(1,absint($_GET['mpage'])) : 1;
            $firms = mis360_member_firms($member->ID,$page);
        ?>
        <section class="mis360-member-dashboard">
            <div class="mis360-member-heading">
                <div class="mis360-profile-identity"><span class="mis360-profile-avatar" aria-hidden="true"><?php echo esc_html(mb_substr($member->display_name,0,1)); ?></span><div><h1><?php echo esc_html($member->display_name); ?></h1><p><?php echo esc_html($member->user_email); ?></p><span class="mis360-profile-kind"><?php echo current_user_can('manage_options') ? 'Yönetici hesabı' : ($kind === 'firm' ? 'Firma sahibi' : 'Üye'); ?></span></div></div>
                <div class="mis360-profile-actions"><a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>">+ Yeni firma ekle</a><a href="<?php echo esc_url(wp_logout_url(home_url('/uyelik/'))); ?>">Çıkış yap</a></div>
            </div>
            <?php if ($state['error']) : ?><div class="mis360-member-alert" role="alert"><?php echo esc_html($state['error']); ?></div><?php endif; ?>
            <?php if (isset($_GET['tax_saved'])) : ?><div class="mis360-member-success" role="status">Vergi levhanız kaydedildi. Firma başvurunuzu oluşturabilirsiniz.</div><?php endif; ?>
            <?php include get_template_directory().'/template-parts/account-dashboard.php'; ?>
            <div id="account-profile"></div>
            <?php if ($account_section === 'belgeler') : ?>
                <section class="mis360-member-card mis360-tax-card">
                    <div class="mis360-document-heading"><span class="mis360-document-icon" aria-hidden="true">▤</span><div><span class="mis360-document-label">FİRMA BELGESİ</span><h2>Vergi levhası</h2></div></div>
                    <?php mis360_member_tax_panel($member->ID); ?>
                </section>
            <?php endif; ?>
            <?php if ($account_section === 'profil') : ?>
            <?php mis360_profile_panel(); ?>
        <?php endif; ?>
        </div></div>
        </section>
        <?php else : ?>
        <div class="mis360-member-layout">
            <section class="mis360-member-intro">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="mis360-member-back">← Ana sayfaya dön</a>
                <span class="mis360-member-eyebrow">YOL YARDIM MERKEZİ</span>
                <h1>Yolda da,<br> işinizde de<br> <em>yanınızdayız.</em></h1>
                <p>Firmanızı rehbere ekleyin veya ihtiyacınız olan yol yardım hizmetine ulaşın. Tek hesapla başlayın.</p>
                <div class="mis360-member-benefits"><div><b>01</b><span><strong>Firma sahipleri için</strong>Başvurunuzu oluşturun, yayın durumunu takip edin.</span></div><div><b>02</b><span><strong>Hizmet arayanlar için</strong>Firmaları bulun, hizmet deneyiminizi paylaşın.</span></div></div>
                <span class="mis360-member-intro-note">Firma kaydı lansmana özel ücretsizdir. Ücretli planlar yakında açılacaktır.</span>
            </section>
            <section class="mis360-member-card mis360-member-form-card">
                <nav class="mis360-member-tabs" aria-label="Üyelik işlemleri">
                    <a href="<?php echo esc_url(mis360_member_url('login',$next)); ?>" <?php if ($view === 'login') echo 'aria-current="page"'; ?>>Giriş yap</a>
                    <a href="<?php echo esc_url(mis360_member_url('register',$next)); ?>" <?php if ($view === 'register') echo 'aria-current="page"'; ?>>Üye ol</a>
                </nav>
                <h2><?php echo esc_html($view === 'register' ? 'Aramıza katılın' : ($view === 'forgot' ? 'Şifrenizi yenileyin' : 'Tekrar hoş geldiniz')); ?></h2>
                <p><?php echo esc_html($view === 'register' ? 'Size uygun üyelik türünü seçerek hesabınızı oluşturun.' : ($view === 'forgot' ? 'Hesabınıza bağlı e-posta adresini yazın.' : 'Devam etmek için hesabınıza giriş yapın.')); ?></p>
                <?php if ($next === 'firm') : ?><p class="mis360-member-hint">Firma başvurusu için giriş yapın veya hesap oluşturun. Firma kaydı lansmana özel ücretsizdir.</p><?php endif; ?>
                <?php if ($state['error']) : ?><div class="mis360-member-alert" role="alert"><?php echo esc_html($state['error']); ?></div><?php endif; ?>
                <?php if ($state['message']) : ?><div class="mis360-member-success" role="status"><?php echo esc_html($state['message']); ?></div><?php endif; ?>
                <?php if ($view === 'register' && !get_option('users_can_register')) : ?>
                    <p class="mis360-member-alert">Yeni üyelik şu anda kapalı. Mevcut hesabınızla giriş yapabilirsiniz.</p>
                <?php else : ?>
                <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(mis360_member_url($view,$next)); ?>" class="mis360-member-form">
                    <?php wp_nonce_field('mis360_member_'.$view,'_member_nonce'); ?>
                    <input type="hidden" name="member_action" value="<?php echo esc_attr($view); ?>"><input type="hidden" name="next" value="<?php echo esc_attr($next); ?>"><input type="hidden" name="claim_return" value="<?php echo (int)mis360_claim_intent_id(); ?>">
                    <div class="mis360-member-trap" aria-hidden="true"><label>Web sitesi<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
                    <?php if ($view === 'register') : $selected_kind = mis360_member_input('kind') ?: ($next === 'firm' ? 'firm' : 'customer'); ?>
                        <fieldset class="mis360-member-kind"><legend>Üyelik türü</legend><label><input type="radio" name="kind" value="firm" <?php checked($selected_kind,'firm'); ?> required><span>Firma sahibiyim</span></label><label><input type="radio" name="kind" value="customer" <?php checked($selected_kind,'customer'); ?> required><span>Hizmet arıyorum</span></label></fieldset>
                        <?php mis360_tax_field($selected_kind === 'firm'); ?>
                        <label for="member-name">Ad soyad</label><input id="member-name" name="member_name" autocomplete="name" maxlength="80" required value="<?php echo esc_attr(mis360_member_input('member_name')); ?>">
                    <?php endif; ?>
                    <label for="member-email">E-posta adresi</label><input id="member-email" type="email" name="email" autocomplete="email" maxlength="100" required value="<?php echo esc_attr(mis360_member_input('email')); ?>" placeholder="ornek@eposta.com">
                    <?php if ($view !== 'forgot') : ?>
                        <label for="member-password">Şifre</label><input id="member-password" type="password" name="password" autocomplete="<?php echo $view === 'register' ? 'new-password' : 'current-password'; ?>" <?php if ($view === 'register') echo 'minlength="12" maxlength="128" aria-describedby="member-password-help"'; ?> required>
                        <?php if ($view === 'register') : ?><small id="member-password-help">En az 12 karakter kullanın.</small><label for="member-confirm">Şifre tekrar</label><input id="member-confirm" type="password" name="confirm" autocomplete="new-password" minlength="12" maxlength="128" required><?php else : ?><div class="mis360-member-form-links"><label class="mis360-member-remember"><input type="checkbox" name="remember" value="1"> Beni hatırla</label><a href="<?php echo esc_url(mis360_member_url('forgot',$next)); ?>">Şifremi unuttum</a></div><?php endif; ?>
                    <?php endif; ?>
                    <?php if ($view === 'register') : ?><p class="mis360-legal-links">Üyelik işlemleri hakkında <a target="_blank" rel="noopener" href="<?php echo esc_url(home_url('/kvkk-aydinlatma-metni/')); ?>">KVKK Aydınlatma Metni</a>, <a target="_blank" rel="noopener" href="<?php echo esc_url(home_url('/gizlilik-politikasi/')); ?>">Gizlilik Politikası</a> ve <a target="_blank" rel="noopener" href="<?php echo esc_url(home_url('/kullanim-kosullari/')); ?>">Kullanım Koşulları</a> sayfalarını inceleyebilirsiniz.</p><?php endif; ?>
                    <button type="submit" class="mis360-member-button"><?php echo esc_html($view === 'register' ? 'Hesap oluştur' : ($view === 'forgot' ? 'Yenileme bağlantısı gönder' : 'Giriş yap')); ?> <span aria-hidden="true">→</span></button>
                </form>
                <?php if (get_privacy_policy_url()) : ?><p class="mis360-member-privacy">Kişisel verileriniz hakkında <a href="<?php echo esc_url(get_privacy_policy_url()); ?>">gizlilik politikasını</a> inceleyebilirsiniz.</p><?php endif; ?>
                <?php endif; ?>
            </section>
        </div>
        <?php endif; ?>
    </div>
</main>
<?php get_footer(); ?>
