<?php
/**
 * @var array $user
 * @var array $classes
 */
$title = 'My Classes';
require VIEW_PATH . '/partials/student_header.php';
?>

<style>
    .modules-grid {
        display:flex; flex-wrap:wrap;
        gap:24px;
    }
    .module-card {
        background:var(--surface); border:1px solid var(--border);
        border-radius:12px; width:300px;
        transition:box-shadow 0.2s,transform 0.2s,border-color 0.2s;
        overflow:hidden; display:flex; flex-direction:column;
    }
    .module-card:hover { box-shadow:0 4px 14px rgba(0,0,0,0.06); transform:translateY(-2px); border-color:#d0d4e0; }
    .module-card .m-cover { position:relative; width:100%; height:160px; }
    .module-card .m-cover img { width:100%; height:100%; object-fit:cover; display:block; }
    .module-card .m-badge {
        position:absolute; top:12px; left:12px;
        background:#3b82f6; color:#fff;
        padding:4px 12px; border-radius:99px;
        font-size:0.75rem; font-weight:600;
    }
    .module-card .m-badge.theory { background:#3b82f6; }
    .module-card .m-badge.revision { background:#10b981; }
    .module-card .m-badge.paper { background:#8b5cf6; }
    
    .module-card .m-content { padding:20px; flex:1; display:flex; flex-direction:column; }
    .module-card .m-title { font-size:1.15rem; font-weight:700; color:var(--text); margin-bottom:4px; }
    .module-card .m-desc { font-size:0.85rem; color:var(--muted); margin-bottom:16px; }
    .module-card .m-footer {
        margin-top:auto; display:flex; justify-content:flex-end; align-items:center;
        padding-top:16px; border-top:1px solid var(--border);
    }
    .module-card .m-action-btn {
        background:#fef2f2; color:#ef4444; width:32px; height:32px; border-radius:50%;
        display:flex; align-items:center; justify-content:center; text-decoration:none;
        transition:background 0.2s;
    }
    .module-card .m-action-btn:hover { background:#fee2e2; }
</style>

<div class="page-header" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 4px;">My Enrolled Classes</h2>
    <p>View your classes and access learning materials.</p>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert-success">
        <span class="material-icons">check_circle</span>
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
        <?php unset($_SESSION['flash_success']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert-error">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <?php unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if (empty($classes)): ?>
    <div class="module-card" style="width:100%; text-align: center; padding: 40px;">
        <span class="material-icons" style="font-size: 48px; color: var(--muted); margin-bottom: 16px;">sentiment_dissatisfied</span>
        <h3 style="color: var(--text);">No Classes Found</h3>
        <p style="color: var(--muted); margin-top: 8px;">You haven't been enrolled in any classes yet.</p>
    </div>
<?php else: ?>
    <div class="modules-grid">
        <?php foreach ($classes as $class): ?>
            <div class="module-card">
                <a href="<?= BASE_URL ?>/student/classes/view?id=<?= $class['class_id'] ?>" style="text-decoration:none; display:block; height:100%; display:flex; flex-direction:column; color:inherit;">
                    <div class="m-cover">
                        <?php 
                            $coverImg = !empty($class['cover_image']) ? $class['cover_image'] : 'placeholder.jpeg'; 
                            $typeClass = strtolower($class['class_type'] ?? 'theory');
                        ?>
                        <img src="<?= BASE_URL ?>/public/cover_images/<?= htmlspecialchars($coverImg) ?>" alt="Class Cover">
                        <span class="m-badge <?= $typeClass ?>">
                            <?= ucfirst($typeClass) ?>
                        </span>
                    </div>
                    
                    <div class="m-content">
                        <div class="m-title"><?= htmlspecialchars($class['name']) ?></div>
                        <div class="m-desc">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle;">person</span> Tutor: <?= htmlspecialchars($class['tutor_fname'] . ' ' . $class['tutor_lname']) ?>
                        </div>
                        
                        <div class="m-footer">
                            <div class="m-action-btn" title="Enter Class">
                                <span class="material-icons" style="font-size:18px;">arrow_forward</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/partials/student_footer.php'; ?>
