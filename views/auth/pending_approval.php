<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Pending — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #070714;
            color: #f0f0ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            max-width: 480px;
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.09);
            border-radius: 24px;
            padding: 52px 44px;
            text-align: center;
        }
        .icon-wrap {
            width: 88px; height: 88px;
            margin: 0 auto 28px;
            background: linear-gradient(135deg, rgba(245,158,11,0.15), rgba(252,211,77,0.08));
            border: 2px solid rgba(245,158,11,0.3);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 40px;
            animation: pulse 2.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.25); }
            50%       { box-shadow: 0 0 0 16px rgba(245,158,11,0); }
        }
        h1 { font-size: 1.65rem; font-weight: 700; margin-bottom: 12px; }
        p { color: #7b7b9d; font-size: 0.925rem; line-height: 1.7; margin-bottom: 10px; }
        .highlight { color: #fcd34d; font-weight: 500; }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 28px 0;
        }
        .info-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 12px;
            padding: 14px;
            font-size: 0.82rem;
        }
        .info-item .emoji { margin-bottom: 6px; display: flex; align-items: center; justify-content: center; }
        .info-item .material-icons { font-size: 24px; }
        .info-item .label { color: #5a5a7a; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; }
        .info-item .value { color: #e0e0ff; font-weight: 500; font-size: 0.85rem; margin-top: 2px; }

        .btn-outline {
            display: inline-block;
            margin-top: 20px;
            padding: 11px 28px;
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 10px;
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: border-color 0.2s, color 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-outline:hover { border-color: rgba(255,255,255,0.3); color: #f0f0ff; }
    </style>
</head>
<body>

<div class="card">
    <div class="icon-wrap"><span class="material-icons" style="font-size:40px">hourglass_empty</span></div>
    <h1>Account Pending Approval</h1>
    <p>
        Thank you for registering! Your account has been created and is currently
        <span class="highlight">awaiting administrator review</span>.
    </p>
    <p>You will be able to log in once your account has been approved.</p>

    <div class="info-grid">
        <div class="info-item">
            <div class="emoji"><span class="material-icons">email</span></div>
            <div class="label">Registered as</div>
            <div class="value"><?= htmlspecialchars($_SESSION['pending_email'] ?? 'your email', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <div class="info-item">
            <div class="emoji"><span class="material-icons">schedule</span></div>
            <div class="label">Typical wait time</div>
            <div class="value">1 – 2 business days</div>
        </div>
        <div class="info-item">
            <div class="emoji"><span class="material-icons">notifications</span></div>
            <div class="label">Next step</div>
            <div class="value">Admin approves your account</div>
        </div>
        <div class="info-item">
            <div class="emoji"><span class="material-icons">check_circle</span></div>
            <div class="label">After approval</div>
            <div class="value">Log in normally</div>
        </div>
    </div>

    <p style="font-size:0.82rem;">
        Already approved? Try logging in — if you are still unable to access your account, contact the administrator.
    </p>

    <a href="<?= BASE_URL ?>/login" class="btn-outline" id="btn-back-login"><span class="material-icons" style="font-size:18px">arrow_back</span> Back to Login</a>
</div>

</body>
</html>
