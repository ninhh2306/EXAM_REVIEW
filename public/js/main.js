// public/js/main.js
// EXAM_REVIEW — Main JavaScript

document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // 1. SCROLL REVEAL
    // =========================================
    const reveals = document.querySelectorAll('.reveal');

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    reveals.forEach(function (el) {
        observer.observe(el);
    });


    // =========================================
    // 2. ACTIVE NAV LINK theo grade_id trên URL
    // =========================================
    var params  = new URLSearchParams(window.location.search);
    var gradeId = params.get('grade_id');

    if (gradeId) {
        document.querySelectorAll('.navbar__nav a').forEach(function (a) {
            if (a.href.includes('grade_id=' + gradeId)) {
                a.classList.add('active');
            }
        });
    }


    // =========================================
    // 3. HERO SLIDER
    // =========================================
    var slides  = document.querySelectorAll('.hero__slide');
    var dots    = document.querySelectorAll('.hero__dot');
    var current = 0;
    var sliderTimer;

    function goToSlide(index) {
        slides[current].classList.remove('hero__slide--active');
        dots[current].classList.remove('hero__dot--active');
        current = index;
        slides[current].classList.add('hero__slide--active');
        dots[current].classList.add('hero__dot--active');
    }

    function nextSlide() {
        goToSlide((current + 1) % slides.length);
    }

    if (slides.length > 0) {
        sliderTimer = setInterval(nextSlide, 4000);
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                clearInterval(sliderTimer);
                goToSlide(parseInt(this.getAttribute('data-index')));
                sliderTimer = setInterval(nextSlide, 4000);
            });
        });
    }


    // =========================================
    // 4. SMOOTH SCROLL cho anchor links (#...)
    // =========================================
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });


    
});