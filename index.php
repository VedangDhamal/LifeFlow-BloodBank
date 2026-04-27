<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LifeFlow — Blood Bank Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,300;0,600;0,700;1,300&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<?php include 'includes/theme_toggle.php'; ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);overflow-x:hidden;transition:background 0.3s,color 0.3s}

body::before{
  content:'';position:fixed;inset:0;
  background:radial-gradient(ellipse 70% 50% at 50% -10%,rgba(192,57,43,0.08) 0%,transparent 60%),
             radial-gradient(ellipse 40% 30% at 90% 90%,rgba(192,57,43,0.05) 0%,transparent 50%);
  pointer-events:none;z-index:0;
}

nav{
  position:fixed;top:0;left:0;right:0;z-index:100;
  display:flex;align-items:center;justify-content:space-between;
  padding:18px 60px;
  background:var(--nav-bg);backdrop-filter:blur(12px);
  border-bottom:1px solid var(--border);
  transition:background 0.3s,border-color 0.3s;
}
.nav-logo{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--red);letter-spacing:0.5px}
.nav-logo span{color:var(--text)}
.nav-links{display:flex;gap:32px;list-style:none}
.nav-links a{color:var(--muted);text-decoration:none;font-size:14px;transition:color 0.2s}
.nav-links a:hover{color:var(--text)}
.nav-actions{display:flex;gap:10px;align-items:center}
.btn{padding:9px 22px;border-radius:8px;font-family:'Outfit',sans-serif;font-size:13px;font-weight:500;cursor:pointer;text-decoration:none;transition:all 0.2s;border:none;display:inline-flex;align-items:center;gap:6px}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--muted)}
.btn-outline:hover{border-color:var(--muted2);color:var(--text)}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 20px rgba(192,57,43,0.3)}
.btn-lg{padding:14px 32px;font-size:15px;border-radius:10px}

.hero{
  min-height:100vh;display:flex;align-items:center;justify-content:center;
  text-align:center;padding:100px 20px 60px;position:relative;z-index:1;
}
.hero-badge{
  display:inline-flex;align-items:center;gap:8px;
  background:rgba(192,57,43,0.1);border:1px solid rgba(192,57,43,0.25);
  padding:6px 16px;border-radius:30px;font-size:12px;color:var(--red);
  margin-bottom:28px;letter-spacing:1px;text-transform:uppercase;
  animation:fadeUp 0.6s ease both;
}
.pulse{width:7px;height:7px;border-radius:50%;background:var(--red);animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:0.5;transform:scale(1.3)}}
.hero h1{font-family:'Fraunces',serif;font-size:clamp(42px,7vw,80px);font-weight:700;line-height:1.1;margin-bottom:24px;animation:fadeUp 0.6s ease 0.1s both;}
.hero h1 em{font-style:italic;color:var(--red)}
.hero p{font-size:17px;color:var(--muted);max-width:540px;margin:0 auto 40px;line-height:1.7;animation:fadeUp 0.6s ease 0.2s both;}
.hero-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;animation:fadeUp 0.6s ease 0.3s both}
.blood-drops{position:absolute;top:0;left:0;right:0;bottom:0;pointer-events:none;overflow:hidden;}
.drop{position:absolute;width:6px;height:6px;border-radius:50%;background:rgba(192,57,43,0.2);animation:fall linear infinite;}
@keyframes fall{0%{transform:translateY(-20px);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(100vh);opacity:0}}

.stats{display:flex;justify-content:center;gap:60px;flex-wrap:wrap;padding:40px 60px;border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:var(--dark2);position:relative;z-index:1;transition:background 0.3s;}
.stat-item{text-align:center}
.stat-num{font-family:'Fraunces',serif;font-size:40px;font-weight:700;color:var(--red);line-height:1}
.stat-label{font-size:13px;color:var(--muted);margin-top:4px;letter-spacing:0.5px}

