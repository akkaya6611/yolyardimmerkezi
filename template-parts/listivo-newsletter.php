<?php
/**
 * Listivo Demo 5 - Yol Yardım Merkezi Bülten & Bilgilendirme
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="lst-newsletter-section">
    <div class="lst-container">
        <div class="lst-newsletter-card">
            <div class="lst-newsletter-text">
                <span class="lst-pill-sub lst-pill-white">Haberdar Olun</span>
                <h2 class="lst-newsletter-title">Yol Yardım Merkezi Bültenine Katılın</h2>
                <p class="lst-newsletter-desc">
                    81 ilde en güncel çekici tarifeleri, kışlık/yazlık araç bakım rehberleri ve acil durum güvenlik tavsiyeleri e-postanıza gelsin.
                </p>
            </div>

            <form id="lstNewsletterForm" class="lst-newsletter-form" onsubmit="event.preventDefault(); alert('Bültenimize başarıyla abone oldunuz!');">
                <div class="lst-newsletter-input-wrap">
                    <input type="email" placeholder="E-posta adresinizi giriniz..." required class="lst-newsletter-input">
                    <button type="submit" class="lst-btn lst-btn-coral lst-btn-newsletter">
                        <span>Abone Ol</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
