<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
include '../includes/db.php';
$success = $error = '';

// Add donation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_donation'])) {
    $donor_id = (int)$_POST['donor_id'];
    $blood_group = $_POST['blood_group'];
    $units = (int)$_POST['units'];
    $date = $_POST['donation_date'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $donor_res = mysqli_query($conn, "SELECT name FROM donors WHERE id=$donor_id");
    $donor_row = mysqli_fetch_assoc($donor_res);
    $donor_name = $donor_row ? mysqli_real_escape_string($conn, $donor_row['name']) : '';

    mysqli_query($conn, "INSERT INTO donations (donor_id,donor_name,blood_group,units,donation_date,notes) VALUES ($donor_id,'$donor_name','$blood_group',$units,'$date','$notes')");
    mysqli_query($conn, "UPDATE donors SET total_donations=total_donations+1, last_donation='$date' WHERE id=$donor_id");
    mysqli_query($conn, "UPDATE blood_stock SET units_available=units_available+$units WHERE blood_group='$blood_group'");
    $success = "Donation recorded successfully!";
}

$donations = mysqli_query($conn, "SELECT d.*, don.name as dname FROM donations d LEFT JOIN donors don ON d.donor_id=don.id ORDER BY d.donation_date DESC LIMIT 50");
$donors_list = mysqli_query($conn, "SELECT id, name, blood_group FROM donors WHERE status='Active' ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donations — LifeFlow Admin</title>

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
.sb-item:hover{color:var(--text)}
.sb-item.active{color:var(--red-light);border-left-color:var(--red);background:rgba(192,57,43,0.07)}
.sb-footer{margin-top:auto;padding:16px 20px 0;border-top:1px solid var(--border);font-size:12px;color:var(--muted)}
.sb-footer strong{color:var(--text);display:block;margin-bottom:2px}
a.logout{color:var(--muted);font-size:12px;text-decoration:none;margin-top:8px;display:block}
.main{flex:1;display:flex;flex-direction:column;min-width:0}
.topbar{display:flex;align-items:center;justify-content:space-between;padding:18px 32px;border-bottom:1px solid var(--border);background:var(--dark2);position:sticky;top:0;z-index:10}
.page-title{font-family:'Fraunces',serif;font-size:22px;font-weight:700}
.content{padding:28px 32px}
.grid{display:grid;grid-template-columns:1fr 340px;gap:24px}
.panel{background:var(--dark2);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.panel-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border)}
.panel-title{font-family:'Fraunces',serif;font-size:15px;font-weight:600}
table{width:100%;border-collapse:collapse}
th{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;padding:10px 16px;text-align:left;border-bottom:1px solid var(--border);background:rgba(0,0,0,0.2)}
td{font-size:13px;padding:11px 16px;border-bottom:1px solid rgba(46,37,37,0.5)}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,0.02)}
.blood-tag{font-weight:700;color:var(--red-light)}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert-success{background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.3);color:var(--success)}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
input,select,textarea{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:10px 12px;border-radius:8px;outline:none;transition:border-color 0.2s;width:100%}
input:focus,select:focus{border-color:var(--red)}
select{appearance:none;cursor:pointer}
.btn{display:block;width:100%;padding:12px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:13px;font-weight:600;cursor:pointer;border:none;background:var(--red);color:#fff;transition:all 0.2s}
.btn:hover{background:var(--red2)}
</style>
</head>
<body>
<div class="sidebar">
  <div class="sb-logo">Life<span>Flow</span> 🩸</div>
  <div class="sb-label">Main</div>
  <a href="dashboard.php" class="sb-item">⬡ Dashboard</a>
  <a href="donors.php" class="sb-item">👤 Donors</a>
  <a href="requests.php" class="sb-item">🏥 Blood Requests</a>
  <a href="stock.php" class="sb-item">🩸 Blood Stock</a>
  <a href="donations.php" class="sb-item active">📋 Donations</a>
  <div class="sb-label">Other</div>
  <a href="../index.php" class="sb-item">🌐 View Website</a>
  <div class="sb-footer">
    <strong><?= htmlspecialchars($_SESSION['admin_name']) ?></strong>Administrator
    <a href="logout.php" class="logout">→ Logout</a>
    <button onclick="toggleTheme()" id="themeBtn" style="background:transparent;border:1px solid var(--border);color:var(--muted);border-radius:6px;padding:4px 10px;font-size:13px;cursor:pointer;margin-top:8px;width:100%;transition:all 0.2s">🌙 Toggle Theme</button>
  </div>
</div>
<div class="main">
  <div class="topbar"><div class="page-title">Donations Log</div></div>
  <div class="content">
    <div class="grid">
      <!-- Donations table -->
      <div class="panel">
        <div class="panel-header"><div class="panel-title">Recent Donations</div></div>
        <table>
          <thead><tr><th>Donor</th><th>Blood Group</th><th>Units</th><th>Date</th><th>Notes</th></tr></thead>
          <tbody>
          <?php if (mysqli_num_rows($donations) === 0): ?>
            <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--muted)">No donations recorded yet</td></tr>
          <?php else: ?>
          <?php while ($d = mysqli_fetch_assoc($donations)): ?>
            <tr>
              <td style="font-weight:500"><?= htmlspecialchars($d['donor_name'] ?: $d['dname'] ?: 'Unknown') ?></td>
              <td><span class="blood-tag"><?= $d['blood_group'] ?></span></td>
              <td><?= $d['units'] ?></td>
              <td style="color:var(--muted)"><?= date('d M Y', strtotime($d['donation_date'])) ?></td>
              <td style="color:var(--muted);font-size:12px"><?= htmlspecialchars($d['notes']) ?: '—' ?></td>
            </tr>
          <?php endwhile; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Add donation form -->
      <div>
        <div class="panel">
          <div class="panel-header"><div class="panel-title">Record Donation</div></div>
          <div style="padding:20px">
            <?php if($success): ?><div class="alert alert-success">✓ <?= $success ?></div><?php endif; ?>
            <form method="POST">
              <div class="form-group">
                <label>Donor *</label>
                <select name="donor_id" required>
                  <option value="">Select donor</option>
                  <?php 
                  mysqli_data_seek($donors_list, 0);
                  while ($d = mysqli_fetch_assoc($donors_list)): ?>
                  <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> (<?= $d['blood_group'] ?>)</option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Blood Group *</label>
                <select name="blood_group" required>
                  <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                  <option value="<?=$bg?>"><?=$bg?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Units *</label>
                <input type="number" name="units" value="1" min="1" max="5" required>
              </div>
              <div class="form-group">
                <label>Donation Date *</label>
                <input type="date" name="donation_date" value="<?= date('Y-m-d') ?>" required>
              </div>
              <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" placeholder="Optional notes">
              </div>
              <button type="submit" name="add_donation" class="btn">🩸 Record Donation</button>
            </form>
          </div>
        </div>
      </div>
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
