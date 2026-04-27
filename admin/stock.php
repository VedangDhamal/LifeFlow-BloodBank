<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
include '../includes/db.php';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    foreach ($_POST['units'] as $group => $units) {
        $units = max(0, (int)$units);
        $group = mysqli_real_escape_string($conn, $group);
        mysqli_query($conn, "UPDATE blood_stock SET units_available=$units WHERE blood_group='$group'");
    }
    $success = "Blood stock updated successfully!";
}

$stock = mysqli_query($conn, "SELECT * FROM blood_stock ORDER BY blood_group");
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blood Stock — LifeFlow Admin</title>

<style>
html.light{--red:#c0392b;--red2:#e74c3c;--red-light:#c0392b;--dark:#f8f5f4;--dark2:#ffffff;--dark3:#f0eceb;--border:#e0d5d3;--text:#1a1010;--muted:#6b5555;--muted2:#9e8888;--success:#1a7a3c;--warn:#b07d00;}
html.dark{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0d0f14;--dark2:#13161e;--dark3:#1a1e29;--border:#252a38;--text:#e8e6e0;--muted:#7a7f94;--muted2:#505570;--success:#5cbf8a;--warn:#f39c12;}
</style>
<script>(function(){var s=localStorage.getItem('lfTheme')||'light';document.documentElement.className=s;})();</script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0e0c0c;--dark2:#1a1515;--dark3:#241e1e;--border:#2e2525;--text:#f0ebe8;--muted:#8a7d7d;--muted2:#5a4f4f;--success:#27ae60;--warn:#f39c12}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);display:flex;min-height:100vh}
.sidebar{width:220px;background:var(--dark2);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:24px 0;position:sticky;top:0;height:100vh;flex-shrink:0}
.sb-logo{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--red-light);padding:0 20px 20px;border-bottom:1px solid var(--border);margin-bottom:16px}
.sb-logo span{color:var(--text)}
.sb-label{font-size:10px;color:var(--muted2);letter-spacing:2px;text-transform:uppercase;padding:0 20px;margin:12px 0 6px}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 20px;color:var(--muted);font-size:13px;border-left:2px solid transparent;transition:all 0.2s;text-decoration:none}
.sb-item:hover{color:var(--text)}
.sb-item.active{color:var(--red-light);border-left-color:var(--red);background:rgba(192,57,43,0.07)}
.sb-footer{margin-top:auto;padding:16px 20px 0;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.sb-footer strong{color:var(--text);display:block;margin-bottom:2px}
a.logout{color:var(--muted);font-size:12px;text-decoration:none;margin-top:8px;display:block}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 32px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.page-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700}
.content{padding:28px 32px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.3);color:var(--success)}
.stock-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px}
.stock-card{background:var(--dark2);border:1px solid var(--border);border-radius:14px;padding:24px;text-align:center;position:relative;overflow:hidden;transition:border-color 0.2s}
.stock-card:hover{border-color:var(--muted2)}
.stock-card.critical{border-color:rgba(192,57,43,0.4);background:rgba(192,57,43,0.05)}
.stock-card.low{border-color:rgba(243,156,18,0.4)}
.stock-type{font-family:'Fraunces',serif;font-size:40px;font-weight:700;color:var(--red-light);line-height:1;margin-bottom:4px}
.stock-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px}
.stock-input{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:18px;font-weight:600;padding:8px;border-radius:8px;outline:none;width:80px;text-align:center;transition:border-color 0.2s}
.stock-input:focus{border-color:var(--red)}
.stock-status{font-size:11px;margin-top:8px}
.s-critical{color:var(--red-light)}
.s-low{color:var(--warn)}
.s-ok{color:var(--success)}
.stock-bar{height:4px;background:var(--dark3);border-radius:2px;margin-top:12px;overflow:hidden}
.stock-bar-fill{height:100%;border-radius:2px}
.btn{padding:12px 28px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;background:var(--red);color:#fff;transition:all 0.2s}
.btn:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 20px rgba(192,57,43,0.4)}
.legend{display:flex;gap:20px;margin-bottom:20px;font-size:12px;color:var(--muted)}
.legend-item{display:flex;align-items:center;gap:6px}
.legend-dot{width:8px;height:8px;border-radius:50%}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-logo">Life<span>Flow</span> 🩸</div>
  <div class="sb-label">Main</div>
  <a href="dashboard.php" class="sb-item">⬡ Dashboard</a>
  <a href="donors.php" class="sb-item">👤 Donors</a>
  <a href="requests.php" class="sb-item">🏥 Blood Requests</a>
  <a href="stock.php" class="sb-item active">🩸 Blood Stock</a>
  <a href="donations.php" class="sb-item">📋 Donations</a>
  <div class="sb-label">Other</div>
  <a href="../index.php" class="sb-item">🌐 View Website</a>
  <div class="sb-footer">
    <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong>Administrator
    <a href="logout.php" class="logout">→ Logout</a>
    <button onclick="toggleTheme()" id="themeBtn" style="background:transparent;border:1px solid var(--border);color:var(--muted);border-radius:6px;padding:4px 10px;font-size:13px;cursor:pointer;margin-top:8px;width:100%;transition:all 0.2s">🌙 Toggle Theme</button>
  </div>
