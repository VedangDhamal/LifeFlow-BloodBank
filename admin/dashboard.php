<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
include '../includes/db.php';

// Stats
$total_donors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM donors"))[0];
$total_requests = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood_requests"))[0];
$pending_requests = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood_requests WHERE status='Pending'"))[0];
$total_units = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(units_available) FROM blood_stock"))[0];

$blood_stock = mysqli_query($conn, "SELECT * FROM blood_stock ORDER BY blood_group");
$recent_requests = mysqli_query($conn, "SELECT * FROM blood_requests ORDER BY requested_at DESC LIMIT 8");
$recent_donors = mysqli_query($conn, "SELECT * FROM donors ORDER BY created_at DESC LIMIT 6");

// Handle request status update
if (isset($_POST['update_request'])) {
    $rid = (int)$_POST['request_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE blood_requests SET status='$status' WHERE id=$rid");
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — LifeFlow</title>

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

/* Sidebar */
.sidebar{width:220px;background:var(--dark2);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:24px 0;position:sticky;top:0;height:100vh;flex-shrink:0}
.sb-logo{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--red-light);padding:0 20px 20px;border-bottom:1px solid var(--border);margin-bottom:16px}
.sb-logo span{color:var(--text)}
.sb-label{font-size:10px;color:var(--muted2);letter-spacing:2px;text-transform:uppercase;padding:0 20px;margin:12px 0 6px}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 20px;color:var(--muted);cursor:pointer;font-size:13px;border-left:2px solid transparent;transition:all 0.2s;text-decoration:none}
.sb-item:hover{color:var(--text);background:rgba(255,255,255,0.02)}
.sb-item.active{color:var(--red-light);border-left-color:var(--red);background:rgba(192,57,43,0.07)}
.sb-badge{margin-left:auto;background:var(--red);color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;font-weight:600}
.sb-footer{margin-top:auto;padding:16px 20px 0;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.sb-footer strong{color:var(--text);display:block;font-size:13px;margin-bottom:2px}
a.logout{color:var(--muted);font-size:12px;text-decoration:none;margin-top:8px;display:block}
a.logout:hover{color:var(--red-light)}

/* Main */
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 32px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.page-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700}
.content{padding:28px 32px;flex:1}

/* Stats */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
.stat{background:var(--dark2);border:1px solid var(--border);border-radius:12px;padding:18px;position:relative;overflow:hidden}
.stat::before{content:'';position:absolute;top:0;left:0;right:0;height:2px}
.stat:nth-child(1)::before{background:var(--red-light)}
.stat:nth-child(2)::before{background:var(--success)}
.stat:nth-child(3)::before{background:var(--warn)}
.stat:nth-child(4)::before{background:#7b8ff5}
.stat-label{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:8px}
.stat-val{font-family:'Fraunces',serif;font-size:30px;font-weight:700;line-height:1}

/* Grid */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;margin-bottom:20px}

/* Panel */
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:12px;overflow:hidden;margin-bottom:20px}
.panel-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)}
.panel-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600}
.panel-body{padding:0}

/* Table */
table{width:100%;border-collapse:collapse}
th{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:rgba(0,0,0,0.2)}
td{font-size:13px;padding:11px 16px;border-bottom:1px solid rgba(46,37,37,0.5);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,0.02)}

/* Badge */
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px}
.badge-pending{background:rgba(243,156,18,0.15);color:var(--warn)}
.badge-confirmed,.badge-approved{background:rgba(39,174,96,0.15);color:var(--success)}
.badge-cancelled,.badge-rejected{background:rgba(192,57,43,0.15);color:var(--red-light)}
.badge-fulfilled{background:rgba(123,143,245,0.15);color:#7b8ff5}
.badge-critical{background:rgba(192,57,43,0.2);color:var(--red-light);border:1px solid rgba(192,57,43,0.3)}
.badge-urgent{background:rgba(243,156,18,0.2);color:var(--warn)}
.badge-normal{background:rgba(255,255,255,0.05);color:var(--muted)}

/* Blood stock grid */
.stock-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;padding:16px}
.stock-card{background:var(--dark3);border:1px solid var(--border);border-radius:10px;padding:14px;text-align:center}
.stock-type{font-family:'Fraunces',serif;font-size:24px;font-weight:700;color:var(--red-light);line-height:1}
.stock-units{font-size:12px;color:var(--muted);margin-top:4px}
.stock-bar{height:3px;background:var(--dark2);border-radius:2px;margin-top:8px;overflow:hidden}
.stock-bar-fill{height:100%;border-radius:2px;transition:width 0.8s}

/* Action buttons */
.btn-action{padding:4px 10px;border-radius:6px;font-family:'Outfit',sans-serif;font-size:11px;font-weight:600;cursor:pointer;border:none;transition:all 0.15s}
.btn-approve{background:rgba(39,174,96,0.2);color:var(--success);border:1px solid rgba(39,174,96,0.3)}
.btn-approve:hover{background:rgba(39,174,96,0.35)}
.btn-reject{background:rgba(192,57,43,0.15);color:var(--red-light);border:1px solid rgba(192,57,43,0.3)}
.btn-reject:hover{background:rgba(192,57,43,0.3)}
.action-form{display:inline}

