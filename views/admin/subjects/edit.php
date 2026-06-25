<?php require VIEW_PATH . '/partials/header.php'; ?>
<div class="page-header">
    <h2>Edit Subject</h2>
    <p>Update the subject details.</p>
</div>

<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert-error">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
        <?php unset($_SESSION['flash_error']); ?>
    </div>
<?php endif; ?>

<div class="custom-card" style="max-width: 600px;">
    <div class="custom-card-body">
        <form action="<?= BASE_URL ?>/admin/subjects/edit" method="POST">
            <input type="hidden" name="subject_id" value="<?= $subject['subject_id'] ?>">
            <div class="form-group">
                <label class="form-label">Code</label>
                <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($subject['code']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($subject['name']) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"><?= htmlspecialchars($subject['description']) ?></textarea>
            </div>
            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Update Subject</button>
                <a href="<?= BASE_URL ?>/admin/subjects" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
