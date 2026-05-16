// Auth pages
async function refreshCaptcha() {
  const q = document.getElementById('captcha-q');
  const row = document.getElementById('captcha-row');
  const ans = document.getElementById('captcha-a');
  const rec = document.getElementById('recaptcha');
  if (!q || !row || !rec) return;
  const r = await F.get(F.api + '/auth.php?action=captcha');
  if (r.type === 'recaptcha' && r.siteKey) {
    row.style.display = 'none';
    if (ans) ans.required = false;
    rec.style.display = 'block';
    rec.innerHTML = '<div style="color:var(--muted);font-size:.85rem">Loading reCAPTCHA…</div>';
    if (!window.grecaptcha) {
      const s = document.createElement('script');
      s.src = 'https://www.google.com/recaptcha/api.js?render=explicit';
      s.async = true;
      s.defer = true;
      s.onload = () => { try { window.grecaptcha.render(rec, { sitekey: r.siteKey }); } catch (_) {} };
      s.onerror = () => {};
      document.head.appendChild(s);
    } else {
      try { window.grecaptcha.render(rec, { sitekey: r.siteKey }); } catch (_) {}
    }

    // If reCAPTCHA doesn't render (blocked), automatically fall back to math captcha.
    setTimeout(async () => {
      const hasFrame = !!rec.querySelector('iframe');
      if (hasFrame) return;
      const r2 = await F.get(F.api + '/auth.php?action=captcha&force=math');
      rec.style.display = 'none';
      row.style.display = 'flex';
      if (ans) ans.required = true;
      q.textContent = r2.question || '—';
    }, 1200);
    return;
  }
  // Math fallback
  rec.style.display = 'none';
  row.style.display = 'flex';
  if (ans) ans.required = true;
  q.textContent = r.question || '—';
}

function scorePassword(pw) {
  const s = String(pw || '');
  let score = 0;
  if (s.length >= 6) score += 1;
  if (s.length >= 10) score += 1;
  if (/[a-z]/.test(s) && /[A-Z]/.test(s)) score += 1;
  if (/\d/.test(s)) score += 1;
  if (/[^A-Za-z0-9]/.test(s)) score += 1;
  return Math.min(score, 5);
}
function renderPwStrength() {
  const inp = document.getElementById('pw');
  const bar = document.getElementById('pw-bar');
  const lbl = document.getElementById('pw-label');
  if (!inp || !bar || !lbl) return;
  const sc = scorePassword(inp.value);
  const pct = (sc / 5) * 100;
  bar.style.width = pct + '%';
  const text = sc <= 2 ? 'Fair' : sc <= 4 ? 'Strong' : 'Excellent';
  lbl.textContent = inp.value ? `Strength: ${text}` : 'Strength: —';
  bar.dataset.level = String(sc);
}

async function doSignup(e) {
  e.preventDefault();
  const f = e.target;
  const fd = new FormData(f); fd.append('action','signup');
  const r = await F.post(F.api + '/auth.php', fd);
  const m = F.$('.msg', f);
  if (r.ok) { m.className = 'msg ok'; m.textContent = 'Welcome to Laleh — entering studio…'; setTimeout(()=> location.href = r.redirect, 800); }
  else { m.className = 'msg err'; m.textContent = r.msg || 'Signup failed'; }
}
async function doLogin(e) {
  e.preventDefault();
  const f = e.target;
  const fd = new FormData(f); fd.append('action','login');
  const r = await F.post(F.api + '/auth.php', fd);
  const m = F.$('.msg', f);
  if (r.ok) { m.className = 'msg ok'; m.textContent = 'Signed in. Welcome back.'; setTimeout(()=> location.href = r.redirect, 600); }
  else { m.className = 'msg err'; m.textContent = r.msg || 'Login failed'; }
}
window.doSignup = doSignup; window.doLogin = doLogin;
window.refreshCaptcha = refreshCaptcha;

document.addEventListener('DOMContentLoaded', () => {
  refreshCaptcha();
  const pw = document.getElementById('pw');
  if (pw) {
    pw.addEventListener('input', renderPwStrength);
    renderPwStrength();
  }
});
