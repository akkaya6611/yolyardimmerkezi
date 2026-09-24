<?php
/**
 * Template Name: Hakkımızda
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<style>
/* ===================================================
   HAKKIMIZDA SAYFASI STİLLERİ - Modern & Kurumsal
   =================================================== */
.about-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #741316 100%);
    padding: 85px 20px 105px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}
.about-hero::before {
    content: '';
    position: absolute;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(250, 83, 67, 0.18) 0%, transparent 70%);
    top: -100px;
    left: -60px;
    border-radius: 50%;
}
.about-hero::after {
    content: '';
    position: absolute;
    width: 450px;
    height: 450px;
    background: radial-gradient(circle, rgba(116, 19, 22, 0.3) 0%, transparent 70%);
    bottom: -80px;
    right: -60px;
    border-radius: 50%;
}
.about-hero .container {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
    padding: 0 20px;
}
.about-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.12);
    color: #fca5a5;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 7px 20px;
    border-radius: 50px;
    font-size: 13.5px;
    font-weight: 700;
    margin-bottom: 20px;
    backdrop-filter: blur(6px);
}
.about-hero h1 {
    font-family: var(--font-heading, sans-serif);
    font-size: 44px;
    font-weight: 900;
    line-height: 1.25;
    margin: 0 0 18px;
    letter-spacing: -0.5px;
}
.about-hero h1 .hl {
    background: linear-gradient(90deg, #FA5343, #fb923c, #f87171);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.about-hero p {
    font-size: 17.5px;
    color: #cbd5e1;
    line-height: 1.7;
    margin: 0 auto;
    max-width: 720px;
}

/* ===================================================
   İSTATİSTİK BANT
   =================================================== */
.about-stats-strip {
    background: #ffffff;
    border-bottom: 1px solid #e2e8f0;
    padding: 30px 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    max-width: 1150px;
    margin: 0 auto;
    text-align: center;
}
.stat-box {
    padding: 10px;
}
.stat-box .number {
    font-family: var(--font-heading, sans-serif);
    font-size: 38px;
    font-weight: 900;
    color: #741316;
    line-height: 1.1;
    margin-bottom: 6px;
}
.stat-box .label {
    font-size: 14.5px;
    color: #64748b;
    font-weight: 600;
}

/* ===================================================
   HİKAYEMİZ / BİZ KİMİZ
   =================================================== */
.about-story-section {
    padding: 85px 20px;
    background: #ffffff;
}
.story-wrapper {
    max-width: 1150px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1.1fr;
    gap: 60px;
    align-items: center;
}
.story-image-wrap {
    position: relative;
}
.story-image-wrap img {
    width: 100%;
    height: 440px;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    display: block;
}
.story-badge-float {
    position: absolute;
    bottom: -20px;
    right: 20px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 16px 22px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    display: flex;
    align-items: center;
    gap: 14px;
}
.story-badge-float .badge-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: linear-gradient(135deg, #FA5343, #741316);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.story-badge-float .badge-text h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #0f172a;
}
.story-badge-float .badge-text p {
    margin: 2px 0 0;
    font-size: 12.5px;
    color: #64748b;
}

.story-content .sub-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #FA5343;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 12px;
    display: inline-block;
}
.story-content h2 {
    font-family: var(--font-heading, sans-serif);
    font-size: 34px;
    font-weight: 900;
    color: #0f172a;
    line-height: 1.28;
    margin: 0 0 18px;
}
.story-content p {
    font-size: 16px;
    color: #475569;
    line-height: 1.8;
    margin-bottom: 18px;
}
.story-checklist {
    list-style: none;
    padding: 0;
    margin: 24px 0 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.story-checklist li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 15px;
    color: #1e293b;
    font-weight: 600;
}
.story-checklist li span.check-icon {
    color: #16a34a;
    font-size: 18px;
    font-weight: 900;
    line-height: 1;
    margin-top: 2px;
    flex-shrink: 0;
}

/* ===================================================
   DEĞERLERİMİZ
   =================================================== */
