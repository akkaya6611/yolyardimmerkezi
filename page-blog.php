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

$posts_query = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => 9,
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
    array(
        'title'    => 'Lastik Patladığında Güvenli Stepne Değişimi ve Mobil Lastikçi',
        'desc'     => 'Kriko ile araç kaldırma güvenliği, bijon gevşetme sıralaması ve yerinde mobil lastik tamiri desteği hakkında bilmeniz gerekenler.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/01/Ford-F-150-Raptor-4-750x500.jpg',
        'category' => 'Mobil Lastik',
        'date'     => '08 Eylül 2026',
    ),
    array(
        'title'    => 'Motosiklet Transferinde Kilitli Sehpa ve Sabitleme Güvencesi',
        'desc'     => 'İki tekerlekli araçların nakliyesinde grenaj çizilmelerini ve devrilmeyi önleyen profesyonel taşıma donanımları.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg',
        'category' => 'Motosiklet Taşıma',
        'date'     => '02 Eylül 2026',
    ),
    array(
        'title'    => 'Emtia Nakliyat Sigortası Nedir? Çekici Kaskosunun Önemi',
        'desc'     => 'Aracınız çekici üzerindeyken meydana gelebilecek kazalarda yasal haklarınız ve sigorta kapsamı.',
        'image'    => 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_2.jpg',
        'category' => 'Yasal Haklar',
        'date'     => '28 Ağustos 2026',
    ),
);
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <span class="yym-hero-mini-badge">📰 BİLGİ BANKASI</span>
            <h1 class="yym-page-hero-title">Sürücü Rehberi & Blog</h1>
            <p class="yym-page-hero-desc">
                Güvenli sürüş, yolda kalma anında yapılması gerekenler, akü bakımı ve çekici çağırma ipuçları.
            </p>
        </div>
    </div>

    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px;">
        <div class="lst-news-grid">
            <?php
            if ($posts_query->have_posts()) :
                while ($posts_query->have_posts()) : $posts_query->the_post();
                    $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'medium_large') : 'https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg';
                    $cats = get_the_category();
                    $cat_name = !empty($cats) ? $cats[0]->name : 'Rehber';
            ?>
                    <article class="lst-news-card">
                        <div class="lst-news-img-wrap">
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php the_title_attribute(); ?>" class="lst-news-img" loading="lazy">
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
                            <a href="<?php the_permalink(); ?>" class="lst-news-link">Devamını Oku →</a>
                        </div>
                    </article>
            <?php
                endwhile;
                wp_reset_postdata();
            else :
                foreach ($default_articles as $article) :
            ?>
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
                            <a href="#rehber" class="lst-news-link">İpuçlarını Oku →</a>
                        </div>
                    </article>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>

<?php
get_footer();
