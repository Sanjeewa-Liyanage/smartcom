<?php
/**
 * @var array $user
 * @var array $class
 * @var array $topic
 * @var array $materials
 */
$title = 'Materials';
$breadcrumbs = [
    ['label' => 'My Classes', 'url' => '/student/classes'],
    ['label' => $class['name'] . ' Topics', 'url' => '/student/classes/view?id=' . $class['class_id']],
    ['label' => $topic['name'] . ' Materials', 'url' => '']
];
require VIEW_PATH . '/partials/student_header.php';
?>

<style>
    .topic-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .topic-header {
        background: #f8fafc;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
    }
    .topic-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 4px;
    }
    .topic-desc {
        font-size: 0.85rem;
        color: var(--muted);
    }
    .material-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .material-item {
        padding: 16px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.15s;
    }
    .material-item:last-child {
        border-bottom: none;
    }
    .material-item:hover {
        background: #fcfcfc;
    }
    .material-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .material-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .icon-pdf { background: #ffebee; color: #d32f2f; }
    .icon-word { background: #e3f2fd; color: #1976d2; }
    .icon-pptx { background: #fff3e0; color: #f57c00; }
    .icon-zip { background: #f3e5f5; color: #7b1fa2; }
    .icon-link { background: #e0f2f1; color: #00796b; }
    
    .material-title { font-weight: 500; color: var(--text); }
    .material-meta { font-size: 0.75rem; color: var(--muted); margin-top: 4px; }
    .material-actions { display: flex; gap: 8px; }
</style>

<div class="page-header" style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>/student/classes/view?id=<?= $class['class_id'] ?>" class="btn btn-secondary" style="padding: 8px; display: flex; align-items: center; border-radius: 50%;">
        <span class="material-icons">arrow_back</span>
    </a>
    <div>
        <h2 style="margin: 0;"><?= htmlspecialchars($topic['name']) ?></h2>
        <p style="margin: 0; color: var(--muted);"><?= htmlspecialchars($class['name']) ?> &bull; Tutor: <?= htmlspecialchars($class['tutor_fname'] . ' ' . $class['tutor_lname']) ?></p>
    </div>
</div>

<div class="topic-card">
    <div class="topic-header">
        <div class="topic-title">Materials</div>
        <?php if (!empty($topic['description'])): ?>
            <div class="topic-desc"><?= nl2br(htmlspecialchars($topic['description'])) ?></div>
        <?php endif; ?>
    </div>
    
    <ul class="material-list">
        <?php if (empty($materials)): ?>
            <li class="material-item" style="justify-content: center; color: var(--muted); font-size: 0.875rem; padding: 24px;">
                No materials are currently available for this topic.
            </li>
        <?php else: ?>
            <?php foreach ($materials as $mat): 
                $iconClass = 'icon-' . $mat['type'];
                $iconName = 'insert_drive_file';
                switch ($mat['type']) {
                    case 'pdf': $iconName = 'picture_as_pdf'; break;
                    case 'word': $iconName = 'description'; break;
                    case 'pptx': $iconName = 'slideshow'; break;
                    case 'zip': $iconName = 'folder_zip'; break;
                    case 'link': $iconName = 'link'; break;
                }
            ?>
                <li class="material-item">
                    <div class="material-info">
                        <div class="material-icon <?= $iconClass ?>">
                            <span class="material-icons"><?= $iconName ?></span>
                        </div>
                        <div>
                            <div class="material-title"><?= htmlspecialchars($mat['title']) ?></div>
                            <div class="material-meta">
                                Type: <?= strtoupper($mat['type']) ?> &bull; Uploaded: <?= date('M d, Y', strtotime($mat['upload_date'])) ?>
                            </div>
                        </div>
                    </div>
                    <div class="material-actions">
                        <?php if ($mat['type'] === 'link'): ?>
                            <a href="<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                                <span class="material-icons" style="font-size: 16px; margin-right: 4px;">open_in_new</span> Open Link
                            </a>
                        <?php elseif ($mat['type'] === 'pdf'): ?>
                            <a href="<?= BASE_URL . '/public' . $mat['file_path'] ?>" target="_blank" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                                <span class="material-icons" style="font-size: 16px; margin-right: 4px;">visibility</span> View PDF
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL . '/public' . $mat['file_path'] ?>" download class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
                                <span class="material-icons" style="font-size: 16px; margin-right: 4px;">download</span> Download
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<?php require VIEW_PATH . '/partials/student_footer.php'; ?>
