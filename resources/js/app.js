import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// ===== Smooth Scroll via Lenis CDN =====
(function initLenis() {
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/lenis@1.1.18/dist/lenis.min.js';
    script.onload = () => {
        if (typeof Lenis !== 'undefined') {
            const lenis = new Lenis({
                duration: 1.2,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                orientation: 'vertical',
                smoothWheel: true,
            });
            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);
        }
    };
    document.head.appendChild(script);
})();

// ===== Header Scroll Shadow =====
(function initHeaderScroll() {
    const header = document.getElementById('main-header');
    if (!header) return;
    
    let ticking = false;
    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                if (window.scrollY > 20) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
                ticking = false;
            });
            ticking = true;
        }
    });
})();

// ===== Spotlight Card Effect =====
(function initSpotlight() {
    document.querySelectorAll('.spotlight-card').forEach((card) => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            card.style.setProperty('--mouse-x', x + 'px');
            card.style.setProperty('--mouse-y', y + 'px');
        });
    });
})();

// ===== Scroll Text Reveal (Intersection Observer) =====
(function initReveal() {
    const revealElements = document.querySelectorAll('.reveal-text, .reveal-card');
    if (revealElements.length === 0) return;

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
    );

    revealElements.forEach((el) => observer.observe(el));
})();

// ===== Magnetic Buttons =====
(function initMagneticButtons() {
    document.querySelectorAll('.magnetic-btn').forEach((btn) => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;
            const strength = 8;
            btn.style.transform = `translate(${x / strength}px, ${y / strength}px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0, 0)';
        });
    });
})();

// ===== Promo Carousel Navigation =====
(function initPromoCarousel() {
    const carousel = document.getElementById('promo-carousel');
    const prevBtn = document.getElementById('promo-prev');
    const nextBtn = document.getElementById('promo-next');
    if (!carousel || !prevBtn || !nextBtn) return;

    const scrollAmount = 300;

    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    });

    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    });

    // Touch swipe support
    let startX = 0;
    let isDragging = false;

    carousel.addEventListener('touchstart', (e) => {
        startX = e.touches[0].clientX;
        isDragging = true;
    }, { passive: true });

    carousel.addEventListener('touchend', (e) => {
        if (!isDragging) return;
        isDragging = false;
        const endX = e.changedTouches[0].clientX;
        const diff = startX - endX;
        if (Math.abs(diff) > 50) {
            carousel.scrollBy({ left: diff > 0 ? scrollAmount : -scrollAmount, behavior: 'smooth' });
        }
    }, { passive: true });
})();