.about-values-section {
    padding: 85px 20px;
    background: #f8fafc;
}
.values-container {
    max-width: 1150px;
    margin: 0 auto;
}
.section-center-head {
    text-align: center;
    max-width: 650px;
    margin: 0 auto 50px;
}
.section-center-head .sub-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #FA5343;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    margin-bottom: 10px;
    display: inline-block;
}
.section-center-head h2 {
    font-family: var(--font-heading, sans-serif);
    font-size: 34px;
    font-weight: 900;
    color: #0f172a;
    margin: 0 0 14px;
}
.section-center-head p {
    font-size: 16px;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 26px;
}
.value-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 34px 28px;
    transition: all 0.25s ease;
}
.value-card:hover {
    border-color: #FA5343;
    transform: translateY(-5px);
    box-shadow: 0 16px 32px rgba(250, 83, 67, 0.1);
}
.value-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: white;
    margin-bottom: 20px;
}
.value-card h3 {
    font-family: var(--font-heading, sans-serif);
    font-size: 19px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}
.value-card p {
    font-size: 14.5px;
    color: #64748b;
    line-height: 1.65;
    margin: 0;
}

/* ===================================================
   NASIL ÇALIŞIR?
   =================================================== */
.about-how-section {
    padding: 85px 20px;
    background: #ffffff;
}
.how-container {
    max-width: 1150px;
    margin: 0 auto;
}
.how-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}
.how-card {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 34px 26px;
    text-align: center;
    transition: all 0.2s ease;
}
.how-card:hover {
    border-color: #741316;
    box-shadow: 0 12px 28px rgba(0,0,0,0.06);
}
.how-step {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #741316 0%, #FA5343 100%);
    color: white;
    font-size: 18px;
    font-weight: 900;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 18px;
    box-shadow: 0 6px 16px rgba(116, 19, 22, 0.3);
}
.how-card h4 {
    font-family: var(--font-heading, sans-serif);
    font-size: 18px;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 10px;
}
.how-card p {
    font-size: 14.5px;
    color: #64748b;
    line-height: 1.6;
    margin: 0;
}

/* ===================================================
   CTA BÖLÜMÜ
   =================================================== */
