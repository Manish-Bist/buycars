/* admin.js — BuyCars admin panel JS */

/* ── Sidebar toggle (mobile) ── */
const sidebarToggle = document.getElementById('sidebarToggle');
const adminSidebar  = document.getElementById('adminSidebar');
if (sidebarToggle && adminSidebar) {
  sidebarToggle.addEventListener('click', () => adminSidebar.classList.toggle('open'));
  document.addEventListener('click', (e) => {
    if (!adminSidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
      adminSidebar.classList.remove('open');
    }
  });
}

/* ── Auto-dismiss flash toast ── */
const flashToast = document.getElementById('flashToast');
if (flashToast) {
  setTimeout(() => {
    flashToast.style.transition = 'opacity .4s ease, transform .4s ease';
    flashToast.style.opacity = '0';
    flashToast.style.transform = 'translateX(14rem)';
    setTimeout(() => flashToast.remove(), 400);
  }, 4000);
}

/* ── Animate bar fills on dashboard ── */
document.querySelectorAll('.bar-fill').forEach(bar => {
  const target = bar.style.width;
  bar.style.width = '0%';
  setTimeout(() => { bar.style.width = target; }, 200);
});
