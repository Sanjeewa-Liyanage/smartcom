<?php
/**
 * @var array $user
 * @var array $class
 * @var array $topics
 * @var array $materialsByTopic
 * @var string $csrf
 */
$title = 'Class Materials';
$breadcrumbs = [
    ['label' => 'My Classes', 'url' => '/tutor/classes'],
    ['label' => $class['name'] . ' Topics', 'url' => '']
];
require VIEW_PATH . '/partials/tutor_header.php';
?>

<style>
    /* Modals */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 1000;
        display: none; align-items: center; justify-content: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-content {
        background: #fff; border-radius: 12px; width: 100%; max-width: 500px;
        padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { margin: 0; font-size: 1.2rem; }
    .btn-close { background: none; border: none; cursor: pointer; color: var(--muted); }
</style>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert-success"><span class="material-icons">check_circle</span> <?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert-error"><span class="material-icons">error</span> <?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="margin: 0;"><?= htmlspecialchars($class['name']) ?> Materials</h2>
        <p style="color: var(--muted); font-size: 0.875rem; margin-top: 4px;">Subject: <?= htmlspecialchars($class['subject_name']) ?></p>
    </div>
    <button class="btn-primary" onclick="openModal('topicModal')">
        <span class="material-icons">add</span> New Topic
    </button>
</div>

<?php if (empty($topics)): ?>
    <div class="module-card" style="text-align: center; padding: 40px;">
        <span class="material-icons" style="font-size: 48px; color: var(--muted); margin-bottom: 16px;">folder_open</span>
        <h3 style="color: var(--text);">No Topics Yet</h3>
        <p style="color: var(--muted); margin-top: 8px;">Create your first topic to start organizing your materials.</p>
    </div>
<?php else: ?>
    <div class="modules-grid">
        <?php foreach ($topics as $topic): ?>
            <div class="module-card">
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
                        <div class="m-footer-actions">
                            <!-- empty for topics unless needed -->
                        </div>
                        <a href="<?= BASE_URL ?>/tutor/topics/view?id=<?= $topic['topic_id'] ?>" class="m-action-btn" title="View Materials">
                            <span class="material-icons" style="font-size:18px;">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Create Topic Modal -->
<div class="modal-overlay" id="topicModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Create New Topic</h3>
            <button class="btn-close" onclick="closeModal('topicModal')"><span class="material-icons">close</span></button>
        </div>
        <form action="<?= BASE_URL ?>/tutor/topics/store" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Topic Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g. Chapter 1: Introduction">
            </div>
            <div class="form-group">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Cover Image (Optional)</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            <div style="text-align: right; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeModal('topicModal')">Cancel</button>
                <button type="submit" class="btn-primary">Create Topic</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
</script>

<?php require VIEW_PATH . '/partials/tutor_footer.php'; ?>
