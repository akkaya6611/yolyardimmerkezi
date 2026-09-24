<?php
if (!defined('ABSPATH')) exit;
get_header(); while(have_posts()): the_post(); ?>
<main class="mis360-legal"><header class="mis360-legal-heading"><a href="<?php echo esc_url(home_url('/')); ?>">Ana sayfa</a><span> / Bilgilendirme</span><p class="mis360-legal-eyebrow">YOL YARDIM MERKEZİ</p><h1><?php the_title(); ?></h1><p>Üyelik, verileriniz ve platformun kullanımına ilişkin bilgiler.</p><small>Yerel taslak · 23 Eylül 2026</small></header>
<div class="mis360-legal-layout"><nav aria-label="Bilgilendirme sayfaları"><?php foreach(mis360_legal_pages() as $slug=>$label): ?><a href="<?php echo esc_url(home_url('/'.$slug.'/')); ?>" <?php if(is_page($slug))echo 'aria-current="page"'; ?>><?php echo esc_html($label); ?></a><?php endforeach; ?><a href="mailto:info@yolyardimmerkezi.com.tr">Başvuru / iletişim ↗</a></nav><article class="mis360-legal-content"><aside class="mis360-legal-draft"><strong>Yayına hazırlık taslağı</strong><p>Resmi işletmeci adı, açık adres, saklama süreleri ve hizmet sağlayıcı bilgileri tamamlanmadan bu metin nihai kabul edilmemelidir.</p></aside><?php the_content(); ?></article></div></main>
<?php endwhile; get_footer(); ?>
