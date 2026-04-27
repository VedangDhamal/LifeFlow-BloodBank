<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
include '../includes/db.php';

if (isset($_POST['update_request'])) {
    $rid = (int)$_POST['request_id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE blood_requests SET status='$status' WHERE id=$rid");
    header('Location: requests.php'); exit;
}

$filter = isset($_GET['status']) ? $_GET['status'] : '';
$where = $filter ? "WHERE status='$filter'" : '';
$requests = mysqli_query($conn, "SELECT * FROM blood_requests $where ORDER BY requested_at DESC");
$total = mysqli_num_rows($requests);
$pending = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM blood_requests WHERE status='Pending'"))[0];
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blood Requests — LifeFlow Admin</title>

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
.sb-badge{margin-left:auto;background:var(--red);color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;font-weight:600}
.sb-footer{margin-top:auto;padding:16px 20px 0;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.sb-footer strong{color:var(--text);display:block;margin-bottom:2px}
a.logout{color:var(--muted);font-size:12px;text-decoration:none;margin-top:8px;display:block}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 32px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.page-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700}
.content{padding:28px 32px}
.filter-tabs{display:flex;gap:4px;background:var(--dark2);border:1px solid var(--border);padding:4px;border-radius:10px;margin-bottom:20px;width:fit-content}
.filter-tab{padding:7px 16px;border-radius:7px;font-size:12px;font-weight:500;cursor:pointer;color:var(--muted);transition:all 0.15s;text-decoration:none}
.filter-tab:hover{color:var(--text)}
.filter-tab.active{background:var(--dark3);color:var(--text)}
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:rgba(0,0,0,0.2)}
td{font-size:13px;padding:12px 16px;border-bottom:1px solid rgba(46,37,37,0.5);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,0.02)}
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;text-transform:uppercase}
.badge-Pending{background:rgba(243,156,18,0.15);color:var(--warn)}
.badge-Approved{background:rgba(39,174,96,0.15);color:var(--success)}
.badge-Rejected{background:rgba(192,57,43,0.15);color:var(--red-light)}
.badge-Fulfilled{background:rgba(123,143,245,0.15);color:#7b8ff5}
.badge-Critical{background:rgba(192,57,43,0.2);color:var(--red-light);border:1px solid rgba(192,57,43,0.3)}
.badge-Urgent{background:rgba(243,156,18,0.2);color:var(--warn)}
.badge-Normal{background:rgba(255,255,255,0.05);color:var(--muted)}
.blood-tag{font-weight:700;color:var(--red-light)}
.btn-sm{padding:4px 10px;border-radius:6px;font-family:'Outfit',sans-serif;font-size:11px;font-weight:600;cursor:pointer;border:1px solid;transition:all 0.15s}
.btn-approve{background:transparent;border-color:rgba(39,174,96,0.3);color:var(--success)}
.btn-approve:hover{background:rgba(39,174,96,0.2)}
.btn-reject{background:transparent;border-color:rgba(192,57,43,0.3);color:var(--red-light)}
.btn-reject:hover{background:rgba(192,57,43,0.2)}
.btn-fulfill{background:transparent;border-color:rgba(123,143,245,0.3);color:#7b8ff5}
.btn-fulfill:hover{background:rgba(123,143,245,0.2)}
.actions-cell{display:flex;gap:6px;align-items:center}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-logo">Life<span>Flow</span> 🩸</div>
  <div class="sb-label">Main</div>
  <a href="dashboard.php" class="sb-item">⬡ Dashboard</a>
  <a href="donors.php" class="sb-item">👤 Donors</a>
  <a href="requests.php" class="sb-item active">🏥 Blood Requests <span class="sb-badge"><?= $pending ?></span></a>
  <a href="stock.php" class="sb-item">🩸 Blood Stock</a>
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
    <div class="page-title">Blood Requests</div>
    <span style="font-size:13px;color:var(--muted)"><?= $total ?> request(s)</span>
  </div>
  <div class="content">
    <div class="filter-tabs">
      <a href="requests.php" class="filter-tab <?= !$filter?'active':'' ?>">All</a>
      <a href="requests.php?status=Pending" class="filter-tab <?= $filter==='Pending'?'active':'' ?>">Pending (<?=$pending?>)</a>
      <a href="requests.php?status=Approved" class="filter-tab <?= $filter==='Approved'?'active':'' ?>">Approved</a>
      <a href="requests.php?status=Fulfilled" class="filter-tab <?= $filter==='Fulfilled'?'active':'' ?>">Fulfilled</a>
      <a href="requests.php?status=Rejected" class="filter-tab <?= $filter==='Rejected'?'active':'' ?>">Rejected</a>
    </div>

    <div class="panel">
      <table>
        <thead><tr><th>Patient</th><th>Blood</th><th>Units</th><th>Hospital</th><th>Contact</th><th>Urgency</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (mysqli_num_rows($requests) === 0): ?>
          <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--muted)">No requests found</td></tr>
        <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($requests)): ?>
          <tr>
            <td>
              <div style="font-weight:500"><?= htmlspecialchars($r['patient_name']) ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= $r['patient_age'] ? $r['patient_age'].' yrs' : '' ?></div>
            </td>
            <td><span class="blood-tag"><?= $r['blood_group'] ?></span></td>
            <td><?= $r['units_needed'] ?></td>
            <td style="max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($r['hospital_name']) ?></td>
            <td>
              <div><?= htmlspecialchars($r['contact_name']) ?></div>
              <div style="font-size:11px;color:var(--muted)"><?= $r['contact_phone'] ?></div>
            </td>
            <td><span class="badge badge-<?= $r['urgency'] ?>"><?= $r['urgency'] ?></span></td>
            <td style="font-size:12px;color:var(--muted)"><?= date('d M Y', strtotime($r['requested_at'])) ?></td>
            <td><span class="badge badge-<?= $r['status'] ?>"><?= $r['status'] ?></span></td>
            <td>
              <div class="actions-cell">
              <?php if ($r['status'] === 'Pending'): ?>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="update_request" value="1">
                  <input type="hidden" name="status" value="Approved">
                  <button type="submit" class="btn-sm btn-approve">Approve</button>
                </form>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="update_request" value="1">
                  <input type="hidden" name="status" value="Rejected">
                  <button type="submit" class="btn-sm btn-reject">Reject</button>
                </form>
              <?php elseif ($r['status'] === 'Approved'): ?>
                <form method="POST" style="display:inline">
                  <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                  <input type="hidden" name="update_request" value="1">
                  <input type="hidden" name="status" value="Fulfilled">
                  <button type="submit" class="btn-sm btn-fulfill">Mark Fulfilled</button>
                </form>
              <?php else: ?>
                <span style="font-size:11px;color:var(--muted2)">—</span>
              <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
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