.about-cta-section {
    padding: 80px 20px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #741316 100%);
    color: white;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.cta-box {
    position: relative;
    z-index: 2;
    max-width: 760px;
    margin: 0 auto;
}
.cta-box h2 {
    font-family: var(--font-heading, sans-serif);
    font-size: 36px;
    font-weight: 900;
    margin: 0 0 16px;
    letter-spacing: -0.5px;
}
.cta-box p {
    font-size: 16.5px;
    color: #cbd5e1;
    margin: 0 0 30px;
    line-height: 1.7;
}
.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}
.cta-btn-primary {
    background: linear-gradient(135deg, #FA5343 0%, #dc2626 100%);
    color: white !important;
    padding: 14px 30px;
    border-radius: 10px;
    font-size: 15.5px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s;
    box-shadow: 0 6px 20px rgba(250, 83, 67, 0.4);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.cta-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(250, 83, 67, 0.55);
}
.cta-btn-secondary {
    background: rgba(255,255,255,0.12);
    color: white !important;
    border: 1px solid rgba(255,255,255,0.25);
    padding: 14px 30px;
    border-radius: 10px;
    font-size: 15.5px;
    font-weight: 700;
    text-decoration: none;
    backdrop-filter: blur(8px);
    transition: all 0.25s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.cta-btn-secondary:hover {
    background: rgba(255,255,255,0.22);
    transform: translateY(-2px);
}

/* ===================================================
   RESPONSIVE
   =================================================== */
@media (max-width: 992px) {
    .story-wrapper { grid-template-columns: 1fr; gap: 40px; }
    .story-image-wrap img { height: 340px; }
    .values-grid { grid-template-columns: repeat(2, 1fr); }
    .how-grid { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
}
@media (max-width: 600px) {
    .about-hero h1 { font-size: 30px; }
    .story-content h2 { font-size: 26px; }
    .values-grid { grid-template-columns: 1fr; }
    .stats-grid { grid-template-columns: 1fr; }
    .cta-box h2 { font-size: 26px; }
    .story-badge-float { position: static; margin-top: 14px; }
}
</style>

<!-- HERO BÖLÜMÜ -->
<section class="about-hero">
    <div class="container">
        <div class="about-badge">
            🚨 7/24 Nöbetçi Çekici ve Kurtarıcı Ağı
        </div>
        <h1>Türkiye'nin En Kapsamlı <br><span class="hl">Yol Yardım & Çekici</span> Platformu</h1>
        <p>Aracınız yolda kaldığında, arıza veya kaza durumlarında doğru çekici ekibine ulaşmak artık çok kolay. 81 ilde lisanslı, donanımlı ve doğrudan iletişim kurulabilen nöbetçi kurtarıcılar tek çatı altında.</p>
    </div>
</section>

<!-- İSTATİSTİK BANTI -->
<section class="about-stats-strip">
    <div class="stats-grid">
        <div class="stat-box">
            <div class="number">81 İl</div>
            <div class="label">Tüm Türkiye Kapsamı</div>
        </div>
        <div class="stat-box">
            <div class="number">922</div>
            <div class="label">İlçede Aktif Çekici Filosu</div>
        </div>
        <div class="stat-box">
            <div class="number">15 - 30</div>
            <div class="label">Dakika Ortalama Varış Süresi</div>
        </div>
        <div class="stat-box">
            <div class="number">%0</div>
            <div class="label">Aracı ve Çağrı Merkezi Komisyonu</div>
        </div>
    </div>
</section>

<!-- HİKAYEMİZ & MİSYONUMUZ -->
<section class="about-story-section">
    <div class="story-wrapper">
        <div class="story-image-wrap">
            <img src="https://images.unsplash.com/photo-1613214149922-f1809c99b414?auto=format&fit=crop&w=900&q=80" alt="Yol Yardım Ekibi">
            <div class="story-badge-float">
                <div class="badge-icon">
                    🚨
                </div>
                <div class="badge-text">
                    <h4>7/24 Aktif Kurtarma Filosu</h4>
                    <p>Gece gündüz nöbetçi ekipler görevde</p>
                </div>
            </div>
        </div>

        <div class="story-content">
            <span class="sub-title">BİZ KİMİZ?</span>
            <h2>Aracıları Ortadan Kaldırdık, Sizi Doğrudan Çekici Ustasıyla Buluşturuyoruz</h2>
            <p><strong>Yol Yardım Merkezi</strong>, sürücülerin yolda kaldığı en stresli anlarda karşılaştığı yüksek çağrı merkezi komisyonları, saatler süren beklemeler ve belirsiz fiyat politikasını kökten değiştirmek amacıyla kuruldu.</p>
            <p>Platformumuz üzerinden şehrinizi ve ilçenizi seçtiğinizde, doğrudan o bölgede sahada hazır bekleyen oto çekici esnafının telefon numarasına, WhatsApp hattına ve araç donanımına ulaşırsınız. Telefonda görüştüğünüz kişi doğrudan yanınıza gelecek olan ustadır.</p>

            <ul class="story-checklist">
                <li><span class="check-icon">✓</span> <span>Aracı çağrı merkezi olmadan doğrudan esnafla iletişim</span></li>
                <li><span class="check-icon">✓</span> <span>Tek dokunuşla WhatsApp üzerinden anlık GPS konumu gönderme</span></li>
                <li><span class="check-icon">✓</span> <span>%100 Emtia nakliyat sigortalı ve kayar kasalı güvenli taşıma</span></li>
                <li><span class="check-icon">✓</span> <span>Şeffaf fiyatlandırma ve yerinde mobil POS / kredi kartı ile ödeme</span></li>
            </ul>
        </div>
    </div>
</section>

<!-- DEĞERLERİMİZ -->
<section class="about-values-section">
    <div class="values-container">
        <div class="section-center-head">
            <span class="sub-title">TEMEL İLKELERİMİZ</span>
            <h2>Bizi Farklı Kılan Değerlerimiz</h2>
            <p>Yolda kalan sürücülerle işini titizlikle yapan çekici esnafını dürüst, şeffaf ve hızlı bir sistemde buluşturuyoruz.</p>
        </div>

        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #741316, #991b1b);">
                    🛡️
                </div>
                <h3>Sıfır Komisyon & Şeffaflık</h3>
                <p>Sürücüler ve kurtarıcılar arasında aracılık ücreti veya gizli komisyon almıyoruz. Fiyatı doğrudan hizmeti veren ustayla belirlersiniz.</p>
            </div>

            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #FA5343, #ea580c);">
                    ⚡
                </div>
                <h3>15-30 Dk Hızlı Müdahale</h3>
                <p>En yakın lokasyondaki nöbetçi kurtarıcıyı anında listeleyerek bekleme sürenizi minimuma indiriyoruz.</p>
            </div>

            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    📍
                </div>
                <h3>GPS ile Anında Konum</h3>
                <p>Yol kenarında adres tarif etme zahmetine son. Tek tuşla enlem ve boylam koordinatlarınızı çekicinin WhatsApp'ına gönderin.</p>
            </div>

            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #0284c7, #2563eb);">
                    <?php echo mis360_tow_symbol(); ?>
                </div>
                <h3>Geniş Araç Filosu</h3>
                <p>Kayar kasa, ahtapot vinç, sıfır araç taşıyıcı ve motosiklet aparatlı kurtarıcılarla her tür araca özel çözüm sunuyoruz.</p>
            </div>

            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    🔒
                </div>
                <h3>%100 Kaskolu Taşıma</h3>
                <p>Kayıtlı firmalarımızın araçları resmi emtia nakliyat sigortası teminatı altında olup aracınız sıfır hasar güvencesiyle taşınır.</p>
            </div>

            <div class="value-card">
                <div class="value-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    🕒
                </div>
                <h3>7/24 Kesintisiz Hizmet</h3>
                <p>Gecenin bir yarısı, hafta sonu veya resmi tatil fark etmeksizin nöbetçi çekici ağımız kesintisiz görev başındadır.</p>
            </div>
        </div>
    </div>
</section>

<!-- NASIL ÇALIŞIR? -->
<section class="about-how-section">
    <div class="how-container">
        <div class="section-center-head">
            <span class="sub-title">BASİT 3 ADIM</span>
            <h2>Nasıl Çalışır?</h2>
            <p>Yol Yardım Merkezi ile en yakın çekiciye ulaşmak sadece 3 adımdan ibarettir.</p>
        </div>

        <div class="how-grid">
            <div class="how-card">
                <div class="how-step">1</div>
                <h4>Şehrinizi ve İlçenizi Seçin</h4>
                <p>81 İl Rehberimizden yolda kaldığınız ili ve ilçeyi seçerek bölgenizdeki aktif kurtarıcıları filtreleyin.</p>
            </div>

            <div class="how-card">
                <div class="how-step">2</div>
                <h4>En Yakın Ekibi Belirleyin</h4>
                <p>Firmaların varış süresini, araç donanımını ve puanlarını inceleyerek size en uygun ekibi seçin.</p>
            </div>

            <div class="how-card">
                <div class="how-step">3</div>
                <h4>Doğrudan Arayın veya Konum Atın</h4>
                <p>Tek dokunuşla firmayı arayın veya WhatsApp üzerinden anlık GPS konumunuzu ileterek yola çıkmasını sağlayın.</p>
            </div>
        </div>
    </div>
</section>

<!-- ÇAĞRI BÖLÜMÜ (CTA) -->
<section class="about-cta-section">
    <div class="cta-box">
        <h2>Çekici veya Yol Yardım Firmanız mı Var?</h2>
        <p>Türkiye'nin en hızlı büyüyen bağımsız yol yardım rehberinde yerinizi lansmana özel ücretsiz alın, bölgenizdeki yüzlerce araç sahibine doğrudan ulaşın.</p>
        <div class="cta-buttons">
            <a href="<?php echo esc_url(home_url('/firma-ekle/')); ?>" class="cta-btn-primary">
                ➕ Firmanızı Ekleyin · Lansmana Özel Ücretsiz
            </a>
            <a href="<?php echo esc_url(home_url('/firmalar/')); ?>" class="cta-btn-secondary">
                <?php echo mis360_tow_symbol(); ?> Çekici Firmalarını İncele
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>
