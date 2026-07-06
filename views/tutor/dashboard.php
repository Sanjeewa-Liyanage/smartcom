<?php
/**
 * @var array $user
 */
$title = 'Dashboard';
require VIEW_PATH . '/partials/tutor_header.php';
?>

<div class="welcome-banner">
    <div>
        <h2>Welcome back, <span><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span> <span class="material-icons" style="font-size:24px;vertical-align:bottom">waving_hand</span></h2>
        <p>Here's your teaching overview. Modules become available as the system rolls out.</p>
    </div>
    <span class="welcome-badge"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">school</span> Tutor</span>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap"><span class="material-icons">menu_book</span></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Courses Assigned</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap"><span class="material-icons">group</span></div>
        <div class="stat-value">0</div>
        <div class="stat-label">My Students</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap"><span class="material-icons">quiz</span></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Quizzes Created</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap"><span class="material-icons">insert_drive_file</span></div>
        <div class="stat-value">0</div>
        <div class="stat-label">Materials Uploaded</div>
    </div>
</div>

<!-- Modules -->
<div class="section-title"><span class="material-icons" style="font-size:20px;vertical-align:bottom">inventory_2</span> Available Modules</div>
<div class="modules-grid">
    <div class="module-card">
        <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">menu_book</span></div><div class="m-title">Course & Material Management</div></div>
        <div class="m-desc">Upload PDFs, notes, past papers and organise study materials by subject.</div>
        <span class="coming-soon">Coming in Phase 2</span>
    </div>
    <div class="module-card">
        <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">quiz</span></div><div class="m-title">Quizzes & Auto-grading</div></div>
        <div class="m-desc">Design quizzes with MCQ questions. Automatic grading saves you time.</div>
        <span class="coming-soon">Coming in Phase 3</span>
    </div>
    <div class="module-card">
        <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">assignment</span></div><div class="m-title">Assignments & Feedback</div></div>
        <div class="m-desc">Create assignments, review student submissions, and provide personalised feedback.</div>
        <span class="coming-soon">Coming in Phase 3</span>
    </div>
    <div class="module-card">
        <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">bar_chart</span></div><div class="m-title">Student Performance</div></div>
        <div class="m-desc">View individual and class-wide academic performance analytics and reports.</div>
        <span class="coming-soon">Coming in Phase 4</span>
    </div>
</div>

<?php require VIEW_PATH . '/partials/tutor_footer.php'; ?>
