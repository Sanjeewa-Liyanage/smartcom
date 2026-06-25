<?php
/**
 * @var array $classes
 */
require VIEW_PATH . '/partials/header.php';
?>
<div class="page-header">
    <h2>Manage Classes (Batches)</h2>
    <p>View, edit, and manage student batches and assigned tutors.</p>
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
    <h3>Classes List</h3>
    <a href="<?= BASE_URL ?>/admin/classes/create" class="btn btn-primary">
        <span class="material-icons" style="font-size:18px">add</span> Add Class
    </a>
</div>

<div class="custom-card">
    <div class="custom-card-body" style="padding:0;">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Class Name</th>
                    <th>Tutor</th>
                    <th>Schedule</th>
                    <th>Status</th>
                    <th style="width:250px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classes as $c): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c['subject_name']) ?></strong></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['tutor_fname'] . ' ' . $c['tutor_lname']) ?></td>
                        <td>
                            <?php 
                                $sched = trim($c['schedule_details']);
                                echo htmlspecialchars($sched ?: 'N/A');
                            ?>
                        </td>
                        <td>
                            <?php if($c['status'] === 'active'): ?>
                                <span style="background:var(--success-bg);color:var(--success);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;">Active</span>
                            <?php else: ?>
                                <span style="background:var(--danger-bg);color:var(--danger);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="<?= BASE_URL ?>/admin/classes/enrollments?class_id=<?= $c['class_id'] ?>" class="btn btn-sm btn-info" style="background:#e3f2fd; color:#1565c0; border-color:#90caf9;">Enrollments</a>
                                <a href="<?= BASE_URL ?>/admin/classes/edit?id=<?= $c['class_id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="<?= BASE_URL ?>/admin/classes/delete" method="POST" onsubmit="return confirm('Delete this class?');" style="display:inline;">
                                    <input type="hidden" name="class_id" value="<?= $c['class_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($classes)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--muted); padding:30px;">No classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
