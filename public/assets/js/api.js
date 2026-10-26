/**
 * ContaTech API Client
 * Detecta la carpeta real (ContaTech, ContaTech1, etc.)
 */
const ContaAPI = (function () {
  function detectBase() {
    const scripts = document.getElementsByTagName('script');
    for (let i = 0; i < scripts.length; i++) {
      const src = scripts[i].src || '';
      const m = src.match(/^(?:https?:\/\/[^/]+)?(\/.*?)\/public\/assets\/js\/api\.js/i);
      if (m && m[1]) return m[1];
    }
    const path = window.location.pathname;
    const idx = path.indexOf('/views/');
    if (idx > 0) return path.substring(0, idx);
    const segs = path.split('/').filter(Boolean);
    if (segs.length > 0) return '/' + segs[0];
    return '';
  }

  const ROOT = detectBase();
  const BASE = ROOT + '/api/index.php?path=';

  function getToken() {
    return localStorage.getItem('contatech_token') || '';
  }

  function setSession(token, user) {
    localStorage.setItem('contatech_token', token);
    localStorage.setItem('contatech_user', JSON.stringify(user));
    document.cookie = 'contatech_token=' + token + '; path=/; max-age=28800; SameSite=Lax';
  }

  function clearSession() {
    localStorage.removeItem('contatech_token');
    localStorage.removeItem('contatech_user');
    document.cookie = 'contatech_token=; path=/; max-age=0';
  }

  function getUser() {
    try {
      return JSON.parse(localStorage.getItem('contatech_user') || 'null');
    } catch (e) {
      return null;
    }
  }

  function loginUrl() {
    return ROOT + '/views/auth/login.php';
  }

  function dashboardUrl() {
    return ROOT + '/views/dashboard/index.php';
  }

  async function request(path, options) {
    options = options || {};
    const headers = Object.assign(
      { 'Content-Type': 'application/json', Accept: 'application/json' },
      options.headers || {}
    );
    const token = getToken();
    if (token) headers['Authorization'] = 'Bearer ' + token;

    const url = BASE + encodeURIComponent(path);

    let res;
    try {
      res = await fetch(url, {
        method: options.method || 'GET',
        headers: headers,
        body: options.body ? JSON.stringify(options.body) : undefined,
      });
    } catch (netErr) {
      throw new Error('Sin conexión (' + url + '): ' + netErr.message);
    }

    const text = await res.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (e) {
      throw new Error(
        'HTTP ' + res.status + ' en ' + url + '. ' +
        text.replace(/<[^>]+>/g, ' ').trim().slice(0, 200)
      );
    }

    if (res.status === 401) {
      clearSession();
      if (window.location.pathname.indexOf('/auth/') === -1) {
        window.location.href = loginUrl();
      }
      throw new Error(data.message || 'No autorizado');
    }

    if (!data.success) {
      throw new Error(data.message || ('Error en la solicitud (HTTP ' + res.status + ')'));
    }
    return data;
  }

  return {
    ROOT: ROOT,
    BASE: BASE,
    getToken: getToken,
    setSession: setSession,
    clearSession: clearSession,
    getUser: getUser,
    loginUrl: loginUrl,
    dashboardUrl: dashboardUrl,
    get: function (path) { return request(path); },
    post: function (path, body) { return request(path, { method: 'POST', body: body }); },
    put: function (path, body) { return request(path, { method: 'PUT', body: body }); },
    del: function (path) { return request(path, { method: 'DELETE' }); },
  };
})();