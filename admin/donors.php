<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
include '../includes/db.php';

// Handle delete
if (isset($_POST['delete_donor'])) {
    $id = (int)$_POST['donor_id'];
    mysqli_query($conn, "DELETE FROM donors WHERE id=$id");
    header('Location: donors.php'); exit;
}

// Handle status toggle
if (isset($_POST['toggle_status'])) {
    $id = (int)$_POST['donor_id'];
    $current = $_POST['current_status'];
    $new = $current === 'Active' ? 'Inactive' : 'Active';
    mysqli_query($conn, "UPDATE donors SET status='$new' WHERE id=$id");
    header('Location: donors.php'); exit;
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$blood_filter = isset($_GET['blood']) ? $_GET['blood'] : '';
$where = "WHERE 1=1";
if ($search) $where .= " AND (name LIKE '%$search%' OR phone LIKE '%$search%' OR city LIKE '%$search%')";
if ($blood_filter) $where .= " AND blood_group='$blood_filter'";
$donors = mysqli_query($conn, "SELECT * FROM donors $where ORDER BY created_at DESC");
$total = mysqli_num_rows($donors);
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donors — LifeFlow Admin</title>

<style>
html.light{--red:#c0392b;--red2:#e74c3c;--red-light:#c0392b;--dark:#f8f5f4;--dark2:#ffffff;--dark3:#f0eceb;--border:#e0d5d3;--text:#1a1010;--muted:#6b5555;--muted2:#9e8888;--success:#1a7a3c;--warn:#b07d00;}
html.dark{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0d0f14;--dark2:#13161e;--dark3:#1a1e29;--border:#252a38;--text:#e8e6e0;--muted:#7a7f94;--muted2:#505570;--success:#5cbf8a;--warn:#f39c12;}
</style>
<script>(function(){var s=localStorage.getItem('lfTheme')||'light';document.documentElement.className=s;})();</script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0e0c0c;--dark2:#1a1515;--dark3:#241e1e;--border:#2e2525;--text:#f0ebe8;--muted:#8a7d7d;--muted2:#5a4f4f;--success:#27ae60}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);display:flex;min-height:100vh}
.sidebar{width:220px;background:var(--dark2);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:24px 0;position:sticky;top:0;height:100vh;flex-shrink:0}
.sb-logo{font-family:'Fraunces',serif;font-size:18px;font-weight:700;color:var(--red-light);padding:0 20px 20px;border-bottom:1px solid var(--border);margin-bottom:16px}
.sb-logo span{color:var(--text)}
.sb-label{font-size:10px;color:var(--muted2);letter-spacing:2px;text-transform:uppercase;padding:0 20px;margin:12px 0 6px}
.sb-item{display:flex;align-items:center;gap:10px;padding:9px 20px;color:var(--muted);font-size:13px;border-left:2px solid transparent;transition:all 0.2s;text-decoration:none}
.sb-item:hover{color:var(--text);background:rgba(255,255,255,0.02)}
.sb-item.active{color:var(--red-light);border-left-color:var(--red);background:rgba(192,57,43,0.07)}
.sb-footer{margin-top:auto;padding:16px 20px 0;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.sb-footer strong{color:var(--text);display:block;font-size:13px;margin-bottom:2px}
a.logout{color:var(--muted);font-size:12px;text-decoration:none;margin-top:8px;display:block}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 32px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.page-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700}
.content{padding:28px 32px}
.filters{display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;align-items:center}
input.search{background:var(--dark2);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:9px 14px;border-radius:8px;outline:none;width:240px;transition:border-color 0.2s}
input.search:focus{border-color:var(--red)}
input.search::placeholder{color:var(--muted2)}
select.filter{background:var(--dark2);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:9px 14px;border-radius:8px;outline:none;cursor:pointer;appearance:none}
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:12px;overflow:hidden}
table{width:100%;border-collapse:collapse}
th{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:rgba(0,0,0,0.2)}
td{font-size:13px;padding:11px 16px;border-bottom:1px solid rgba(46,37,37,0.5);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,0.02)}
.badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:10px;font-weight:600;text-transform:uppercase}
.badge-Active{background:rgba(39,174,96,0.15);color:var(--success)}
.badge-Inactive{background:rgba(192,57,43,0.15);color:var(--red-light)}
.blood-tag{font-size:13px;font-weight:700;color:var(--red-light)}
.donor-avatar{width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,var(--red),#8b1a1a);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;color:#fff;flex-shrink:0}
.donor-cell{display:flex;align-items:center;gap:10px}
.btn-sm{padding:4px 10px;border-radius:6px;font-family:'Outfit',sans-serif;font-size:11px;font-weight:600;cursor:pointer;border:1px solid;transition:all 0.15s}
.btn-toggle{background:transparent;border-color:var(--border);color:var(--muted)}
.btn-toggle:hover{border-color:var(--muted2);color:var(--text)}
.btn-del{background:transparent;border-color:rgba(192,57,43,0.3);color:var(--red-light)}
.btn-del:hover{background:rgba(192,57,43,0.2)}
.count-badge{background:var(--dark3);border:1px solid var(--border);padding:4px 12px;border-radius:20px;font-size:12px;color:var(--muted)}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-logo">Life<span>Flow</span> 🩸</div>
  <div class="sb-label">Main</div>
  <a href="dashboard.php" class="sb-item">⬡ Dashboard</a>
  <a href="donors.php" class="sb-item active">👤 Donors</a>
  <a href="requests.php" class="sb-item">🏥 Blood Requests</a>
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
    <div class="page-title">Donors</div>
    <span class="count-badge"><?= $total ?> total</span>
  </div>
  <div class="content">
    <form method="GET" class="filters">
      <input type="text" name="search" class="search" placeholder="🔍 Search by name, phone, city..." value="<?= htmlspecialchars($search) ?>">
      <select name="blood" class="filter" onchange="this.form.submit()">
        <option value="">All Blood Groups</option>
        <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
        <option value="<?=$bg?>" <?=$blood_filter===$bg?'selected':''?>><?=$bg?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" style="padding:9px 16px;border-radius:8px;background:var(--red);color:#fff;border:none;font-family:'Outfit',sans-serif;font-size:13px;cursor:pointer">Search</button>
      <?php if($search || $blood_filter): ?><a href="donors.php" style="font-size:13px;color:var(--muted);text-decoration:none;padding:9px">Clear</a><?php endif; ?>
    </form>

    <div class="panel">
      <table>
        <thead><tr><th>Donor</th><th>Blood Group</th><th>Age / Gender</th><th>Contact</th><th>City</th><th>Donations</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php if (mysqli_num_rows($donors) === 0): ?>
          <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--muted)">No donors found</td></tr>
        <?php else: ?>
        <?php 
        mysqli_data_seek($donors, 0);
        while ($d = mysqli_fetch_assoc($donors)):
          $initials = strtoupper(substr($d['name'],0,1) . (strpos($d['name'],' ')!==false ? substr($d['name'],strpos($d['name'],' ')+1,1) : ''));
        ?>
          <tr>
            <td>
              <div class="donor-cell">
                <div class="donor-avatar"><?= $initials ?></div>
                <div>
                  <div style="font-weight:500"><?= htmlspecialchars($d['name']) ?></div>
                  <div style="font-size:11px;color:var(--muted)"><?= htmlspecialchars($d['email']) ?></div>
                </div>
              </div>
            </td>
            <td><span class="blood-tag"><?= $d['blood_group'] ?></span></td>
            <td><?= $d['age'] ?> / <?= $d['gender'] ?></td>
            <td><?= $d['phone'] ?></td>
            <td><?= htmlspecialchars($d['city']) ?></td>
            <td style="text-align:center"><?= $d['total_donations'] ?></td>
            <td><span class="badge badge-<?= $d['status'] ?>"><?= $d['status'] ?></span></td>
            <td style="display:flex;gap:6px">
              <form method="POST" style="display:inline">
                <input type="hidden" name="donor_id" value="<?= $d['id'] ?>">
                <input type="hidden" name="current_status" value="<?= $d['status'] ?>">
                <button type="submit" name="toggle_status" class="btn-sm btn-toggle"><?= $d['status']==='Active'?'Deactivate':'Activate' ?></button>
              </form>
              <form method="POST" style="display:inline" onsubmit="return confirm('Delete this donor?')">
                <input type="hidden" name="donor_id" value="<?= $d['id'] ?>">
                <button type="submit" name="delete_donor" class="btn-sm btn-del">Delete</button>
              </form>
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
