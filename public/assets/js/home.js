(() => {
    const hasSwiperContainer = !!document.querySelector('.swiper');
    if (typeof window !== 'undefined' && window.Swiper && hasSwiperContainer) {
        const swiper = new Swiper('.swiper', {
            slidesPerView: 'auto',
            spaceBetween: 16,
            pagination: { el: '.swiper-pagination', clickable: true },
        });
        window.__appSwiper = swiper;
    }
})();