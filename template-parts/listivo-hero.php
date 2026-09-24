<?php
/**
 * Listivo Demo 5 - Yol Yardım Merkezi Hero & Acil Arama Bölümü
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="lst-hero-section">
    <div class="lst-container lst-hero-container">
        <!-- Sol Alan: Çapraz Bindirilmiş Kurtarıcı Görselleri -->
        <div class="lst-hero-images-wrap">
            <div class="lst-hero-img-box lst-hero-img-top">
                <img src="https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_1.jpg" alt="7/24 Acil Oto Çekici ve Kurtarma" class="lst-hero-img">
            </div>
            <div class="lst-hero-img-box lst-hero-img-bottom">
                <img src="https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/hero_2.jpg" alt="En Yakın Çekici 15 Dakikada Yanınızda" class="lst-hero-img">
            </div>
        </div>

        <!-- Sağ Alan: Başlık, Hizmet Sekmeleri ve Arama Kartı -->
        <div class="lst-hero-content">
            <div class="lst-hero-heading-wrap">
                <h1 class="lst-hero-heading">
                    Yolda mı Kaldınız?<br>
                    En Yakın Çekici Yanınızda!
                    <span class="lst-hero-arrow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="90" height="110" viewBox="0 0 113 138" fill="none">
                            <path d="M64.5426 2.27885C78.0427 7.77898 89.9761 27.1222 91.0428 38.2789C95.7385 87.388 63.8716 105.431 17.4817 113.366" stroke="#FA5343" stroke-width="3.5" stroke-dasharray="8 6"/>
                            <path d="M23.061 99.2057C9.95166 120.397 5.60595 112.336 25.9132 122.443" stroke="#FA5343" stroke-width="3.5" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h1>
            </div>

            <!-- Hizmet Türü Sekmeleri (Oto Çekici, Akü Takviye, Lastik Tamiri, Motosiklet) -->
            <div class="lst-vehicle-tabs" role="tablist">
                <button type="button" class="lst-vehicle-tab is-active" data-vehicle="cekici" role="tab" aria-selected="true">
                    <img src="https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/truck.png" alt="Oto Çekici" class="lst-tab-icon">
                    <span class="lst-tab-label">Oto Çekici</span>
                </button>
                <button type="button" class="lst-vehicle-tab" data-vehicle="aku" role="tab" aria-selected="false">
                    <span style="font-size: 20px;">⚡</span>
                    <span class="lst-tab-label">Akü Takviye</span>
                </button>
                <button type="button" class="lst-vehicle-tab" data-vehicle="lastik" role="tab" aria-selected="false">
                    <span style="font-size: 20px;">🔧</span>
                    <span class="lst-tab-label">Lastik Yardım</span>
                </button>
                <button type="button" class="lst-vehicle-tab" data-vehicle="motosiklet" role="tab" aria-selected="false">
                    <img src="https://listivo5.tangiblewp.com/wp-content/uploads/2022/06/bike.png" alt="Motosiklet" class="lst-tab-icon">
                    <span class="lst-tab-label">Motosiklet</span>
                </button>
            </div>

            <!-- 4'lü Hızlı Arama Kartı (İl, İlçe, Hizmet, Fiyat) -->
            <div class="lst-search-card">
                <form id="lstHeroSearchForm" class="lst-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>#firmalar">
                    <div class="lst-search-fields-grid">
                        <div class="lst-field-group">
                            <label class="lst-field-label">Bulunduğunuz İl</label>
                            <select name="city" class="lst-select">
                                <option value="">Tüm İller (81 İl)</option>
                                <option value="34-istanbul">34 İstanbul</option>
                                <option value="06-ankara">06 Ankara</option>
                                <option value="35-izmir">35 İzmir</option>
                                <option value="38-kayseri" selected>38 Kayseri</option>
                                <option value="16-bursa">16 Bursa</option>
                                <option value="07-antalya">07 Antalya</option>
                                <option value="01-adana">01 Adana</option>
                                <option value="42-konya">42 Konya</option>
                                <option value="27-gaziantep">27 Gaziantep</option>
                                <option value="41-kocaeli">41 Kocaeli</option>
                                <option value="55-samsun">55 Samsun</option>
                                <option value="61-trabzon">61 Trabzon</option>
                            </select>
                        </div>

                        <div class="lst-field-group">
                            <label class="lst-field-label">İlçe / Bölge</label>
                            <select name="district" class="lst-select">
                                <option value="">Tüm İlçeler</option>
                                <option value="melikgazi">Melikgazi</option>
                                <option value="kocasinan">Kocasinan</option>
                                <option value="talas">Talas</option>
                                <option value="kadikoy">Kadıköy</option>
                                <option value="umraniye">Ümraniye</option>
                                <option value="besiktas">Beşiktaş</option>
                                <option value="cankaya">Çankaya</option>
                                <option value="yenimahalle">Yenimahalle</option>
                                <option value="karsiyaka">Karşıyaka</option>
                                <option value="bornova">Bornova</option>
                            </select>
                        </div>

                        <div class="lst-field-group">
                            <label class="lst-field-label">Hizmet Türü</label>
                            <select name="service" class="lst-select">
                                <option value="">Tüm Hizmetler</option>
                                <option value="oto-cekici" selected>Oto Çekici & Kurtarıcı</option>
                                <option value="aku-takviye">Akü Takviye & Satış</option>
                                <option value="mobil-lastik">Mobil Lastik Tamiri</option>
                                <option value="yakit-ikmali">Acil Yakıt İkmali</option>
                                <option value="motosiklet">Motosiklet Taşıma</option>
                                <option value="agir-vasita">Ağır Vasıta Kurtarma</option>
                                <option value="sehirlerarasi">Şehirlerarası Taşıma</option>
                            </select>
                        </div>

                        <div class="lst-field-group">
                            <label class="lst-field-label">Mesafe / Tarife</label>
                            <select name="distance" class="lst-select">
                                <option value="">Standart Tarife</option>
                                <option value="sehir-ici">Şehir İçi Sabit Fiyat</option>
                                <option value="0-25">0 - 25 Km Mesafe</option>
                                <option value="25-50">25 - 50 Km Mesafe</option>
                                <option value="50+">50+ Km / Şehirlerarası</option>
                            </select>
                        </div>
                    </div>

                    <div class="lst-search-btn-wrap">
                        <button type="submit" class="lst-btn lst-btn-coral lst-btn-search">
                            <svg class="lst-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <span>🚨 En Yakın Çekiciyi Bul & Çağır</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
