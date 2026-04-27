<?php
include 'includes/db.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email    = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone    = trim($_POST['phone']);
    $blood    = $_POST['blood_group'];
    $age      = (int)$_POST['age'];
    $gender   = $_POST['gender'];
    $city     = mysqli_real_escape_string($conn, trim($_POST['city']));
    $state    = mysqli_real_escape_string($conn, trim($_POST['state']));
    $address  = mysqli_real_escape_string($conn, trim($_POST['address']));
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // ── Validations ──────────────────────────────────────────────
    if (empty($name)) {
        $error = "Full name is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be exactly 10 digits (numbers only).";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address (must contain @).";
    } elseif ($age < 18 || $age > 65) {
        $error = "Donors must be between 18 and 65 years old.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check duplicate email
        $check = mysqli_query($conn, "SELECT id FROM donors WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "This email is already registered!";
        } else {
            $phone_safe = mysqli_real_escape_string($conn, $phone);
            $hashed     = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO donors (name,email,phone,blood_group,age,gender,city,state,address,password)
                    VALUES ('$name','$email','$phone_safe','$blood',$age,'$gender','$city','$state','$address','$hashed')";
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register as Donor — LifeFlow</title>
<style>
/* Light mode default */
:root{--red:#c0392b;--red2:#e74c3c;--red-light:#c0392b;--dark:#f8f5f4;--dark2:#ffffff;--dark3:#f0eceb;--border:#ddd0ce;--text:#1a1010;--muted:#6b5555;--muted2:#9e8888;--nav-bg:rgba(255,255,255,0.94);--shadow:rgba(192,57,43,0.08);}
/* Dark mode */
html.dark{--red:#c0392b;--red2:#e74c3c;--red-light:#ff6b6b;--dark:#0e0c0c;--dark2:#1a1515;--dark3:#241e1e;--border:#2e2525;--text:#f0ebe8;--muted:#8a7d7d;--muted2:#5a4f4f;--nav-bg:rgba(14,12,12,0.88);--shadow:rgba(0,0,0,0.4);}
.theme-toggle{width:38px;height:38px;border-radius:50%;border:1px solid var(--border);background:var(--dark3);color:var(--text);font-size:17px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;flex-shrink:0;}
.theme-toggle:hover{border-color:var(--red);transform:scale(1.08);}
</style>
<script>(function(){var s=localStorage.getItem('lfTheme')||'light';document.documentElement.className=s;})();</script>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@300;600;700&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>

*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);transition:background 0.3s,color 0.3s;min-height:100vh;display:flex;flex-direction:column}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 60% 40% at 30% 20%,rgba(192,57,43,0.1) 0%,transparent 60%);pointer-events:none}
nav{display:flex;align-items:center;justify-content:space-between;padding:18px 60px;border-bottom:1px solid var(--border);background:rgba(14,12,12,0.9);backdrop-filter:blur(10px);position:sticky;top:0;z-index:10}
.nav-logo{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--red);text-decoration:none}
.nav-logo span{color:var(--text)}
a.back{color:var(--muted);text-decoration:none;font-size:13px;transition:color 0.2s}
a.back:hover{color:var(--text)}

.page{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 20px;position:relative;z-index:1}
.card{background:var(--dark2);border:1px solid var(--border);border-radius:18px;padding:40px;width:100%;max-width:640px;animation:fadeUp 0.5s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card-header{margin-bottom:28px}
.card-title{font-family:'Fraunces',serif;font-size:28px;font-weight:700;margin-bottom:6px}
.card-sub{font-size:14px;color:var(--muted)}

.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px}
.alert-success{background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.3);color:#2ecc71}
.alert-error{background:rgba(192,57,43,0.15);border:1px solid rgba(192,57,43,0.3);color:var(--red)}

.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group.full{grid-column:1/-1}
label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
input,select,textarea{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:11px 14px;border-radius:8px;outline:none;transition:border-color 0.2s;width:100%}
input:focus,select:focus,textarea:focus{border-color:var(--red)}
select{appearance:none;cursor:pointer}
input::placeholder,textarea::placeholder{color:var(--muted2)}

.divider{height:1px;background:var(--border);margin:24px 0}
.section-title-sm{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:16px}

.btn{padding:13px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all 0.2s;width:100%}
.btn-red{background:var(--red);color:#fff}
.btn-red:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 20px rgba(192,57,43,0.4)}

