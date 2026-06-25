<?php
/**
 * Admin Dashboard view — variables provided by AdminController::dashboard()
 * via Controller::render():
 *
 * @var array       $stats   ['students' => int, 'tutors' => int, 'parents' => int, 'pending' => int]
 * @var array       $user    Current logged-in user session data
 * @var string|null $success Flash success message (may be null)
 */
?>
<?php require VIEW_PATH . '/partials/header.php'; ?>

<div class="page-header">
    <h2>Dashboard Overview</h2>
    <p>Welcome back, <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>. Here's what's happening.</p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert-success">
        <span class="material-icons" style="font-size:18px">check_circle</span> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card students">
        <span class="stat-trend">Students</span>
        <div class="stat-icon-wrap"><span class="material-icons">person</span></div>
        <div class="stat-value"><?= $stats['students'] ?></div>
        <div class="stat-label">Total Students</div>
    </div>
    <div class="stat-card tutors">
        <span class="stat-trend">Tutors</span>
        <div class="stat-icon-wrap"><span class="material-icons">school</span></div>
        <div class="stat-value"><?= $stats['tutors'] ?></div>
        <div class="stat-label">Total Tutors</div>
    </div>
    <div class="stat-card parents">
        <span class="stat-trend">Parents</span>
        <div class="stat-icon-wrap"><span class="material-icons">family_restroom</span></div>
        <div class="stat-value"><?= $stats['parents'] ?></div>
        <div class="stat-label">Total Parents</div>
    </div>
    <div class="stat-card pending">
        <span class="stat-trend">Pending</span>
        <div class="stat-icon-wrap"><span class="material-icons">hourglass_empty</span></div>
        <div class="stat-value"><?= $stats['pending'] ?></div>
        <div class="stat-label">Pending Approvals</div>
    </div>
</div>

<!-- Quick Actions -->
<div class="section-header">
    <h3>Quick Actions</h3>
</div>
<div class="actions-grid">
    <a href="<?= BASE_URL ?>/admin/users" class="action-card" id="action-manage-users">
        <div class="a-icon-wrap"><span class="material-icons">group</span></div>
        <div class="a-title">Manage Users</div>
        <div class="a-desc">View, filter, suspend, or activate all user accounts.</div>
    </a>
    <a href="<?= BASE_URL ?>/admin/pending" class="action-card" id="action-approvals">
        <div class="a-icon-wrap"><span class="material-icons">how_to_reg</span></div>
        <div class="a-title">Approve Registrations</div>
        <div class="a-desc">Review and approve pending student & tutor applications.</div>
    </a>
    <a href="<?= BASE_URL ?>/admin/tutors/create" class="action-card" id="action-create-tutor">
        <div class="a-icon-wrap"><span class="material-icons">person_add</span></div>
        <div class="a-title">Create Tutor</div>
        <div class="a-desc">Create a new tutor account and send credentials.</div>
    </a>
</div>

<!-- Phase note -->

<?php require VIEW_PATH . '/partials/footer.php'; ?>
