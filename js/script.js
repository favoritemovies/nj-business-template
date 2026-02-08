document.addEventListener('DOMContentLoaded', () => {
  // ===== Mobile menu =====
  const btn  = document.querySelector('.nav-toggle');
  const list = document.querySelector('.site-nav ul');
  if (btn && list) {
    btn.addEventListener('click', () => {
      list.classList.toggle('open');
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
    });
    list.addEventListener('click', (e) => {
      if (e.target.closest('a')) {
        list.classList.remove('open');
        btn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ===== Date field hint (Inquiry) =====
  const dateWrap  = document.querySelector('.date-field');
  const dateInput = dateWrap ? dateWrap.querySelector('input[type="date"]') : null;
  if (dateWrap && dateInput) {
    const sync = () => dateWrap.classList.toggle('filled', !!dateInput.value);
    dateInput.addEventListener('focus',  () => dateWrap.classList.add('filled'));
    dateInput.addEventListener('blur',   sync);
    dateInput.addEventListener('input',  sync);
    dateInput.addEventListener('change', sync);
    sync();
  }

  // ===== Gallery: responsive srcset (thumb.php) =====
  const galleryImgs = document.querySelectorAll('.gallery img');
  galleryImgs.forEach(img => {
    const src = img.getAttribute('src') || '';
    if (!src.includes('/thumb.php')) return;

    const m = src.match(/[?&]src=([^&]+)/);
    if (!m) return;

    const enc = m[1]; // already encoded
    img.setAttribute('srcset', [
      `/thumb.php?src=${enc}&w=640 640w`,
      `/thumb.php?src=${enc}&w=1024 1024w`,
      `/thumb.php?src=${enc}&w=1600 1600w`,
      `/thumb.php?src=${enc}&w=2200 2200w`
    ].join(', '));
    img.setAttribute('sizes', '(max-width:640px) 100vw, (max-width:1100px) 50vw, 33vw');
  });

  // ===== Lightbox + swipe =====
  const gallery = document.querySelector('.gallery');
  const lb = document.getElementById('lightbox');
  const lbImg = document.getElementById('lightboxImg');
  if (!gallery || !lb || !lbImg) return;

  // ✅ Находим крестик максимально надёжно (любой из вариантов)
  const closeBtn =
    lb.querySelector('#lightboxClose') ||
    lb.querySelector('[data-lb-close]') ||
    lb.querySelector('.lightbox-close') ||
    lb.querySelector('.close');

  const imgs = Array.from(gallery.querySelectorAll('img'));
  let idx = -1;

  function setImage(i){
    if (!imgs.length) return;
    idx = (i + imgs.length) % imgs.length;

    const src = imgs[idx].getAttribute('src') || '';
    const m = src.match(/[?&]src=([^&]+)/);
    const enc = m ? m[1] : null;

    // крупная версия
    lbImg.src = enc ? `/thumb.php?src=${enc}&w=2200` : src;
  }

  function openAt(i){
    if (!imgs.length) return;
    setImage(i);
    lb.classList.add('open');
    lb.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function close(){
    lb.classList.remove('open');
    lb.setAttribute('aria-hidden', 'true');
    lbImg.src = '';
    document.body.style.overflow = '';
  }

  function next(){ setImage(idx + 1); }
  function prev(){ setImage(idx - 1); }

  imgs.forEach((img, i) => {
    img.style.cursor = 'zoom-in';
    img.addEventListener('click', () => openAt(i));
  });

  // ✅ КРЕСТИК: stopPropagation чтобы клик не мешали слои
  if (closeBtn) {
    // на всякий случай, чтобы не было submit по умолчанию
    if (closeBtn.tagName === 'BUTTON' && !closeBtn.getAttribute('type')) {
      closeBtn.setAttribute('type', 'button');
    }

    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      close();
    });
    closeBtn.addEventListener('touchend', (e) => {
      e.preventDefault();
      e.stopPropagation();
      close();
    }, { passive: false });
  }

  // клик по фону (именно по оверлею) закрывает
  lb.addEventListener('click', (e) => {
    if (e.target === lb) close();
  });

  document.addEventListener('keydown', (e) => {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
  });

  // swipe
  let x0 = null;
  lb.addEventListener('touchstart', (e) => { x0 = e.touches[0].clientX; }, { passive:true });
  lb.addEventListener('touchend', (e) => {
    if (x0 == null) return;
    const x1 = e.changedTouches[0].clientX;
    const dx = x1 - x0;
    x0 = null;
    if (Math.abs(dx) < 40) return;
    if (dx < 0) next(); else prev();
  }, { passive:true });
});
