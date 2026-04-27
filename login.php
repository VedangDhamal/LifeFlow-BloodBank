<?php
session_start();
include 'includes/db.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM donors WHERE email='$email' AND status='Active'");
    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['donor_id'] = $row['id'];
            $_SESSION['donor_name'] = $row['name'];
            $_SESSION['donor_blood'] = $row['blood_group'];
            header('Location: donor_dashboard.php');
            exit;
        }
    }
    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donor Login — LifeFlow</title>
<style>
/* Light mode default */
:root{--red:#c0392b;--red2:#e74c3c;--red-light:#c0392b;--dark:#f8f5f4;--dark2:#ffffff;--dark3:#f0eceb;--border:#ddd0ce;--text:#1a1010;--muted:#6b5555;--muted2:#9e8888;--nav-bg:rgba(255,255,255,0.94);--shadow:rgba(192,57,43,0.08);}
/* Dark mode */
html.dark{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0e0c0c;--dark2:#1a1515;--dark3:#241e1e;--border:#2e2525;--text:#f0ebe8;--muted:#8a7d7d;--muted2:#5a4f4f;--nav-bg:rgba(14,12,12,0.88);--shadow:rgba(0,0,0,0.4);}
.theme-toggle{width:38px;height:38px;border-radius:50%;border:1px solid var(--border);background:var(--dark3);color:var(--text);font-size:17px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;flex-shrink:0;}
.theme-toggle:hover{border-color:var(--red);transform:scale(1.08);}
</style>
<script>(function(){var s=localStorage.getItem('lfTheme')||'light';document.documentElement.className=s;})();</script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
<style>

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);transition:background 0.3s,color 0.3s;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 60% 40% at 50% 30%,rgba(192,57,43,0.1) 0%,transparent 60%);pointer-events:none}
.card{background:var(--dark2);border:1px solid var(--border);border-radius:18px;padding:40px;width:100%;max-width:420px;position:relative;z-index:1;animation:fadeUp 0.5s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.logo{font-family:'Fraunces',serif;font-size:22px;font-weight:700;color:var(--red);margin-bottom:32px;display:block;text-align:center}
.logo span{color:var(--text)}
h1{font-family:'Fraunces',serif;font-size:26px;font-weight:700;margin-bottom:6px}
.sub{font-size:14px;color:var(--muted);margin-bottom:28px}
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;background:rgba(192,57,43,0.15);border:1px solid rgba(192,57,43,0.3);color:var(--red)}
.form-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px}
label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
input{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:12px 14px;border-radius:8px;outline:none;transition:border-color 0.2s}
input:focus{border-color:var(--red)}
input::placeholder{color:var(--muted2)}
.btn{display:block;width:100%;padding:13px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;background:var(--red);color:#fff;margin-top:8px;transition:all 0.2s}
.btn:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 20px rgba(192,57,43,0.4)}
.links{display:flex;justify-content:space-between;margin-top:20px;font-size:13px;color:var(--muted)}
.links a{color:var(--red);text-decoration:none}
.links a:hover{text-decoration:underline}
.divider{display:flex;align-items:center;gap:12px;margin:20px 0;color:var(--muted2);font-size:12px}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
.btn-outline{background:transparent;border:1px solid var(--border);color:var(--muted);margin-top:0}
.btn-outline:hover{border-color:var(--muted2);color:var(--text);transform:none;box-shadow:none}
</style>
</head>
<body>
<button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode" id="themeBtn" style="position:fixed;top:16px;right:16px;z-index:999">🌙</button>
<div class="card">
  <a href="index.php" class="logo">Life<span>Flow</span> 🩸</a>
  <h1>Welcome back</h1>
  <div class="sub">Login to your donor account</div>

  <?php if($error): ?><div class="alert">✕ <?= $error ?></div><?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="your@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password" placeholder="Your password" required>
    </div>
    <button type="submit" class="btn">Login →</button>
  </form>

  <div class="divider">or</div>
  <a href="admin/login.php" class="btn btn-outline">🔐 Admin Login</a>

  <div class="links">
    <span>New donor? <a href="register.php">Register here</a></span>
    <a href="request.php">Request Blood</a>
  </div>
</div>
<script>
function toggleTheme(){
  var html = document.documentElement;
  var isDark = html.classList.contains('dark');
  html.className = isDark ? 'light' : 'dark';
  localStorage.setItem('lfTheme', isDark ? 'light' : 'dark');
  document.getElementById('themeBtn').textContent = isDark ? '🌙' : '☀️';
}
document.addEventListener('DOMContentLoaded', function(){
  document.getElementById('themeBtn').textContent =
    document.documentElement.classList.contains('dark') ? '☀️' : '🌙';
});
</script>
</body>
</html>
