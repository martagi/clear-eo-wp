/* CLEAR-EO fields in the block editor's sidebar (Event details, Webinar details, Application panel, Partner card…).
   They edit the post's registered meta (inc/meta-boxes.php), so they are saved with the post itself. */
(({ plugins, editor, element, components, data, blockEditor }) => {
  const cfg = window.clearEoFields;
  if (!cfg) return;
  const h = element.createElement;
  const { TextControl, TextareaControl, SelectControl, CheckboxControl, Button, BaseControl } = components;
  const { MediaUpload, MediaUploadCheck } = blockEditor;
  const Panel = editor.PluginDocumentSettingPanel || (window.wp.editPost && window.wp.editPost.PluginDocumentSettingPanel);
  const t = cfg.i18n;
  const common = { __nextHasNoMarginBottom: true, __next40pxDefaultSize: true };

  const Media = ({ label, help, value, onChange }) => {
    const media = data.useSelect(s => (value ? s('core').getMedia(value) : null), [value]);
    const url = media && ((media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url) || media.source_url);
    return h(BaseControl, { label, help, __nextHasNoMarginBottom: true },
      url ? h('img', { src: url, alt: '', className: 'ce-media-preview' }) : null,
      h('div', null,
        h(MediaUploadCheck, null, h(MediaUpload, {
          allowedTypes: ['image'], value, onSelect: m => onChange(m.id),
          render: ({ open }) => h(Button, { variant: 'secondary', onClick: open }, value ? t.replace : t.choose)
        })),
        value ? h(Button, { variant: 'link', isDestructive: true, onClick: () => onChange(0), className: 'ce-media-remove' }, t.remove) : null));
  };

  const Input = ({ f, value, onChange }) => {
    const p = { label: f.label, help: f.help || undefined, ...common };
    switch (f.type) {
      case 'textarea': return h(TextareaControl, { ...p, value: value || '', onChange });
      // One item per line; empty lines are dropped when the post is saved
      case 'lines': return h(TextareaControl, { ...p, rows: 4, value: (value || []).join('\n'), onChange: v => onChange(v.split('\n')) });
      case 'select': return h(SelectControl, { ...p, value: value || '', onChange,
        options: Object.entries(f.choices || {}).map(([v, l]) => ({ value: v, label: l })) });
      case 'checkbox': return h(CheckboxControl, { ...p, checked: !!value, onChange });
      case 'media': return h(Media, { label: f.label, help: f.help, value: value || 0, onChange });
      case 'date': case 'url': return h(TextControl, { ...p, type: f.type, value: value || '', onChange });
      default: return h(TextControl, { ...p, value: value || '', onChange });
    }
  };

  const Repeater = ({ f, value, onChange }) => {
    const rows = Array.isArray(value) ? value : [];
    const blank = Object.fromEntries(f.sub.map(s => [s.key, s.type === 'lines' ? [] : s.type === 'select' ? Object.keys(s.choices || {})[0] || '' : '']));
    const set = (i, k, v) => onChange(rows.map((r, j) => (j === i ? { ...r, [k]: v } : r)));
    const move = (i, d) => { const r = rows.slice(); [r[i], r[i + d]] = [r[i + d], r[i]]; onChange(r); };
    return h(BaseControl, { label: f.label, help: f.help || undefined, __nextHasNoMarginBottom: true },
      rows.map((r, i) => h('div', { key: i, className: 'ce-row' },
        f.sub.map(s => h(Input, { key: s.key, f: s, value: r[s.key], onChange: v => set(i, s.key, v) })),
        h('div', { className: 'ce-row-actions' },
          h(Button, { size: 'small', icon: 'arrow-up-alt2', label: t.up, disabled: !i, onClick: () => move(i, -1) }),
          h(Button, { size: 'small', icon: 'arrow-down-alt2', label: t.down, disabled: i === rows.length - 1, onClick: () => move(i, 1) }),
          h(Button, { size: 'small', variant: 'link', isDestructive: true, onClick: () => onChange(rows.filter((_, j) => j !== i)) }, t.remove)))),
      h(Button, { variant: 'secondary', onClick: () => onChange(rows.concat([blank])) }, f.add));
  };

  const Fields = () => {
    const meta = data.useSelect(s => s('core/editor').getEditedPostAttribute('meta') || {}, []);
    const { editPost } = data.useDispatch('core/editor');
    const set = (k, v) => editPost({ meta: { [k]: v } });
    return h(Panel, { name: 'clear-eo-fields', title: cfg.title, className: 'ce-panel' },
      cfg.intro ? h(element.RawHTML, { className: 'ce-intro' }, cfg.intro) : null,
      cfg.fields.map(f => h('div', { key: f.key, className: 'ce-panel-field' },
        f.type === 'repeater'
          ? h(Repeater, { f, value: meta[f.key], onChange: v => set(f.key, v) })
          : h(Input, { f, value: meta[f.key], onChange: v => set(f.key, v) }))));
  };

  plugins.registerPlugin('clear-eo-fields', { render: Fields, icon: null });
})(window.wp);
