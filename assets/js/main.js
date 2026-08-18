document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.getElementById('navToggle');
  const siteNav = document.getElementById('siteNav');
  if (navToggle && siteNav) {
    navToggle.addEventListener('click', () => {
      siteNav.classList.toggle('open');
      const expanded = siteNav.classList.contains('open');
      navToggle.setAttribute('aria-expanded', String(expanded));
    });
  }

  const adminToggle = document.getElementById('adminNavToggle');
  const adminSidebar = document.getElementById('adminSidebar');
  if (adminToggle && adminSidebar) {
    adminToggle.addEventListener('click', () => {
      adminSidebar.classList.toggle('open');
    });
  }

  initScrollReveal();
  initCounters();
  initFaqAccordion();
});

// Fades/slides elements into view as they scroll into the viewport.
// Add class="reveal" to any element, optionally with data-reveal-delay="150"
// (milliseconds) to stagger a group of siblings.
function initScrollReveal() {
  const items = document.querySelectorAll('.reveal');
  if (!items.length) return;

  if (!('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('revealed'));
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const delay = el.getAttribute('data-reveal-delay') || 0;
        setTimeout(() => el.classList.add('revealed'), delay);
        obs.unobserve(el);
      });
    },
    { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
  );

  items.forEach((el) => observer.observe(el));
}

// Animates elements like <span class="counter" data-count-to="15000"
// data-suffix="+">0</span> counting up from 0 once they scroll into view.
function initCounters() {
  const counters = document.querySelectorAll('.counter');
  if (!counters.length) return;

  const animate = (el) => {
    const target = parseInt(el.getAttribute('data-count-to') || '0', 10);
    const suffix = el.getAttribute('data-suffix') || '';
    // Plain numbers like a calendar year shouldn't get a "2,015" thousands
    // separator — data-plain="true" opts out of toLocaleString().
    const plain = el.getAttribute('data-plain') === 'true';
    const duration = 1400;
    const start = performance.now();

    function tick(now) {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      const value = Math.round(target * eased);
      el.textContent = (plain ? value.toString() : value.toLocaleString()) + suffix;
      if (progress < 1) requestAnimationFrame(tick);
    }
    requestAnimationFrame(tick);
  };

  if (!('IntersectionObserver' in window)) {
    counters.forEach(animate);
    return;
  }

  const observer = new IntersectionObserver(
    (entries, obs) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        animate(entry.target);
        obs.unobserve(entry.target);
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((el) => observer.observe(el));
}

// Simple accordion: clicking a .faq-question toggles its sibling .faq-answer
// open/closed, and closes any other open item in the same .faq-list.
function initFaqAccordion() {
  const faqItems = document.querySelectorAll('.faq-item');
  if (!faqItems.length) return;

  faqItems.forEach((item) => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      item.closest('.faq-list').querySelectorAll('.faq-item.open').forEach((openItem) => {
        openItem.classList.remove('open');
      });
      if (!isOpen) {
        item.classList.add('open');
      }
    });
  });
}