.section{padding:80px 60px;position:relative;z-index:1;transition:background 0.3s;}
.section-label{font-size:11px;color:var(--red);letter-spacing:2px;text-transform:uppercase;margin-bottom:12px}
.section-title{font-family:'Fraunces',serif;font-size:38px;font-weight:600;margin-bottom:16px}
.section-sub{font-size:15px;color:var(--muted);max-width:500px;line-height:1.7}
.blood-groups-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-top:48px;}
.bg-card{background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:24px;text-align:center;transition:all 0.25s;cursor:default;position:relative;overflow:hidden;box-shadow:0 2px 8px var(--shadow);}
.bg-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--red),var(--red2));transform:scaleX(0);transition:transform 0.3s;transform-origin:left;}
.bg-card:hover{border-color:var(--red);transform:translateY(-4px);box-shadow:0 8px 24px var(--shadow);}
.bg-card:hover::before{transform:scaleX(1)}
.bg-type{font-family:'Fraunces',serif;font-size:36px;font-weight:700;color:var(--red);margin-bottom:8px}
.bg-units{font-size:13px;color:var(--muted)}
.bg-bar{height:4px;background:var(--dark3);border-radius:2px;margin-top:12px;overflow:hidden}
.bg-bar-fill{height:100%;background:linear-gradient(90deg,var(--red),var(--red2));border-radius:2px;transition:width 1s ease}

.features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px}
.feature-card{background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:28px;transition:all 0.2s;box-shadow:0 2px 8px var(--shadow);}
.feature-card:hover{border-color:var(--red);box-shadow:0 6px 20px var(--shadow);}
.feature-icon{width:44px;height:44px;border-radius:10px;background:rgba(192,57,43,0.1);border:1px solid rgba(192,57,43,0.2);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:16px;}
.feature-title{font-size:16px;font-weight:600;margin-bottom:8px}
.feature-desc{font-size:13px;color:var(--muted);line-height:1.7}

.steps{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-top:48px;position:relative}
.steps::before{content:'';position:absolute;top:28px;left:10%;right:10%;height:1px;background:linear-gradient(90deg,transparent,var(--border),var(--border),transparent);z-index:0;}
.step{text-align:center;position:relative;z-index:1}
.step-num{width:56px;height:56px;border-radius:50%;background:var(--dark2);border:2px solid var(--red);display:flex;align-items:center;justify-content:center;font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--red);margin:0 auto 16px;box-shadow:0 2px 12px var(--shadow);}
.step-title{font-size:14px;font-weight:600;margin-bottom:6px}
.step-desc{font-size:12px;color:var(--muted);line-height:1.6}

.cta{margin:0 60px 80px;border-radius:20px;background:var(--dark2);border:1px solid var(--border);padding:60px;text-align:center;position:relative;overflow:hidden;z-index:1;box-shadow:0 4px 20px var(--shadow);}
.cta::before{content:'';position:absolute;top:-50%;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse,rgba(192,57,43,0.08) 0%,transparent 70%);pointer-events:none;}
.cta h2{font-family:'Fraunces',serif;font-size:40px;font-weight:700;margin-bottom:16px}
.cta p{color:var(--muted);font-size:15px;margin-bottom:32px}

footer{border-top:1px solid var(--border);padding:30px 60px;display:flex;justify-content:space-between;align-items:center;color:var(--muted);font-size:13px;position:relative;z-index:1;background:var(--dark2);transition:background 0.3s;}

@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.reveal{opacity:0;transform:translateY(24px);transition:all 0.6s ease}
.reveal.visible{opacity:1;transform:translateY(0)}

@media(max-width:900px){
  nav{padding:16px 24px}
  .nav-links{display:none}
  .section{padding:60px 24px}
  .blood-groups-grid{grid-template-columns:repeat(2,1fr)}
  .features-grid{grid-template-columns:1fr}
  .steps{grid-template-columns:repeat(2,1fr)}
  .stats{padding:30px 24px;gap:30px}
  .cta{margin:0 24px 60px;padding:40px 24px}
  footer{padding:24px;flex-direction:column;gap:8px;text-align:center}
}
</style>
</head>
<body>

