const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = 9333;
const BASE = 'http://localhost:8080';
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'nd-chrome-'));
const shotDir = path.join(process.cwd(), 'writable');

const chrome = spawn(CHROME, [
  '--headless=new',
  '--remote-debugging-port=' + PORT,
  '--user-data-dir=' + profile,
  '--no-first-run',
  '--no-default-browser-check',
  '--disable-gpu',
  '--hide-scrollbars',
  'about:blank'
], { stdio: 'ignore' });

const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function getJson(url, options) {
  const r = await fetch(url, options);
  return r.json();
}

async function waitForChrome() {
  for (let i = 0; i < 50; i++) {
    try {
      const v = await getJson('http://127.0.0.1:' + PORT + '/json/version');
      if (v.webSocketDebuggerUrl) return v;
    } catch (e) {}
    await sleep(200);
  }
  throw new Error('Chrome not ready');
}

class CDP {
  constructor(ws) {
    this.ws = ws;
    this.id = 0;
    this.pending = new Map();
    this.events = [];
    this.errors = [];
    ws.addEventListener('message', (event) => {
      const msg = JSON.parse(event.data);
      if (msg.id && this.pending.has(msg.id)) {
        const { resolve, reject } = this.pending.get(msg.id);
        this.pending.delete(msg.id);
        if (msg.error) reject(new Error(JSON.stringify(msg.error)));
        else resolve(msg.result);
        return;
      }
      this.events.push(msg);
      if (msg.method === 'Runtime.exceptionThrown') {
        this.errors.push('EXCEPTION ' + (msg.params.exceptionDetails.text || '') + ' ' + (msg.params.exceptionDetails.exception?.description || ''));
      }
      if (msg.method === 'Runtime.consoleAPICalled' && msg.params.type === 'error') {
        this.errors.push('CONSOLE ' + msg.params.args.map((a) => a.value || a.description || '').join(' '));
      }
      if (msg.method === 'Log.entryAdded' && msg.params.entry.level === 'error') {
        this.errors.push('LOG ' + msg.params.entry.text);
      }
    });
  }

  send(method, params = {}) {
    const id = ++this.id;
    this.ws.send(JSON.stringify({ id, method, params }));
    return new Promise((resolve, reject) => this.pending.set(id, { resolve, reject }));
  }
}

async function evaluate(cdp, expression, awaitPromise = true) {
  const result = await cdp.send('Runtime.evaluate', {
    expression,
    returnByValue: true,
    awaitPromise
  });
  if (result.exceptionDetails) {
    throw new Error(result.exceptionDetails.text + ' ' + (result.exceptionDetails.exception?.description || ''));
  }
  return result.result.value;
}

async function navigate(cdp, url) {
  const start = cdp.events.length;
  const loaded = new Promise((resolve) => {
    const timer = setInterval(() => {
      const found = cdp.events.slice(start).some((e) => e.method === 'Page.loadEventFired');
      if (found) {
        clearInterval(timer);
        resolve();
      }
    }, 50);
    setTimeout(() => {
      clearInterval(timer);
      resolve();
    }, 8000);
  });
  await cdp.send('Page.navigate', { url });
  await loaded;
  await sleep(700);
}

async function screenshot(cdp, name) {
  const result = await cdp.send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
  fs.writeFileSync(path.join(shotDir, name), Buffer.from(result.data, 'base64'));
}

