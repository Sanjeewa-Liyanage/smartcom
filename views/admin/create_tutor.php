<?php
/**
 * Create Tutor view — variables provided by AdminController via Controller::render():
 *
 * @var string $csrf   CSRF token for form submission
 * @var array  $user   Current logged-in user session data
 * @var array  $errors Validation error messages (may be empty)
 * @var string $name   Previously submitted name (empty on first load)
 * @var string $email  Previously submitted email (empty on first load)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Tutor — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg:        #f5f6fa;
            --sidebar:   #ffffff;
            --surface:   #ffffff;
            --border:    #e8eaed;
            --text:      #1a1a2e;
            --muted:     #8a8fa8;
            --accent:    #e53935;
            --danger:    #c62828;
            --danger-bg: #ffebee;
            --input-bg:  #f9fafb;
        }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

        /* ── Sidebar (Simplified for standalone view) ── */
        .sidebar {
            width: 220px; flex-shrink: 0; background: var(--sidebar);
            border-right: 1px solid var(--border); display: flex; flex-direction: column;
        }
        .sidebar-brand { padding: 22px 20px 18px; border-bottom: 1px solid var(--border); }
        .sidebar-brand .app-name { font-size: 0.95rem; font-weight: 700; color: var(--text); }
        .sidebar-nav { flex: 1; padding: 16px 0; }
        .nav-link {
            display: block; padding: 10px 20px; font-size: 0.875rem; font-weight: 500;
            color: var(--muted); text-decoration: none;
        }
        .nav-link:hover { background: #f5f6fa; color: var(--text); }

        /* ── Main ── */
        .main { flex: 1; display: flex; flex-direction: column; }
        .topbar {
            padding: 0 32px; height: 58px; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; background: #fff;
        }
        .topbar h1 { font-size: 1.05rem; font-weight: 700; }
        .content { padding: 28px 32px; max-width: 600px; margin: 0 auto; width: 100%; }
        
        .form-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; padding: 30px;
        }
        .form-card h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 5px; }
        .form-card p { color: var(--muted); font-size: 0.875rem; margin-bottom: 24px; }

        .alert {
            background: var(--danger-bg); border: 1px solid #ef9a9a;
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 0.875rem; color: var(--danger);
        }
        .alert ul { list-style: none; margin-left: 0; padding-left: 0; }
        .alert li { display: flex; align-items: center; gap: 6px; }
        .alert .material-icons { font-size: 16px; }

        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 0.82rem; font-weight: 600; margin-bottom: 7px; }
        .form-group input {
            width: 100%; padding: 12px 14px; background: var(--input-bg);
            border: 1px solid var(--border); border-radius: 10px;
            font-family: 'Inter', sans-serif; font-size: 0.95rem;
        }
        .form-group input:focus { border-color: var(--accent); outline: none; }
        
        .btn-primary {
            width: 100%; padding: 13px; background: var(--accent);
            border: none; border-radius: 10px; color: #fff; font-weight: 600;
            cursor: pointer; margin-top: 10px;
        }
        .btn-primary:hover { background: var(--danger); }
        .btn-cancel {
            display: block; text-align: center; margin-top: 15px;
            color: var(--muted); text-decoration: none; font-size: 0.875rem;
        }
        .btn-cancel:hover { color: var(--text); }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="app-name">Smart Commerce</div>
    </div>
    <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link" style="display:flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:18px">arrow_back</span> Back to Dashboard</a>
    </nav>
</aside>

<div class="main">
    <header class="topbar">
        <h1>Create Tutor</h1>
    </header>
    <div class="content">
        <div class="form-card">
            <h2>Create New Tutor</h2>
            <p>Generate a tutor account and send an email with the login credentials.</p>

            <?php if (!empty($errors)): ?>
                <div class="alert">
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><span class="material-icons">warning</span> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/admin/tutors/create">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <button type="submit" class="btn-primary">Create Account</button>
                <a href="<?= BASE_URL ?>/admin/dashboard" class="btn-cancel">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