<nav>
  <div class="nav-logo">Life<span>Flow</span> 🩸</div>
  <ul class="nav-links">
    <li><a href="#blood-groups">Blood Stock</a></li>
    <li><a href="#features">Features</a></li>
    <li><a href="#how">How It Works</a></li>
  </ul>
  <div class="nav-actions">
    <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode" id="themeBtn">🌙</button>
    <a href="login.php" class="btn btn-outline">Login</a>
    <a href="register.php" class="btn btn-red">Register as Donor</a>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="blood-drops" id="drops"></div>
  <div>
    <div class="hero-badge"><div class="pulse"></div> Saving Lives Every Day</div>
    <h1>Every Drop<br>of Blood <em>Matters</em></h1>
    <p>A complete blood bank management system connecting donors, patients, and hospitals — making blood availability faster and more reliable.</p>
    <div class="hero-actions">
      <a href="register.php" class="btn btn-red btn-lg">🩸 Become a Donor</a>
      <a href="request.php" class="btn btn-outline btn-lg">Request Blood</a>
      <a href="admin/login.php" class="btn btn-outline btn-lg">Admin Panel</a>
    </div>
  </div>
</section>

<!-- Stats -->
<div class="stats">
  <div class="stat-item"><div class="stat-num" id="sDonors">0</div><div class="stat-label">Registered Donors</div></div>
  <div class="stat-item"><div class="stat-num" id="sUnits">0</div><div class="stat-label">Units Available</div></div>
  <div class="stat-item"><div class="stat-num" id="sRequests">0</div><div class="stat-label">Requests Fulfilled</div></div>
  <div class="stat-item"><div class="stat-num" id="sLives">0</div><div class="stat-label">Lives Saved</div></div>
</div>

<!-- Blood Groups -->
<section class="section" id="blood-groups">
  <div class="reveal">
    <div class="section-label">Live Inventory</div>
    <div class="section-title">Blood Stock Status</div>
    <div class="section-sub">Real-time availability of all blood groups. Updated as donations come in and requests are fulfilled.</div>
  </div>
  <div class="blood-groups-grid reveal">
    <?php
    // In a real PHP environment, this would fetch from DB
    $groups = [
      'A+' => 15, 'A-' => 8, 'B+' => 20, 'B-' => 5,
      'AB+' => 10, 'AB-' => 3, 'O+' => 25, 'O-' => 7
    ];
    $max = 30;
    foreach($groups as $group => $units):
      $pct = round(($units / $max) * 100);
      $color = $units <= 5 ? '#e74c3c' : ($units <= 10 ? '#f39c12' : '#27ae60');
    ?>
    <div class="bg-card">
      <div class="bg-type"><?= $group ?></div>
      <div class="bg-units"><?= $units ?> units available</div>
      <div class="bg-bar"><div class="bg-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Features -->
<section class="section" id="features" style="background:var(--dark3)"><?php // uses CSS var so auto-updates ?>
  <div class="reveal">
    <div class="section-label">What We Offer</div>
    <div class="section-title">Built for Everyone</div>
    <div class="section-sub">From individual donors to hospitals — LifeFlow handles it all with ease.</div>
  </div>
  <div class="features-grid reveal">
    <div class="feature-card">
      <div class="feature-icon">👤</div>
      <div class="feature-title">Donor Management</div>
      <div class="feature-desc">Register donors, track donation history, manage eligibility and send reminders for next donation dates.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🩸</div>
      <div class="feature-title">Blood Stock Tracking</div>
      <div class="feature-desc">Real-time monitoring of blood units by group. Automatic alerts when stock runs critically low.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🏥</div>
      <div class="feature-title">Hospital Requests</div>
      <div class="feature-desc">Hospitals and patients can request blood units with urgency levels. Admins approve and track fulfillment.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon">🔍</div>
      <div class="feature-title">Donor Search</div>
      <div class="feature-desc">Search available donors by blood group, city or area. Find the nearest compatible donor instantly.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📊</div>
      <div class="feature-title">Admin Dashboard</div>
      <div class="feature-desc">Full control panel for admins to manage donors, requests, stock levels and generate reports.</div>
    </div>
    <div class="feature-card">
      <div class="feature-icon">📜</div>
      <div class="feature-title">Donation Certificates</div>
      <div class="feature-desc">Donors receive digital certificates for each donation. Printable and shareable proof of contribution.</div>
    </div>
  </div>
