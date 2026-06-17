<?php

/**
 * @var string $csrf
 * @var array $pendingUsers
 * @var array $user
 */

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Approvals — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --bg:#f5f6fa; --sidebar:#ffffff; --surface:#ffffff;
            --border:#e8eaed; --text:#1a1a2e; --muted:#8a8fa8;
            --accent:#e53935;
            --success:#2e7d32; --success-bg:#e8f5e9;
            --warning:#f57c00; --warning-bg:#fff3e0;
            --danger:#c62828; --danger-bg:#ffebee;
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
            display:flex; align-items:center; background:#fff;
            position:sticky; top:0; z-index:50;
        }
        .topbar h1 { font-size:1.05rem; font-weight:700; color:var(--text); }
        .content { padding:28px 32px; }

        .page-header { margin-bottom:22px; }
        .page-header h2 { font-size:1.4rem; font-weight:700; }
        .page-header p { font-size:0.875rem; color:var(--muted); margin-top:3px; }

        .alert-success {
            background:var(--success-bg); border:1px solid #a5d6a7;
            border-radius:10px; padding:12px 18px; margin-bottom:20px;
            font-size:0.875rem; color:var(--success); display:flex; align-items:center; gap:6px;
        }

        .pending-banner {
            display:inline-flex; align-items:center; gap:8px;
            padding:10px 18px; background:var(--warning-bg);
            border:1px solid #ffe082; border-radius:10px;
            font-size:0.85rem; color:var(--warning); margin-bottom:20px; font-weight:500;
        }

        .table-card {
            background:var(--surface); border:1px solid var(--border);
            border-radius:14px; overflow:hidden;
        }
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

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:999px; font-size:0.72rem; font-weight:600; }
        .badge-student { background:#e3f2fd; color:#1565c0; }
        .badge-tutor   { background:#e8f5e9; color:#2e7d32; }
        .badge-parent  { background:#fce4ec; color:#c2185b; }

        .actions { display:flex; gap:6px; }
        .btn-sm {
            padding:6px 14px; border-radius:8px; font-size:0.78rem; font-weight:600;
            border:1px solid transparent; cursor:pointer; font-family:'Inter',sans-serif;
            transition:transform 0.15s,opacity 0.15s; display:inline-flex; align-items:center; gap:4px;
        }
        .btn-sm:hover { opacity:0.85; transform:translateY(-1px); }
        .btn-approve { background:#2e7d32; border-color:#2e7d32; color:#fff; }
        .btn-reject  { background:var(--danger-bg); border-color:#ef9a9a; color:var(--danger); }

        .empty-state { padding:52px; text-align:center; color:var(--muted); }
        .empty-state .material-icons { font-size:2.5rem; margin-bottom:10px; opacity:0.4; }
        .empty-state p { font-size:0.9rem; }
        .empty-state .sub { font-size:0.8rem; color:#bbbdcb; margin-top:5px; }

        .user-cell { display:flex; align-items:center; gap:10px; }
        .user-avatar {
            width:34px; height:34px; border-radius:50%; flex-shrink:0;
            display:flex; align-items:center; justify-content:center;
            font-size:12px; font-weight:700; color:#fff;
        }
        .ua-student { background:#1565c0; }
        .ua-tutor   { background:#2e7d32; }
        .ua-parent  { background:#c2185b; }
        .user-name  { font-weight:600; font-size:0.875rem; color:var(--text); }
        .user-email { font-size:0.75rem; color:var(--muted); }
    </style>
</head>
<body>

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
        <a href="<?= BASE_URL ?>/admin/users"   class="nav-link" id="nav-users"><span class="material-icons">group</span> All Users</a>
        <a href="<?= BASE_URL ?>/admin/pending" class="nav-link active" id="nav-pending">
            <span class="material-icons">hourglass_empty</span> Pending Approvals
            <?php if (count($pendingUsers) > 0): ?>
                <span class="badge"><?= count($pendingUsers) ?></span>
            <?php endif; ?>
        </a>
        <div class="nav-section-label">Coming Soon</div>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">menu_book</span> Courses</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">event_available</span> Attendance</a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="role">System Admin</div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout" id="btn-pending-logout"><span class="material-icons" style="font-size:16px">logout</span> Sign Out</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <h1>Pending Approvals</h1>
    </header>

    <div class="content">
        <div class="page-header">
            <h2>Pending Approvals</h2>
            <p>Review and approve or reject new registration requests.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><span class="material-icons" style="font-size:18px">check_circle</span> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($pendingUsers)): ?>
            <div class="pending-banner">
                <span class="material-icons" style="font-size:18px">hourglass_empty</span> <strong><?= count($pendingUsers) ?></strong> account<?= count($pendingUsers) !== 1 ? 's' : '' ?> awaiting review
            </div>
        <?php endif; ?>

        <div class="table-card">
            <?php if (empty($pendingUsers)): ?>
                <div class="empty-state">
                    <div class="material-icons">check_circle_outline</div>
                    <p>No pending approvals!</p>
                    <p class="sub">All registration requests have been processed.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Role Applied</th>
                        <th>Date Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingUsers as $u): ?>
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
                                    default   => ucfirst($u['role'])
                                } ?>
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:0.8rem;">
                            <?= date('d M Y, H:i', strtotime($u['created_at'])) ?>
                        </td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="<?= BASE_URL ?>/admin/approve-user" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                     <button type="submit" class="btn-sm btn-approve" id="btn-approve-<?= $u['user_id'] ?>"><span class="material-icons" style="font-size:16px">check</span> Approve</button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>/admin/reject-user" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                     <button type="submit" class="btn-sm btn-reject" id="btn-reject-<?= $u['user_id'] ?>" onclick="return confirm('Reject this application?')"><span class="material-icons" style="font-size:16px">close</span> Reject</button>
                                </form>
                            </div>
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
