<?php require VIEW_PATH . '/partials/header.php'; ?>
<div class="page-header">
    <h2>Add New Subject</h2>
    <p>Create a new overarching academic subject.</p>
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
        <form action="<?= BASE_URL ?>/admin/subjects/create" method="POST">
            <div class="form-group">
                <label class="form-label">Code (e.g. ACC)</label>
                <input type="text" name="code" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Name (e.g. Accounting)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Save Subject</button>
                <a href="<?= BASE_URL ?>/admin/subjects" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
