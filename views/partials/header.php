<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = defined('BASE_PATH') ? BASE_PATH : '';
$relativePath = str_replace($basePath, '', $currentPath);
$pathParts = array_values(array_filter(explode('/', $relativePath)));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel' ?> — Smart Commerce Core</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css?v=<?= filemtime(ROOT_PATH . '/public/css/admin.css') ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!-- Consistent Sidebar -->
    <aside class="sidebar">
        <a href="<?= BASE_URL ?>/admin/dashboard" class="sidebar-brand">
            <div class="logo">S</div>
            <div class="brand-text">
                <div class="app-name">Smart Commerce</div>
                <div class="role-tag">Admin Panel</div>
            </div>
        </a>
        <nav class="sidebar-nav">
            <div class="nav-section-label">Main</div>
            <a href="<?= BASE_URL ?>/admin/dashboard" class="nav-link <?= $relativePath === '/admin/dashboard' ? 'active' : '' ?>"><span class="material-icons">dashboard</span> Dashboard</a>
            
            <div class="nav-section-label">User Management</div>
            <a href="<?= BASE_URL ?>/admin/users" class="nav-link <?= strpos($relativePath, '/admin/users') === 0 ? 'active' : '' ?>"><span class="material-icons">group</span> All Users</a>
            <a href="<?= BASE_URL ?>/admin/pending" class="nav-link <?= strpos($relativePath, '/admin/pending') === 0 ? 'active' : '' ?>"><span class="material-icons">hourglass_empty</span> Pending Approvals</a>
            
            <div class="nav-section-label">LMS Management</div>
            <a href="<?= BASE_URL ?>/admin/subjects" class="nav-link <?= strpos($relativePath, '/admin/subjects') === 0 ? 'active' : '' ?>"><span class="material-icons">menu_book</span> Subjects</a>
            <a href="<?= BASE_URL ?>/admin/classes" class="nav-link <?= strpos($relativePath, '/admin/classes') === 0 ? 'active' : '' ?>"><span class="material-icons">class</span> Classes</a>
            <a href="<?= BASE_URL ?>/admin/attendance/scan" class="nav-link <?= $relativePath === '/admin/attendance/scan' ? 'active' : '' ?>"><span class="material-icons">event_available</span> Scan Attendance</a>
            <a href="<?= BASE_URL ?>/admin/attendance/log" class="nav-link <?= $relativePath === '/admin/attendance/log' ? 'active' : '' ?>"><span class="material-icons">calendar_month</span> Attendance Log</a>

            <div class="nav-section-label">Finance</div>
            <a href="<?= BASE_URL ?>/admin/finance/collect" class="nav-link <?= $relativePath === '/admin/finance/collect' ? 'active' : '' ?>"><span class="material-icons">payments</span> Fee Collection</a>
            <a href="<?= BASE_URL ?>/admin/finance/records" class="nav-link <?= $relativePath === '/admin/finance/records' ? 'active' : '' ?>"><span class="material-icons">receipt_long</span> Payment Records</a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= BASE_URL ?>/logout" class="btn-logout"><span class="material-icons">logout</span> Sign Out</a>
        </div>
    </aside>
    
    <div class="main">
        <header class="topbar">
            <h1><?= htmlspecialchars($title ?? 'Admin Panel', ENT_QUOTES, 'UTF-8') ?></h1>
        </header>

        <!-- Dynamic Breadcrumb -->
        <?php if (!empty($pathParts)): ?>
        <div class="breadcrumb" style="padding: 12px 30px; background: #fdfdfd; border-bottom: 1px solid var(--border); font-size: 0.85rem; color: var(--muted); display:flex; gap: 8px; align-items:center;">
            <a href="<?= BASE_URL ?>/admin/dashboard" style="color: var(--accent); text-decoration:none; display:flex; align-items:center;"><span class="material-icons" style="font-size:18px;">home</span></a>
            <?php
            $accumulatedPath = '';
            foreach ($pathParts as $index => $part) {
                $accumulatedPath .= '/' . $part;
                echo '<span class="material-icons" style="font-size:16px;">chevron_right</span>';
                
                $label = ucwords(str_replace(['-', '_'], ' ', $part));
                if ($index === array_key_last($pathParts)) {
                    echo '<span style="color:var(--text); font-weight:600;">' . htmlspecialchars($label) . '</span>';
                } else {
                    echo '<a href="' . BASE_URL . $accumulatedPath . '" style="color:var(--accent); text-decoration:none;">' . htmlspecialchars($label) . '</a>';
                }
            }
            ?>
        </div>
        <?php endif; ?>

        <div class="content">
