<?php
session_start();
include 'includes/db.php';

$donor_name = 'Donor Name';
$blood_group = 'O+';
$donation_number = 1;
$donation_date = date('d F Y');
$city = 'Your City';
$cert_id = 'LF-' . date('Y') . '-0001';
$suffix = 'st';

if (isset($_SESSION['donor_id'])) {
    $id = (int)$_SESSION['donor_id'];
    $donor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donors WHERE id=$id"));
    if ($donor) {
        $donor_name     = $donor['name'];
        $blood_group    = $donor['blood_group'];
        $donation_number = (int)$donor['total_donations'];
        $city           = $donor['city'];
        $donation_date  = $donor['last_donation'] ? date('d F Y', strtotime($donor['last_donation'])) : date('d F Y');
        $cert_id        = 'LF-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }
} elseif (isset($_GET['donor_id'])) {
    $id = (int)$_GET['donor_id'];
    $donor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM donors WHERE id=$id"));
    if ($donor) {
        $donor_name     = $donor['name'];
        $blood_group    = $donor['blood_group'];
        $donation_number = (int)$donor['total_donations'];
        $city           = $donor['city'];
        $donation_date  = $donor['last_donation'] ? date('d F Y', strtotime($donor['last_donation'])) : date('d F Y');
        $cert_id        = 'LF-' . date('Y') . '-' . str_pad($id, 4, '0', STR_PAD_LEFT);
    }
}

if ($donation_number == 1)      $suffix = 'st';
elseif ($donation_number == 2)  $suffix = 'nd';
elseif ($donation_number == 3)  $suffix = 'rd';
else                            $suffix = 'th';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Donation Certificate — LifeFlow</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Crimson+Text:ital,wght@0,400;0,600;1,400&family=Outfit:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Outfit', sans-serif;
    background: #1a0a0a;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 40px 20px;
    min-height: 100vh;
  }

  /* Screen-only controls */
  .controls {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .ctrl-btn {
    padding: 10px 24px;
    border-radius: 8px;
    font-family: 'Outfit', sans-serif;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .btn-print { background: #c0392b; color: #fff; }
  .btn-print:hover { background: #e74c3c; transform: translateY(-1px); }
  .btn-back { background: transparent; border: 1px solid #3a2a2a; color: #8a7d7d; }
  .btn-back:hover { color: #f0ebe8; border-color: #5a4f4f; }

  /* Certificate */
  .certificate-wrap {
    width: 800px;
    max-width: 100%;
  }

  .certificate {
    width: 800px;
    max-width: 100%;
    background: #fff;
    position: relative;
    overflow: hidden;
    aspect-ratio: 1.414 / 1; /* A4 landscape ratio */
  }

  /* Outer border frame */
  .cert-frame {
    position: absolute;
    inset: 12px;
    border: 1px solid #8b1a1a;
    z-index: 2;
    pointer-events: none;
  }

  .cert-frame-inner {
    position: absolute;
    inset: 16px;
    border: 3px solid #8b1a1a;
    z-index: 2;
    pointer-events: none;
  }

  /* Corner ornaments */
  .corner {
    position: absolute;
    width: 60px;
    height: 60px;
    z-index: 3;
  }
  .corner svg { width: 100%; height: 100%; }
  .corner-tl { top: 16px; left: 16px; }
  .corner-tr { top: 16px; right: 16px; transform: scaleX(-1); }
  .corner-bl { bottom: 16px; left: 16px; transform: scaleY(-1); }
  .corner-br { bottom: 16px; right: 16px; transform: scale(-1); }

  /* Background decorations */
  .cert-bg {
    position: absolute;
    inset: 0;
    z-index: 0;
  }

  .cert-bg-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.04;
    background: #8b1a1a;
  }

  .cert-bg-circle.c1 { width: 400px; height: 400px; top: -100px; left: -100px; }
  .cert-bg-circle.c2 { width: 300px; height: 300px; bottom: -80px; right: -80px; }
  .cert-bg-circle.c3 { width: 200px; height: 200px; top: 50%; left: 50%; transform: translate(-50%, -50%); opacity: 0.03; }

  /* Watermark */
  .cert-watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-family: 'Cinzel', serif;
    font-size: 120px;
    color: #8b1a1a;
    opacity: 0.04;
    white-space: nowrap;
    z-index: 1;
    letter-spacing: 8px;
    font-weight: 700;
  }

  /* Top ribbon */
  .cert-ribbon {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 8px;
    background: linear-gradient(90deg, #8b1a1a, #c0392b, #e74c3c, #c0392b, #8b1a1a);
    z-index: 4;
  }

  .cert-ribbon-bottom {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 8px;
    background: linear-gradient(90deg, #8b1a1a, #c0392b, #e74c3c, #c0392b, #8b1a1a);
    z-index: 4;
  }

  /* Content */
  .cert-content {
    position: relative;
    z-index: 5;
    padding: 36px 60px 28px;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  /* Header */
  .cert-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 4px;
  }

  .cert-logo-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #8b1a1a, #c0392b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .cert-org {
    font-family: 'Cinzel', serif;
    font-size: 22px;
    font-weight: 700;
    color: #8b1a1a;
    letter-spacing: 3px;
    text-transform: uppercase;
  }

  .cert-subtitle {
    font-family: 'Outfit', sans-serif;
    font-size: 10px;
    color: #888;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 12px;
  }

  /* Divider */
  .cert-divider {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }
  .cert-divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(90deg, transparent, #c0392b, transparent);
  }
  .cert-divider-diamond {
    width: 8px;
    height: 8px;
    background: #c0392b;
    transform: rotate(45deg);
    flex-shrink: 0;
  }

  /* Main title */
  .cert-title {
    font-family: 'Cinzel', serif;
    font-size: 28px;
    font-weight: 700;
    color: #2a0a0a;
    letter-spacing: 5px;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .cert-of {
    font-family: 'Crimson Text', serif;
    font-style: italic;
    font-size: 14px;
    color: #888;
    letter-spacing: 2px;
    margin-bottom: 14px;
  }

  /* Presented to */
  .cert-presented {
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    color: #888;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  /* Donor name */
  .cert-name {
    font-family: 'Crimson Text', serif;
    font-size: 46px;
    font-weight: 600;
    color: #8b1a1a;
    line-height: 1.1;
    margin-bottom: 4px;
    border-bottom: 2px solid #c0392b;
    padding-bottom: 6px;
    min-width: 300px;
  }

  /* Body text */
  .cert-body {
    font-family: 'Crimson Text', serif;
    font-size: 13px;
    color: #444;
    line-height: 1.7;
    max-width: 520px;
    margin-bottom: 14px;
  }

  /* Details row */
  .cert-details {
    display: flex;
    gap: 40px;
    margin-bottom: 14px;
    justify-content: center;
  }

  .cert-detail {
    text-align: center;
    padding: 10px 20px;
    background: rgba(139, 26, 26, 0.05);
    border: 1px solid rgba(139, 26, 26, 0.15);
    border-radius: 8px;
    min-width: 110px;
  }

  .cert-detail-label {
    font-size: 9px;
    color: #888;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 4px;
  }

  .cert-detail-value {
    font-family: 'Cinzel', serif;
    font-size: 16px;
    font-weight: 700;
    color: #8b1a1a;
  }

  /* Footer */
  .cert-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    width: 100%;
    margin-top: auto;
    padding-top: 8px;
  }

  .cert-sign {
    text-align: center;
    flex: 1;
  }

  .cert-sign-line {
    width: 120px;
    height: 1px;
    background: #333;
    margin: 0 auto 4px;
  }

  .cert-sign-name {
    font-family: 'Cinzel', serif;
    font-size: 10px;
    color: #333;
    letter-spacing: 1px;
  }

  .cert-sign-title {
    font-size: 9px;
    color: #888;
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .cert-seal {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 3px solid #c0392b;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    flex-shrink: 0;
  }

  .cert-seal::before {
    content: '';
    position: absolute;
    inset: 4px;
    border-radius: 50%;
    border: 1px dashed #c0392b;
  }

  .cert-seal-icon { font-size: 20px; line-height: 1; }
  .cert-seal-text { font-family: 'Cinzel', serif; font-size: 7px; color: #8b1a1a; letter-spacing: 1px; text-align: center; }

  .cert-id {
    font-size: 9px;
    color: #aaa;
    letter-spacing: 1px;
    margin-top: 6px;
    text-align: center;
    width: 100%;
  }

  /* ===== PRINT STYLES ===== */
  @media print {
    body {
      background: white;
      padding: 0;
    }

    .controls { display: none !important; }

    .certificate-wrap {
      width: 100%;
    }

    .certificate {
      width: 100%;
      page-break-inside: avoid;
      box-shadow: none;
    }

    @page {
      size: A4 landscape;
      margin: 0;
    }
  }
</style>
</head>
<body>

<!-- Screen controls -->
<div class="controls">
  <button class="ctrl-btn btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
  <a href="javascript:history.back()" class="ctrl-btn btn-back">← Go Back</a>
</div>

<!-- Certificate -->
<div class="certificate-wrap">
  <div class="certificate">

    <!-- Background -->
    <div class="cert-bg">
      <div class="cert-bg-circle c1"></div>
      <div class="cert-bg-circle c2"></div>
      <div class="cert-bg-circle c3"></div>
    </div>

    <!-- Watermark -->
    <div class="cert-watermark">LIFEF LOW</div>

    <!-- Ribbons -->
    <div class="cert-ribbon"></div>
    <div class="cert-ribbon-bottom"></div>

    <!-- Border frames -->
    <div class="cert-frame"></div>
    <div class="cert-frame-inner"></div>

    <!-- Corner ornaments -->
    <div class="corner corner-tl">
      <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 4 L24 4 L24 8 L8 8 L8 24 L4 24 Z" fill="#8b1a1a" opacity="0.6"/>
        <path d="M4 4 L14 4 L14 6 L6 6 L6 14 L4 14 Z" fill="#8b1a1a"/>
        <circle cx="14" cy="14" r="3" fill="#c0392b" opacity="0.5"/>
        <path d="M24 4 L24 24 M4 24 L24 24" stroke="#8b1a1a" stroke-width="0.5" opacity="0.4"/>
      </svg>
    </div>
    <div class="corner corner-tr">
      <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 4 L24 4 L24 8 L8 8 L8 24 L4 24 Z" fill="#8b1a1a" opacity="0.6"/>
        <path d="M4 4 L14 4 L14 6 L6 6 L6 14 L4 14 Z" fill="#8b1a1a"/>
        <circle cx="14" cy="14" r="3" fill="#c0392b" opacity="0.5"/>
      </svg>
    </div>
    <div class="corner corner-bl">
      <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 4 L24 4 L24 8 L8 8 L8 24 L4 24 Z" fill="#8b1a1a" opacity="0.6"/>
        <path d="M4 4 L14 4 L14 6 L6 6 L6 14 L4 14 Z" fill="#8b1a1a"/>
        <circle cx="14" cy="14" r="3" fill="#c0392b" opacity="0.5"/>
      </svg>
    </div>
    <div class="corner corner-br">
      <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M4 4 L24 4 L24 8 L8 8 L8 24 L4 24 Z" fill="#8b1a1a" opacity="0.6"/>
        <path d="M4 4 L14 4 L14 6 L6 6 L6 14 L4 14 Z" fill="#8b1a1a"/>
        <circle cx="14" cy="14" r="3" fill="#c0392b" opacity="0.5"/>
      </svg>
    </div>

    <!-- Main content -->
    <div class="cert-content">

      <!-- Header -->
      <div class="cert-header">
        <div class="cert-logo-icon">🩸</div>
        <div class="cert-org">LifeFlow Blood Bank</div>
        <div class="cert-logo-icon">🩸</div>
      </div>
      <div class="cert-subtitle">Committed to saving lives through voluntary blood donation</div>

      <!-- Divider -->
      <div class="cert-divider">
        <div class="cert-divider-line"></div>
        <div class="cert-divider-diamond"></div>
        <div class="cert-divider-line"></div>
      </div>

      <!-- Title -->
      <div class="cert-title">Certificate</div>
      <div class="cert-of">of Blood Donation</div>

      <!-- Presented to -->
      <div class="cert-presented">This certificate is proudly presented to</div>

      <!-- Name -->
      <div class="cert-name"><?= htmlspecialchars($donor_name) ?></div>

      <!-- Body -->
      <div class="cert-body">
        In recognition of their generous and selfless act of donating blood on
        <strong><?= $donation_date ?></strong>, making this their
        <strong><?= $donation_number ?><?= $suffix ?></strong> voluntary blood donation.
        Your contribution of blood group <strong><?= $blood_group ?></strong> helps save lives
        and brings hope to patients and families in need. We are deeply grateful for your continued commitment to this noble cause.
      </div>

      <!-- Details -->
      <div class="cert-details">
        <div class="cert-detail">
          <div class="cert-detail-label">Blood Group</div>
          <div class="cert-detail-value"><?= $blood_group ?></div>
        </div>
        <div class="cert-detail">
          <div class="cert-detail-label">Donation No.</div>
          <div class="cert-detail-value"><?= $donation_number ?><?= $suffix ?></div>
        </div>
        <div class="cert-detail">
          <div class="cert-detail-label">Date</div>
          <div class="cert-detail-value" style="font-size:11px"><?= $donation_date ?></div>
        </div>
        <div class="cert-detail">
          <div class="cert-detail-label">Location</div>
          <div class="cert-detail-value" style="font-size:12px"><?= htmlspecialchars($city) ?></div>
        </div>
      </div>

      <!-- Footer signatures -->
      <div class="cert-footer">
        <div class="cert-sign">
          <div class="cert-sign-line"></div>
          <div class="cert-sign-name">Dr. R. Mehta</div>
          <div class="cert-sign-title">Medical Officer</div>
        </div>

        <div class="cert-seal">
          <div class="cert-seal-icon">🩸</div>
          <div class="cert-seal-text">VERIFIED<br>LIFEF LOW</div>
        </div>

        <div class="cert-sign">
          <div class="cert-sign-line"></div>
          <div class="cert-sign-name">Blood Bank Director</div>
          <div class="cert-sign-title">LifeFlow Blood Bank</div>
        </div>
      </div>

      <div class="cert-id">Certificate ID: <?= $cert_id ?> &nbsp;|&nbsp; Issue Date: <?= date('d F Y') ?></div>

    </div>
  </div>
</div>

</body>
</html>
