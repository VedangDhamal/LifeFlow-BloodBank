<?php
include 'includes/db.php';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name  = mysqli_real_escape_string($conn, trim($_POST['patient_name']));
    $patient_age   = (int)$_POST['patient_age'];
    $blood_group   = $_POST['blood_group'];
    $units         = (int)$_POST['units_needed'];
    $hospital      = mysqli_real_escape_string($conn, trim($_POST['hospital_name']));
    $contact_name  = mysqli_real_escape_string($conn, trim($_POST['contact_name']));
    $contact_phone = trim($_POST['contact_phone']);
    $contact_email = trim($_POST['contact_email']);
    $reason        = mysqli_real_escape_string($conn, trim($_POST['reason']));
    $urgency       = $_POST['urgency'];

    // ── Validations ──────────────────────────────────────────────
    if (!preg_match('/^[0-9]{10}$/', $contact_phone)) {
        $error = "Phone number must be exactly 10 digits (numbers only).";
    } elseif (!empty($contact_email) && !filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address (must contain @).";
    } else {
        $phone_safe = mysqli_real_escape_string($conn, $contact_phone);
        $email_safe = mysqli_real_escape_string($conn, $contact_email);
        $sql = "INSERT INTO blood_requests (patient_name,patient_age,blood_group,units_needed,hospital_name,contact_name,contact_phone,contact_email,reason,urgency)
                VALUES ('$patient_name',$patient_age,'$blood_group',$units,'$hospital','$contact_name','$phone_safe','$email_safe','$reason','$urgency')";
        if (mysqli_query($conn, $sql)) {
            $success = "Your blood request has been submitted! Our team will contact you shortly.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Blood — LifeFlow</title>
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
body{font-family:'Outfit',sans-serif;background:var(--dark);color:var(--text);transition:background 0.3s,color 0.3s;min-height:100vh;display:flex;flex-direction:column}
body::before{content:'';position:fixed;inset:0;background:radial-gradient(ellipse 60% 40% at 70% 20%,rgba(192,57,43,0.08) 0%,transparent 60%);pointer-events:none}
nav{display:flex;align-items:center;justify-content:space-between;padding:18px 60px;border-bottom:1px solid var(--border);background:rgba(14,12,12,0.9);backdrop-filter:blur(10px);position:sticky;top:0;z-index:10}
.nav-logo{font-family:'Fraunces',serif;font-size:20px;font-weight:700;color:var(--red);text-decoration:none}
.nav-logo span{color:var(--text)}
a.back{color:var(--muted);text-decoration:none;font-size:13px}
a.back:hover{color:var(--text)}
.page{flex:1;display:flex;align-items:flex-start;justify-content:center;padding:40px 20px;position:relative;z-index:1}
.card{background:var(--dark2);border:1px solid var(--border);border-radius:18px;padding:40px;width:100%;max-width:640px;animation:fadeUp 0.5s ease}
@keyframes fadeUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.card-title{font-family:'Fraunces',serif;font-size:28px;font-weight:700;margin-bottom:6px}
.card-sub{font-size:14px;color:var(--muted);margin-bottom:28px}
.urgency-tabs{display:flex;gap:8px;margin-bottom:24px}
.urgency-tab{flex:1;padding:10px;border-radius:8px;border:1px solid var(--border);background:var(--dark3);color:var(--muted);font-size:13px;cursor:pointer;text-align:center;transition:all 0.2s;font-family:'Outfit',sans-serif}
.urgency-tab:hover{border-color:var(--muted2)}
.urgency-tab.active{border-color:var(--red);background:rgba(192,57,43,0.15);color:var(--red);font-weight:600}
.alert{padding:14px 16px;border-radius:8px;font-size:13px;margin-bottom:20px}
.alert-success{background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.3);color:#2ecc71}
.alert-error{background:rgba(192,57,43,0.15);border:1px solid rgba(192,57,43,0.3);color:var(--red)}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-group{display:flex;flex-direction:column;gap:6px}
.form-group.full{grid-column:1/-1}
label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px}
input,select,textarea{background:var(--dark3);border:1px solid var(--border);color:var(--text);font-family:'Outfit',sans-serif;font-size:13px;padding:11px 14px;border-radius:8px;outline:none;transition:border-color 0.2s;width:100%}
input:focus,select:focus,textarea:focus{border-color:var(--red)}
input::placeholder,textarea::placeholder{color:var(--muted2)}
select{appearance:none;cursor:pointer}
textarea{resize:vertical;min-height:80px}
.divider{height:1px;background:var(--border);margin:24px 0}
.section-title-sm{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:16px}
.btn{display:block;width:100%;padding:13px;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;background:var(--red);color:#fff;margin-top:8px;transition:all 0.2s}
.btn:hover{background:var(--red2);transform:translateY(-1px);box-shadow:0 4px 20px rgba(192,57,43,0.4)}
.critical-notice{background:rgba(192,57,43,0.1);border:1px solid rgba(192,57,43,0.2);border-radius:10px;padding:14px;margin-bottom:24px;font-size:13px;color:var(--muted)}
.critical-notice strong{color:var(--red)}
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
    <div class="card-title">🏥 Request Blood</div>
    <div class="card-sub">Fill in the details below. Our team will process your request as soon as possible.</div>

    <?php if($success): ?><div class="alert alert-success">✓ <?= $success ?></div><?php endif; ?>
    <?php if($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

    <div class="critical-notice">⚠️ For <strong>critical/life-threatening</strong> emergencies, please call your nearest hospital directly. This form is reviewed during business hours.</div>

    <form method="POST" id="reqForm">
      <input type="hidden" name="urgency" id="urgency_hidden" value="Normal">

      <div class="section-title-sm">Urgency Level</div>
      <div class="urgency-tabs">
        <div class="urgency-tab active" onclick="setUrgency('Normal',this)">🟢 Normal</div>
        <div class="urgency-tab" onclick="setUrgency('Urgent',this)">🟡 Urgent</div>
        <div class="urgency-tab" onclick="setUrgency('Critical',this)">🔴 Critical</div>
      </div>

      <div class="section-title-sm">Patient Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Patient Name *</label>
          <input type="text" name="patient_name" placeholder="Patient full name" required>
        </div>
        <div class="form-group">
          <label>Patient Age</label>
          <input type="number" name="patient_age" placeholder="Age" min="0" max="120">
        </div>
        <div class="form-group">
          <label>Blood Group Required *</label>
          <select name="blood_group" required>
            <option value="">Select blood group</option>
            <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
            <option value="<?=$bg?>"><?=$bg?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Units Needed *</label>
          <input type="number" name="units_needed" placeholder="No. of units" min="1" max="20" required>
        </div>
        <div class="form-group full">
          <label>Hospital / Location *</label>
          <input type="text" name="hospital_name" placeholder="Hospital or clinic name" required>
        </div>
        <div class="form-group full">
          <label>Reason / Diagnosis</label>
          <textarea name="reason" placeholder="Brief reason for blood requirement..."></textarea>
        </div>
      </div>

      <div class="divider"></div>
      <div class="section-title-sm">Contact Information</div>
      <div class="form-grid">
        <div class="form-group">
          <label>Contact Person *</label>
          <input type="text" name="contact_name" placeholder="Name of contact person" required>
        </div>
        <div class="form-group">
          <label>Phone Number *</label>
          <input type="tel" id="contact_phone" name="contact_phone" placeholder="10-digit number" required
            maxlength="10" pattern="[0-9]{10}"
            oninput="this.value=this.value.replace(/[^0-9]/g,''); validatePhone()">
          <span class="field-hint" id="phone-hint">Enter exactly 10 digits (numbers only)</span>
        </div>
        <div class="form-group full">
          <label>Email Address</label>
          <input type="email" id="contact_email" name="contact_email" placeholder="For updates on your request"
            oninput="validateEmail()">
          <span class="field-hint" id="email-hint">Must contain @ and a valid domain</span>
        </div>
      </div>

      <button type="submit" class="btn">Submit Blood Request →</button>
    </form>
  </div>
</div>
<script>
function setUrgency(val, el) {
  document.getElementById('urgency_hidden').value = val;
  document.querySelectorAll('.urgency-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
}

function setHint(id, inputId, msg, isOk) {
  const hint  = document.getElementById(id);
  const input = document.getElementById(inputId);
  hint.textContent = msg;
  hint.className   = 'field-hint ' + (isOk ? 'ok' : 'error');
  input.className  = isOk ? 'valid' : 'invalid';
}

function validatePhone() {
  const val = document.getElementById('contact_phone').value;
  if (val.length === 0) return;
  if (/^[0-9]{10}$/.test(val)) {
    setHint('phone-hint', 'contact_phone', '✓ Valid phone number', true);
  } else if (val.length < 10) {
    setHint('phone-hint', 'contact_phone', `✕ ${val.length}/10 digits entered — numbers only`, false);
  } else {
    setHint('phone-hint', 'contact_phone', '✕ Must be exactly 10 digits', false);
  }
}

function validateEmail() {
  const val = document.getElementById('contact_email').value;
  if (val.length === 0) return;
  const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val);
  if (ok) {
    setHint('email-hint', 'contact_email', '✓ Valid email address', true);
  } else if (!val.includes('@')) {
    setHint('email-hint', 'contact_email', '✕ Email must contain @', false);
  } else {
    setHint('email-hint', 'contact_email', '✕ Enter a valid email (e.g. name@example.com)', false);
  }
}

document.querySelector('form').addEventListener('submit', function(e) {
  const phone = document.getElementById('contact_phone').value;
  const email = document.getElementById('contact_email').value;
  if (!/^[0-9]{10}$/.test(phone)) {
    e.preventDefault();
    validatePhone();
    document.getElementById('contact_phone').focus();
    return;
  }
  if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    e.preventDefault();
    validateEmail();
    document.getElementById('contact_email').focus();
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