.login-link{text-align:center;margin-top:16px;font-size:13px;color:var(--muted)}
.login-link a{color:var(--red);text-decoration:none}
.login-link a:hover{text-decoration:underline}
input.invalid{border-color:var(--red) !important;}
input.valid{border-color:#27ae60 !important;}
.field-hint{font-size:11px;margin-top:3px;display:none;}
.field-hint.error{color:var(--red);display:block;}
.field-hint.ok{color:#27ae60;display:block;}
</style>
</head>
<body>
<button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode" id="themeBtn" style="position:fixed;top:16px;right:16px;z-index:999">🌙</button>
<nav>
  <a href="index.php" class="nav-logo">Life<span>Flow</span> 🩸</a>
  <a href="index.php" class="back">← Back to Home</a>
</nav>
<div class="page">
  <div class="card">
    <div class="card-header">
      <div class="card-title">🩸 Register as Donor</div>
      <div class="card-sub">Join our community and help save lives. Fill in your details below.</div>
    </div>

    <?php if($success): ?><div class="alert alert-success">✓ <?= $success ?> <a href="login.php" style="color:inherit;font-weight:600">Login here →</a></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

    <form method="POST">
      <div class="section-title-sm">Personal Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Full Name *</label>
          <input type="text" name="name" placeholder="Your full name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Phone Number *</label>
          <input type="tel" id="phone" name="phone" placeholder="10-digit number" required
            maxlength="10" pattern="[0-9]{10}"
            oninput="this.value=this.value.replace(/[^0-9]/g,''); validatePhone()"
            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
          <span class="field-hint" id="phone-hint">Enter exactly 10 digits (numbers only)</span>
        </div>
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" id="email" name="email" placeholder="your@email.com"
            oninput="validateEmail()"
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          <span class="field-hint" id="email-hint">Must contain @ and a valid domain</span>
        </div>
        <div class="form-group">
          <label>Age *</label>
          <input type="number" name="age" min="18" max="65" placeholder="Must be 18–65" required value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="">Select gender</option>
            <option value="Male" <?= (($_POST['gender']??'')=='Male')?'selected':'' ?>>Male</option>
            <option value="Female" <?= (($_POST['gender']??'')=='Female')?'selected':'' ?>>Female</option>
            <option value="Other" <?= (($_POST['gender']??'')=='Other')?'selected':'' ?>>Other</option>
          </select>
        </div>
        <div class="form-group">
          <label>Blood Group *</label>
          <select name="blood_group" required>
            <option value="">Select blood group</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option value="<?=$bg?>" <?=(($_POST['blood_group']??'')==$bg)?'selected':''?>><?=$bg?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>City *</label>
          <input type="text" name="city" placeholder="Your city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>State</label>
          <input type="text" name="state" placeholder="Your state" value="<?= htmlspecialchars($_POST['state'] ?? '') ?>">
        </div>
        <div class="form-group full">
          <label>Address</label>
          <input type="text" name="address" placeholder="Full address (optional)" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
      </div>

      <div class="divider"></div>
      <div class="section-title-sm">Create Password</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Password *</label>
          <input type="password" id="password" name="password" placeholder="Min 6 characters"
            required minlength="6" oninput="validatePassword()">
          <span class="field-hint" id="pass-hint">Minimum 6 characters required</span>
        </div>
        <div class="form-group">
          <label>Confirm Password *</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password"
            required oninput="validateConfirm()">
          <span class="field-hint" id="confirm-hint">Passwords must match</span>
        </div>
      </div>

      <div style="margin-top:24px">
        <button type="submit" class="btn btn-red">🩸 Register as Donor</button>
      </div>
    </form>

    <div class="login-link">Already registered? <a href="login.php">Login here</a></div>
  </div>
</div>
<script>
function setHint(id, inputId, msg, isOk) {
  const hint  = document.getElementById(id);
  const input = document.getElementById(inputId);
  hint.textContent = msg;
  hint.className   = 'field-hint ' + (isOk ? 'ok' : 'error');
  input.className  = isOk ? 'valid' : 'invalid';
}

function validatePhone() {
  const val = document.getElementById('phone').value;
  if (val.length === 0) return;
  if (/^[0-9]{10}$/.test(val)) {
    setHint('phone-hint', 'phone', '✓ Valid phone number', true);
  } else if (val.length < 10) {
    setHint('phone-hint', 'phone', `✕ ${val.length}/10 digits entered — numbers only`, false);
  } else {
    setHint('phone-hint', 'phone', '✕ Must be exactly 10 digits', false);
  }
}

function validateEmail() {
  const val = document.getElementById('email').value;
  if (val.length === 0) return;
  const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
  if (ok) {
    setHint('email-hint', 'email', '✓ Valid email address', true);
  } else if (!val.includes('@')) {
    setHint('email-hint', 'email', '✕ Email must contain @', false);
  } else {
    setHint('email-hint', 'email', '✕ Enter a valid email (e.g. name@example.com)', false);
  }
}

function validatePassword() {
  const val = document.getElementById('password').value;
  if (val.length === 0) return;
  if (val.length >= 6) {
    setHint('pass-hint', 'password', `✓ Good — ${val.length} characters`, true);
  } else {
    setHint('pass-hint', 'password', `✕ Too short — ${val.length}/6 characters minimum`, false);
  }
  // re-check confirm if already typed
  if (document.getElementById('confirm_password').value.length > 0) validateConfirm();
}

function validateConfirm() {
  const pass    = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  if (confirm.length === 0) return;
  if (pass === confirm) {
    setHint('confirm-hint', 'confirm_password', '✓ Passwords match', true);
  } else {
    setHint('confirm-hint', 'confirm_password', '✕ Passwords do not match', false);
  }
}

// Block form submit if client-side errors exist
document.querySelector('form').addEventListener('submit', function(e) {
  const phone   = document.getElementById('phone').value;
  const email   = document.getElementById('email').value;
  const pass    = document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;

  if (!/^[0-9]{10}$/.test(phone)) {
    e.preventDefault();
    validatePhone();
    document.getElementById('phone').focus();
    return;
  }
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    e.preventDefault();
    validateEmail();
    document.getElementById('email').focus();
    return;
  }
  if (pass.length < 6) {
    e.preventDefault();
    validatePassword();
    document.getElementById('password').focus();
    return;
  }
  if (pass !== confirm) {
    e.preventDefault();
    validateConfirm();
    document.getElementById('confirm_password').focus();
    return;
  }
});

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
