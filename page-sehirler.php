<?php
/**
 * Template Name: 81 İl Çekici Rehberi
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$all_cities = array(
    '01' => 'Adana', '02' => 'Adıyaman', '03' => 'Afyonkarahisar', '04' => 'Ağrı',
    '05' => 'Amasya', '06' => 'Ankara', '07' => 'Antalya', '08' => 'Artvin',
    '09' => 'Aydın', '10' => 'Balıkesir', '11' => 'Bilecik', '12' => 'Bingöl',
    '13' => 'Bitlis', '14' => 'Bolu', '15' => 'Burdur', '16' => 'Bursa',
    '17' => 'Çanakkale', '18' => 'Çankırı', '19' => 'Çorum', '20' => 'Denizli',
    '21' => 'Diyarbakır', '22' => 'Edirne', '23' => 'Elazığ', '24' => 'Erzincan',
    '25' => 'Erzurum', '26' => 'Eskişehir', '27' => 'Gaziantep', '28' => 'Giresun',
    '29' => 'Gümüşhane', '30' => 'Hakkâri', '31' => 'Hatay', '32' => 'Isparta',
    '33' => 'Mersin', '34' => 'İstanbul', '35' => 'İzmir', '36' => 'Kars',
    '37' => 'Kastamonu', '38' => 'Kayseri', '39' => 'Kırklareli', '40' => 'Kırşehir',
    '41' => 'Kocaeli', '42' => 'Konya', '43' => 'Kütahya', '44' => 'Malatya',
    '45' => 'Manisa', '46' => 'Kahramanmaraş', '47' => 'Mardin', '48' => 'Muğla',
    '49' => 'Muş', '50' => 'Nevşehir', '51' => 'Niğde', '52' => 'Ordu',
    '53' => 'Rize', '54' => 'Sakarya', '55' => 'Samsun', '56' => 'Siirt',
    '57' => 'Sinop', '58' => 'Sivas', '59' => 'Tekirdağ', '60' => 'Tokat',
    '61' => 'Trabzon', '62' => 'Tunceli', '63' => 'Şanlıurfa', '64' => 'Uşak',
    '65' => 'Van', '66' => 'Yozgat', '67' => 'Zonguldak', '68' => 'Aksaray',
    '69' => 'Bayburt', '70' => 'Karaman', '71' => 'Kırıkkale', '72' => 'Batman',
    '73' => 'Şırnak', '74' => 'Bartın', '75' => 'Ardahan', '76' => 'Iğdır',
    '77' => 'Yalova', '78' => 'Karabük', '79' => 'Kilis', '80' => 'Osmaniye',
    '81' => 'Düzce'
);
?>

<div class="yym-inner-page-wrap">
    <!-- Hero Bölümü -->
    <div class="yym-page-hero-section">
        <div class="lst-container text-center">
            <span class="yym-hero-mini-badge">📍 81 İL 922 İLÇE AĞI</span>
            <h1 class="yym-page-hero-title">Türkiye Geneli İl Çekici Rehberi</h1>
            <p class="yym-page-hero-desc">
                Hangi ilde yolda kaldıysanız o ilin plakasını seçerek en yakın nöbetçi oto çekici ve yol yardım ekiplerine saniyeler içinde ulaşabilirsiniz.
            </p>

            <!-- Canlı Arama Kutusu -->
            <div class="yym-city-quick-search-box">
                <input type="text" id="citySearchInput" placeholder="Şehir veya plaka no yazın (Örn: 38 veya Kayseri)..." autocomplete="off">
            </div>
        </div>
    </div>

    <!-- 81 İl Grid -->
    <div class="lst-container" style="padding-top: 50px; padding-bottom: 80px;">
        <div class="yym-cities-alphabetical-grid" id="citiesListContainer">
            <?php foreach ($all_cities as $plate => $name) : ?>
                <a href="<?php echo esc_url(home_url('/firmalar/?location=' . urlencode($name))); ?>" class="yym-city-plate-card" data-city="<?php echo esc_attr(mb_strtolower($name, 'UTF-8')); ?>" data-plate="<?php echo esc_attr($plate); ?>">
                    <span class="yym-city-plate-badge"><?php echo esc_html($plate); ?></span>
                    <span class="yym-city-plate-name"><?php echo esc_html($name); ?></span>
                    <span class="yym-city-card-arrow">→</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('citySearchInput');
    var cards = document.querySelectorAll('.yym-city-plate-card');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            cards.forEach(function (card) {
                var city = card.getAttribute('data-city');
                var plate = card.getAttribute('data-plate');
                if (city.indexOf(q) !== -1 || plate.indexOf(q) !== -1) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php
get_footer();
