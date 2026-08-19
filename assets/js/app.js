/* app.js — BuyCars site-wide JS */

/* ── Page Loader ── */
window.addEventListener('load', () => {
  const loader = document.getElementById('pageLoader');
  if (loader) {
    loader.classList.add('hidden');
  }
});

/* ── AOS (Animate On Scroll) init ── */
if (typeof AOS !== 'undefined') {
  AOS.init({ duration: 700, once: true, offset: 60 });
}

/* ── Mobile menu (preserve original toggle) ── */
const menuBtn = document.getElementById('menu-btn');
const navbar  = document.querySelector('.navbar');
if (menuBtn && navbar) {
  menuBtn.onclick = () => {
    menuBtn.classList.toggle('fa-times');
    navbar.classList.toggle('active');
  };
}

/* ── Sticky header ── */
window.addEventListener('scroll', () => {
  const header = document.querySelector('.header');
  if (header) header.classList.toggle('active', window.scrollY > 0);

  // Back-to-top button
  const btn = document.getElementById('backToTop');
  if (btn) btn.classList.toggle('visible', window.scrollY > 400);

  // close mobile menu on scroll
  if (menuBtn && navbar) {
    menuBtn.classList.remove('fa-times');
    navbar.classList.remove('active');
  }
});

/* ── Back to Top ── */
const backToTopBtn = document.getElementById('backToTop');
if (backToTopBtn) {
  backToTopBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

/* ── Toast auto-dismiss ── */
function autoCloseToast() {
  const toast = document.getElementById('flashToast');
  if (!toast) return;
  setTimeout(() => {
    toast.style.animation = 'slideInRight .4s ease reverse forwards';
    setTimeout(() => toast.remove(), 400);
  }, 4000);
}
autoCloseToast();

/* ── Swiper sliders (already in script.js; guard in case both load) ── */
if (typeof Swiper !== 'undefined' && !window._swipersInitialized) {
  window._swipersInitialized = true;
  const swiperCfg = {
    grabCursor: true, centeredSlides: true, spaceBetween: 20, loop: true,
    autoplay: { delay: 9500, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: { 0: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
  };
  document.querySelectorAll('.vehicles-slider, .featured-slider, .review-slider')
    .forEach(el => new Swiper(el, swiperCfg));
}

/* ── Wishlist AJAX toggle ── */
document.querySelectorAll('.wish-btn, .wish-btn-lg').forEach(btn => {
  btn.addEventListener('click', async function (e) {
    e.preventDefault();
    const carId = this.dataset.car;
    if (!carId) return;

    try {
      const res  = await fetch('toggle-wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'car_id=' + encodeURIComponent(carId),
      });
      const data = await res.json();

      if (data.error === 'login_required') {
        window.location.href = 'login.php';
        return;
      }

      if (data.ok) {
        const added = data.action === 'added';
        this.classList.toggle('active', added);
        const icon = this.querySelector('i');
        if (icon) {
          icon.className = added ? 'bx bxs-heart' : 'bx bx-heart';
        }
        if (this.classList.contains('wish-btn-lg')) {
          this.innerHTML = (added
            ? '<i class="bx bxs-heart"></i> Saved'
            : '<i class="bx bx-heart"></i> Save to Wishlist');
        }
        showToast(added ? 'Added to wishlist!' : 'Removed from wishlist.', added ? 'success' : 'error');
      }
    } catch (err) {
      console.error('Wishlist error:', err);
    }
  });
});

/* ── Image upload preview ── */
const imgInput = document.getElementById('imagesInput');
const previewGrid = document.getElementById('previewGrid');
const uploadBox = document.getElementById('uploadBox');

if (imgInput && previewGrid) {
  imgInput.addEventListener('change', function () {
    previewGrid.innerHTML = '';
    Array.from(this.files).forEach(file => {
      const reader = new FileReader();
      reader.onload = e => {
        const div = document.createElement('div');
        div.className = 'preview-item';
        div.innerHTML = `<img src="${e.target.result}" alt="">`;
        previewGrid.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  });
}
if (uploadBox) {
  uploadBox.addEventListener('dragover', e => { e.preventDefault(); uploadBox.classList.add('drag-over'); });
  uploadBox.addEventListener('dragleave', () => uploadBox.classList.remove('drag-over'));
  uploadBox.addEventListener('drop', e => { e.preventDefault(); uploadBox.classList.remove('drag-over'); if (imgInput) { imgInput.files = e.dataTransfer.files; imgInput.dispatchEvent(new Event('change')); } });
}

/* ── Close modal when clicking overlay ── */
document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', function (e) {
    if (e.target === this) this.classList.remove('open');
  });
});

/* ── Keyboard ESC closes modal ── */
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
  }
});

/* ── Inline toast helper ── */
function showToast(message, type = 'success') {
  const container = document.querySelector('.toast-container') || (() => {
    const c = document.createElement('div');
    c.className = 'toast-container';
    document.body.appendChild(c);
    return c;
  })();

  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `
    <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-error-circle'}'></i>
    <span>${message}</span>
    <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
  `;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(14rem)';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}

/* ── Admin sidebar toggle (mobile) ── */
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar  = document.getElementById('adminSidebar');
if (sidebarToggle && adminSidebar) {
  sidebarToggle.addEventListener('click', () => {
    adminSidebar.classList.toggle('open');
  });
}

/* ── Navbar active link highlighting ── */
const currentPath = window.location.pathname.split('/').pop();
document.querySelectorAll('.navbar a').forEach(link => {
  const href = link.getAttribute('href').split('/').pop().split('#')[0];
  if (href && href === currentPath) link.style.color = 'var(--orangeclr)';
});
