<?php
/**
 * @var array $user
 * @var array $class
 * @var array $topics
 */
$title = 'Class Topics';
$breadcrumbs = [
    ['label' => 'My Classes', 'url' => '/student/classes'],
    ['label' => $class['name'] . ' Topics', 'url' => '']
];
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

<div class="page-header" style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>/student/classes" class="btn btn-secondary" style="padding: 8px; display: flex; align-items: center; border-radius: 50%;">
        <span class="material-icons">arrow_back</span>
    </a>
    <div>
        <h2 style="margin: 0;"><?= htmlspecialchars($class['name']) ?> - Topics</h2>
        <p style="margin: 0; color: var(--muted);">Tutor: <?= htmlspecialchars($class['tutor_fname'] . ' ' . $class['tutor_lname']) ?></p>
    </div>
</div>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert-error">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <?php unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<?php if (empty($topics)): ?>
    <div class="module-card" style="width:100%; text-align: center; padding: 40px;">
        <span class="material-icons" style="font-size: 48px; color: var(--muted); margin-bottom: 16px;">folder_open</span>
        <h3 style="color: var(--text);">No Topics Available</h3>
        <p style="color: var(--muted); margin-top: 8px;">There are no topics published for this class yet.</p>
    </div>
<?php else: ?>
    <div class="modules-grid">
        <?php foreach ($topics as $topic): ?>
            <div class="module-card">
                <a href="<?= BASE_URL ?>/student/topics/view?id=<?= $topic['topic_id'] ?>" style="text-decoration:none; display:block; height:100%; display:flex; flex-direction:column; color:inherit;">
                    <div class="m-cover">
                        <?php $coverImg = !empty($topic['cover_image']) ? $topic['cover_image'] : 'placeholder.jpeg'; ?>
                        <img src="<?= BASE_URL ?>/public/cover_images/<?= htmlspecialchars($coverImg) ?>" alt="Topic Cover">
                    </div>
                    
                    <div class="m-content">
                        <div class="m-title"><?= htmlspecialchars($topic['name']) ?></div>
                        <?php if (!empty($topic['description'])): ?>
                            <div class="m-desc"><?= nl2br(htmlspecialchars($topic['description'])) ?></div>
                        <?php endif; ?>
                        
                        <div class="m-footer">
                            <div class="m-action-btn" title="View Materials">
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
