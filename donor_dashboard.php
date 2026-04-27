<?php
session_start();
if (!isset($_SESSION['donor_id'])) { header('Location: login.php'); exit; }
include 'includes/db.php';

$id = $_SESSION['donor_id'];
$donor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donors WHERE id=$id"));
$donations = mysqli_query($conn, "SELECT * FROM donations WHERE donor_id=$id ORDER BY donation_date DESC");
$donation_count = mysqli_num_rows($donations);
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Dashboard — LifeFlow</title>
<?php include 'includes/theme_toggle.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
<style>

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);transition:background 0.3s,color 0.3s;min-height:100vh}
nav{display:flex;align-items:center;justify-content:space-between;padding:18px 60px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.nav-logo{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--red)}
.nav-logo span{color:var(--text)}
.nav-right{display:flex;align-items:center;gap:16px}
.nav-user{font-size:13px;color:var(--muted)}
a.btn-sm{padding:7px 16px;border-radius:7px;border:1px solid var(--border);color:var(--muted);font-size:12px;text-decoration:none;transition:all 0.2s}
a.btn-sm:hover{color:var(--text);border-color:var(--muted2)}
.content{padding:40px 60px}
.welcome{margin-bottom:32px}
.welcome h1{font-family:'Fraunces',serif;font-size:32px;font-weight:700;margin-bottom:4px}
.welcome p{font-size:14px;color:var(--muted)}
.blood-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(192,57,43,0.15);border:1px solid rgba(192,57,43,0.3);padding:4px 12px;border-radius:20px;font-size:13px;color:var(--red);font-weight:600;margin-top:8px}
.stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px}
.stat{background:var(--dark2);border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center}
.stat-val{font-family:'Fraunces',serif;font-size:32px;font-weight:700;color:var(--red);line-height:1}
.stat-label{font-size:12px;color:var(--muted);margin-top:6px}
.grid{display:grid;grid-template-columns:1fr 340px;gap:24px}
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.panel-header{display:flex;align-items:center;justify-content:space-between;padding:18px 24px;border-bottom:1px solid var(--border)}
.panel-title{font-family:'Fraunces',serif;font-size:16px;font-weight:600}
.panel-body{padding:24px}
.donor-profile{display:flex;flex-direction:column;gap:14px}
.profile-row{display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)}
.profile-row:last-child{border-bottom:none}
.profile-label{font-size:12px;color:var(--muted)}
.profile-value{font-size:13px;font-weight:500}
.donation-list{display:flex;flex-direction:column;gap:2px}
.donation-item{display:flex;align-items:center;gap:14px;padding:12px;border-radius:8px;transition:background 0.15s}
.donation-item:hover{background:var(--dark3)}
.don-date{font-size:12px;color:var(--muted);width:90px}
.don-info{flex:1}
.don-name{font-size:13px;font-weight:500}
.don-units{font-size:11px;color:var(--muted);margin-top:1px}
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;background:rgba(39,174,96,0.15);color:var(--success)}
.empty{text-align:center;padding:40px;color:var(--muted);font-size:14px}
.next-donation{background:rgba(192,57,43,0.08);border:1px solid rgba(192,57,43,0.2);border-radius:10px;padding:16px;margin-bottom:20px}
.next-donation-title{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.next-donation-date{font-family:'Fraunces',serif;font-size:18px;color:var(--red);font-weight:600}
.next-donation-note{font-size:12px;color:var(--muted);margin-top:4px}
.cert-banner{background:linear-gradient(135deg,rgba(192,57,43,0.15),rgba(139,26,26,0.1));border:1px solid rgba(192,57,43,0.3);border-radius:12px;padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:0}
.cert-banner-text{}
.cert-banner-title{font-size:14px;font-weight:600;margin-bottom:3px}
.cert-banner-sub{font-size:12px;color:var(--muted)}
a.cert-btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;background:var(--red);color:#fff;text-decoration:none;font-size:13px;font-weight:600;transition:all 0.2s;flex-shrink:0}
a.cert-btn:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 16px rgba(192,57,43,0.4)}
a.cert-btn.disabled{background:var(--dark3);color:var(--muted);pointer-events:none;border:1px solid var(--border)}
</style>
</head>
<body>
<button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode" id="themeBtn" style="position:fixed;top:16px;right:16px;z-index:999">🌙</button>
<nav>
  <div class="nav-logo">Life<span>Flow</span> 🩸</div>
  <div class="nav-right">
    <span class="nav-user">👤 <?= htmlspecialchars($donor['name']) ?></span>
    <a href="logout.php" class="btn-sm">Logout</a>
  </div>
