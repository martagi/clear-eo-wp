/* CLEAR-EO theme: light/dark toggle, menu, application tabs, What's new filters and reader window,
   scroll-spy and scroll reveal. The page works without it: cards then link to each post's own page. */
(() => {
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $ = id => document.getElementById(id);

  // Theme toggle (the saved choice is applied in <head> before the page paints)
  const root = document.documentElement;
  $('themeBtn')?.addEventListener('click', () => {
    const dark = root.dataset.theme ? root.dataset.theme === 'dark' : matchMedia('(prefers-color-scheme: dark)').matches;
    root.dataset.theme = dark ? 'light' : 'dark';
    try { localStorage.setItem('clear-eo-theme', root.dataset.theme); } catch (e) {}
  });

  // Nav: scrolled state, mobile menu
  const nav = $('nav');
  const onScroll = () => nav.classList.toggle('scrolled', scrollY > 40);
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  const menuBtn = $('menuBtn'), links = $('navLinks');
  menuBtn.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    menuBtn.setAttribute('aria-expanded', open);
  });
  links.addEventListener('click', e => {
    if (e.target.closest('a')) { links.classList.remove('open'); menuBtn.setAttribute('aria-expanded', 'false'); }
  });

  // Applications: ARIA tabs
  const tabs = [...document.querySelectorAll('[role="tab"]')];
  const select = t => tabs.forEach(x => {
    const on = x === t;
    x.setAttribute('aria-selected', on);
    x.tabIndex = on ? 0 : -1;
    $(x.getAttribute('aria-controls')).hidden = !on;
  });
  tabs.forEach((t, i) => {
    t.addEventListener('click', () => select(t));
    t.addEventListener('keydown', ev => {
      let j = null;
      if (ev.key === 'ArrowRight') j = (i + 1) % tabs.length;
      if (ev.key === 'ArrowLeft') j = (i - 1 + tabs.length) % tabs.length;
      if (ev.key === 'Home') j = 0;
      if (ev.key === 'End') j = tabs.length - 1;
      if (j !== null) { ev.preventDefault(); tabs[j].focus(); select(tabs[j]); }
    });
  });

  // What's new filters
  const grid = $('newsGrid');
  const filters = [...document.querySelectorAll('.filter')];
  filters.forEach(f => f.addEventListener('click', () => {
    filters.forEach(x => x.setAttribute('aria-pressed', x === f));
    const v = f.dataset.f;
    grid?.querySelectorAll('.card').forEach(c => {
      c.hidden = !(v === 'all' || c.dataset.t === v);
      c.classList.toggle('feature', v === 'all' && c === grid.firstElementChild);
    });
  }));

  // Reader window. On the home page every card with a page has its article in a <template data-article>;
  // the address becomes #news/<date-title> so it can be shared. Elsewhere the card opens the post's page.
  const dlg = $('article');
  const article = s => [...document.querySelectorAll('template[data-article]')].find(t => t.dataset.article === s);
  const openArticle = () => {
    const m = location.hash.match(/^#news\/(.+)$/);
    const t = m && article(decodeURIComponent(m[1]));
    if (!t) { if (dlg.open) dlg.close(); return; }
    $('articleBody').replaceChildren(t.content.cloneNode(true));
    dlg.classList.toggle('has-header', !!dlg.querySelector('.article-header'));
    if (!dlg.open) dlg.showModal();
    dlg.scrollTop = 0;
  };
  if (dlg && typeof dlg.showModal === 'function') {
    document.addEventListener('click', e => {
      const card = e.target.closest('a[data-dialog]');
      if (!card || e.metaKey || e.ctrlKey || e.shiftKey || e.button || !article(card.dataset.dialog)) return;
      e.preventDefault();
      location.hash = 'news/' + card.dataset.dialog;
    });
    dlg.addEventListener('close', () => {
      if (location.hash.startsWith('#news/')) history.replaceState(null, '', '#news');
    });
    dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });
    $('articleClose').addEventListener('click', () => dlg.close());
    addEventListener('hashchange', openArticle);
    openArticle();
  }

  // Scroll-spy: highlight the menu link of the section in view
  const navA = [...links.querySelectorAll('a')].filter(a => a.hash && a.pathname === location.pathname);
  const sections = navA.map(a => document.getElementById(a.hash.slice(1))).filter(Boolean);
  if (sections.length && 'IntersectionObserver' in window) {
    const spy = new IntersectionObserver(entries => entries.forEach(en => {
      if (en.isIntersecting) navA.forEach(a => a.setAttribute('aria-current', a.hash === '#' + en.target.id));
    }), { rootMargin: '-45% 0px -50% 0px' });
    sections.forEach(s => spy.observe(s));
  }

  // Scroll reveal
  const els = document.querySelectorAll('.reveal');
  if (reduce || !('IntersectionObserver' in window)) { els.forEach(el => el.classList.add('in')); return; }
  const io = new IntersectionObserver(entries => entries.forEach(en => {
    if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
  }), { threshold: 0.12 });
  els.forEach(el => io.observe(el));
})();
