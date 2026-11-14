const { spawn } = require('child_process');
const fs = require('fs');
const path = require('path');
const os = require('os');

const CHROME = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const PORT = 9334;
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'nd-chrome-'));
const chrome = spawn(CHROME, ['--headless=new', '--remote-debugging-port=' + PORT, '--user-data-dir=' + profile, '--no-first-run', '--disable-gpu', 'about:blank'], { stdio: 'ignore' });
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function main() {
  let version;
  for (let i = 0; i < 50; i++) {
    try {
      version = await (await fetch('http://127.0.0.1:' + PORT + '/json/version')).json();
      break;
    } catch (e) { await sleep(200); }
  }
  const targets = await (await fetch('http://127.0.0.1:' + PORT + '/json/list')).json();
  const target = targets.find((t) => t.type === 'page');
  const ws = new WebSocket(target.webSocketDebuggerUrl);
  await new Promise((resolve) => ws.addEventListener('open', resolve, { once: true }));

  let id = 0;
  const pending = new Map();
  ws.addEventListener('message', (event) => {
    const msg = JSON.parse(event.data);
    if (msg.id && pending.has(msg.id)) {
      pending.get(msg.id)(msg);
      pending.delete(msg.id);
    }
  });
  function send(method, params = {}) {
    const n = ++id;
    ws.send(JSON.stringify({ id: n, method, params }));
    return new Promise((resolve) => pending.set(n, resolve));
  }
  async function evaluate(expression) {
    const res = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
    return res.result?.result?.value;
  }

  await send('Page.enable');
  await send('Runtime.enable');
  await send('Emulation.setDeviceMetricsOverride', { width: 360, height: 800, deviceScaleFactor: 1, mobile: true });
  await send('Page.navigate', { url: 'http://localhost:8080/' });
  await sleep(3000);

  const result = await evaluate(`(async () => {
    const toggle = document.querySelector('[data-nav-toggle]');
    const nav = document.querySelector('[data-site-nav]');
    const before = {
      cls: nav.className,
      display: getComputedStyle(nav).display,
      visibility: getComputedStyle(nav).visibility,
      opacity: getComputedStyle(nav).opacity,
      expanded: toggle.getAttribute('aria-expanded'),
      innerWidth: window.innerWidth,
      matches: window.matchMedia('(max-width: 760px)').matches
    };
    toggle.click();
    const immediate = {
      cls: nav.className,
      visibility: getComputedStyle(nav).visibility,
      opacity: getComputedStyle(nav).opacity,
      expanded: toggle.getAttribute('aria-expanded')
    };
    await new Promise((r) => setTimeout(r, 500));
    const later = {
      cls: nav.className,
      visibility: getComputedStyle(nav).visibility,
      opacity: getComputedStyle(nav).opacity,
      expanded: toggle.getAttribute('aria-expanded'),
      display: getComputedStyle(nav).display
    };
    return { before, immediate, later };
  })()`);
  console.log(JSON.stringify(result, null, 2));

  ws.close();
  chrome.kill();
  await sleep(500);
  try { fs.rmSync(profile, { recursive: true, force: true }); } catch (e) {}
}
main().catch((e) => { console.error(e); chrome.kill(); process.exit(1); });
