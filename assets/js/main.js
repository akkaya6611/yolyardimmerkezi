/**
 * Yol Yardım Merkezi - Canlı JavaScript ve Typed Efekti
 */

document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    // 1. Daktilo (Typed.js) Efekti - Ekran Görüntüsü ile Birebir
    var typedEl = document.querySelector('.typed-words');
    if (typedEl) {
        var phrases = [
            "Ankara'daki Yol Yardım Firmaları",
            "Yozgat'taki Yol Yardım Firmaları",
            "İstanbul'daki Çekici Firmaları",
            "Kayseri'deki Nöbetçi Kurtarıcılar",
            "İzmir'deki Oto Yol Yardım Noktaları"
        ];
        var phraseIndex = 0;
        var letterIndex = 0;
        var isDeleting = false;
        var typingSpeed = 90;

        function type() {
            var currentPhrase = phrases[phraseIndex];
            
            if (isDeleting) {
                typedEl.textContent = currentPhrase.substring(0, letterIndex - 1);
                letterIndex--;
                typingSpeed = 40;
            } else {
                typedEl.textContent = currentPhrase.substring(0, letterIndex + 1);
                letterIndex++;
                typingSpeed = 90;
            }

            if (!isDeleting && letterIndex === currentPhrase.length) {
                isDeleting = true;
                typingSpeed = 2500; // Cümle tamamlanınca bekleme
            } else if (isDeleting && letterIndex === 0) {
                isDeleting = false;
                phraseIndex = (phraseIndex + 1) % phrases.length;
                typingSpeed = 500;
            }

            setTimeout(type, typingSpeed);
        }

        setTimeout(type, 800);
    }

    // 2. Mobil Menü Açma / Kapatma (Off-Canvas Drawer)
    var hamburgerBtn = document.querySelector('.js-toggle-mobile-menu');
    var mobileDrawer = document.getElementById('yymMobileDrawer');
    var closeBtns = document.querySelectorAll('.js-close-mobile-menu');

    if (hamburgerBtn && mobileDrawer) {
        hamburgerBtn.addEventListener('click', function (e) {
            e.preventDefault();
            mobileDrawer.classList.toggle('is-active');
            if (mobileDrawer.classList.contains('is-active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });

        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                mobileDrawer.classList.remove('is-active');
                document.body.style.overflow = '';
            });
        });
    }


    // 3. Firma Kategori Filtre Sekmeleri
    var catTabs = document.querySelectorAll('.lst-cat-tab');
    var carCards = document.querySelectorAll('.lst-car-card');

    catTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var filter = tab.getAttribute('data-filter');

            catTabs.forEach(function (t) { t.classList.remove('is-active'); });
            tab.classList.add('is-active');

            carCards.forEach(function (card) {
                var category = card.getAttribute('data-category');
                if (filter === 'all' || category === filter) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 4. Favorilere Ekleme Butonları (Kalp İkonu)
    var favButtons = document.querySelectorAll('.lst-btn-favorite');
    favButtons.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var svg = btn.querySelector('svg');
            var isSaved = btn.getAttribute('data-saved') === 'true';

            if (isSaved) {
                btn.setAttribute('data-saved', 'false');
                svg.setAttribute('fill', 'none');
                btn.style.color = 'var(--yym-muted)';
            } else {
                btn.setAttribute('data-saved', 'true');
                svg.setAttribute('fill', '#FA5343');
                svg.setAttribute('stroke', '#FA5343');
                btn.style.color = '#FA5343';
            }
        });
    });

    // 5. Yumuşak Kaydırma (Smooth Scroll)
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var targetId = this.getAttribute('href');
            if (targetId === '#' || targetId.length <= 1) return;

            var targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                var headerOffset = 90;
                var elementPosition = targetElement.getBoundingClientRect().top;
                var offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                if (navElement && navElement.classList.contains('is-open')) {
                    navElement.classList.remove('is-open');
                }
            }
        });
    });
});