</section>

<!-- How it works -->
<section class="section" id="how">
  <div class="reveal">
    <div class="section-label">Process</div>
    <div class="section-title">How It Works</div>
    <div class="section-sub">Simple steps to donate or request blood through our platform.</div>
  </div>
  <div class="steps reveal">
    <div class="step">
      <div class="step-num">1</div>
      <div class="step-title">Register</div>
      <div class="step-desc">Sign up as a donor with your blood group and contact details.</div>
    </div>
    <div class="step">
      <div class="step-num">2</div>
      <div class="step-title">Get Verified</div>
      <div class="step-desc">Admin verifies your eligibility based on health criteria.</div>
    </div>
    <div class="step">
      <div class="step-num">3</div>
      <div class="step-title">Donate</div>
      <div class="step-desc">Visit the blood bank and donate. Your record is updated instantly.</div>
    </div>
    <div class="step">
      <div class="step-num">4</div>
      <div class="step-title">Save Lives</div>
      <div class="step-desc">Your blood reaches patients in need through our request system.</div>
    </div>
  </div>
</section>

<!-- CTA -->
<div class="cta reveal">
  <h2>Ready to Save a Life?</h2>
  <p>Join hundreds of donors making a difference. One donation can save up to 3 lives.</p>
  <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
    <a href="register.php" class="btn btn-red btn-lg">🩸 Register as Donor</a>
    <a href="request.php" class="btn btn-outline btn-lg">Request Blood Now</a>
  </div>
</div>

<!-- Footer -->
<footer>
  <div>🩸 <strong>LifeFlow</strong> — Blood Bank Management System</div>
  <div>Final Year Project · Computer Science</div>
</footer>

<script>
// Animated counters
function animateCount(el, target, duration=1500) {
  let start = 0;
  const step = target / (duration / 16);
  const timer = setInterval(() => {
    start += step;
    if (start >= target) { el.textContent = target + '+'; clearInterval(timer); }
    else el.textContent = Math.floor(start) + '+';
  }, 16);
}

window.addEventListener('load', () => {
  setTimeout(() => {
    animateCount(document.getElementById('sDonors'), 248);
    animateCount(document.getElementById('sUnits'), 93);
    animateCount(document.getElementById('sRequests'), 1200);
    animateCount(document.getElementById('sLives'), 3600);
  }, 500);

  // Animate bar fills
  document.querySelectorAll('.bg-bar-fill').forEach(b => {
    const w = b.style.width; b.style.width = '0';
    setTimeout(() => b.style.width = w, 800);
  });
});

// Scroll reveal
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Blood drops
const container = document.getElementById('drops');
for (let i = 0; i < 12; i++) {
  const drop = document.createElement('div');
  drop.className = 'drop';
  drop.style.cssText = `left:${Math.random()*100}%;top:${Math.random()*100}%;animation-duration:${4+Math.random()*6}s;animation-delay:${Math.random()*4}s;opacity:${0.1+Math.random()*0.2}`;
  container.appendChild(drop);
}

// Theme toggle
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.classList.contains('dark');
  html.className = isDark ? 'light' : 'dark';
  localStorage.setItem('lfTheme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '🌙' : '☀️';
}
// Set correct icon on load
window.addEventListener('DOMContentLoaded', () => {
  const isDark = document.documentElement.classList.contains('dark');
  document.getElementById('themeBtn').textContent = isDark ? '☀️' : '🌙';
});
</script>
</body>
</html>
