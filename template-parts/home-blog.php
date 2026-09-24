<?php
if(!defined('ABSPATH'))exit;
$guide_posts=get_posts(array('post_type'=>'post','post_status'=>'publish','numberposts'=>5));
$guide_posts=array_slice(array_values(array_filter($guide_posts,static function($p){return $p->post_name!=='hello-world';})),0,4);
if(!$guide_posts)return;
?>
<section class="yym-section yym-blog-section" id="rehber"><div class="lst-container">
<div class="yym-section-header-row"><div><span class="yym-section-tag">SÜRÜCÜ BİLGİ MERKEZİ</span><h2 class="yym-section-title">Yol Yardım Rehberi</h2><p class="yym-section-subtitle">Yayınlanan yazılar ve işletme rehberinden bilgiler.</p></div><a class="yym-header-view-all-link" href="<?php echo esc_url(home_url('/blog/')); ?>">Tüm Yazıları Oku →</a></div>
<div class="yym-home-blog-grid"><?php foreach($guide_posts as $guide): $url=get_permalink($guide);$photo=get_the_post_thumbnail_url($guide,'medium_large'); ?>
<article class="yym-home-blog-card"><?php if($photo): ?><a class="yym-home-blog-thumb-wrap" href="<?php echo esc_url($url); ?>"><img class="yym-home-blog-img" src="<?php echo esc_url($photo); ?>" alt="" loading="lazy"></a><?php endif; ?>
<div class="yym-home-blog-content"><div class="yym-home-blog-meta"><?php echo esc_html(get_the_date('d.m.Y',$guide)); ?></div><h3 class="yym-home-blog-title"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($guide->post_title); ?></a></h3><p class="yym-home-blog-desc"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($guide->post_excerpt?:$guide->post_content),25)); ?></p><a class="yym-home-blog-read-more" href="<?php echo esc_url($url); ?>">Devamını Oku →</a></div></article>
<?php endforeach; ?></div></div></section>
