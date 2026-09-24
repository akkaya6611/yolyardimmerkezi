<?php
/**
 * Template Name: Blog & Sürücü Rehberi
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Sayfalama parametresini güvenli al
$paged = max(1, (int) get_query_var('paged', 1), (int) get_query_var('page', 1));
$posts_per_page = 12; // 3 sütunlu grid için sayfa başına 12 makale

$posts_query = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'post_status'    => 'publish',
));

$default_articles = array(
    array(
        'title'    => 'Otoyolda ve TEM’de Araç Bozulduğunda Yapılması Gereken 5 Kural',
        'desc'     => 'Hızlı akan otoyollarda güvenliğinizi sağlamak için reflektör mesafesi, dörtlü flaşör kullanımı ve emniyet şeridi kuralları.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg',
        'category' => 'Yol Güvenliği',
        'date'     => '18 Eylül 2026',
    ),
    array(
        'title'    => 'Kışın Araç Aküsü Neden Biter? Akü Takviyesinde Dikkat Edilecekler',
        'desc'     => 'Soğuk havalarda marş basmayan akülere takviye yaparken kutup başlarının doğru bağlanması ve beyin arızalarını önleme rehberi.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/09/card_1-750x500.jpg',
        'category' => 'Akü & Elektrik',
        'date'     => '15 Eylül 2026',
    ),
    array(
        'title'    => 'Oto Çekici Çağırırken Sabit Fiyat Anlaşması Neden Önemlidir?',
        'desc'     => 'Sürpriz ek maliyetlerle karşılaşmamak için çekici ustasıyla km başına veya sabit fiyat üzerinden anlaşmanın detayları.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_2.jpg',
        'category' => 'Çekici Rehberi',
        'date'     => '12 Eylül 2026',
    ),
);
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <nav class="yym-breadcrumbs" aria-label="Ekmek Kırıntısı" style="justify-content: center; margin-bottom: 12px;">
                <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
                <span>›</span>
                <span class="active">Sürücü Rehberi & Blog</span>
                <?php if ($paged > 1) : ?>
                    <span>›</span>
                    <span class="active">Sayfa <?php echo esc_html($paged); ?></span>
                <?php endif; ?>
            </nav>
            <span class="yym-hero-mini-badge">📰 BİLGİ BANKASI</span>
            <h1 class="yym-page-hero-title">Sürücü Rehberi & Blog</h1>
            <p class="yym-page-hero-desc">
                Güvenli sürüş, yolda kalma anında yapılması gerekenler, akü bakımı ve 81 il acil kurtarıcı çağırma ipuçları.
            </p>
        </div>
    </div>

    <!-- Blog Kartları Listesi -->
    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px;">
        <?php if ($posts_query->have_posts()) : ?>
            <div class="lst-news-grid">
                <?php while ($posts_query->have_posts()) : $posts_query->the_post();
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

            <!-- Sayfa Numaralandırması (Pagination) -->
            <?php if ($posts_query->max_num_pages > 1) : ?>
                <div class="yym-pagination-wrap" style="margin-top: 50px; text-align: center;">
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format'    => '?paged=%#%',
                        'current'   => max(1, $paged),
                        'total'     => $posts_query->max_num_pages,
                        'prev_text' => '← ' . __('Önceki', 'yol-yardim-merkezi'),
                        'next_text' => __('Sonraki', 'yol-yardim-merkezi') . ' →',
                        'type'      => 'list',
                        'end_size'  => 2,
                        'mid_size'  => 2,
                    ));
                    ?>
                </div>
            <?php endif; ?>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>
            <?php if (!empty($default_articles)) : ?>
                <div class="lst-news-grid">
                    <?php foreach ($default_articles as $article) : ?>
                        <article class="lst-news-card">
                            <div class="lst-news-img-wrap">
                                <img src="<?php echo esc_url($article['image']); ?>" alt="<?php echo esc_attr($article['title']); ?>" class="lst-news-img" loading="lazy">
                                <span class="lst-news-badge"><?php echo esc_html($article['category']); ?></span>
                            </div>
                            <div class="lst-news-body">
                                <div class="lst-news-meta">
                                    <span>📅 <?php echo esc_html($article['date']); ?></span>
                                </div>
                                <h3 class="lst-news-title">
                                    <a href="#rehber"><?php echo esc_html($article['title']); ?></a>
                                </h3>
                                <p class="lst-news-desc"><?php echo esc_html($article['desc']); ?></p>
                                <a href="#rehber" class="lst-news-link"><?php _e('İpuçlarını Oku', 'yol-yardim-merkezi'); ?> →</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
