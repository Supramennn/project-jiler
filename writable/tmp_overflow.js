const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = 9336;
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
    await sleep(1400);
  }
  await send('Page.enable');
  await send('Runtime.enable');
  await send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });

  const pages = ['/', '/produk', '/produk/manajemen-stok-barang', '/checkout/manajemen-stok-barang', '/status', '/syarat', '/privasi', '/admin/login'];
  const report = {};
  for (const p of pages) {
    await nav(BASE + p);
    report[p] = await evaluate(`(() => {
      const doc = document.documentElement;
      const smallTargets = [];
      document.querySelectorAll('button, .btn, .nav-toggle, .site-nav a, .admin-nav a').forEach((el) => {
        const rect = el.getBoundingClientRect();
        const style = getComputedStyle(el);
        if (style.display === 'none' || style.visibility === 'hidden' || rect.width === 0 || rect.height === 0) return;
        if (rect.width < 44 || rect.height < 44) {
          smallTargets.push({ tag: el.tagName.toLowerCase(), text: (el.textContent || '').trim().slice(0, 40), w: Math.round(rect.width), h: Math.round(rect.height) });
        }
      });
      return { overflow: doc.scrollWidth - doc.clientWidth, smallTargets };
    })()`);
  }

  await nav(BASE + '/');
  report.focus = await evaluate(`(() => {
    const skip = document.querySelector('.skip-link');
    skip.focus();
    const top = skip.getBoundingClientRect().top;
    const style = getComputedStyle(skip);
    return { skipTopWhenFocused: Math.round(top), skipOpacity: style.opacity, skipHeight: Math.round(skip.getBoundingClientRect().height) };
  })()`);
  await send('Input.dispatchKeyEvent', { type: 'keyDown', key: 'Tab', code: 'Tab', windowsVirtualKeyCode: 9 });
  await send('Input.dispatchKeyEvent', { type: 'keyUp', key: 'Tab', code: 'Tab', windowsVirtualKeyCode: 9 });
  await sleep(200);
  report.keyboardFocus = await evaluate(`(() => {
    const el = document.activeElement;
    const s = getComputedStyle(el);
    return { tag: el.tagName.toLowerCase(), cls: el.className, text: (el.textContent || '').trim().slice(0, 30), outline: s.outlineWidth + ' ' + s.outlineStyle + ' ' + s.outlineColor, top: Math.round(el.getBoundingClientRect().top) };
  })()`);

  await nav(BASE + '/admin/login');
  await evaluate(`(() => {
    document.querySelector('input[name=email]').value = 'admin@nexusdiji.test';
    document.querySelector('input[name=password]').value = 'admin12345';
    document.querySelector('form').requestSubmit();
    return true;
  })()`);
  await sleep(2500);
  report['/admin'] = await evaluate(`(() => {
    const doc = document.documentElement;
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('[data-admin-sidebar]');
    let drawer = null;
    if (toggle && sidebar) {
      toggle.click();
      drawer = { open: sidebar.classList.contains('is-open'), expanded: toggle.getAttribute('aria-expanded') };
      toggle.click();
    }
    return { path: location.pathname, overflow: doc.scrollWidth - doc.clientWidth, drawer };
  })()`);

  console.log(JSON.stringify(report, null, 2));
  ws.close();
  chrome.kill();
  await sleep(300);
  try { fs.rmSync(profile, { recursive: true, force: true }); } catch (e) {}
}
main().catch((e) => { console.error('FAILED', e); chrome.kill(); process.exit(1); });
