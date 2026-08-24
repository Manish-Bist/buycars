/* script.js — BuyCars base */
document.addEventListener('DOMContentLoaded', () => {
  const menu   = document.querySelector('#menu-btn');
  const navbar = document.querySelector('.navbar');
  if (menu && navbar) {
    menu.onclick = () => {
      menu.classList.toggle('fa-times');
      navbar.classList.toggle('active');
    };
  }
});

window.onscroll = () => {
  const menu   = document.querySelector('#menu-btn');
  const navbar = document.querySelector('.navbar');
  if (menu && navbar) {
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
  }
  const header = document.querySelector('.header');
  if (header) header.classList.toggle('active', window.scrollY > 0);
};
