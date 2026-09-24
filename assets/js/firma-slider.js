/**
 * MIS 360 - Firma Slider JavaScript
 * Handles smooth scrolling and navigation buttons for [firma_slider]
 */
document.addEventListener('DOMContentLoaded', function () {
    function initFirmaSliders() {
        var wrappers = document.querySelectorAll('.yym-firma-slider-wrapper');
        wrappers.forEach(function (wrapper) {
            var track = wrapper.querySelector('.yym-fslider-track');
            var btnPrev = wrapper.querySelector('.yym-fslider-prev');
            var btnNext = wrapper.querySelector('.yym-fslider-next');

            if (!track || !btnPrev || !btnNext) {
                return;
            }

            function updateButtons() {
                var maxScrollLeft = track.scrollWidth - track.clientWidth;
                btnPrev.disabled = track.scrollLeft <= 5;
                btnNext.disabled = track.scrollLeft >= maxScrollLeft - 5;
            }

            btnPrev.addEventListener('click', function (e) {
                e.preventDefault();
                var slide = track.querySelector('.yym-fslider-slide');
                var scrollAmount = slide ? (slide.offsetWidth + 18) : 320;
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });

            btnNext.addEventListener('click', function (e) {
                e.preventDefault();
                var slide = track.querySelector('.yym-fslider-slide');
                var scrollAmount = slide ? (slide.offsetWidth + 18) : 320;
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });

            track.addEventListener('scroll', function () {
                window.requestAnimationFrame(updateButtons);
            });

            window.addEventListener('resize', updateButtons);
            updateButtons();
        });
    }

    initFirmaSliders();
});