async function main() {
  const version = await waitForChrome();
  const targets = await getJson('http://127.0.0.1:' + PORT + '/json/list');
  const target = targets.find((t) => t.type === 'page');
  const ws = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise((resolve, reject) => {
    ws.addEventListener('open', resolve, { once: true });
    ws.addEventListener('error', reject, { once: true });
  });
  const cdp = new CDP(ws);
  await cdp.send('Page.enable');
  await cdp.send('Runtime.enable');
  await cdp.send('Log.enable');

  const report = {};

  await cdp.send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
  await navigate(cdp, BASE + '/');
  report.mobileHome = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    const navToggle = document.querySelector('[data-nav-toggle]');
    const hero = document.querySelector('.hero-copy > *');
    const navStyle = getComputedStyle(document.querySelector('.site-nav'));
    return {
      width: doc.clientWidth,
      scrollWidth: doc.scrollWidth,
      overflow: doc.scrollWidth - doc.clientWidth,
      heroOpacity: getComputedStyle(hero).opacity,
      heroAnimation: getComputedStyle(hero).animationName,
      revealCount: document.querySelectorAll('.reveal').length,
      visibleCount: document.querySelectorAll('.reveal.is-visible').length,
      navVisibility: navStyle.visibility,
      toggleHeight: navToggle ? navToggle.getBoundingClientRect().height : 0,
      toggleWidth: navToggle ? navToggle.getBoundingClientRect().width : 0,
      title: document.title
    };
  })()`);
  await screenshot(cdp, 'tmp-home-360.png');

  report.drawer = await evaluate(cdp, `(async () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    toggle.click();
    await new Promise((r) => setTimeout(r, 350));
    const nav = document.querySelector('.site-nav');
    const openVisibility = getComputedStyle(nav).visibility;
    const openOpacity = getComputedStyle(nav).opacity;
    const expanded = toggle.getAttribute('aria-expanded');
    const openClass = nav.classList.contains('is-open');
    document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
    await new Promise((r) => setTimeout(r, 350));
    return {
      openClass,
      expandedAfterOpen: expanded,
      openVisibility,
      openOpacity,
      expandedAfterEscape: toggle.getAttribute('aria-expanded'),
      closedAfterEscape: !nav.classList.contains('is-open')
    };
  })()`);
  await evaluate(cdp, `document.querySelector('[data-nav-toggle]').click()`);
  await sleep(400);
  await screenshot(cdp, 'tmp-nav-open-360.png');
  await evaluate(cdp, `document.querySelector('[data-nav-toggle]').click()`);
  await sleep(300);

  await evaluate(cdp, `(() => {
    const total = document.documentElement.scrollHeight;
    for (let y = 0; y <= total; y += 500) {
      window.scrollTo(0, y);
    }
    window.scrollTo(0, total);
    return total;
  })()`);
  await sleep(1400);
  await evaluate(cdp, 'window.scrollTo(0, 0)');
  await sleep(300);
  report.reveal = await evaluate(cdp, `(() => {
    const all = Array.from(document.querySelectorAll('.reveal'));
    const hidden = all.filter((el) => parseFloat(getComputedStyle(el).opacity) < 0.9);
    return { total: all.length, visible: all.filter((el) => el.classList.contains('is-visible')).length, stillHidden: hidden.length };
  })()`);

  await navigate(cdp, BASE + '/produk/manajemen-stok-barang');
  report.mobileProduct = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    return { width: doc.clientWidth, scrollWidth: doc.scrollWidth, overflow: doc.scrollWidth - doc.clientWidth };
  })()`);
  await screenshot(cdp, 'tmp-product-360.png');

  await navigate(cdp, BASE + '/checkout/manajemen-stok-barang');
  report.mobileCheckout = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    return { width: doc.clientWidth, scrollWidth: doc.scrollWidth, overflow: doc.scrollWidth - doc.clientWidth };
  })()`);
  await screenshot(cdp, 'tmp-checkout-360.png');

  await cdp.send('Emulation.setDeviceMetricsOverride', { width: 1440, height: 900, deviceScaleFactor: 1, mobile: false });
  await navigate(cdp, BASE + '/');
  await sleep(900);
  report.desktopHome = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    return {
      width: doc.clientWidth,
      scrollWidth: doc.scrollWidth,
      overflow: doc.scrollWidth - doc.clientWidth,
      heroOpacity: getComputedStyle(document.querySelector('.hero-copy > *')).opacity,
      visibleCount: document.querySelectorAll('.reveal.is-visible').length
    };
  })()`);
  await screenshot(cdp, 'tmp-home-1440.png');

  await navigate(cdp, BASE + '/admin/login');
  await evaluate(cdp, `(() => {
    document.querySelector('input[name=email]').value = 'admin@nexusdiji.test';
    document.querySelector('input[name=password]').value = 'admin12345';
    document.querySelector('form').requestSubmit();
    return true;
  })()`);
  await sleep(2500);
  for (let i = 0; i < 20; i++) {
    const ready = await evaluate(cdp, `location.pathname === '/admin' && document.readyState === 'complete' && !!document.querySelector('[data-admin-sidebar]')`);
    if (ready) break;
    await sleep(250);
  }
  report.adminLogin = await evaluate(cdp, `(() => ({ path: location.pathname, title: document.title, hasSidebar: !!document.querySelector('[data-admin-sidebar]') }))()`);
  await sleep(800);
  report.admin = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    const bars = Array.from(document.querySelectorAll('.chart-bar'));
    return {
      width: doc.clientWidth,
      scrollWidth: doc.scrollWidth,
      overflow: doc.scrollWidth - doc.clientWidth,
      path: location.pathname,
      bars: bars.length,
      barAnimation: bars[0] ? getComputedStyle(bars[0]).animationName : 'none',
      barHeight: bars[0] ? bars[0].getBoundingClientRect().height : 0,
      reveal: document.querySelectorAll('.reveal.is-visible').length,
      credit: document.body.innerText.includes('Diproduksi oleh Nexus Diji')
    };
  })()`);
  await screenshot(cdp, 'tmp-admin-1440.png');

  await cdp.send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
  await navigate(cdp, BASE + '/admin');
  report.mobileAdmin = await evaluate(cdp, `(() => {
    const doc = document.documentElement;
    return { width: doc.clientWidth, scrollWidth: doc.scrollWidth, overflow: doc.scrollWidth - doc.clientWidth };
  })()`);

  await cdp.send('Emulation.setEmulatedMedia', { features: [{ name: 'prefers-reduced-motion', value: 'reduce' }] });
  await cdp.send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
  await navigate(cdp, BASE + '/');
  await sleep(500);
  report.reducedMotion = await evaluate(cdp, `(() => {
    const hero = document.querySelector('.hero-copy > *');
    const style = getComputedStyle(hero);
    const revealCount = document.querySelectorAll('.reveal').length;
    const allText = document.body.innerText.length;
    return {
      heroAnimationDuration: style.animationDuration,
      heroOpacity: style.opacity,
      revealCount,
      textLength: allText,
      overflow: document.documentElement.scrollWidth - document.documentElement.clientWidth
    };
  })()`);

  report.errors = cdp.errors;
  console.log(JSON.stringify(report, null, 2));

  ws.close();
  chrome.kill();
  await sleep(500);
  try { fs.rmSync(profile, { recursive: true, force: true }); } catch (e) {}
}

main().catch((error) => {
  console.error('FAILED', error);
  chrome.kill();
  process.exit(1);
});
