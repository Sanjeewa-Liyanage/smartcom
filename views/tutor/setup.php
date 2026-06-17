<?php
/**
 * Tutor Setup view — variables provided by TutorController via Controller::render():
 *
 * @var string $csrf      CSRF token for form submission
 * @var array  $errors    Validation error messages (may be empty)
 * @var string $firstName Previously submitted first name
 * @var string $lastName  Previously submitted last name
 * @var string $gender    Previously submitted gender
 * @var string $subject   Previously submitted subject
 * @var array  $user      Current logged-in user session data
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Profile — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
            --danger:    #c62828;
            --danger-bg: #ffebee;
            --input-bg:  #f9fafb;
        }
        body {
            font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            padding: 40px 20px;
        }

        .form-card {
            background: var(--surface); width: 100%; max-width: 500px;
            border-radius: 14px; padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--border);
        }
        .form-card-header { margin-bottom: 28px; text-align: center; }
        .form-card-header h2 { font-size: 1.5rem; font-weight: 700; color: var(--text); }
        .form-card-header p { color: var(--muted); font-size: 0.88rem; margin-top: 6px; line-height: 1.5; }

        .alert {
            background: var(--danger-bg); border: 1px solid #ef9a9a;
            border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 0.875rem; color: var(--danger);
        }
        .alert ul { list-style: none; }
        .alert ul li { display: flex; align-items: center; gap: 6px; }
        .alert .material-icons { font-size: 16px; }

        .form-row { display: flex; gap: 15px; margin-bottom: 18px; }
        .form-row .form-group { flex: 1; margin-bottom: 0; }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 0.82rem; font-weight: 600; color: var(--text); margin-bottom: 7px;
        }
        .form-group input, .form-group select {
            width: 100%; padding: 12px 14px; background: var(--input-bg);
            border: 1px solid var(--border); border-radius: 10px; color: var(--text);
            font-family: 'Inter', sans-serif; font-size: 0.95rem; outline: none;
        }
        .form-group input:focus, .form-group select:focus { border-color: var(--accent); }

        .btn-primary {
            width: 100%; padding: 13px; background: var(--accent);
            border: none; border-radius: 10px; color: #fff;
            font-size: 0.97rem; font-weight: 600; cursor: pointer; margin-top: 10px;
        }
        .btn-primary:hover { background: var(--danger); }
    </style>
</head>
<body>

<div class="form-card">
    <div class="form-card-header">
        <h2>Complete Your Profile</h2>
        <p>Welcome to Smart Commerce Core! Please complete your profile details and set a secure password.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert" role="alert">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><span class="material-icons">warning</span> <?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/tutor/setup">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-row">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($firstName ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($lastName ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="male" <?= ($gender ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= ($gender ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= ($gender ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($subject ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g. Accounting" required>
            </div>
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
        </div>

        <button type="submit" class="btn-primary">Save Profile & Continue</button>
    </form>
</div>

</body>
</html>
