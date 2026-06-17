<?php

/**
 * @var string $csrf
 * @var array $users
 * @var array $user
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg:#f5f6fa; --sidebar:#ffffff; --surface:#ffffff;
            --border:#e8eaed; --text:#1a1a2e; --muted:#8a8fa8;
            --accent:#e53935; --accent2:#ff5722;
            --success:#2e7d32; --success-bg:#e8f5e9;
            --warning:#f57c00; --warning-bg:#fff3e0;
            --danger:#c62828; --danger-bg:#ffebee;
            --info:#1565c0; --info-bg:#e3f2fd;
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
            width:36px; height:36px; background:var(--accent); border-radius:8px;
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
        .nav-link.active { color:var(--accent); background:#fff5f5; font-weight:600; }
        .nav-link.active::before {
            content:''; position:absolute; left:0; top:0; bottom:0;
            width:3px; background:var(--accent); border-radius:0 2px 2px 0;
        }
        .nav-link .icon { font-size:1rem; width:20px; text-align:center; }
        .nav-link .material-icons { font-size: 20px; width: 20px; text-align: center; }
        .nav-link .badge {
            margin-left:auto; background:var(--danger); color:#fff;
            font-size:0.65rem; font-weight:700; padding:2px 7px; border-radius:999px;
        }
        .sidebar-footer { padding:14px 20px; border-top:1px solid var(--border); }
        .sidebar-user { display:flex; align-items:center; gap:10px; margin-bottom:12px; }
        .sidebar-user .avatar {
            width:34px; height:34px; background:var(--accent);
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

        .page-header { margin-bottom:24px; }
        .page-header h2 { font-size:1.4rem; font-weight:700; }
        .page-header p { font-size:0.875rem; color:var(--muted); margin-top:3px; }

        /* Filter bar */
        .filter-bar {
            display:flex; gap:8px; flex-wrap:wrap; align-items:center;
            margin-bottom:20px;
        }
        .filter-tab {
            padding:7px 16px; border-radius:8px; border:1px solid var(--border);
            color:var(--muted); font-size:0.82rem; font-weight:500;
            text-decoration:none; transition:all 0.18s; background:#fff;
        }
        .filter-tab:hover { border-color:#d0d4e0; color:var(--text); }
        .filter-tab.active { background:var(--accent); border-color:var(--accent); color:#fff; }

        /* Table */
        .table-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; overflow:hidden;
        }
        .table-card-header {
            padding:16px 22px; border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:space-between;
        }
        .table-card-header .th-title { font-size:0.95rem; font-weight:700; color:var(--text); }
        .table-card-header .th-count { font-size:0.8rem; color:var(--muted); }
        table { width:100%; border-collapse:collapse; }
        thead th {
            padding:11px 20px; text-align:left;
            font-size:0.72rem; font-weight:600; text-transform:uppercase;
            letter-spacing:0.06em; color:var(--muted);
            background:#f9fafb; border-bottom:1px solid var(--border);
        }
        tbody tr { transition:background 0.12s; }
        tbody tr:hover { background:#fafbfc; }
        tbody tr:not(:last-child) td { border-bottom:1px solid #f0f2f5; }
        tbody td { padding:13px 20px; font-size:0.855rem; vertical-align:middle; }

        /* Badges */
        .badge {
            display:inline-flex; align-items:center; gap:4px;
            padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:600;
        }
        .badge-student { background:var(--info-bg); color:var(--info); }
        .badge-tutor   { background:var(--success-bg); color:var(--success); }
        .badge-parent  { background:#fce4ec; color:#c2185b; }
        .badge-admin   { background:#ede7f6; color:#4527a0; }
        .badge-active   { background:var(--success-bg); color:var(--success); }
        .badge-pending  { background:var(--warning-bg); color:var(--warning); }
        .badge-rejected { background:var(--danger-bg);  color:var(--danger); }
        .badge-suspended{ background:#eceff1; color:#546e7a; }

        /* Action buttons */
        .actions { display:flex; gap:6px; flex-wrap:wrap; }
        .btn-sm {
            padding:5px 12px; border-radius:7px; font-size:0.75rem; font-weight:500;
            border:1px solid transparent; cursor:pointer; font-family:'Inter',sans-serif;
            transition:opacity 0.15s,transform 0.15s; text-decoration:none; display:inline-flex; align-items:center; gap:4px;
        }
        .btn-sm:hover { opacity:0.85; transform:translateY(-1px); }
        .btn-success { background:var(--success-bg); border-color:#a5d6a7; color:var(--success); }
        .btn-danger  { background:var(--danger-bg);  border-color:#ef9a9a; color:var(--danger); }
        .btn-warning { background:var(--warning-bg); border-color:#ffcc02; color:var(--warning); }
        .btn-info    { background:var(--info-bg);    border-color:#90caf9; color:var(--info); }

        .empty-state { padding:52px; text-align:center; color:var(--muted); }
        .empty-state .material-icons { font-size:2.5rem; margin-bottom:10px; opacity:0.4; }

        .user-cell { display:flex; align-items:center; gap:10px; }
        .user-avatar {
            width:32px; height:32px; border-radius:50%; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:700; color:#fff;
        }
        .ua-student { background:#1565c0; }
        .ua-tutor   { background:#2e7d32; }
        .ua-parent  { background:#c2185b; }
        .ua-admin   { background:#4527a0; }
        .user-name  { font-weight:600; font-size:0.875rem; color:var(--text); }
        .user-email { font-size:0.75rem; color:var(--muted); }
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
        <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link" id="nav-dash"><span class="material-icons">dashboard</span> Dashboard</a>
        <div class="nav-section-label">User Management</div>
        <a href="<?= BASE_URL ?>/admin/users" class="nav-link active" id="nav-users"><span class="material-icons">group</span> All Users</a>
        <a href="<?= BASE_URL ?>/admin/pending" class="nav-link" id="nav-pending"><span class="material-icons">hourglass_empty</span> Pending Approvals</a>
        <div class="nav-section-label">Coming Soon</div>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">menu_book</span> Courses</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">event_available</span> Attendance</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">assessment</span> Reports</a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="role">System Admin</div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout" id="btn-users-logout"><span class="material-icons" style="font-size:16px">logout</span> Sign Out</a>
    </div>
</aside>

<!-- Main -->
<div class="main">
    <header class="topbar">
        <h1>User Management</h1>
    </header>

    <div class="content">
        <div class="page-header">
            <h2>All Users</h2>
            <p>View and manage all registered users across roles.</p>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
            <a href="<?= BASE_URL ?>/admin/users" class="filter-tab <?= ($roleFilter ?? '') === '' ? 'active' : '' ?>" id="filter-all">All</a>
            <a href="<?= BASE_URL ?>/admin/users?role=student" class="filter-tab <?= ($roleFilter ?? '') === 'student' ? 'active' : '' ?>" id="filter-students"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">person</span> Students</a>
            <a href="<?= BASE_URL ?>/admin/users?role=tutor"   class="filter-tab <?= ($roleFilter ?? '') === 'tutor'   ? 'active' : '' ?>" id="filter-tutors"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">school</span> Tutors</a>
            <a href="<?= BASE_URL ?>/admin/users?role=parent"  class="filter-tab <?= ($roleFilter ?? '') === 'parent'  ? 'active' : '' ?>" id="filter-parents"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">family_restroom</span> Parents</a>
            <a href="<?= BASE_URL ?>/admin/users?role=admin"   class="filter-tab <?= ($roleFilter ?? '') === 'admin'   ? 'active' : '' ?>" id="filter-admins"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">admin_panel_settings</span> Admins</a>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div style="display:flex; align-items:center; gap: 12px;">
                    <div class="th-title">Users</div>
                    <div class="th-count"><?= count($users) ?> record<?= count($users) !== 1 ? 's' : '' ?></div>
                </div>
                <?php if (($roleFilter ?? '') === 'tutor'): ?>
                    <a href="<?= BASE_URL ?>/admin/tutors/create" class="btn-sm btn-success" style="padding: 8px 14px; border-radius: 8px; font-size: 0.8rem;"><span class="material-icons" style="font-size:16px">person_add</span> Create Tutor</a>
                <?php endif; ?>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <div class="material-icons">group</div>
                    <p>No users found for the selected filter.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar ua-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="user-name"><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="user-email"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= match($u['role']) {
                                    'student' => '<span class="material-icons" style="font-size:12px">person</span> Student',
                                    'tutor'   => '<span class="material-icons" style="font-size:12px">school</span> Tutor',
                                    'parent'  => '<span class="material-icons" style="font-size:12px">family_restroom</span> Parent',
                                    'admin'   => '<span class="material-icons" style="font-size:12px">admin_panel_settings</span> Admin',
                                    default   => ucfirst($u['role'])
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($u['status'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= match($u['status']) {
                                    'active'    => '<span class="material-icons" style="font-size:12px">check_circle</span> Active',
                                    'pending'   => '<span class="material-icons" style="font-size:12px">hourglass_empty</span> Pending',
                                    'rejected'  => '<span class="material-icons" style="font-size:12px">cancel</span> Rejected',
                                    'suspended' => '<span class="material-icons" style="font-size:12px">block</span> Suspended',
                                    default     => ucfirst($u['status'])
                                } ?>
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:0.8rem;">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($u['role'] !== 'admin'): ?>
                            <div class="actions">
                                <?php if ($u['status'] === 'active'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/toggle-user" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <input type="hidden" name="action" value="suspend">
                                        <button type="submit" class="btn-sm btn-warning" title="Suspend account"><span class="material-icons" style="font-size:14px">block</span> Suspend</button>
                                    </form>
                                <?php elseif ($u['status'] === 'suspended'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/toggle-user" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="btn-sm btn-success" title="Re-activate account"><span class="material-icons" style="font-size:14px">check_circle</span> Activate</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($u['role'] === 'student' && empty($u['parent_id'])): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/enable-parent-control" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <button type="submit" class="btn-sm btn-info" title="Enable Parent Control"><span class="material-icons" style="font-size:14px">family_restroom</span> Enable Parent</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                                <span style="color:var(--muted);font-size:0.75rem;">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
