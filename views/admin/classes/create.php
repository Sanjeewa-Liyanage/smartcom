<?php
/**
 * @var array $subjects
 * @var array $tutors
 */
require VIEW_PATH . '/partials/header.php';
?>
<div class="page-header">
    <h2>Add New Class</h2>
    <p>Create a new batch and assign a tutor.</p>
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
        <form action="<?= BASE_URL ?>/admin/classes/create" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-control" required style="background-color: #fff;">
                    <option value="">Select Subject...</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['subject_id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tutor</label>
                <select name="tutor_id" class="form-control" required style="background-color: #fff;">
                    <option value="">Select Tutor...</option>
                    <?php foreach ($tutors as $t): ?>
                        <option value="<?= $t['tutor_id'] ?>"><?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Class Name (e.g. 2026 A/L Accounting)</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Class Type</label>
                <select name="class_type" class="form-control" required style="background-color: #fff;">
                    <option value="theory">Theory</option>
                    <option value="revision">Revision</option>
                    <option value="paper">Paper</option>
                </select>
            </div>
            <div class="form-group" style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label class="form-label">Schedule Day</label>
                    <select name="schedule_day" class="form-control" required style="background-color: #fff;">
                        <option value="">Select Day...</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label class="form-label">Schedule Time</label>
                    <input type="time" name="schedule_time" class="form-control" required>
                </div>
            </div>
            <div class="form-group" style="margin-top:16px;">
                <label class="form-label">Cover Image (Optional)</label>
                <input type="file" name="cover_image" class="form-control" accept="image/*">
            </div>
            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Save Class</button>
                <a href="<?= BASE_URL ?>/admin/classes" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
