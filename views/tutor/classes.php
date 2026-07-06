<?php
/**
 * @var array $user
 * @var array $classes
 */
$title = 'My Classes';
require VIEW_PATH . '/partials/tutor_header.php';
?>

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

<div class="section-title"><span class="material-icons" style="font-size:20px;vertical-align:bottom">menu_book</span> Assigned Classes</div>

<?php if (empty($classes)): ?>
    <div class="module-card" style="text-align: center; padding: 40px;">
        <span class="material-icons" style="font-size: 48px; color: var(--muted); margin-bottom: 16px;">sentiment_dissatisfied</span>
        <h3 style="color: var(--text);">No Classes Assigned</h3>
        <p style="color: var(--muted); margin-top: 8px;">You haven't been assigned to any classes yet. Please contact the administrator.</p>
    </div>
<?php else: ?>
    <div class="modules-grid">
        <?php foreach ($classes as $class): ?>
            <div class="module-card">
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
                        <?= htmlspecialchars($class['subject_name']) ?> &bull; <?= htmlspecialchars($class['subject_code']) ?><br>
                        <span style="font-size: 0.8rem; margin-top: 4px; display: block; color: var(--muted);">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle;">schedule</span> <?= htmlspecialchars(trim($class['schedule_details']) ?: 'TBD') ?><br>
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle;">info</span> Status: <span style="text-transform: capitalize;"><?= htmlspecialchars($class['status']) ?></span>
                        </span>
                    </div>
                    
                    <div class="m-footer">
                        <div class="m-footer-actions">
                            <!-- Removed edit, students, materials icons as requested -->
                        </div>
                        <a href="<?= BASE_URL ?>/tutor/classes/view?id=<?= $class['class_id'] ?>" class="m-action-btn" title="Manage Materials">
                            <span class="material-icons" style="font-size:18px;">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/partials/tutor_footer.php'; ?>
