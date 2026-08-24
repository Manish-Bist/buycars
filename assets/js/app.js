/* app.js — BuyCars */

/* ── Page Loader: hide immediately on DOM ready, no waiting for CDN ── */
document.addEventListener('DOMContentLoaded', () => {
  const loader = document.getElementById('pageLoader');
  if (loader) {
    loader.classList.add('hidden');
  }
});
/* Absolute fallback — hides loader after 1.5s no matter what */
setTimeout(() => {
  const loader = document.getElementById('pageLoader');
  if (loader) loader.classList.add('hidden');
}, 1500);

/* ── AOS init (only if library loaded) ── */
document.addEventListener('DOMContentLoaded', () => {
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, offset: 60 });
  }
});

/* ── Mobile menu ── */
document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.getElementById('menu-btn');
  const navbar  = document.querySelector('.navbar');
  if (menuBtn && navbar) {
    menuBtn.onclick = () => {
      menuBtn.classList.toggle('fa-times');
      navbar.classList.toggle('active');
    };
  }
});

/* ── Sticky header + back-to-top ── */
window.addEventListener('scroll', () => {
  const header = document.querySelector('.header');
  if (header) header.classList.toggle('active', window.scrollY > 0);

  const btn = document.getElementById('backToTop');
  if (btn) btn.classList.toggle('visible', window.scrollY > 400);

  const menuBtn = document.getElementById('menu-btn');
  const navbar  = document.querySelector('.navbar');
  if (menuBtn && navbar) {
    menuBtn.classList.remove('fa-times');
    navbar.classList.remove('active');
  }
});

/* ── Back to Top ── */
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('backToTop');
  if (btn) btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
});

/* ── Toast auto-dismiss ── */
document.addEventListener('DOMContentLoaded', () => {
  const toast = document.getElementById('flashToast');
  if (!toast) return;
  setTimeout(() => {
    toast.style.transition = 'opacity .4s, transform .4s';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(14rem)';
    setTimeout(() => toast.remove(), 400);
  }, 4000);
});

/* ── Swiper sliders (init after load so CDN has time) ── */
function initSwipers() {
  if (typeof Swiper === 'undefined') return;
  const cfg = {
    grabCursor: true,
    centeredSlides: true,
    spaceBetween: 20,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    breakpoints: {
      0:    { slidesPerView: 1 },
      768:  { slidesPerView: 2 },
      1024: { slidesPerView: 3 },
    },
  };
  document.querySelectorAll('.vehicles-slider').forEach(el => new Swiper(el, { ...cfg }));
  document.querySelectorAll('.featured-slider').forEach(el => new Swiper(el, { ...cfg }));
  document.querySelectorAll('.review-slider').forEach(el => new Swiper(el, { ...cfg }));
}
/* Try on DOM ready, then again after full load (CDN might still be loading) */
document.addEventListener('DOMContentLoaded', initSwipers);
window.addEventListener('load', initSwipers);

/* ── Wishlist AJAX toggle ── */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.wish-btn, .wish-btn-lg').forEach(btn => {
    btn.addEventListener('click', async function (e) {
      e.preventDefault();
      const carId = this.dataset.car;
      if (!carId) return;
      try {
        const res  = await fetch(window.BASE_URL + 'toggle-wishlist.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'car_id=' + encodeURIComponent(carId),
        });
        const data = await res.json();
        if (data.error === 'login_required') { window.location.href = window.BASE_URL + 'login.php'; return; }
        if (data.ok) {
          const added = data.action === 'added';
          this.classList.toggle('active', added);
          const icon = this.querySelector('i');
          if (icon) icon.className = added ? 'bx bxs-heart' : 'bx bx-heart';
          if (this.classList.contains('wish-btn-lg')) {
            this.innerHTML = added
              ? "<i class='bx bxs-heart'></i> Saved"
              : "<i class='bx bx-heart'></i> Save to Wishlist";
          }
          showToast(added ? 'Added to wishlist!' : 'Removed from wishlist.', added ? 'success' : 'error');
        }
      } catch (err) { console.error('Wishlist error:', err); }
    });
  });
});

/* ── Image upload preview ── */
document.addEventListener('DOMContentLoaded', () => {
  const imgInput   = document.getElementById('imagesInput');
  const previewGrid = document.getElementById('previewGrid');
  const uploadBox  = document.getElementById('uploadBox');
  if (imgInput && previewGrid) {
    imgInput.addEventListener('change', function () {
      previewGrid.innerHTML = '';
      Array.from(this.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
          const div = document.createElement('div');
          div.className = 'preview-item';
          div.innerHTML = '<img src="' + e.target.result + '" alt="">';
          previewGrid.appendChild(div);
        };
        reader.readAsDataURL(file);
      });
    });
  }
  if (uploadBox) {
    uploadBox.addEventListener('dragover', e => { e.preventDefault(); uploadBox.classList.add('drag-over'); });
    uploadBox.addEventListener('dragleave', () => uploadBox.classList.remove('drag-over'));
    uploadBox.addEventListener('drop', e => {
      e.preventDefault(); uploadBox.classList.remove('drag-over');
      if (imgInput) { imgInput.files = e.dataTransfer.files; imgInput.dispatchEvent(new Event('change')); }
    });
  }
});

/* ── Modal close on overlay click / ESC ── */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function (e) {
      if (e.target === this) this.classList.remove('open');
    });
  });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
    }
  });
});

/* ── Admin sidebar toggle ── */
document.addEventListener('DOMContentLoaded', () => {
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    document.addEventListener('click', e => {
      if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }
});

/* ── Inline toast helper ── */
function showToast(message, type) {
  type = type || 'success';
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.innerHTML =
    '<i class="bx ' + (type === 'success' ? 'bx-check-circle' : 'bx-error-circle') + '"></i>' +
    '<span>' + message + '</span>' +
    '<button class="toast-close" onclick="this.parentElement.remove()">&times;</button>';
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(14rem)';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}
