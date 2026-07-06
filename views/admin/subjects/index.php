<?php

/**
 * @var array $subjects
 */

require VIEW_PATH . '/partials/header.php'; 
?>
<div class="page-header">
    <h2>Manage Subjects</h2>
    <p>View, edit, and manage all academic subjects.</p>
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

<div class="section-header">
    <h3>Subjects List</h3>
    <a href="<?= BASE_URL ?>/admin/subjects/create" class="btn btn-primary">
        <span class="material-icons" style="font-size:18px">add</span> Add Subject
    </a>
</div>

<div class="custom-card">
    <div class="custom-card-body" style="padding:0;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th style="width:150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['code']) ?></strong></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['description']) ?></td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="<?= BASE_URL ?>/admin/subjects/edit?id=<?= $s['subject_id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="<?= BASE_URL ?>/admin/subjects/delete" method="POST" onsubmit="return confirm('Delete this subject? This will cascade delete its classes.');" style="display:inline;">
                                    <input type="hidden" name="subject_id" value="<?= $s['subject_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($subjects)): ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--muted); padding:30px;">No subjects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