</nav>
<div class="content">
  <div class="welcome">
    <h1>Hello, <?= htmlspecialchars(explode(' ', $donor['name'])[0]) ?>! 👋</h1>
    <p>Welcome to your donor dashboard.</p>
    <div class="blood-badge">🩸 <?= $donor['blood_group'] ?> Donor</div>
  </div>

  <div class="stats-row">
    <div class="stat"><div class="stat-val"><?= $donor['total_donations'] ?></div><div class="stat-label">Total Donations</div></div>
    <div class="stat"><div class="stat-val"><?= $donor['total_donations'] * 3 ?></div><div class="stat-label">Lives Impacted</div></div>
    <div class="stat"><div class="stat-val"><?= $donor['blood_group'] ?></div><div class="stat-label">Blood Group</div></div>
    <div class="stat"><div class="stat-val"><?= ucfirst(strtolower($donor['status'])) ?></div><div class="stat-label">Status</div></div>
  </div>

  <div class="grid">
    <!-- Donation History -->
    <div class="panel">
      <div class="panel-header">
        <div class="panel-title">Donation History</div>
        <span style="font-size:12px;color:var(--muted)"><?= $donation_count ?> donation(s)</span>
      </div>
      <div class="panel-body" style="padding:12px 16px">
        <?php if ($donation_count === 0): ?>
          <div class="empty">No donations recorded yet.<br><small>Visit the blood bank to make your first donation!</small></div>
        <?php else: ?>
          <div class="donation-list">
          <?php 
          mysqli_data_seek($donations, 0);
          while ($d = mysqli_fetch_assoc($donations)): ?>
            <div class="donation-item">
              <div class="don-date"><?= date('d M Y', strtotime($d['donation_date'])) ?></div>
              <div class="don-info">
                <div class="don-name">Blood Donation</div>
                <div class="don-units"><?= $d['units'] ?> unit(s) · <?= $d['blood_group'] ?></div>
              </div>
              <span class="badge">✓ Done</span>
            </div>
          <?php endwhile; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Profile -->
    <div style="display:flex;flex-direction:column;gap:20px">
      <?php if ($donor['last_donation']): 
        $next = date('d M Y', strtotime($donor['last_donation'] . ' +56 days'));
        $today = new DateTime();
        $nextDate = new DateTime($donor['last_donation'] . ' +56 days');
        $canDonate = $today >= $nextDate;
      ?>
      <div class="panel">
        <div class="panel-body">
          <div class="next-donation">
            <div class="next-donation-title"><?= $canDonate ? '✅ Eligible to Donate' : '⏳ Next Donation Date' ?></div>
            <div class="next-donation-date"><?= $canDonate ? 'Now!' : $next ?></div>
            <div class="next-donation-note"><?= $canDonate ? 'You are eligible to donate blood!' : 'Donors must wait 56 days between donations' ?></div>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <div class="panel">
        <div class="panel-body">
          <div class="cert-banner">
            <div class="cert-banner-text">
              <div class="cert-banner-title">🏅 Donation Certificate</div>
              <?php if ($donor['total_donations'] > 0): ?>
                <div class="cert-banner-sub">You have <?= $donor['total_donations'] ?> donation(s) — get your certificate!</div>
              <?php else: ?>
                <div class="cert-banner-sub">Make your first donation to earn a certificate</div>
              <?php endif; ?>
            </div>
            <?php if ($donor['total_donations'] > 0): ?>
              <a href="certificate.php" target="_blank" class="cert-btn">🖨️ Get Certificate</a>
            <?php else: ?>
              <a class="cert-btn disabled">Not yet earned</a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="panel">
        <div class="panel-header"><div class="panel-title">My Profile</div></div>
        <div class="panel-body">
          <div class="donor-profile">
            <div class="profile-row"><span class="profile-label">Name</span><span class="profile-value"><?= htmlspecialchars($donor['name']) ?></span></div>
            <div class="profile-row"><span class="profile-label">Blood Group</span><span class="profile-value" style="color:var(--red)"><?= $donor['blood_group'] ?></span></div>
            <div class="profile-row"><span class="profile-label">Age</span><span class="profile-value"><?= $donor['age'] ?> years</span></div>
            <div class="profile-row"><span class="profile-label">Gender</span><span class="profile-value"><?= $donor['gender'] ?></span></div>
            <div class="profile-row"><span class="profile-label">Phone</span><span class="profile-value"><?= $donor['phone'] ?></span></div>
            <div class="profile-row"><span class="profile-label">City</span><span class="profile-value"><?= htmlspecialchars($donor['city']) ?></span></div>
            <div class="profile-row"><span class="profile-label">Member Since</span><span class="profile-value"><?= date('M Y', strtotime($donor['created_at'])) ?></span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
