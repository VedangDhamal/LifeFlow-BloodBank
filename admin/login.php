<?php
session_start();
include '../includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");
    if ($row = mysqli_fetch_assoc($result)) {
        if ($password === $row['password']) { // simple check; use password_hash in production
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['username'];
            header('Location: dashboard.php');
            exit;
        }
    }
    $error = "Invalid username or password.";
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — LifeFlow</title>

<style>
html.light{--red:#c0392b;--red2:#e74c3c;--red-light:#c0392b;--dark:#f8f5f4;--dark2:#ffffff;--dark3:#f0eceb;--border:#e0d5d3;--text:#1a1010;--muted:#6b5555;--muted2:#9e8888;--success:#1a7a3c;--warn:#b07d00;}
html.dark{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0d0f14;--dark2:#13161e;--dark3:#1a1e29;--border:#252a38;--text:#e8e6e0;--muted:#7a7f94;--muted2:#505570;--success:#5cbf8a;--warn:#f39c12;}
</style>
<script>(function(){var s=localStorage.getItem('lfTheme')||'light';document.documentElement.className=s;})();</script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0e0c0c;--dark2:#1a1515;--dark3:#241e1e;--border:#2e2525;--text:#f0ebe8;--muted:#8a7d7d;--muted2:#5a4f4f}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 50% 40% at 50% 40%,rgba(192,57,43,0.12) 0%,transparent 60%);pointer-events:none}
.card{background:var(--dark2);border:1px solid var(--border);border-radius:18px;padding:40px;width:100%;max-width:380px;position:relative;z-index:1;animation:fadeUp 0.5s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.admin-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(192,57,43,0.12);border:1px solid rgba(192,57,43,0.25);padding:5px 12px;border-radius:20px;font-size:11px;color:var(--red-light);letter-spacing:1px;text-transform:uppercase;margin-bottom:20px}
h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin-bottom:6px}
.sub{font-size:13px;color:var(--muted);margin-bottom:28px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;background:rgba(192,57,43,0.15);border:1px solid rgba(192,57,43,0.3);color:var(--red-light)}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
input{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:12px 14px;border-radius:8px;outline:none;transition:border-color 0.2s}
input:focus{border-color:var(--red)}
input::placeholder{color:var(--muted2)}
.btn{display:block;width:100%;padding:13px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;background:var(--red);color:#fff;margin-top:8px;transition:all 0.2s}
.btn:hover{background:var(--red2);box-shadow:0 4px 20px rgba(192,57,43,0.4)}
.back{display:block;text-align:center;margin-top:16px;font-size:13px;color:var(--muted);text-decoration:none}
.back:hover{color:var(--text)}
.hint{font-size:11px;color:var(--muted2);text-align:center;margin-top:12px}
</style>
</head>
<body>
<button onclick="toggleTheme()" id="themeBtn" style="position:fixed;top:16px;right:16px;z-index:999;background:var(--dark2);border:1px solid var(--border);color:var(--text);border-radius:8px;padding:6px 12px;font-size:13px;cursor:pointer">🌙</button>
<div class="card">
  <div class="admin-badge">🔐 Admin Access</div>
  <h1>Admin Login</h1>
  <div class="sub">Enter your admin credentials to continue</div>

  <?php if($error): ?><div class="alert">✕ <?= $error ?></div><?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" placeholder="admin" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Password" required>
    </div>
    <button type="submit" class="btn">Login to Admin Panel →</button>
  </form>
  <div class="hint">Default: username <strong>admin</strong> / password <strong>admin123</strong></div>
  <a href="../index.php" class="back">← Back to Home</a>
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
