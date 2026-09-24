<?php
/**
 * Yol Yardım Merkezi - Firmalar Arşivi & Arama Sayfası (archive-firma.php)
 * Türkiye geneli 81 il ve ilçelerdeki çekici & yol yardım firmalarını listeler.
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

// Arama ve Filtre Parametreleri
$search_keyword = mis360_filter_value('keyword');
$search_location = mis360_filter_value('location');
$search_category = mis360_filter_value('category');
$search_city = mis360_filter_value('city');
$search_district = mis360_filter_value('district');
$is_filtered = $search_keyword !== '' || $search_location !== '' || $search_category !== '' || $search_city !== '' || $search_district !== '';

// Toplam sonuç sayısı
global $wp_query;
$total_firms = $wp_query->found_posts;
?>

<div class="yym-inner-page-wrap">
    <!-- 1. ÜST HERO & ARAMA BANNERI -->
    <section class="yym-archive-hero">
        <div class="lst-container">
            <!-- Ekmek Kırıntısı (Breadcrumbs) -->
            <nav class="yym-breadcrumbs" aria-label="Ekmek Kırıntısı">
                <a href="<?php echo esc_url(home_url('/')); ?>">Ana Sayfa</a>
                <span>›</span>
                <span class="active">Yol Yardım Firmaları</span>
                <?php if ($search_location || $search_city || $search_district) : ?>
                    <span>›</span>
                    <span class="active"><?php echo esc_html(implode(" / ", array_filter(array($search_city, $search_district, $search_location)))); ?></span>
                <?php endif; ?>
            </nav>

            <div class="yym-archive-hero-content">
                <span class="yym-hero-mini-badge">🟢 TÜRKİYE GENELİ 7/24 ÇEKİCİ AĞI</span>
                <h1 class="yym-archive-title">Türkiye Geneli Nöbetçi Yol Yardım & Çekici Firmaları</h1>
                <p class="yym-archive-desc">
                    81 İl ve 922 İlçede kayıtlı, emtia sigortalı ve nöbetçi oto kurtarma firmalarıyla doğrudan iletişime geçin. Aracı komisyonu yok!
                </p>
            </div>

            <!-- FİLTRELEME & ARAMA FORMU (Floating Card) -->
            <div class="yym-archive-filter-card">
                <form method="get" action="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-archive-filter-form">
                    <!-- Kelime Arama -->
                    <div class="yym-filter-col">
                        <label for="filter-keyword" class="yym-filter-label">Firma veya Hizmet</label>
                        <div class="yym-input-with-icon">
                            <span class="yym-input-icon">🔍</span>
                            <input type="text" id="filter-keyword" name="keyword" value="<?php echo esc_attr($search_keyword); ?>" placeholder="Örn: Özpolat, Akü, Çekici..." class="yym-filter-input">
                        </div>
                    </div>

                    <!-- Şehir / İlçe -->
                    <div class="yym-filter-col">
                        <label class="yym-filter-label">Konum (Şehir / İlçe)</label>
                        <?php mis360_location_fields(); ?>
                    </div>

                    <!-- Hizmet Kategorisi -->
                    <div class="yym-filter-col">
                        <label for="filter-category" class="yym-filter-label">Hizmet Türü</label>
                        <div class="yym-input-with-icon">
                            <span class="yym-input-icon">🛠️</span>
                            <select id="filter-category" name="category" class="yym-filter-select">
                                <option value="">Tüm Hizmetler</option>
                        <?php mis360_category_options($search_category); ?>
                    </select>
                        </div>
                    </div>

                    <!-- Gönder Butonu -->
                    <div class="yym-filter-col yym-filter-col-btn">
                        <button type="submit" class="yym-btn-filter-submit">
                            <span class="yym-btn-icon">⚡</span>
                            <span>Firmaları Filtrele</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- 2. SONUÇLAR VE FİRMA LİSTESİ -->
    <div class="lst-container" style="padding-top: 40px; padding-bottom: 80px;">
        <!-- Sonuç Bilgi ve Sıralama Çubuğu -->
        <div class="yym-results-header-bar">
            <div class="yym-results-count-text">
                Toplam <strong><?php echo esc_html($total_firms); ?></strong> Yol Yardım Firması Listeleniyor
                <?php if ($is_filtered) : ?>
                    <span class="yym-filter-active-indicator">(Filtrelenmiş Sonuçlar)</span>
                <?php endif; ?>
            </div>

            <?php if ($is_filtered) : ?>
                <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-btn-clear-filters" title="Tüm filtreleri kaldır">
                    ✕ Filtreleri Temizle
                </a>
            <?php endif; ?>
        </div>

        <?php if (have_posts()) : ?>
            <!-- MODERN MARKETPLACE FİRMA GRID -->
            <div class="mis360-firma-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/firma-card', null, array('heading' => 'h2')); ?>
                <?php endwhile; ?>
            </div>

            <!-- Sayfalama (Pagination) -->
            <div class="yym-pagination-wrap">
                <?php
                echo paginate_links(array(
                    'prev_text' => '← Önceki',
                    'next_text' => 'Sonraki →',
                    'type'      => 'list',
                ));
                ?>
            </div>

        <?php else : ?>
            <div class="yym-no-results-card">
                <div class="yym-no-results-icon">🔍</div>
                <?php if ($is_filtered) : ?>
                    <h3>Aradığınız Kriterlere Uygun Firma Bulunamadı</h3>
                    <p>Belirttiğiniz şehir, hizmet türü veya anahtar kelime için kayıtlı firma bulunamadı. Filtreleri temizleyerek yeniden arayabilirsiniz.</p>
                <?php else : ?>
                    <h3>Henüz Kayıtlı Firma Yok</h3>
                    <p>Firmalar eklendiğinde burada listelenecek.</p>
                <?php endif; ?>
                <div class="yym-no-results-actions">
                    <?php if ($is_filtered) : ?>
                        <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="yym-btn-accent-large">Filtreleri Temizle</a>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="yym-btn-secondary-large">+ Firma Ekle</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- SEO Bilgilendirme Kutusu -->
        <div class="yym-archive-seo-box">
            <h3 class="yym-seo-title">Türkiye Geneli 7/24 Nöbetçi Çekici & Yol Yardım Hizmeti Hakkında</h3>
            <p>
                <strong>Yol Yardım Merkezi</strong>, Türkiye'nin 81 ilinde ve tüm otoyol güzergahlarında arıza yapan, kaza geçiren, lastiği patlayan veya aküsü biten sürücüleri en yakın nöbetçi oto çekici ve kurtarıcı ekipleriyle buluşturan güvenilir ve doğrulanmış bir yol yardım ağıdır. Platformumuz üzerinden aradığınız kurtarıcı operatörüyle doğrudan görüşür, komisyonsuz ve sabit fiyat güvencesiyle hizmet alırsınız.
            </p>
            <div class="yym-seo-features-row">
                <div class="yym-seo-feat">
                    <span class="yym-sf-icon">🛡️</span>
                    <div>
                        <strong>%0 Komisyon</strong>
                        <small>Doğrudan araç başındaki ustayla anlaşın.</small>
                    </div>
                </div>
                <div class="yym-seo-feat">
                    <span class="yym-sf-icon">⚡</span>
                    <div>
                        <strong>15-25 Dk Varış</strong>
                        <small>Bulunduğunuz noktaya en yakın ekipler.</small>
                    </div>
                </div>
                <div class="yym-seo-feat">
                    <span class="yym-sf-icon">📍</span>
                    <div>
                        <strong>81 İl Kapsamı</strong>
                        <small>Şehir içi ve şehirlerarası transfer desteği.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();
