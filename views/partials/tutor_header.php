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
    <title><?= htmlspecialchars($title ?? 'Tutor Dashboard') ?> — Smart Commerce Core</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tutor.css?v=<?= filemtime(ROOT_PATH . '/public/css/tutor.css') ?>">
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="logo">T</div>
        <div class="brand-text">
            <div class="app-name">Smart Commerce</div>
            <div class="role-tag">Tutor Portal</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="<?= BASE_URL ?>/tutor/dashboard" class="nav-link <?= $relativePath === '/tutor/dashboard' ? 'active' : '' ?>">
            <span class="material-icons">dashboard</span> Dashboard
        </a>
        <a href="<?= BASE_URL ?>/tutor/classes" class="nav-link <?= strpos($relativePath, '/tutor/classes') === 0 ? 'active' : '' ?>">
            <span class="material-icons">menu_book</span> My Classes
        </a>
        
        <div class="nav-section-label">Coming Soon</div>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">quiz</span> Quizzes</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">assignment</span> Assignments</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">group</span> My Students</a>
        <a href="#" class="nav-link" style="opacity:0.4;cursor:not-allowed"><span class="material-icons">bar_chart</span> Performance</a>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($user['name'] ?? 'T', 0, 1)) ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars($user['name'] ?? 'Tutor', ENT_QUOTES, 'UTF-8') ?></div>
                <div class="role">Tutor</div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout" class="btn-logout" id="btn-tutor-logout"><span class="material-icons" style="font-size:16px">logout</span> Sign Out</a>
    </div>
</aside>

<div class="main">
    <header class="topbar">
        <h1><?= htmlspecialchars($title ?? 'Tutor Dashboard', ENT_QUOTES, 'UTF-8') ?></h1>
    </header>

    <?php if (!empty($pathParts)): ?>
    <div class="breadcrumb" style="padding: 12px 32px; background: #fdfdfd; border-bottom: 1px solid var(--border); font-size: 0.85rem; color: var(--muted); display:flex; gap: 8px; align-items:center;">
        <a href="<?= BASE_URL ?>/tutor/dashboard" style="color: var(--accent); text-decoration:none; display:flex; align-items:center;"><span class="material-icons" style="font-size:18px;">home</span></a>
        <?php
        if (isset($breadcrumbs) && is_array($breadcrumbs)) {
            foreach ($breadcrumbs as $index => $crumb) {
                echo '<span class="material-icons" style="font-size:16px;">chevron_right</span>';
                if ($index === array_key_last($breadcrumbs)) {
                    echo '<span style="color:var(--text); font-weight:600;">' . htmlspecialchars($crumb['label']) . '</span>';
                } else {
                    echo '<a href="' . BASE_URL . $crumb['url'] . '" style="color:var(--accent); text-decoration:none;">' . htmlspecialchars($crumb['label']) . '</a>';
                }
            }
        } else {
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
        }
        ?>
    </div>
    <?php endif; ?>

    <div class="content">