/* Mini donor list */
.donor-row{display:flex;align-items:center;gap:12px;padding:10px 20px;border-bottom:1px solid rgba(46,37,37,0.5)}
.donor-row:last-child{border-bottom:none}
.donor-avatar{width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--red),#8b1a1a);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#fff;flex-shrink:0}
.donor-info{flex:1}
.donor-name{font-size:13px;font-weight:500}
.donor-detail{font-size:11px;color:var(--muted)}
.blood-tag{font-size:12px;font-weight:700;color:var(--red-light)}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <div class="sb-logo">Life<span>Flow</span> 🩸</div>
  <div class="sb-label">Main</div>
  <a href="dashboard.php" class="sb-item active">⬡ Dashboard</a>
  <a href="donors.php" class="sb-item">👤 Donors</a>
  <a href="requests.php" class="sb-item">🏥 Blood Requests <span class="sb-badge"><?= $pending_requests ?></span></a>
  <a href="stock.php" class="sb-item">🩸 Blood Stock</a>
  <a href="donations.php" class="sb-item">📋 Donations</a>
  <div class="sb-label">Reports</div>
  <a href="../index.php" class="sb-item">🌐 View Website</a>
  <div class="sb-footer">
    <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong>
    Administrator
    <a href="logout.php" class="logout">→ Logout</a>
    <button onclick="toggleTheme()" id="themeBtn" style="background:transparent;border:1px solid var(--border);color:var(--muted);border-radius:6px;padding:4px 10px;font-size:13px;cursor:pointer;margin-top:8px;width:100%;transition:all 0.2s">🌙 Toggle Theme</button>
  </div>
</div>

<!-- Main -->
<div class="main">
  <div class="topbar">
    <div class="page-title">Admin Dashboard</div>
    <div style="font-size:13px;color:var(--muted)"><?= date('l, d F Y') ?></div>
  </div>

  <div class="content">
    <!-- Stats -->
    <div class="stats">
      <div class="stat"><div class="stat-label">Total Donors</div><div class="stat-val"><?= $total_donors ?></div></div>
      <div class="stat"><div class="stat-label">Units in Stock</div><div class="stat-val"><?= $total_units ?? 0 ?></div></div>
      <div class="stat"><div class="stat-label">Pending Requests</div><div class="stat-val"><?= $pending_requests ?></div></div>
      <div class="stat"><div class="stat-label">Total Requests</div><div class="stat-val"><?= $total_requests ?></div></div>
    </div>

    <!-- Blood Stock -->
    <div class="panel">
      <div class="panel-header"><div class="panel-title">Blood Stock Overview</div><a href="stock.php" style="font-size:12px;color:var(--muted);text-decoration:none">Manage →</a></div>
      <div class="stock-grid">
        <?php 
        mysqli_data_seek($blood_stock, 0);
        while ($s = mysqli_fetch_assoc($blood_stock)):
          $pct = min(100, round(($s['units_available'] / 30) * 100));
          $color = $s['units_available'] <= 5 ? '#e74c3c' : ($s['units_available'] <= 10 ? '#f39c12' : '#27ae60');
        ?>
        <div class="stock-card">
          <div class="stock-type"><?= $s['blood_group'] ?></div>
          <div class="stock-units"><?= $s['units_available'] ?> units</div>
          <div class="stock-bar"><div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div></div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <div class="grid-2">
      <!-- Recent Requests -->
      <div class="panel">
        <div class="panel-header"><div class="panel-title">Recent Blood Requests</div><a href="requests.php" style="font-size:12px;color:var(--muted);text-decoration:none">View all →</a></div>
        <div class="panel-body">
          <table>
            <thead><tr><th>Patient</th><th>Blood</th><th>Urgency</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($r = mysqli_fetch_assoc($recent_requests)): ?>
              <tr>
                <td>
                  <div style="font-weight:500"><?= htmlspecialchars($r['patient_name']) ?></div>
                  <div style="font-size:11px;color:var(--muted)"><?= $r['units_needed'] ?> unit(s) · <?= htmlspecialchars($r['hospital_name']) ?></div>
                </td>
                <td><span class="blood-tag"><?= $r['blood_group'] ?></span></td>
                <td><span class="badge badge-<?= strtolower($r['urgency']) ?>"><?= $r['urgency'] ?></span></td>
                <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
                <td>
                  <?php if ($r['status'] === 'Pending'): ?>
                  <form method="POST" class="action-form">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <input type="hidden" name="update_request" value="1">
                    <input type="hidden" name="status" value="Approved">
                    <button type="submit" class="btn-action btn-approve">✓</button>
                  </form>
                  <form method="POST" class="action-form" style="margin-left:4px">
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <input type="hidden" name="update_request" value="1">
                    <input type="hidden" name="status" value="Rejected">
                    <button type="submit" class="btn-action btn-reject">✕</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Recent Donors -->
      <div class="panel">
        <div class="panel-header"><div class="panel-title">Recent Donors</div><a href="donors.php" style="font-size:12px;color:var(--muted);text-decoration:none">View all →</a></div>
        <div class="panel-body">
          <?php while ($d = mysqli_fetch_assoc($recent_donors)): 
            $initials = strtoupper(substr($d['name'],0,1) . (strpos($d['name'],' ')!==false ? substr($d['name'],strpos($d['name'],' ')+1,1) : ''));
          ?>
          <div class="donor-row">
            <div class="donor-avatar"><?= $initials ?></div>
            <div class="donor-info">
              <div class="donor-name"><?= htmlspecialchars($d['name']) ?></div>
              <div class="donor-detail"><?= $d['city'] ?> · <?= $d['phone'] ?></div>
            </div>
            <div class="blood-tag"><?= $d['blood_group'] ?></div>
            <span class="badge badge-<?= strtolower($d['status']) ?>"><?= $d['status'] ?></span>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Animate stock bars
window.addEventListener('load', () => {
  document.querySelectorAll('.stock-bar-fill').forEach(b => {
    const w = b.style.width; b.style.width = '0';
    setTimeout(() => b.style.width = w, 300);
  });
});
</script>

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
