<?php
/**
 * Yol Yardım Merkezi - Hizmet Bölgeleri (Local SEO Izgarası)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

$phone_raw = yym_get_phone_raw();
?>

<section class="yym-section yym-areas-section" id="bolgeler">
    <div class="yym-container">
        <div class="yym-section-header">
            <span class="yym-subtitle"><?php _e('GENİŞ HİZMET AĞI', 'yol-yardim-merkezi'); ?></span>
            <h2 class="yym-title"><?php _e('Hizmet Verdiğimiz Popüler Bölgeler', 'yol-yardim-merkezi'); ?></h2>
            <p class="yym-desc"><?php _e('Tüm ana arterlerde, otoyollarda, köprü bağlantılarında ve çevre ilçelerde nöbetçi çekici noktalarımız bulunmaktadır.', 'yol-yardim-merkezi'); ?></p>
        </div>

        <div class="yym-areas-grid">
            <?php
            $bolgeler_query = new WP_Query(array(
                'post_type'      => 'bolge',
                'posts_per_page' => 24,
                'orderby'        => 'title',
                'order'          => 'ASC'
            ));

            if ($bolgeler_query->have_posts()) :
                while ($bolgeler_query->have_posts()) : $bolgeler_query->the_post();
                    ?>
                    <a href="<?php the_permalink(); ?>" class="yym-area-tag">
                        <span class="yym-area-pin">📍</span>
                        <span class="yym-area-name"><?php the_title(); ?></span>
                        <span class="yym-area-badge"><?php _e('Nöbetçi Ekip', 'yol-yardim-merkezi'); ?></span>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                // Varsayılan popüler ilçe listesi (İstanbul & Çevresi Örneği)
                $default_areas = array(
                    'Kadıköy', 'Ümraniye', 'Ataşehir', 'Maltepe', 'Kartal', 'Pendik',
                    'Tuzla', 'Üsküdar', 'Beykoz', 'Çekmeköy', 'Sancaktepe', 'Sultanbeyli',
                    'Beşiktaş', 'Şişli', 'Sarıyer', 'Bakırköy', 'Zeytinburnu', 'Fatih',
                    'Beyoğlu', 'Eyüpsultan', 'Gaziosmanpaşa', 'Esenler', 'Bağcılar', 'Güngören',
                    'Bahçelievler', 'Küçükçekmece', 'Başakşehir', 'Avcılar', 'Beylikdüzü', 'Esenyurt',
                    'Büyükçekmece', 'Silivri', 'Çatalca', 'Arnavutköy', 'Kuzey Marmara Otoyolu', 'TEM & E-5'
                );

                foreach ($default_areas as $area) :
                    ?>
                    <a href="#firmalar" class="yym-area-tag" title="<?php printf(esc_attr__('%s En Yakın Oto Çekici Ara', 'yol-yardim-merkezi'), $area); ?>">
                        <span class="yym-area-pin">📍</span>
                        <span class="yym-area-name"><?php echo esc_html($area); ?> <?php _e('Çekici', 'yol-yardim-merkezi'); ?></span>
                        <span class="yym-area-badge"><?php _e('15 Dk', 'yol-yardim-merkezi'); ?></span>
                    </a>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="yym-areas-cta">
            <p><?php _e('Aradığınız bölge listede yok mu? Merak etmeyin, çevre illere ve tüm otoyollara 7/24 hizmet sağlıyoruz.', 'yol-yardim-merkezi'); ?></p>
            <a href="mailto:<?php echo esc_attr(yym_get_email()); ?>?subject=<?php echo rawurlencode('Bölge Bilgi Talebi'); ?>" class="yym-btn yym-btn-call yym-btn-sm">
                ✉️ <?php _e('Bize Ulaşın & Bilgi Alın', 'yol-yardim-merkezi'); ?>
            </a>
        </div>
    </div>
</section>
