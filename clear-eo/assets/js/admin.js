/* CLEAR-EO fields under the editor: image pickers and repeatable rows (speakers, panel sections) */
(() => {
  document.addEventListener('click', e => {
    const media = e.target.closest('[data-media]');
    if (media && e.target.closest('[data-pick]')) {
      e.preventDefault();
      const frame = wp.media({ title: 'Choose image', library: { type: 'image' }, multiple: false });
      frame.on('select', () => {
        const a = frame.state().get('selection').first().toJSON();
        const img = media.querySelector('img');
        media.querySelector('input').value = a.id;
        img.src = (a.sizes && (a.sizes.medium || a.sizes.full) || a).url;
        img.hidden = false;
        media.querySelector('[data-clear]').hidden = false;
      });
      frame.open();
      return;
    }
    if (media && e.target.closest('[data-clear]')) {
      e.preventDefault();
      media.querySelector('input').value = '';
      media.querySelector('img').hidden = true;
      e.target.closest('[data-clear]').hidden = true;
      return;
    }

    const rep = e.target.closest('[data-repeater]');
    if (!rep) return;
    const rows = rep.querySelector('.ce-rows');
    const row = e.target.closest('.ce-row');
    if (e.target.closest('[data-add]')) {
      e.preventDefault();
      // Names only need to be unique: the server re-numbers the rows in the order they are sent
      const html = rep.querySelector('template').innerHTML.replace(/__i__/g, 'n' + Date.now());
      rows.insertAdjacentHTML('beforeend', html);
      rows.lastElementChild.querySelector('input, textarea, select')?.focus();
    } else if (row && e.target.closest('[data-remove]')) {
      e.preventDefault();
      row.remove();
    } else if (row && e.target.closest('[data-up]')) {
      e.preventDefault();
      row.previousElementSibling && rows.insertBefore(row, row.previousElementSibling);
    } else if (row && e.target.closest('[data-down]')) {
      e.preventDefault();
      row.nextElementSibling && rows.insertBefore(row.nextElementSibling, row);
    }
  });
})();
