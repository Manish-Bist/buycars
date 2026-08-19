/* script.js — BuyCars base JS (adapted from original prototype) */

const menu   = document.querySelector('#menu-btn');
const navbar = document.querySelector('.navbar');

if (menu && navbar) {
  menu.onclick = () => {
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
  };
}

window.onscroll = () => {
  if (menu && navbar) {
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
  }
  const header = document.querySelector('.header');
  if (header) header.classList.toggle('active', window.scrollY > 0);
};

/* Swiper sliders */
if (typeof Swiper !== 'undefined') {
  const cfg = {
    grabCursor: true, centeredSlides: true, spaceBetween: 20, loop: true,
    autoplay: { delay: 9500, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
  };
  document.querySelectorAll('.vehicles-slider').forEach(el => new Swiper(el, cfg));
  document.querySelectorAll('.featured-slider').forEach(el => new Swiper(el, cfg));
  document.querySelectorAll('.review-slider').forEach(el => new Swiper(el, cfg));
}
