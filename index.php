<?php
/**
 * Yol Yardım Merkezi - Standart Blog & Arşiv Liste Şablonu (index.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Dinamik başlık ve alt başlık belirleme
$archive_title = '';
$archive_desc  = __('Yol yardım rehberi, güvenli sürüş tavsiyeleri ve güncel sürücü duyuruları.', 'yol-yardim-merkezi');

if (is_category()) {
    $archive_title = single_cat_title('', false);
    $archive_desc  = category_description() ?: __('Bu kategoriye ait sürücü rehberi yazıları.', 'yol-yardim-merkezi');
} elseif (is_tag()) {
    $archive_title = single_tag_title('', false);
} elseif (is_author()) {
    $archive_title = get_the_author();
} elseif (is_date()) {
    $archive_title = get_the_date('F Y') . ' ' . __('Arşivi', 'yol-yardim-merkezi');
} elseif (is_search()) {
    $archive_title = sprintf(__('Arama Sonuçları: %s', 'yol-yardim-merkezi'), get_search_query());
} else {
    $archive_title = __('Sürücü Rehberi & Blog', 'yol-yardim-merkezi');
}
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <nav class="yym-breadcrumbs" aria-label="Ekmek Kırıntısı" style="justify-content: center; margin-bottom: 12px;">
                <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
                <span>›</span>
                <span class="active"><?php echo esc_html($archive_title); ?></span>
            </nav>
            <span class="yym-hero-mini-badge">📰 BİLGİ BANKASI</span>
            <h1 class="yym-page-hero-title"><?php echo esc_html($archive_title); ?></h1>
            <p class="yym-page-hero-desc"><?php echo esc_html($archive_desc); ?></p>
        </div>
    </div>

    <!-- Blog Kartları Listesi -->
    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px;">
        <?php if (have_posts()) : ?>
            <div class="lst-news-grid">
                <?php while (have_posts()) : the_post(); 
                    $thumb = has_post_thumbnail() 
                        ? get_the_post_thumbnail_url(get_the_ID(), 'large') 
                        : get_template_directory_uri() . '/assets/images/brand-tow-icon.png';
                    $cats = get_the_category();
                    $cat_name = !empty($cats) ? $cats[0]->name : __('Rehber', 'yol-yardim-merkezi');
                ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('lst-news-card'); ?>>
                        <div class="lst-news-img-wrap">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" class="lst-news-img" loading="lazy">
                            </a>
                            <span class="lst-news-badge"><?php echo esc_html($cat_name); ?></span>
                        </div>
                        <div class="lst-news-body">
                            <div class="lst-news-meta">
                                <span>📅 <?php echo get_the_date('d M Y'); ?></span>
                            </div>
                            <h3 class="lst-news-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="lst-news-desc"><?php echo wp_trim_words(get_the_excerpt(), 18, '...'); ?></p>
                            <a href="<?php the_permalink(); ?>" class="lst-news-link"><?php _e('Devamını Oku', 'yol-yardim-merkezi'); ?> →</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>

            <div class="yym-pagination" style="margin-top: 50px; text-align: center;">
                <?php the_posts_pagination(array(
                    'prev_text' => '← ' . __('Önceki', 'yol-yardim-merkezi'),
                    'next_text' => __('Sonraki', 'yol-yardim-merkezi') . ' →',
                )); ?>
            </div>
        <?php else : ?>
            <div class="yym-empty-notice" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 16px; border: 1px solid #E2E8F0;">
                <span style="font-size: 3rem; display: block; margin-bottom: 12px;">📭</span>
                <h3><?php _e('Henüz yayınlanmış bir yazı bulunmuyor.', 'yol-yardim-merkezi'); ?></h3>
                <p style="color: #64748B; margin-top: 8px;"><?php _e('Farklı bir arama yapabilir veya ana sayfaya dönebilirsiniz.', 'yol-yardim-merkezi'); ?></p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="yym-btn-cta-primary" style="display: inline-block; margin-top: 20px;">
                    <?php _e('Ana Sayfaya Dön', 'yol-yardim-merkezi'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