</div>
<div class="main">
  <div class="topbar">
    <div class="page-title">Blood Stock Management</div>
  </div>
  <div class="content">
    <?php if ($success): ?><div class="alert">✓ <?= $success ?></div><?php endif; ?>

    <div class="legend">
      <div class="legend-item"><div class="legend-dot" style="background:#e74c3c"></div> Critical (≤5 units)</div>
      <div class="legend-item"><div class="legend-dot" style="background:#f39c12"></div> Low (≤10 units)</div>
      <div class="legend-item"><div class="legend-dot" style="background:#27ae60"></div> OK (>10 units)</div>
    </div>

    <form method="POST">
      <div class="stock-grid">
        <?php 
        mysqli_data_seek($stock, 0);
        while ($s = mysqli_fetch_assoc($stock)):
          $u = $s['units_available'];
          $cls = $u <= 5 ? 'critical' : ($u <= 10 ? 'low' : '');
          $color = $u <= 5 ? '#e74c3c' : ($u <= 10 ? '#f39c12' : '#27ae60');
          $status = $u <= 5 ? 'CRITICAL' : ($u <= 10 ? 'LOW' : 'AVAILABLE');
          $statusCls = $u <= 5 ? 's-critical' : ($u <= 10 ? 's-low' : 's-ok');
          $pct = min(100, round(($u / 30) * 100));
        ?>
        <div class="stock-card <?= $cls ?>">
          <div class="stock-type"><?= $s['blood_group'] ?></div>
          <div class="stock-label">Units Available</div>
          <input type="number" name="units[<?= $s['blood_group'] ?>]" class="stock-input" value="<?= $u ?>" min="0" max="999">
          <div class="stock-status <?= $statusCls ?>"><?= $status ?></div>
          <div class="stock-bar"><div class="stock-bar-fill" style="width:<?=$pct?>%;background:<?=$color?>"></div></div>
        </div>
        <?php endwhile; ?>
      </div>
      <button type="submit" name="update_stock" class="btn">💾 Save Stock Updates</button>
    </form>
  </div>
</div>

<script>
function toggleTheme() {
  const html = document.documentElement;
  const isDark = html.classList.contains('dark');
  html.className = isDark ? 'light' : 'dark';
  localStorage.setItem('lfTheme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '🌙' : '☀️';
}
window.addEventListener('DOMContentLoaded', () => {
  const isDark = document.documentElement.classList.contains('dark');
  document.getElementById('themeBtn').textContent = isDark ? '☀️' : '🌙';
});
</script>
</body>
</html>
