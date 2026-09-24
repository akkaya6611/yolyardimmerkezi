<?php
if(!defined('ABSPATH'))exit;
function mis360_claim_admin_url($id=0){return add_query_arg(array('post_type'=>'firma','page'=>'mis360-claims','claim'=>$id),admin_url('edit.php'));}
add_action('admin_menu',function(){if(!current_user_can('manage_options'))return;$q=new WP_Query(array('post_type'=>'m360_claim','post_status'=>'private','posts_per_page'=>1,'fields'=>'ids','meta_key'=>'_claim_state','meta_value'=>'pending'));$badge=$q->found_posts?' <span class="awaiting-mod"><span class="pending-count">'.(int)$q->found_posts.'</span></span>':'';add_submenu_page('edit.php?post_type=firma','Firma Sahiplenme Talepleri','Sahiplenme Talepleri'.$badge,'manage_options','mis360-claims','mis360_claim_admin_panel');});
function mis360_claim_tax_decide($id,$state,$note,$version){
    if(!current_user_can('manage_options'))return new WP_Error('auth','Yetkiniz yok.');$p=get_post($id);
    if(!$p||$p->post_type!=='m360_claim'||!in_array($state,array('approved','changes'),true))return new WP_Error('claim','Geçersiz talep.');
    $uid=(int)$p->post_author;$actual=mis360_tax_version($uid);$note=sanitize_textarea_field($note);
    if(!$actual||!hash_equals($actual,$version))return new WP_Error('stale','Belge değişmiş. Sayfayı yenileyip tekrar inceleyin.');
    if($state==='changes'&&mb_strlen($note)<5)return new WP_Error('note','Düzeltme gerekçesi yazın.');
    $saved=mis360_tax_record_decision($uid,$state,mb_substr($note,0,2000),$actual);if(is_wp_error($saved))return $saved;
    do_action('mis360_review_recorded',$uid,'tax',$state,$note,(int)get_post_meta($id,'_claim_firm',true));return true;
}
function mis360_claim_admin_panel(){
    if(!current_user_can('manage_options'))return;$id=isset($_GET['claim'])&&is_scalar($_GET['claim'])?absint($_GET['claim']):0;echo '<div class="wrap"><h1>Firma Sahiplenme Talepleri</h1>';
    if($id){$p=get_post($id);if(!$p||$p->post_type!=='m360_claim'){echo '<p>Talep bulunamadı.</p></div>';return;}
        if(($_SERVER['REQUEST_METHOD']??'')==='POST'){
            check_admin_referer('mis360_claim_review_'.$id);$decision=mis360_member_input('claim_decision');
            $result=mis360_member_input('review_kind')==='tax'?mis360_claim_tax_decide($id,$decision,mis360_member_input('review_note',true),mis360_member_input('tax_version')):mis360_claim_decide($id,$decision,mis360_member_input('review_note',true),mis360_member_input('matched')==='yes');
            echo '<div class="notice '.(is_wp_error($result)?'notice-error':'notice-success').'"><p>'.esc_html(is_wp_error($result)?$result->get_error_message():'İnceleme kaydedildi.').'</p></div>';
        }
        $uid=(int)$p->post_author;$u=get_userdata($uid);$firm=(int)get_post_meta($id,'_claim_firm',true);$tax=mis360_tax_review($uid);$state=get_post_meta($id,'_claim_state',true);
        echo '<p><a href="'.esc_url(mis360_claim_admin_url()).'">← Tüm talepler</a></p><section style="padding:24px;background:#fff;border:1px solid #ccd0d4;max-width:850px"><h2>'.esc_html($p->post_title).'</h2><p>Başvuran: '.esc_html($u?$u->display_name.' · '.$u->user_email:'Silinmiş kullanıcı').'</p><p>Durum: '.esc_html(array('pending'=>'İnceleme bekliyor','approved'=>'Onaylandı','rejected'=>'Reddedildi')[$state]??$state).'</p><div style="white-space:pre-wrap;background:#f5f7fa;padding:16px">'.esc_html($p->post_content).'</div><p><a class="button" target="_blank" rel="noopener" href="'.esc_url(get_permalink($firm)).'">Firma kaydını aç</a></p><h3>1. Başvuranın vergi levhası</h3><p>'.mis360_tax_admin_link($uid).' · '.esc_html($tax['label']).'</p>';
        if($state==='pending'&&mis360_tax_present($uid)){echo '<form method="post">';wp_nonce_field('mis360_claim_review_'.$id);echo '<input type="hidden" name="review_kind" value="tax"><input type="hidden" name="tax_version" value="'.esc_attr(mis360_tax_version($uid)).'"><p><label>Belge inceleme açıklaması<br><textarea name="review_note" rows="3" maxlength="2000" style="width:100%">'.esc_textarea($tax['note']).'</textarea></label></p><button class="button" name="claim_decision" value="approved">Belgeyi onayla ve dosyayı sil</button> <button class="button" name="claim_decision" value="changes">Belgede düzeltme iste</button></form>';}
        if($state==='pending'){echo '<h3>2. Firma ile eşleşme ve sahiplik devri</h3><p>Belgeyi onaylamadan önce ticari unvan ve adresin firma kaydıyla ilişkisini doğrulayın. Onayla birlikte dosya silinir; eşleşme kontrolünü aynı inceleme sırasında tamamlayın. Belge yüklenmiş olması tek başına sahiplik kanıtı değildir.</p><form method="post">';wp_nonce_field('mis360_claim_review_'.$id);echo '<input type="hidden" name="review_kind" value="claim"><p><label><input type="checkbox" name="matched" value="yes"> Belgeyi ve firma ile eşleşmesini kontrol ettim.</label></p><p><label>Üyeye gösterilecek karar gerekçesi<br><textarea name="review_note" rows="4" maxlength="2000" style="width:100%"></textarea></label></p><button class="button button-primary" name="claim_decision" value="approved">Onayla ve firmayı üyeye aktar</button> <button class="button" name="claim_decision" value="rejected">Talebi reddet</button></form>';}
        else echo '<p>'.nl2br(esc_html(get_post_meta($id,'_claim_note',true))).'</p>';echo '</section>';
    }else{
        $page=max(1,absint($_GET['paged']??1));$q=new WP_Query(array('post_type'=>'m360_claim','post_status'=>'private','posts_per_page'=>20,'paged'=>$page));echo '<table class="widefat striped"><thead><tr><th>Firma</th><th>Başvuran</th><th>Durum</th><th></th></tr></thead><tbody>';
        foreach($q->posts as $p){$u=get_userdata($p->post_author);$state=get_post_meta($p->ID,'_claim_state',true);echo '<tr><td>'.esc_html($p->post_title).'</td><td>'.esc_html($u?$u->display_name:'—').'</td><td>'.esc_html(array('pending'=>'İnceleme bekliyor','approved'=>'Onaylandı','rejected'=>'Reddedildi')[$state]??$state).'</td><td><a class="button" href="'.esc_url(mis360_claim_admin_url($p->ID)).'">Talebi incele</a></td></tr>';}
        if(!$q->posts)echo '<tr><td colspan="4">Henüz sahiplenme talebi yok.</td></tr>';echo '</tbody></table>';echo paginate_links(array('base'=>add_query_arg('paged','%#%',mis360_claim_admin_url()),'current'=>$page,'total'=>$q->max_num_pages));
    }echo '</div>';
}
