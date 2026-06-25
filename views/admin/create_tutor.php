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
<?php
$title = 'Create Tutor';
require VIEW_PATH . '/partials/header.php';
?>
        <div style="max-width: 600px; margin: 0 auto; width: 100%;">
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
<?php require VIEW_PATH . '/partials/footer.php'; ?>
