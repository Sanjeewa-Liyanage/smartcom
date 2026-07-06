<?php
/**
 * @var array $user
 * @var array $class
 * @var array $topic
 * @var array $materials
 * @var string $csrf
 */
$title = 'Topic Materials';
$breadcrumbs = [
    ['label' => 'My Classes', 'url' => '/tutor/classes'],
    ['label' => $class['name'] . ' Topics', 'url' => '/tutor/classes/view?id=' . $class['class_id']],
    ['label' => $topic['name'] . ' Materials', 'url' => '']
];
require VIEW_PATH . '/partials/tutor_header.php';
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
        display: flex;
        justify-content: space-between;
        align-items: center;
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

<div style="display: flex; gap: 12px; align-items: center; margin-bottom: 24px;">
    <a href="<?= BASE_URL ?>/tutor/classes/view?id=<?= $class['class_id'] ?>" class="btn-secondary" style="padding: 8px; display: flex; align-items: center; border-radius: 50%;">
        <span class="material-icons">arrow_back</span>
    </a>
    <div>
        <h2 style="margin: 0;"><?= htmlspecialchars($topic['name']) ?></h2>
        <p style="color: var(--muted); font-size: 0.875rem; margin-top: 4px;"><?= htmlspecialchars($class['name']) ?> &bull; <?= htmlspecialchars($class['subject_name']) ?></p>
    </div>
</div>

<div class="topic-card">
    <div class="topic-header">
        <div>
            <div class="topic-title">Materials</div>
            <?php if (!empty($topic['description'])): ?>
                <div class="topic-desc"><?= nl2br(htmlspecialchars($topic['description'])) ?></div>
            <?php endif; ?>
        </div>
        <button class="btn-secondary" style="padding: 6px 12px; font-size: 0.8rem;" onclick="openUploadModal(<?= $topic['topic_id'] ?>)">
            <span class="material-icons" style="font-size: 16px;">upload</span> Upload Material
        </button>
    </div>
    
    <ul class="material-list">
        <?php if (empty($materials)): ?>
            <li class="material-item" style="justify-content: center; color: var(--muted); font-size: 0.875rem; padding: 24px;">
                No materials uploaded for this topic.
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
                                <?php if (!empty($mat['release_time'])): ?>
                                    <br><span style="color: var(--accent-mid);"><span class="material-icons" style="font-size:12px;vertical-align:middle">schedule</span> Releases: <?= date('M d, Y H:i', strtotime($mat['release_time'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="material-actions">
                        <?php if ($mat['type'] === 'link'): ?>
                            <a href="<?= htmlspecialchars($mat['file_path']) ?>" target="_blank" class="btn-secondary" style="padding: 6px 10px;" title="Open Link">
                                <span class="material-icons" style="font-size: 18px;">open_in_new</span>
                            </a>
                        <?php elseif ($mat['type'] === 'pdf'): ?>
                            <a href="<?= BASE_URL . '/public' . $mat['file_path'] ?>" target="_blank" class="btn-secondary" style="padding: 6px 10px;" title="View PDF">
                                <span class="material-icons" style="font-size: 18px;">visibility</span>
                            </a>
                        <?php else: ?>
                            <a href="<?= BASE_URL . '/public' . $mat['file_path'] ?>" download class="btn-secondary" style="padding: 6px 10px;" title="Download">
                                <span class="material-icons" style="font-size: 18px;">download</span>
                            </a>
                        <?php endif; ?>
                        
                        <form action="<?= BASE_URL ?>/tutor/materials/delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this material?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                            <input type="hidden" name="material_id" value="<?= $mat['material_id'] ?>">
                            <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                            <input type="hidden" name="topic_id" value="<?= $topic['topic_id'] ?>">
                            <button type="submit" class="btn-danger" style="padding: 6px 10px;" title="Delete">
                                <span class="material-icons" style="font-size: 18px;">delete</span>
                            </button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

<!-- Upload Material Modal -->
<div class="modal-overlay" id="uploadModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Upload Material</h3>
            <button class="btn-close" onclick="closeModal('uploadModal')"><span class="material-icons">close</span></button>
        </div>
        <form action="<?= BASE_URL ?>/tutor/materials/upload" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
            <input type="hidden" name="topic_id" id="upload_topic_id" value="">
            
            <div class="form-group">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required placeholder="e.g. Lecture Notes">
            </div>
            <div class="form-group">
                <label class="form-label">Material Type</label>
                <select name="type" id="material_type" class="form-control" required onchange="toggleFileInput()">
                    <option value="pdf">PDF Document (Max 8MB)</option>
                    <option value="word">Word Document (Max 8MB)</option>
                    <option value="pptx">PowerPoint (Max 8MB)</option>
                    <option value="zip">ZIP Archive (Max 20MB)</option>
                    <option value="link">External Link</option>
                </select>
            </div>
            
            <div class="form-group" id="file_group">
                <label class="form-label">File</label>
                <input type="file" name="file" id="file_input" class="form-control">
            </div>
            
            <div class="form-group" id="link_group" style="display: none;">
                <label class="form-label">URL Link</label>
                <input type="url" name="link_url" id="link_input" class="form-control" placeholder="https://...">
            </div>

            <div class="form-group">
                <label class="form-label">Release Time (Optional)</label>
                <input type="datetime-local" name="release_time" class="form-control">
                <small style="color:var(--muted); font-size:0.75rem;">Leave blank to release immediately.</small>
            </div>
            
            <div style="text-align: right; margin-top: 24px;">
                <button type="button" class="btn-secondary" onclick="closeModal('uploadModal')">Cancel</button>
                <button type="submit" class="btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }
    
    function openUploadModal(topicId) {
        document.getElementById('upload_topic_id').value = topicId;
        openModal('uploadModal');
    }
    
    function toggleFileInput() {
        const type = document.getElementById('material_type').value;
        if (type === 'link') {
            document.getElementById('file_group').style.display = 'none';
            document.getElementById('link_group').style.display = 'block';
            document.getElementById('file_input').removeAttribute('required');
            document.getElementById('link_input').setAttribute('required', 'required');
        } else {
            document.getElementById('file_group').style.display = 'block';
            document.getElementById('link_group').style.display = 'none';
            document.getElementById('file_input').setAttribute('required', 'required');
            document.getElementById('link_input').removeAttribute('required');
        }
    }
    // Initialize required state
    toggleFileInput();
</script>

<?php require VIEW_PATH . '/partials/tutor_footer.php'; ?>
