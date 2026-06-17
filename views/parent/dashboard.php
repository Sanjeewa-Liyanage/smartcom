<?php
/**
 * @var array $user
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --bg:#f5f6fa; --sidebar:#ffffff; --surface:#ffffff;
            --border:#e8eaed; --text:#1a1a2e; --muted:#8a8fa8;
            --accent:#6a1b9a; --accent-light:#f3e5f5; --accent-mid:#8e24aa;
            --danger:#c62828; --danger-bg:#ffebee;
            --warning:#f57c00; --warning-bg:#fff3e0;
        }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

        .sidebar {
            width:220px; flex-shrink:0; background:var(--sidebar);
            border-right:1px solid var(--border); display:flex; flex-direction:column;
            position:fixed; top:0; left:0; height:100vh; overflow-y:auto; z-index:100;
        }
        .sidebar-brand {
            padding:22px 20px 18px; border-bottom:1px solid var(--border);
            display:flex; align-items:center; gap:10px;
        }
        .sidebar-brand .logo {
            width:36px; height:36px; background:var(--accent-mid); border-radius:8px;
            display:flex; align-items:center; justify-content:center;
            font-size:18px; color:#fff; font-weight:800; flex-shrink:0;
        }
        .sidebar-brand .brand-text .app-name { font-size:0.95rem; font-weight:700; color:var(--text); line-height:1.2; }
        .sidebar-brand .brand-text .role-tag { font-size:0.7rem; color:var(--muted); font-weight:500; }
        .sidebar-nav { flex:1; padding:16px 0; }
        .nav-section-label { padding:10px 20px 4px; font-size:0.65rem; font-weight:700; color:#bbbdcb; text-transform:uppercase; letter-spacing:0.1em; }
        .nav-link {
            display:flex; align-items:center; gap:10px; padding:10px 20px;
            font-size:0.875rem; font-weight:500; color:var(--muted); text-decoration:none;
            transition:color 0.18s,background 0.18s; position:relative;
        }
        .nav-link:hover { background:#f5f6fa; color:var(--text); }
        .nav-link.active { color:var(--accent-mid); background:var(--accent-light); font-weight:600; }
        .nav-link.active::before {
            content:''; position:absolute; left:0; top:0; bottom:0;
            width:3px; background:var(--accent-mid); border-radius:0 2px 2px 0;
        }
        .nav-link .icon { font-size:1rem; width:20px; text-align:center; }
        .nav-link .material-icons { font-size: 20px; width: 20px; text-align: center; }
        .sidebar-footer { padding:14px 20px; border-top:1px solid var(--border); }
        .sidebar-user { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .sidebar-user .avatar {
            width:34px; height:34px; background:var(--accent-mid);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; color:#fff; flex-shrink:0;
        }
        .sidebar-user .info .name { font-size:0.82rem; font-weight:600; color:var(--text); }
        .sidebar-user .info .role { font-size:0.7rem; color:var(--muted); }
        .btn-logout {
            display:flex; align-items:center; gap:8px; width:100%; padding:9px 14px;
            background:#fff5f5; border:1px solid #fecaca;
            border-radius:8px; color:var(--danger); font-size:0.82rem; font-weight:500;
            text-decoration:none; transition:background 0.18s;
        }
        .btn-logout:hover { background:#fee2e2; }

        .main { margin-left:220px; flex:1; display:flex; flex-direction:column; }
        .topbar {
            padding:0 32px; height:58px; border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;
            background:#fff; position:sticky; top:0; z-index:50;
        }
        .topbar h1 { font-size:1.05rem; font-weight:700; color:var(--text); }
        .content { padding:28px 32px; }

        .welcome-banner {
            background:#fff; border:1px solid var(--border); border-radius:14px;
            padding:24px 28px; margin-bottom:20px;
            display:flex; align-items:center; justify-content:space-between;
        }
        .welcome-banner h2 { font-size:1.3rem; font-weight:700; }
        .welcome-banner h2 span { color:var(--accent-mid); }
        .welcome-banner p { color:var(--muted); font-size:0.875rem; margin-top:4px; }
        .welcome-badge {
            background:var(--accent-light); color:var(--accent-mid);
            padding:8px 18px; border-radius:999px; font-size:0.82rem; font-weight:600;
        }

        /* Linked student card */
        .child-card {
            background:#fff; border:1px solid var(--border);
            border-radius:14px; padding:20px 24px; margin-bottom:22px;
            display:flex; align-items:center; gap:16px;
            border-left:4px solid var(--accent-mid);
        }
        .child-avatar {
            width:50px; height:50px; background:#1565c0;
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            font-size:20px; font-weight:800; color:#fff; flex-shrink:0;
        }
        .child-info .label { font-size:0.72rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.06em; margin-bottom:3px; font-weight:600; }
        .child-info .name { font-size:1rem; font-weight:700; color:var(--text); }
        .child-info .id { font-size:0.78rem; color:var(--muted); margin-top:2px; }
        .no-student {
            background:var(--warning-bg); border:1px solid #ffe082;
            border-radius:12px; padding:14px 18px; margin-bottom:22px;
            font-size:0.875rem; color:var(--warning); font-weight:500;
        }

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
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">P</div>
        <div class="brand-text">
            <div class="app-name">Smart Commerce</div>
            <div class="role-tag">Parent Portal</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="<?= BASE_URL ?>/parent/dashboard" class="nav-link active" id="nav-parent-dash">
            <span class="material-icons">dashboard</span> Dashboard
        </a>
        <div class="nav-section-label">Coming Soon</div>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">bar_chart</span> Child's Progress</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">event_available</span> Attendance</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">assignment</span> Grades</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">payment</span> Fee Payments</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">notifications</span> Notifications</a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="role">Parent / Guardian</div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout" id="btn-parent-logout"><span class="material-icons" style="font-size:16px">logout</span> Sign Out</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <h1>Parent Dashboard</h1>
    </header>

    <div class="content">
        <div class="welcome-banner">
            <div>
                <h2>Welcome, <span><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></span> <span class="material-icons" style="font-size:24px;vertical-align:bottom">family_restroom</span></h2>
                <p>Monitor your child's academic progress and stay informed about their studies.</p>
            </div>
            <span class="welcome-badge"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">family_restroom</span> Parent</span>
        </div>

        <!-- Linked Child Cards -->
        <?php if (!empty($linkedStudents)): ?>
            <?php foreach ($linkedStudents as $student): ?>
            <div class="child-card">
                <div class="child-avatar"><?= strtoupper(substr($student['student_name'], 0, 1)) ?></div>
                <div class="child-info">
                    <div class="label"><span class="material-icons" style="font-size:12px;vertical-align:middle">push_pin</span> Linked Student</div>
                    <div class="name"><?= htmlspecialchars($student['student_name'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="id">Student ID: #<?= $student['student_id'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
        <div class="no-student">
            <span class="material-icons" style="font-size:18px;vertical-align:text-bottom">warning</span> No student account linked to your profile yet. Please contact the administrator.
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">event_available</span></div>
                <div class="stat-value">—%</div>
                <div class="stat-label">Child's Attendance</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">bar_chart</span></div>
                <div class="stat-value">—</div>
                <div class="stat-label">Average Grade</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">payment</span></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Pending Fees</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap"><span class="material-icons">notifications</span></div>
                <div class="stat-value">0</div>
                <div class="stat-label">Notifications</div>
            </div>
        </div>

        <div class="section-title"><span class="material-icons" style="font-size:20px;vertical-align:bottom">inventory_2</span> Available Modules</div>
        <div class="modules-grid">
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">bar_chart</span></div><div class="m-title">Academic Progress</div></div>
                <div class="m-desc">View your child's quiz results, assignment grades, and overall performance trends.</div>
                <span class="coming-soon">Coming in Phase 4</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">event_available</span></div><div class="m-title">Attendance Tracking</div></div>
                <div class="m-desc">See attendance records and receive alerts if attendance falls below threshold.</div>
                <span class="coming-soon">Coming in Phase 2</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">payment</span></div><div class="m-title">Fee Payments</div></div>
                <div class="m-desc">Upload payment receipts online and track payment history and status.</div>
                <span class="coming-soon">Coming in Phase 5</span>
            </div>
            <div class="module-card">
                <div class="m-header"><div class="m-icon-wrap"><span class="material-icons">notifications</span></div><div class="m-title">Notifications</div></div>
                <div class="m-desc">Receive alerts about upcoming deadlines, low attendance, and fee reminders.</div>
                <span class="coming-soon">Coming in Phase 4</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
