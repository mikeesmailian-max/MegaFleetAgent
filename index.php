<?php
// --- MegaFleet Portal gate ---------------------------------------------
$PASSWORD   = '499871';
$COOKIE_KEY = 'mf_portal_access';
$SECRET     = 'mf-portal-2026-x9k2';                 // server-side only, never sent to client
$EXPECTED   = hash('sha256', $PASSWORD . '|' . $SECRET);
$COOKIE_TTL = 60 * 60 * 24 * 30;                     // 30 days

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted = isset($_POST['pw']) ? $_POST['pw'] : '';
    if (hash_equals($PASSWORD, $submitted)) {
        setcookie($COOKIE_KEY, $EXPECTED, [
            'expires'  => time() + $COOKIE_TTL,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        header('Location: /');
        exit;
    }
    $error = true;
}

$authed = isset($_COOKIE[$COOKIE_KEY]) && hash_equals($EXPECTED, $_COOKIE[$COOKIE_KEY]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MegaFleet — Secure Portal</title>
<meta name="robots" content="noindex, nofollow">
<link rel="canonical" href="https://megafleetai.com/">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  *{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased}
  body{font-family:'Inter',system-ui,sans-serif;background:#05070f}
  a{color:inherit;text-decoration:none}
  input:focus{outline:none}

  @keyframes gridScroll{to{transform:translateY(40px)}}
  @keyframes dashDrift{to{transform:translateX(-1400px)}}
  @keyframes scanline{0%{transform:translateX(-60%);opacity:0}20%{opacity:1}80%{opacity:1}100%{transform:translateX(60%);opacity:0}}
  @keyframes flicker{0%,100%{opacity:1}48%{opacity:1}50%{opacity:.72}52%{opacity:1}}
  @keyframes floatUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
  @keyframes floatIn{from{opacity:0;transform:translateY(24px) scale(.94)}to{opacity:1;transform:translateY(0) scale(1)}}
  @keyframes shake{0%,100%{transform:translateX(0)}20%{transform:translateX(-8px)}40%{transform:translateX(7px)}60%{transform:translateX(-5px)}80%{transform:translateX(4px)}}

  .stage{position:relative;width:100%;min-height:100vh;overflow:hidden;
    background:radial-gradient(circle at 50% 120%, #16204a 0%, #0a0f22 45%, #05070f 100%)}
  .flagstripes{position:absolute;inset:0;pointer-events:none;opacity:.05;
    background:repeating-linear-gradient(180deg,#d42a34 0 46px,transparent 46px 92px)}
  .stars-wrap{position:absolute;top:54px;left:50%;transform:translateX(-50%);width:min(640px,82vw);
    pointer-events:none;z-index:1;text-align:center}
  .stars-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:15px 30px;opacity:.13}
  .stars-label{margin-top:14px;color:rgba(159,184,255,.28);font-family:'Orbitron',sans-serif;font-size:11px;letter-spacing:6px}
  .usmap{position:absolute;top:50%;left:50%;transform:translate(-50%,-52%);width:min(1100px,120vw);
    max-width:none;pointer-events:none;z-index:0;opacity:.16;
    filter:drop-shadow(0 0 18px rgba(91,141,239,.55));animation:mapGlow 6s ease-in-out infinite}
  @keyframes mapGlow{0%,100%{opacity:.13}50%{opacity:.22}}
  .floor{position:absolute;left:0;right:0;bottom:0;height:46%;overflow:hidden;perspective:340px;pointer-events:none}
  .floor-grid{position:absolute;inset:-20% -20% -40% -20%;transform:rotateX(68deg);
    background-image:linear-gradient(rgba(90,150,255,.55) 2px, transparent 2px), linear-gradient(90deg, rgba(90,150,255,.35) 2px, transparent 2px);
    background-size:40px 40px;animation:gridScroll 1.1s linear infinite;
    mask-image:linear-gradient(to bottom, transparent, #000 40%);-webkit-mask-image:linear-gradient(to bottom, transparent, #000 40%)}

  .gate{position:relative;z-index:4;min-height:100vh;display:flex;flex-direction:column;
    align-items:center;justify-content:center;padding:40px 24px}
  .brand{text-align:center;animation:floatUp .8s ease both}
  .brand-row{display:inline-flex;align-items:center;gap:12px;margin-bottom:26px}
  .brand-ico{width:42px;height:42px;border-radius:11px;background:linear-gradient(135deg,#5b8def,#2f5bd0);
    display:flex;align-items:center;justify-content:center;box-shadow:0 0 24px rgba(91,141,239,.6)}
  .brand-txt{font-family:'Orbitron',sans-serif;color:rgba(255,255,255,.85);font-size:15px;font-weight:700;letter-spacing:4px}
  h1{font-family:'Orbitron',sans-serif;font-size:58px;font-weight:900;letter-spacing:2px;line-height:1;
    background:linear-gradient(180deg,#ffffff,#8fb0ff);-webkit-background-clip:text;background-clip:text;
    -webkit-text-fill-color:transparent;text-shadow:0 0 40px rgba(91,141,239,.35);animation:flicker 6s infinite;
    text-align:center}
  .subtitle{color:rgba(159,184,255,.75);font-size:15px;margin-top:16px;letter-spacing:.5px;text-align:center}

  .truck-scene{position:relative;width:100%;max-width:680px;margin:24px 0 6px;animation:floatUp 1s ease .15s both}
  .ground-glow{position:absolute;left:50%;bottom:30px;transform:translateX(-50%);width:74%;height:40px;
    background:radial-gradient(ellipse, rgba(91,141,239,.4), transparent 70%);filter:blur(14px);pointer-events:none}
  .truck-wrap{position:relative}
  .truck-wrap img.rig{display:block;width:100%;height:auto;
    -webkit-mask-image:radial-gradient(115% 130% at 50% 42%, #000 58%, transparent 82%);
    mask-image:radial-gradient(115% 130% at 50% 42%, #000 58%, transparent 82%);
    filter:drop-shadow(0 20px 30px rgba(0,0,0,.5))}
  .road-dash{position:absolute;left:4%;right:4%;bottom:22px;height:3px;
    background:repeating-linear-gradient(90deg,#5b8def 0 40px,transparent 40px 80px);opacity:.55}
  .road-line{position:absolute;left:0;right:0;bottom:16px;height:2px;
    background:linear-gradient(90deg, transparent, rgba(91,141,239,.5), transparent)}

  .card{position:relative;width:100%;max-width:400px;margin-top:8px;padding:28px;border-radius:22px;
    background:rgba(16,26,58,.55);border:1px solid rgba(91,141,239,.35);
    backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);
    box-shadow:0 24px 60px -20px rgba(0,0,0,.7), inset 0 1px 0 rgba(255,255,255,.08);
    overflow:hidden;animation:floatUp 1s ease .25s both}
  .card.shake{animation:shake .5s}
  .scanline{position:absolute;top:0;left:0;width:40%;height:2px;
    background:linear-gradient(90deg, transparent, #7fd9ff, transparent);animation:scanline 3.5s ease-in-out infinite}
  .card label{display:block;color:rgba(159,184,255,.85);font-size:12px;font-weight:600;letter-spacing:1.5px;
    text-transform:uppercase;margin-bottom:12px}
  .input-row{display:flex;align-items:center;gap:10px;padding:0 16px;border-radius:13px;
    background:rgba(5,9,20,.6);border:1px solid <?= $error ? 'rgba(255,107,138,0.6)' : 'rgba(91,141,239,0.3)' ?>;
    transition:border-color .2s}
  .input-row input{flex:1;background:transparent;border:none;color:#fff;font-size:17px;letter-spacing:2px;
    padding:15px 0;font-family:'Inter',sans-serif}
  .err-msg{color:#ff6b8a;font-size:13px;margin-top:12px;display:flex;align-items:center;gap:6px}
  .submit-btn{width:100%;margin-top:20px;padding:15px;border:none;border-radius:13px;
    background:linear-gradient(135deg,#5b8def,#2f5bd0);color:#fff;font-size:15px;font-weight:700;
    letter-spacing:1px;cursor:pointer;box-shadow:0 0 28px rgba(91,141,239,.5);
    transition:transform .15s, box-shadow .2s}
  .submit-btn:hover{transform:translateY(-2px);box-shadow:0 6px 34px rgba(91,141,239,.7)}
  .submit-btn:active{transform:translateY(0)}
  .gate-foot{margin-top:28px;color:rgba(159,184,255,.4);font-size:12px;letter-spacing:.5px;
    animation:floatUp 1s ease .3s both}

  /* --- launchpad (post-unlock) --- */
  .launch-badge-wrap{text-align:center;margin-top:84px;margin-bottom:8px;position:relative;z-index:2}
  .launch-badge{display:inline-flex;align-items:center;gap:10px;padding:8px 18px;border-radius:999px;
    background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);
    backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px)}
  .launch-badge-ico{width:22px;height:22px;border-radius:6px;background:linear-gradient(135deg,#6c8cff,#3f5bd8);
    display:flex;align-items:center;justify-content:center}
  .launch-badge-txt{color:rgba(255,255,255,.9);font-size:14px;font-weight:600;letter-spacing:.2px}
  .launch-h1{color:#fff;font-size:40px;font-weight:700;letter-spacing:-.02em;margin-top:28px;text-align:center;
    position:relative;z-index:2;font-family:'Inter',sans-serif}
  .launch-sub{color:rgba(255,255,255,.55);font-size:16px;margin-top:10px;text-align:center;position:relative;z-index:2}
  .launch-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,180px));gap:40px 48px;
    justify-content:center;margin-top:64px;margin-bottom:60px;max-width:760px;width:100%;
    position:relative;z-index:2;margin-left:auto;margin-right:auto}
  .tile{display:flex;flex-direction:column;align-items:center;gap:16px;cursor:pointer}
  .tile-ico{width:132px;height:132px;border-radius:30px;
    box-shadow:0 18px 40px -12px rgba(0,0,0,.55), inset 0 1px 0 rgba(255,255,255,.25);
    display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;
    transition:transform .25s cubic-bezier(.34,1.56,.64,1)}
  .tile:hover .tile-ico{transform:translateY(-8px) scale(1.06)}
  .tile-ico .gloss{position:absolute;inset:0;background:linear-gradient(160deg,rgba(255,255,255,.28),transparent 55%);pointer-events:none}
  .tile-ico .glyph{width:68px;height:68px;display:flex;align-items:center;justify-content:center;position:relative;z-index:1}
  .tile-name{color:#fff;font-size:16px;font-weight:600;letter-spacing:-.01em;text-align:center}
  .tile-sub{color:rgba(255,255,255,.5);font-size:12.5px;margin-top:4px;line-height:1.35;max-width:160px;text-align:center}
  .launch-foot{position:relative;z-index:2;text-align:center;padding-bottom:40px;
    color:rgba(255,255,255,.35);font-size:12.5px}
  .logout{position:absolute;top:20px;right:24px;z-index:5;color:rgba(255,255,255,.4);font-size:12px;
    padding:8px 14px;border-radius:999px;border:1px solid rgba(255,255,255,.14);
    background:rgba(255,255,255,.05)}
  .logout:hover{color:#fff;border-color:rgba(255,255,255,.3)}
</style>
</head>
<body>
<div class="stage">
  <div class="flagstripes"></div>

  <div class="stars-wrap">
    <div class="stars-grid">
      <?php for ($i = 0; $i < 48; $i++): ?>
        <svg width="18" height="18" viewBox="0 0 24 24" style="justify-self:center;filter:drop-shadow(0 0 4px rgba(159,184,255,0.6));"><path d="M12 2l2.9 6.3 6.9.7-5.2 4.6 1.5 6.8L12 17.6 5.9 20.4l1.5-6.8L2.2 9l6.9-.7z" fill="#dbe6ff"/></svg>
      <?php endfor; ?>
    </div>
    <div class="stars-label">48 STATES · ONE FLEET</div>
  </div>

  <img class="usmap" src="/assets/us-map-48.svg" alt="" aria-hidden="true">

  <div class="floor"><div class="floor-grid"></div></div>

<?php if ($authed): ?>

  <a href="/logout.php" class="logout">Sign out</a>
  <div style="position:relative;z-index:5;">
    <div class="launch-badge-wrap">
      <div class="launch-badge">
        <div class="launch-badge-ico">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M3 13l2-5h14l2 5M5 13h14v4a1 1 0 01-1 1h-1a2 2 0 11-4 0H9a2 2 0 11-4 0H5v-4z" stroke="#fff" stroke-width="1.8" stroke-linejoin="round"/></svg>
        </div>
        <span class="launch-badge-txt">MegaFleet</span>
      </div>
    </div>

    <h1 class="launch-h1">Apps</h1>
    <p class="launch-sub">Tap an app to open it</p>

    <div class="launch-grid">
      <a class="tile" href="https://megafleet-j5k7rjui.manus.space" target="_blank" rel="noopener">
        <div class="tile-ico" style="background:linear-gradient(160deg,#5b8def,#2f5bd0)">
          <div class="gloss"></div>
          <div class="glyph">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="7" r="3" stroke="#fff" stroke-width="1.6"/><path d="M4 19c0-2.8 2.2-5 5-5s5 2.2 5 5" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/><path d="M16 8h5M16 12h5M16 16h3" stroke="#fff" stroke-width="1.6" stroke-linecap="round" opacity="0.85"/></svg>
          </div>
        </div>
        <div>
          <div class="tile-name">Dispatch Share</div>
          <div class="tile-sub">Share driver info with brokers</div>
        </div>
      </a>

      <a class="tile" href="https://megafleetpay-kxkqjvmf.manus.space" target="_blank" rel="noopener">
        <div class="tile-ico" style="background:linear-gradient(160deg,#3ecf8e,#17a06b)">
          <div class="gloss"></div>
          <div class="glyph">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none"><rect x="3" y="5.5" width="18" height="13" rx="2.5" stroke="#fff" stroke-width="1.6"/><path d="M3 9.5h18" stroke="#fff" stroke-width="1.6"/><path d="M6.5 14.5h4" stroke="#fff" stroke-width="1.6" stroke-linecap="round"/></svg>
          </div>
        </div>
        <div>
          <div class="tile-name">MegaFleet Pay</div>
          <div class="tile-sub">Charge cards for transactions</div>
        </div>
      </a>
    </div>

    <div class="launch-foot">MegaFleet Corp · Internal Employee Portal</div>
  </div>

<?php else: ?>

  <div class="gate">
    <div class="brand">
      <div class="brand-row">
        <div class="brand-ico">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M3 13l2-5h14l2 5M5 13h14v4a1 1 0 01-1 1h-1a2 2 0 11-4 0H9a2 2 0 11-4 0H5v-4z" stroke="#fff" stroke-width="1.8" stroke-linejoin="round"/></svg>
        </div>
        <span class="brand-txt">MEGAFLEET CORP</span>
      </div>
      <h1>SECURE ACCESS</h1>
      <p class="subtitle">Authorized personnel only · Enter credentials to continue</p>
    </div>

    <div class="truck-scene">
      <div class="ground-glow"></div>
      <div class="truck-wrap">
        <img class="rig" src="/assets/megafleet-rig.png" alt="MegaFleet Corp 18-wheeler">
      </div>
      <div class="road-dash"></div>
      <div class="road-line"></div>
    </div>

    <form method="POST" action="/" autocomplete="off">
      <div class="card<?= $error ? ' shake' : '' ?>" id="card">
        <div class="scanline"></div>
        <label for="pw">Access Code</label>
        <div class="input-row">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="5" y="11" width="14" height="9" rx="2" stroke="#7fd9ff" stroke-width="1.7"/><path d="M8 11V8a4 4 0 018 0v3" stroke="#7fd9ff" stroke-width="1.7"/></svg>
          <input type="password" id="pw" name="pw" placeholder="••••••••••" autofocus>
        </div>
        <?php if ($error): ?>
        <div class="err-msg">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#ff6b8a" stroke-width="1.7"/><path d="M12 7v6M12 16v.5" stroke="#ff6b8a" stroke-width="1.7" stroke-linecap="round"/></svg>
          Access denied — invalid code
        </div>
        <?php endif; ?>
        <button type="submit" class="submit-btn">ENTER PORTAL →</button>
      </div>
    </form>

    <div class="gate-foot">MegaFleet Corp · Encrypted Employee Gateway</div>
  </div>

<?php endif; ?>
</div>
</body>
</html>
