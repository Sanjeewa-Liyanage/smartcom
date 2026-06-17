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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Smart Commerce Core</title>
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
            --accent2:   #ff5722;
            --success:   #2e7d32;
            --success-bg:#e8f5e9;
            --warning:   #f57c00;
            --warning-bg:#fff3e0;
            --danger:    #c62828;
            --danger-bg: #ffebee;
            --info:      #1565c0;
            --info-bg:   #e3f2fd;
        }
        body { font-family:'Inter',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 220px; flex-shrink: 0;
            background: var(--sidebar);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0;
            height: 100vh; overflow-y: auto;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 10px;
        }
        .sidebar-brand .logo {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; font-weight: 800; flex-shrink: 0;
        }
        .sidebar-brand .brand-text .app-name { font-size: 0.95rem; font-weight: 700; color: var(--text); line-height: 1.2; }
        .sidebar-brand .brand-text .role-tag { font-size: 0.7rem; color: var(--muted); font-weight: 500; }

        .sidebar-nav { flex: 1; padding: 16px 0; }
        .nav-section-label {
            padding: 10px 20px 4px;
            font-size: 0.65rem; font-weight: 700;
            color: #bbbdcb; text-transform: uppercase; letter-spacing: 0.1em;
        }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; font-size: 0.875rem; font-weight: 500;
            color: var(--muted); text-decoration: none;
            transition: color 0.18s, background 0.18s;
            border-radius: 0; position: relative;
        }
        .nav-link:hover { background: #f5f6fa; color: var(--text); }
        .nav-link.active {
            color: var(--accent);
            background: #fff5f5;
            font-weight: 600;
        }
        .nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 0; bottom: 0;
            width: 3px; background: var(--accent);
            border-radius: 0 2px 2px 0;
        }
        .nav-link .icon { font-size: 1rem; width: 20px; text-align: center; }
        .nav-link .material-icons { font-size: 20px; width: 20px; text-align: center; }
        .nav-link .badge {
            margin-left: auto; background: var(--danger);
            color: #fff; font-size: 0.65rem; font-weight: 700;
            padding: 2px 7px; border-radius: 999px; min-width: 20px; text-align: center;
        }
        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 12px;
        }
        .sidebar-user .avatar {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff; flex-shrink: 0;
        }
        .sidebar-user .info .name { font-size: 0.82rem; font-weight: 600; color: var(--text); }
        .sidebar-user .info .role { font-size: 0.7rem; color: var(--muted); }
        .btn-logout {
            display: flex; align-items: center; gap: 8px;
            width: 100%; padding: 9px 14px;
            background: #fff5f5; border: 1px solid #fecaca;
            border-radius: 8px; color: var(--danger); font-size: 0.82rem; font-weight: 500;
            text-decoration: none; transition: background 0.18s;
        }
        .btn-logout:hover { background: #fee2e2; }

        /* ── Main ── */
        .main { margin-left: 220px; flex: 1; display: flex; flex-direction: column; }

        /* ── Top Bar ── */
        .topbar {
            padding: 0 32px;
            height: 58px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            background: #fff;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar h1 { font-size: 1.05rem; font-weight: 700; color: var(--text); }
        .topbar .breadcrumb { font-size: 0.75rem; color: var(--muted); }
        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .topbar-time { font-size: 0.8rem; color: var(--muted); font-weight: 500; }

        /* ── Content ── */
        .content { padding: 28px 32px; }

        /* ── Page header ── */
        .page-header { margin-bottom: 24px; }
        .page-header h2 { font-size: 1.4rem; font-weight: 700; color: var(--text); }
        .page-header p { font-size: 0.875rem; color: var(--muted); margin-top: 3px; }

        /* ── Alert ── */
        .alert-success {
            background: var(--success-bg); border: 1px solid #a5d6a7;
            border-radius: 10px; padding: 12px 18px; margin-bottom: 22px;
            font-size: 0.875rem; color: var(--success);
            display: flex; align-items: center; gap: 10px;
        }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .stat-icon-wrap {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; margin-bottom: 14px;
        }
        .stat-card.students .stat-icon-wrap { background: var(--info-bg); }
        .stat-card.tutors   .stat-icon-wrap { background: #e8f5e9; }
        .stat-card.parents  .stat-icon-wrap { background: #fce4ec; }
        .stat-card.pending  .stat-icon-wrap { background: var(--warning-bg); }
        .stat-value { font-size: 2.2rem; font-weight: 800; line-height: 1; margin-bottom: 5px; color: var(--text); }
        .stat-label { font-size: 0.8rem; color: var(--muted); font-weight: 500; }
        .stat-card .stat-trend {
            position: absolute; top: 18px; right: 18px;
            font-size: 0.7rem; font-weight: 600; padding: 3px 8px; border-radius: 999px;
        }
        .stat-card.students .stat-trend { background: var(--info-bg); color: var(--info); }
        .stat-card.tutors   .stat-trend { background: #e8f5e9;  color: var(--success); }
        .stat-card.parents  .stat-trend { background: #fce4ec;  color: #c2185b; }
        .stat-card.pending  .stat-trend { background: var(--warning-bg); color: var(--warning); }

        /* ── Section Header ── */
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }
        .section-header h3 { font-size: 1rem; font-weight: 700; color: var(--text); }
        .section-header a {
            font-size: 0.8rem; color: var(--accent); text-decoration: none; font-weight: 500;
        }
        .section-header a:hover { text-decoration: underline; }

        /* ── Quick Actions ── */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 14px;
        }
        .action-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            text-decoration: none;
            color: var(--text);
            display: flex; flex-direction: column; gap: 8px;
            transition: box-shadow 0.2s, transform 0.2s, border-color 0.2s;
        }
        .action-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            border-color: #d0d4e0;
            transform: translateY(-2px);
        }
        .action-card .a-icon-wrap {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
        }
        .action-card:nth-child(1) .a-icon-wrap { background: var(--info-bg); }
        .action-card:nth-child(2) .a-icon-wrap { background: var(--success-bg); }
        .action-card:nth-child(3) .a-icon-wrap { background: var(--warning-bg); }
        .action-card .a-title { font-size: 0.9rem; font-weight: 600; }
        .action-card .a-desc { font-size: 0.78rem; color: var(--muted); line-height: 1.4; }

        /* ── Phase note ── */
        .phase-note {
            margin-top: 28px; padding: 18px 22px;
            background: #fff; border: 1px solid var(--border);
            border-radius: 12px; border-left: 4px solid var(--accent);
        }
        .phase-note .pn-title { font-size: 0.85rem; font-weight: 700; color: var(--text); margin-bottom: 5px; }
        .phase-note .pn-body { font-size: 0.8rem; color: var(--muted); line-height: 1.6; }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">S</div>
        <div class="brand-text">
            <div class="app-name">Smart Commerce</div>
            <div class="role-tag">Admin Panel</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link active" id="nav-admin-dash">
            <span class="material-icons">dashboard</span> Dashboard
        </a>

        <div class="nav-section-label">User Management</div>
        <a href="<?= BASE_URL ?>/admin/users" class="nav-link" id="nav-admin-users">
            <span class="material-icons">group</span> All Users
        </a>
        <a href="<?= BASE_URL ?>/admin/pending" class="nav-link" id="nav-admin-pending">
            <span class="material-icons">hourglass_empty</span> Pending Approvals
            <?php if ($stats['pending'] > 0): ?>
                <span class="badge"><?= $stats['pending'] ?></span>
            <?php endif; ?>
        </a>

        <div class="nav-section-label">Coming Soon</div>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">menu_book</span> Courses</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">event_available</span> Attendance</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">assessment</span> Reports</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">payment</span> Payments</a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="role">System Admin</div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout" id="btn-admin-logout">
            <span class="material-icons" style="font-size:16px">logout</span> Sign Out
        </a>
    </div>
</aside>

<!-- Main Content -->
<div class="main">
    <!-- Top Bar -->
    <header class="topbar">
        <div>
            <h1>Dashboard</h1>
        </div>
        <div class="topbar-right">
            <span class="topbar-time" id="live-clock"></span>
        </div>
    </header>

    <!-- Content -->
    <div class="content">

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
        <div class="phase-note">
            <div class="pn-title"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">inventory_2</span> Phase 1 — Auth & User Management</div>
            <div class="pn-body">
                Course management, attendance, quizzes, assignments, payments, and reporting modules are coming in upcoming phases.
                The foundation (MVC, RBAC, Auth) is fully operational.
            </div>
        </div>

    </div>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('live-clock').textContent =
        now.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
updateClock();
setInterval(updateClock, 1000);
</script>
</body>
</html>
