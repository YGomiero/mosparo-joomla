/**
 * mosparo Joomla integration. Copyright (C) 2026 Yann Gomiero.
 * License: GNU General Public License version 2 or later.
 */
(() => {
  'use strict';
  const start = () => {
    const config = Joomla.getOptions('plg_captcha_mosparo');
    if (!config || typeof mosparo !== 'function') return;
    let sequence = 0;
    const mount = (box, fields) => {
      const form = box.closest('form');
      if (!form || box.dataset.mosparoMounted) return;
      box.dataset.mosparoMounted = '1';
      if (!box.id) box.id = `joomla-mosparo-${++sequence}`;
      const allowed = new Set(fields);
      let widget;
      const restrictFields = (element, data) => {
        // Explicit names also exclude passwords after a "show password" control changes their type.
        data.fields = data.fields.filter(field => {
          const isHoneypot = widget?.hpFieldElement?.name === field.name;
          if ((allowed.has(field.name) || isHoneypot) && !/password|passwd|secret|token/i.test(field.name)) return true;
          if (!data.ignoredFields.includes(field.name)) data.ignoredFields.push(field.name);
          return false;
        });
        return data;
      };
      widget = new mosparo(box.id, config.url, config.uuid, config.publicKey, {
        language: config.language,
        loadCssResource: config.loadCssResource,
        onGetFormData: restrictFields,
      });
    };
    document.querySelectorAll('[data-mosparo-fields]').forEach(box => {
      try { mount(box, JSON.parse(box.dataset.mosparoFields)); } catch (error) { /* Server rejects unverified forms. */ }
    });
    if (Joomla.getOptions('plg_captcha_mosparo.login')) {
      document.querySelectorAll('form').forEach(form => {
        const option = form.querySelector('input[name="option"]')?.value;
        const action = new URL(form.action, document.baseURI);
        const task = form.querySelector('input[name="task"]')?.value || action.searchParams.get('task');
        // Joomla 6's login page puts the task in its SEF action URL, without hidden option/task fields.
        const siteLogin = task === 'user.login' && (option === 'com_users' || action.searchParams.get('option') === 'com_users' || form.id === 'com-users-login__form');
        if (!(siteLogin || (option === 'com_login' && ['login', 'login.login'].includes(task)))) return;
        if (!form.querySelector('input[name="username"]') || form.querySelector('[data-mosparo-mounted]')) return;
        const button = form.querySelector('button[type="submit"],input[type="submit"]');
        const box = document.createElement('div');
        box.className = 'joomla-mosparo my-3';
        if (button) button.parentNode.insertBefore(box, button); else form.appendChild(box);
        mount(box, ['username']);
      });
    }
  };
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, {once: true});
  else start();
})();
