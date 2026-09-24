<?php
if (!defined('ABSPATH')) exit;
function mis360_legal_install() {
    if(get_option('mis360_legal_version')==='1')return;
    $pages=json_decode(file_get_contents(__DIR__.'/legal-content.json'),true);
    if(!is_array($pages))return;
    foreach($pages as $page){
        $existing=get_page_by_path($page['slug']);
        if($existing)continue; // Never overwrite an editor's existing legal text.
        $id=wp_insert_post(array('post_type'=>'page','post_status'=>'publish','post_title'=>$page['title'],'post_name'=>$page['slug'],'post_content'=>wp_slash($page['content']),'comment_status'=>'closed','ping_status'=>'closed'),true);
        if(is_wp_error($id)||!$id)return;
    }
    $privacy=get_page_by_path('gizlilik-politikasi');
    if($privacy&&!get_option('wp_page_for_privacy_policy'))update_option('wp_page_for_privacy_policy',$privacy->ID);
    update_option('mis360_legal_version','1',false);
}
add_action('admin_init',function(){if(current_user_can('manage_options'))mis360_legal_install();});
function mis360_legal_pages() { return array('gizlilik-politikasi'=>'Gizlilik Politikası','kvkk-aydinlatma-metni'=>'KVKK Aydınlatma Metni','vergi-levhasi-bilgilendirmesi'=>'Vergi Levhası ve Belge Güvenliği','kullanim-kosullari'=>'Üyelik ve Kullanım Koşulları'); }
add_filter('template_include',function($template){return is_page(array_keys(mis360_legal_pages())) ? get_template_directory().'/page-legal.php' : $template;});
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('mis360-legal',get_template_directory_uri().'/assets/css/legal-pages.css',array(),filemtime(get_template_directory().'/assets/css/legal-pages.css'));});
add_filter('wp_robots',function($robots){if(is_page(array_keys(mis360_legal_pages()))){$robots['noindex']=true;unset($robots['index']);}return $robots;});
add_action('admin_notices',function(){if(current_user_can('manage_options')) echo '<div class="notice notice-warning"><p><strong>Hukuki sayfalar yerel taslaktır.</strong> Yayından önce resmi işletmeci adı/adresi, veri işleme dayanakları, sağlayıcı/aktarım bilgileri ve saklama-imha süreleri tamamlanmalıdır. Metinleri <a href="'.esc_url(admin_url('edit.php?post_type=page')).'">Sayfalar</a> ekranından düzenleyebilirsiniz.</p></div>';});
