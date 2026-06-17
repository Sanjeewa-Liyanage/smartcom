<?php
/**
 * Verify OTP view — variables provided by AuthController via Controller::render():
 *
 * @var string $csrf       CSRF token for form submission
 * @var array  $errors     Validation error messages (may be empty)
 * @var string $maskedEmail  Masked email (e.g. p***@gmail.com)
 * @var string|null $success Flash success message
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP — Smart Commerce Core</title>
    <meta name="description" content="Enter the verification code sent to your email to reset your password.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg:        #f5f6fa;
            --surface:   #ffffff;
            --border:    #e8eaed;
            --text:      #1a1a2e;
            --muted:     #8a8fa8;
            --accent:    #e53935;
            --accent2:   #ff5722;
            --danger:    #c62828;
            --danger-bg: #ffebee;
            --success:   #2e7d32;
            --success-bg:#e8f5e9;
            --input-bg:  #f9fafb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Left Panel ── */
        .left-panel {
            flex: 0 0 42%;
            background: #1a1a2e;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute; top: -100px; right: -80px;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(229,57,53,0.25) 0%, transparent 70%);
            border-radius: 50%;
        }
        .left-panel::after {
            content: '';
            position: absolute; bottom: -80px; left: -60px;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(255,87,34,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .brand { position: relative; z-index: 1; }
        .brand-logo {
            width: 52px; height: 52px;
            background: var(--accent);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; color: #fff; font-weight: 800;
            margin-bottom: 28px;
        }
        .brand h1 {
            font-size: 1.9rem; font-weight: 800;
            color: #ffffff; line-height: 1.2; margin-bottom: 12px;
        }
        .brand h1 span { color: var(--accent); }
        .brand p {
            color: #9ca3af; font-size: 0.9rem; line-height: 1.6; max-width: 300px;
        }
        .step-badges {
            display: flex; gap: 8px; flex-wrap: wrap; margin-top: 36px;
        }
        .step-badge {
            padding: 5px 14px; border-radius: 999px;
            font-size: 0.78rem; font-weight: 500;
            border: 1px solid rgba(255,255,255,0.12);
            color: #d1d5db; background: rgba(255,255,255,0.06);
            display: inline-flex; align-items: center; gap: 5px;
        }
        .step-badge .material-icons { font-size: 15px; }
        .step-badge.done {
            border-color: #4caf50;
            color: #a5d6a7;
            background: rgba(76,175,80,0.1);
        }
        .step-badge.active {
            border-color: var(--accent);
            color: #fff;
            background: rgba(229,57,53,0.2);
        }

        /* ── Right Panel ── */
        .right-panel {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 40px 20px; background: var(--bg);
        }
        .form-card { width: 100%; max-width: 400px; }

        .form-card-header { margin-bottom: 28px; }
        .form-card-header h2 { font-size: 1.5rem; font-weight: 700; color: var(--text); }
        .form-card-header p { color: var(--muted); font-size: 0.88rem; margin-top: 4px; line-height: 1.5; }
        .form-card-header .email-highlight {
            color: var(--text); font-weight: 600;
        }

        /* ── Alerts ── */
        .alert-danger {
            background: var(--danger-bg); border: 1px solid #ef9a9a;
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 0.875rem; color: var(--danger);
        }
        .alert-danger ul { list-style: none; }
        .alert-danger ul li { display: flex; align-items: center; gap: 6px; }
        .alert-danger .material-icons { font-size: 16px; }
        .alert-success {
            background: var(--success-bg); border: 1px solid #a5d6a7;
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 0.875rem; color: var(--success);
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success .material-icons { font-size: 18px; }

        /* ── Form ── */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 0.82rem; font-weight: 600;
            color: var(--text); margin-bottom: 7px;
        }
        .form-group input {
            width: 100%; padding: 12px 14px;
            background: var(--input-bg); border: 1px solid var(--border);
            border-radius: 10px; color: var(--text);
            font-family: 'Inter', sans-serif; font-size: 0.95rem; outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .form-group input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(229,57,53,0.1);
        }
        .form-group input::placeholder { color: #c0c4d0; }

        .otp-input {
            letter-spacing: 0.6em;
            font-size: 1.5rem !important;
            font-weight: 700;
            text-align: center;
            padding: 14px !important;
        }

        /* ── Timer ── */
        .timer-bar {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 18px; font-size: 0.8rem;
        }
        .timer-bar .timer-text { color: var(--muted); }
        .timer-bar .timer-text .countdown { color: var(--accent); font-weight: 700; font-variant-numeric: tabular-nums; }
        .timer-bar .resend-link {
            color: var(--accent); text-decoration: none; font-weight: 600;
            font-size: 0.8rem; transition: color 0.18s;
        }
        .timer-bar .resend-link:hover { color: var(--danger); }
        .timer-bar .resend-link.disabled {
            color: var(--muted); pointer-events: none; opacity: 0.5;
        }

        /* ── Button ── */
        .btn-primary {
            width: 100%; padding: 13px;
            background: var(--accent);
            border: none; border-radius: 10px; color: #fff;
            font-family: 'Inter', sans-serif; font-size: 0.97rem; font-weight: 600;
            cursor: pointer; transition: background 0.18s, transform 0.15s;
            margin-top: 6px;
        }
        .btn-primary:hover { background: var(--danger); transform: translateY(-1px); }

        /* ── Links ── */
        .divider {
            display: flex; align-items: center; gap: 12px;
            margin: 22px 0; color: var(--muted); font-size: 0.8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }
        .form-links {
            display: flex; flex-direction: column; gap: 9px; text-align: center;
        }
        .form-links a {
            color: var(--muted); text-decoration: none; font-size: 0.875rem;
            transition: color 0.18s;
        }
        .form-links a:hover { color: var(--text); }
        .form-links a span { color: var(--accent); font-weight: 600; }

        @media (max-width: 768px) { .left-panel { display: none; } }
    </style>
</head>
<body>

<!-- Left Branding Panel -->
<div class="left-panel">
    <div class="brand">
        <div class="brand-logo">S</div>
        <h1>Smart <span>Commerce</span> Core</h1>
        <p>Enter the 6-digit verification code we sent to your email to continue.</p>
        <div class="step-badges">
            <span class="step-badge done"><span class="material-icons">check_circle</span> Email Sent</span>
            <span class="step-badge active"><span class="material-icons">pin</span> Verify OTP</span>
            <span class="step-badge"><span class="material-icons">lock_reset</span> New Password</span>
        </div>
    </div>
</div>

<!-- Right Form Panel -->
<div class="right-panel">
    <div class="form-card">
        <div class="form-card-header">
            <h2>Check your email</h2>
            <p>We've sent a 6-digit verification code to <span class="email-highlight"><?= htmlspecialchars($maskedEmail ?? '', ENT_QUOTES, 'UTF-8') ?></span>. Enter it below to continue.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><span class="material-icons">check_circle</span> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert-danger">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><span class="material-icons">warning</span> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/verify-otp" novalidate id="verify-otp-form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="otp">Verification Code</label>
                <input
                    type="text" id="otp" name="otp"
                    class="otp-input"
                    placeholder="000000"
                    maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                    required autocomplete="one-time-code"
                >
            </div>

            <div class="timer-bar">
                <div class="timer-text">Code expires in <span class="countdown" id="countdown">10:00</span></div>
                <a href="<?= BASE_URL ?>/forgot-password" class="resend-link" id="resend-link">Resend Code</a>
            </div>

            <button type="submit" class="btn-primary" id="btn-verify-otp">Verify Code <span class="material-icons" style="font-size:18px;vertical-align:middle">arrow_forward</span></button>
        </form>

        <div class="divider">or</div>

        <div class="form-links">
            <a href="<?= BASE_URL ?>/forgot-password">Try a <span>different email</span></a>
            <a href="<?= BASE_URL ?>/login">Back to <span>Sign in</span></a>
        </div>
    </div>
</div>

<script>
(function() {
    // 10-minute countdown timer
    let remaining = 600;
    const countdownEl = document.getElementById('countdown');

    function updateTimer() {
        const min = Math.floor(remaining / 60);
        const sec = remaining % 60;
        countdownEl.textContent = min + ':' + String(sec).padStart(2, '0');

        if (remaining <= 0) {
            countdownEl.textContent = 'Expired';
            countdownEl.style.color = '#c62828';
            clearInterval(timer);
        }
        remaining--;
    }

    const timer = setInterval(updateTimer, 1000);
    updateTimer();
})();
</script>

</body>
</html>
