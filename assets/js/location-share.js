/**
 * Yol Yardım Merkezi - HTML5 Konum Paylaşımı ve WhatsApp Entegrasyonu
 */

(function () {
    'use strict';

    // yymData nesnesi functions.php tarafından wp_localize_script ile aktarılır
    var settings = window.yymData || {
        whatsapp: '905320000000',
        phoneRaw: '+908503000000',
        defaultLocationMsg: 'Merhaba, acil yol yardımına ve çekiciye ihtiyacım var. Bulunduğum konum:',
        locatingText: 'Konum alınıyor...',
        locationErrorText: 'Konum alınamadı. WhatsApp açılıyor...'
    };

    /**
     * Konum alıp doğrudan WhatsApp'a yönlendirir
     */
    function shareLocationViaWhatsApp(buttonEl) {
        var originalHtml = buttonEl ? buttonEl.innerHTML : '';

        if (buttonEl) {
            buttonEl.innerHTML = '<span class="yym-pulse-dot"></span> ' + settings.locatingText;
            buttonEl.disabled = true;
        }

        function restoreButton() {
            if (buttonEl) {
                buttonEl.innerHTML = originalHtml;
                buttonEl.disabled = false;
            }
        }

        if (!navigator.geolocation) {
            // Geolocation desteklenmiyor, doğrudan WhatsApp mesajı aç
            window.location.href = 'https://wa.me/' + settings.whatsapp + '?text=' + encodeURIComponent('Merhaba, acil yol yardımına ve çekiciye ihtiyacım var.');
            restoreButton();
            return;
        }

        var geoOptions = {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        };

        navigator.geolocation.getCurrentPosition(
            function (position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;
                var mapsUrl = 'https://maps.google.com/?q=' + lat + ',' + lng;
                var fullMessage = settings.defaultLocationMsg + '\n📍 ' + mapsUrl;

                var waUrl = 'https://wa.me/' + settings.whatsapp + '?text=' + encodeURIComponent(fullMessage);
                window.location.href = waUrl;
                restoreButton();
            },
            function (error) {
                console.warn('Geolocation hatası / izin verilmedi:', error.message);
                // İzin verilmediğinde veya hata olduğunda kullanıcıyı mağdur etmeden doğrudan WhatsApp'a aktar
                var fallbackMsg = 'Merhaba, acil çekici ve yol yardımına ihtiyacım var. Bulunduğum yeri tarif ediyorum: ';
                var waUrl = 'https://wa.me/' + settings.whatsapp + '?text=' + encodeURIComponent(fallbackMsg);
                window.location.href = waUrl;
                restoreButton();
            },
            geoOptions
        );
    }

    /**
     * Form içindeki konum kutusunu GPS koordinatlarıyla otomatik doldurur
     */
    function autoFillFormLocation(buttonEl) {
        var fromInput = document.getElementById('yym_from');
        if (!fromInput) return;

        var originalText = buttonEl.textContent;
        buttonEl.textContent = '⏳ Alınıyor...';
        buttonEl.disabled = true;

        if (!navigator.geolocation) {
            fromInput.placeholder = 'Tarayıcınız konum desteklemiyor';
            buttonEl.textContent = originalText;
            buttonEl.disabled = false;
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function (position) {
                var lat = position.coords.latitude.toFixed(6);
                var lng = position.coords.longitude.toFixed(6);
                fromInput.value = 'GPS: ' + lat + ', ' + lng + ' (Harita Konumu)';
                fromInput.style.borderColor = '#10b981';
                buttonEl.textContent = '✓ Alındı';
                buttonEl.disabled = false;
            },
            function () {
                fromInput.placeholder = 'Lütfen ilçe veya cadde yazınız';
                buttonEl.textContent = originalText;
                buttonEl.disabled = false;
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    // Olay Dinleyicileri (Event Listeners)
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Genel "Konum Gönder" Butonları
        var shareBtns = document.querySelectorAll('.js-share-location-btn');
        shareBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                shareLocationViaWhatsApp(btn);
            });
        });

        // 1b. Doğrudan Firmaya "Konum Gönder" Butonları (.js-share-firm-location-btn)
        var firmShareBtns = document.querySelectorAll('.js-share-firm-location-btn');
        firmShareBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var firmPhone = btn.getAttribute('data-phone') || settings.whatsapp;
                var firmName = btn.getAttribute('data-firm') || 'Firma';
                var origText = btn.innerHTML;

                btn.innerHTML = '<span class="yym-pulse-dot"></span> GPS Konum Alınıyor...';
                btn.disabled = true;

                if (!navigator.geolocation) {
                    window.location.href = 'https://wa.me/' + firmPhone + '?text=' + encodeURIComponent('Merhaba ' + firmName + ', acil çekici ve yol yardımına ihtiyacım var.');
                    btn.innerHTML = origText;
                    btn.disabled = false;
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        var lat = pos.coords.latitude;
                        var lng = pos.coords.longitude;
                        var mapsUrl = 'https://maps.google.com/?q=' + lat + ',' + lng;
                        var msg = 'Merhaba ' + firmName + ', Yol Yardım Merkezi üzerinden size ulaşıyorum. Acil yardıma ihtiyacım var.\n📍 Anlık Konumum: ' + mapsUrl;
                        window.location.href = 'https://wa.me/' + firmPhone + '?text=' + encodeURIComponent(msg);
                        btn.innerHTML = origText;
                        btn.disabled = false;
                    },
                    function (err) {
                        var fallback = 'Merhaba ' + firmName + ', acil çekici ve yol yardımına ihtiyacım var. Bulunduğum yeri WhatsApp üzerinden tarif ediyorum:';
                        window.location.href = 'https://wa.me/' + firmPhone + '?text=' + encodeURIComponent(fallback);
                        btn.innerHTML = origText;
                        btn.disabled = false;
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            });
        });

        // 2. Form İçi "Konum Al" Butonları
        var autoFillBtns = document.querySelectorAll('.js-auto-fill-location');
        autoFillBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                autoFillFormLocation(btn);
            });
        });
    });
})();
