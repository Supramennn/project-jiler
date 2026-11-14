const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = 9335;
const BASE = 'http://localhost:8080';
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'nd-chrome-'));
const chrome = spawn(CHROME, ['--headless=new', '--remote-debugging-port=' + PORT, '--user-data-dir=' + profile, '--no-first-run', '--disable-gpu', 'about:blank'], { stdio: 'ignore' });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function main() {
  for (let i = 0; i < 50; i++) {
    try { await (await fetch('http://127.0.0.1:' + PORT + '/json/version')).json(); break; } catch (e) { await sleep(200); }
  }
  const targets = await (await fetch('http://127.0.0.1:' + PORT + '/json/list')).json();
  const target = targets.find((t) => t.type === 'page');
  const ws = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise((resolve) => ws.addEventListener('open', resolve, { once: true }));
  let id = 0;
  const pending = new Map();
  ws.addEventListener('message', (event) => {
    const msg = JSON.parse(event.data);
    if (msg.id && pending.has(msg.id)) { pending.get(msg.id)(msg); pending.delete(msg.id); }
  });
  function send(method, params = {}) {
    const n = ++id;
    ws.send(JSON.stringify({ id: n, method, params }));
    return new Promise((resolve) => pending.set(n, resolve));
  }
  async function evaluate(expression) {
    const res = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
    if (res.result?.exceptionDetails) throw new Error(JSON.stringify(res.result.exceptionDetails));
    return res.result?.result?.value;
  }
  async function nav(url) {
    await send('Page.navigate', { url });
    await sleep(1600);
  }
  await send('Page.enable');
  await send('Runtime.enable');

  const audit = `(() => {
    function rgb(value) {
      const m = value.match(/rgba?\\(([^)]+)\\)/);
      if (!m) return null;
      const parts = m[1].split(',').map((v) => parseFloat(v.trim()));
      return { r: parts[0], g: parts[1], b: parts[2], a: parts.length > 3 ? parts[3] : 1 };
    }
    function lum(c) {
      const f = (v) => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
      return 0.2126 * f(c.r) + 0.7152 * f(c.g) + 0.0722 * f(c.b);
    }
    function ratio(a, b) {
      const l1 = lum(a), l2 = lum(b);
      const hi = Math.max(l1, l2), lo = Math.min(l1, l2);
      return (hi + 0.05) / (lo + 0.05);
    }
    function bgOf(el) {
      let node = el;
      while (node && node !== document.documentElement) {
        const bg = rgb(getComputedStyle(node).backgroundColor);
        if (bg && bg.a > 0.5) return bg;
        node = node.parentElement;
      }
      return { r: 246, g: 241, b: 231, a: 1 };
    }
    function label(el) {
      const id = el.id ? '#' + el.id : '';
      const cls = el.className && typeof el.className === 'string' ? '.' + el.className.trim().split(/\\s+/).slice(0, 2).join('.') : '';
      return el.tagName.toLowerCase() + id + cls;
    }
    const results = [];
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);
    let node;
    while ((node = walker.nextNode())) {
      const text = node.textContent.trim().replace(/\\s+/g, ' ');
      if (text.length < 2) continue;
      const el = node.parentElement;
      if (!el) continue;
      const style = getComputedStyle(el);
      if (style.display === 'none' || style.visibility === 'hidden' || parseFloat(style.opacity) === 0) continue;
      if (el.closest('[aria-hidden="true"]')) continue;
      if (el.closest('.skip-link')) continue;
      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) continue;
      const fg = rgb(style.color);
      if (!fg) continue;
      const bg = bgOf(el);
      const size = parseFloat(style.fontSize);
      const weight = parseInt(style.fontWeight, 10) || 400;
      const large = size >= 24 || (size >= 18.67 && weight >= 700);
      const need = large ? 3 : 4.5;
      const r = ratio(fg, bg);
      if (r < need) {
        results.push({ sel: label(el), text: text.slice(0, 70), color: style.color, bg: 'rgb(' + bg.r + ',' + bg.g + ',' + bg.b + ')', size, weight, ratio: Math.round(r * 100) / 100, need });
      }
    }
    return results;
  })()`;

  const report = {};
  await send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 900, deviceScaleFactor: 1, mobile: false });
  for (const [name, url] of [['home-desktop', '/'], ['product-desktop', '/produk/manajemen-stok-barang'], ['checkout-desktop', '/checkout/manajemen-stok-barang'], ['login-desktop', '/admin/login']]) {
    await nav(BASE + url);
    report[name] = await evaluate(audit);
  }

  await nav(BASE + '/admin/login');
  await evaluate(`(() => {
    document.querySelector('input[name=email]').value = 'admin@nexusdiji.test';
    document.querySelector('input[name=password]').value = 'admin12345';
    document.querySelector('form').requestSubmit();
    return true;
  })()`);
  await sleep(2500);
  report['admin-desktop'] = await evaluate(audit);

  await send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
  for (const [name, url] of [['home-mobile', '/'], ['product-mobile', '/produk/manajemen-stok-barang'], ['checkout-mobile', '/checkout/manajemen-stok-barang']]) {
    await nav(BASE + url);
    report[name] = await evaluate(audit);
  }
  await nav(BASE + '/admin');
  report['admin-mobile'] = await evaluate(audit);

  await nav(BASE + '/');
  await evaluate(`document.querySelector('[data-nav-toggle]').click()`);
  await sleep(400);
  report['nav-open-mobile'] = await evaluate(audit);

  console.log(JSON.stringify(report, null, 2));
  ws.close();
  chrome.kill();
  await sleep(300);
  try { fs.rmSync(profile, { recursive: true, force: true }); } catch (e) {}
}
main().catch((e) => { console.error('FAILED', e); chrome.kill(); process.exit(1); });
