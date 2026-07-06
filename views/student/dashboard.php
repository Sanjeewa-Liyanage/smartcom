<?php
/**
 * @var array $user
 * @var array $student
 */
$title = 'Student Dashboard';
require VIEW_PATH . '/partials/student_header.php';
?>

<style>
    .welcome-banner {
        background:#fff; border:1px solid var(--border); border-radius:14px;
        padding:24px 28px; margin-bottom:20px;
        display:flex; align-items:center; justify-content:space-between;
    }
    .welcome-banner h2 { font-size:1.3rem; font-weight:700; margin-bottom:0;}
    .welcome-banner h2 span { color:var(--accent-mid); }
    .welcome-banner p { color:var(--muted); font-size:0.875rem; margin-top:4px; margin-bottom:0; }
    .welcome-badge {
        background:var(--accent-light); color:var(--accent-mid);
        padding:8px 18px; border-radius:999px; font-size:0.82rem; font-weight:600;
    }

    .student-id-bar {
        display:inline-flex; align-items:center; gap:8px;
        background:var(--accent-light); border:1px solid #90caf9;
        border-radius:10px; padding:10px 18px; font-size:0.82rem;
        color:var(--accent-mid); margin-bottom:22px; font-weight:500;
    }
    .student-id-bar strong { font-weight:700; }

    .stats-grid {
        display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr));
        gap:16px; margin-bottom:28px;
    }
    .stat-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:14px; padding:20px;
        transition:box-shadow 0.2s,transform 0.2s;
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 4px 18px rgba(0,0,0,0.07); }
    .stat-icon-wrap {
        width:40px; height:40px; border-radius:10px;
        display:flex; align-items:center; justify-content:center;
        font-size:1.2rem; margin-bottom:12px; background:var(--accent-light);
    }
    .stat-value { font-size:2rem; font-weight:800; color:var(--text); line-height:1; margin-bottom:4px; }
    .stat-label { font-size:0.78rem; color:var(--muted); font-weight:500; }

    .section-title { font-size:1rem; font-weight:700; margin-bottom:14px; color:var(--text); }

    .modules-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(230px,1fr)); gap:14px; }
    .module-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:12px; padding:22px;
        transition:box-shadow 0.2s,transform 0.2s,border-color 0.2s;
    }
    .module-card:hover { box-shadow:0 4px 14px rgba(0,0,0,0.06); transform:translateY(-2px); border-color:#d0d4e0; }
    .module-card .m-header { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
    .module-card .m-icon-wrap {
        width:36px; height:36px; border-radius:8px;
        background:var(--accent-light); display:flex; align-items:center; justify-content:center;
        font-size:1.1rem;
    }
    .module-card .m-title { font-size:0.9rem; font-weight:600; }
    .module-card .m-desc { font-size:0.8rem; color:var(--muted); line-height:1.5; }
    .coming-soon {
        display:inline-block; margin-top:10px;
        padding:3px 10px; background:var(--accent-light);
        border-radius:999px; font-size:0.7rem; color:var(--accent-mid); font-weight:600;
    }
</style>
        <div class="welcome-banner">
            <div>
                <h2>Hello, <span><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span> <span class="material-icons" style="font-size:24px;vertical-align:bottom">school</span></h2>
                <p>Your learning journey starts here. Modules will unlock as the system is built out.</p>
            </div>
            <span class="welcome-badge"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">person</span> Student</span>
        </div>

        <?php if (!empty($student)): ?>
        <div class="student-id-bar">
            <span class="material-icons" style="font-size:18px">badge</span> Student ID: <strong>#<?= $student['student_id'] ?></strong>
            &nbsp;·&nbsp; Share this ID with your parent so they can link their account.
        </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">menu_book</span></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Enrolled Courses</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">assignment</span></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Pending Assignments</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">quiz</span></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Quizzes Attempted</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">event_available</span></div>
                <div class="stat-value">—%</div>
                <div class="stat-label">Attendance</div>
            </div>
        </div>

        <div class="section-title"><span class="material-icons" style="font-size:20px;vertical-align:bottom">inventory_2</span> Available Modules</div>
        <div class="modules-grid">
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">menu_book</span></div><div class="m-title">Course Materials</div></div>
                <div class="m-desc">Download PDFs, notes, and past papers uploaded by your tutors.</div>
                <span class="coming-soon">Coming in Phase 2</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">quiz</span></div><div class="m-title">Online Quizzes</div></div>
                <div class="m-desc">Take quizzes assigned by tutors and see your results instantly.</div>
                <span class="coming-soon">Coming in Phase 3</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">assignment</span></div><div class="m-title">Assignments</div></div>
                <div class="m-desc">Submit assignments online and receive tutor feedback and grades.</div>
                <span class="coming-soon">Coming in Phase 3</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">bar_chart</span></div><div class="m-title">Performance Reports</div></div>
                <div class="m-desc">View detailed analytics of your academic performance trends.</div>
                <span class="coming-soon">Coming in Phase 4</span>
            </div>
        </div>
    </div>
</div>
<?php require VIEW_PATH . '/partials/student_footer.php'; ?>
