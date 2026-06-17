<?php
/**
 * Variables injected by AuthController::registerStudentForm() / registerStudent()
 * via extract($data) in Controller::render().
 *
 * @var string   $csrf   CSRF token for the hidden form field
 * @var string[] $errors Validation error messages
 * @var string   $name   Repopulated name field value
 * @var string   $email  Repopulated email field value
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration — Smart Commerce Core</title>
    <meta name="description" content="Register as a student on Smart Commerce Core LMS.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --bg:#f5f6fa; --surface:#ffffff; --border:#e8eaed;
            --text:#1a1a2e; --muted:#8a8fa8;
            --accent:#1565c0; --accent-light:#e3f2fd;
            --danger:#c62828; --danger-bg:#ffebee; --input-bg:#f9fafb;
        }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; }

        .left-panel {
            flex:0 0 42%; background:#1a1a2e;
            display:flex; flex-direction:column; justify-content:center;
            padding:60px; position:relative; overflow:hidden;
        }
        .left-panel::before {
            content:''; position:absolute; top:-100px; right:-80px;
            width:300px; height:300px;
            background:radial-gradient(circle,rgba(21,101,192,0.3) 0%,transparent 70%);
            border-radius:50%;
        }
        .brand { position:relative; z-index:1; }
        .brand-logo {
            width:52px; height:52px; background:#1565c0; border-radius:14px;
            display:flex; align-items:center; justify-content:center;
            font-size:26px; color:#fff; font-weight:800; margin-bottom:24px;
        }
        .brand h2 { font-size:1.8rem; font-weight:800; color:#fff; line-height:1.2; margin-bottom:10px; }
        .brand h2 span { color:#64b5f6; }
        .brand p { color:#9ca3af; font-size:0.9rem; line-height:1.65; max-width:300px; }
        .info-box {
            margin-top:32px; padding:16px 20px;
            background:rgba(21,101,192,0.15); border:1px solid rgba(100,181,246,0.25);
            border-radius:12px; font-size:0.83rem; color:#90caf9; line-height:1.6;
        }
        .info-box strong { display:block; margin-bottom:4px; color:#bbdefb; }

        .right-panel { flex:1; display:flex; align-items:center; justify-content:center; padding:40px 20px; }
        .form-card { width:100%; max-width:440px; }
        .back-link {
            display:inline-flex; align-items:center; gap:6px;
            color:var(--muted); font-size:0.83rem; text-decoration:none;
            margin-bottom:20px; transition:color 0.18s;
        }
        .back-link:hover { color:var(--text); }
        .role-tag {
            display:inline-flex; align-items:center; gap:6px;
            background:var(--accent-light); border-radius:999px; padding:4px 14px;
            font-size:0.78rem; color:var(--accent); font-weight:600; margin-bottom:14px;
        }
        .form-card h2 { font-size:1.5rem; font-weight:700; margin-bottom:4px; }
        .form-card .subtitle { color:var(--muted); font-size:0.875rem; margin-bottom:26px; }

        .alert { background:var(--danger-bg); border:1px solid #ef9a9a; border-radius:10px; padding:12px 16px; margin-bottom:18px; font-size:0.875rem; color:var(--danger); }
        .alert ul { list-style:none; }
        .alert ul li { display: flex; align-items: center; gap: 6px; }
        .alert .material-icons { font-size: 16px; }

        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:0.82rem; font-weight:600; color:var(--text); margin-bottom:6px; }
        .form-group input {
            width:100%; padding:11px 14px;
            background:var(--input-bg); border:1px solid var(--border);
            border-radius:10px; color:var(--text);
            font-family:'Inter',sans-serif; font-size:0.92rem; outline:none;
            transition:border-color 0.18s,box-shadow 0.18s;
        }
        .form-group input:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(21,101,192,0.1); }
        .form-group input::placeholder { color:#c0c4d0; }
        .btn-primary {
            width:100%; padding:12px; background:var(--accent);
            border:none; border-radius:10px; color:#fff;
            font-family:'Inter',sans-serif; font-size:0.97rem; font-weight:600;
            cursor:pointer; transition:background 0.18s,transform 0.15s; margin-top:6px;
        }
        .btn-primary:hover { background:#0d47a1; transform:translateY(-1px); }
        .form-footer { margin-top:20px; text-align:center; }
        .form-footer a { color:var(--muted); font-size:0.875rem; text-decoration:none; }
        .form-footer a span { color:var(--accent); font-weight:600; }
        .form-footer a:hover { color:var(--text); }
        @media(max-width:768px){ .left-panel{display:none;} .form-row{grid-template-columns:1fr;} }
    </style>
</head>
<body>

<div class="left-panel">
    <div class="brand">
        <div class="brand-logo">S</div>
        <h2>Join as a <span>Student</span></h2>
        <p>Access course materials, submit assignments, take quizzes, and track your academic progress — all in one place.</p>
        <div class="info-box">
            <strong><span class="material-icons" style="font-size:16px;vertical-align:middle">assignment</span> What happens after registration?</strong>
            Your account will be reviewed by the administrator. You will be able to log in once it is approved. This usually takes 1–2 business days.
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="form-card">
        <a href="<?= BASE_URL ?>/login" class="back-link"><span class="material-icons" style="font-size:16px">arrow_back</span> Back to login</a>
        <div class="role-tag"><span class="material-icons" style="font-size:16px">person</span> Student Registration</div>
        <h2>Create your account</h2>
        <p class="subtitle">Fill in your details below to get started</p>

        <?php if (!empty($errors)): ?>
            <div class="alert" role="alert">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <li><span class="material-icons">warning</span> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/register" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name"
                    value="<?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Your full name" required autocomplete="name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email"
                    value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="you@example.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="parent_name">Parent Name</label>
                <input type="text" id="parent_name" name="parent_name"
                    value="<?= htmlspecialchars($parentName ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Parent / Guardian full name" required>
            </div>

            <div class="form-group">
                <label for="parent_email">Parent Email</label>
                <input type="email" id="parent_email" name="parent_email"
                    value="<?= htmlspecialchars($parentEmail ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="parent@example.com" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                        placeholder="Min. 8 characters" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Repeat password" required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-primary" id="btn-register-student">
                Create Student Account <span class="material-icons" style="font-size:18px;vertical-align:middle">arrow_forward</span>
            </button>
        </form>

        <div class="form-footer">
            <a href="<?= BASE_URL ?>/login">Already have an account? <span>Sign in</span></a>
        </div>
    </div>
</div>

</body>
</html>
