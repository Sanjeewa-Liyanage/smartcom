<?php
/**
 * @var array $class
 * @var array $subjects
 * @var array $tutors
 */
require VIEW_PATH . '/partials/header.php';
?>
<div class="page-header">
    <h2>Edit Class</h2>
    <p>Update batch details and tutor assignment.</p>
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
        <form action="<?= BASE_URL ?>/admin/classes/edit" method="POST">
            <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
            <div class="form-group">
                <label class="form-label">Subject</label>
                <select name="subject_id" class="form-control" required style="background-color: #fff;">
                    <option value="">Select Subject...</option>
                    <?php foreach ($subjects as $s): ?>
                        <option value="<?= $s['subject_id'] ?>" <?= $class['subject_id'] == $s['subject_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tutor</label>
                <select name="tutor_id" class="form-control" required style="background-color: #fff;">
                    <option value="">Select Tutor...</option>
                    <?php foreach ($tutors as $t): ?>
                        <option value="<?= $t['tutor_id'] ?>" <?= $class['tutor_id'] == $t['tutor_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['first_name'] . ' ' . $t['last_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Class Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($class['name']) ?>" required>
            </div>
            <?php
                $details = $class['schedule_details'] ?? '';
                $parts = explode(' ', $details);
                $scheduleDate = $parts[0] ?? '';
                $scheduleTime = '';
                if (count($parts) >= 2) {
                    $timeString = implode(' ', array_slice($parts, 1));
                    $scheduleTime = date('H:i', strtotime($timeString));
                }
            ?>
            <div class="form-group" style="display:flex; gap:16px;">
                <div style="flex:1;">
                    <label class="form-label">Schedule Day</label>
                    <select name="schedule_day" class="form-control" required style="background-color: #fff;">
                        <option value="">Select Day...</option>
                        <option value="Monday" <?= $scheduleDate === 'Monday' ? 'selected' : '' ?>>Monday</option>
                        <option value="Tuesday" <?= $scheduleDate === 'Tuesday' ? 'selected' : '' ?>>Tuesday</option>
                        <option value="Wednesday" <?= $scheduleDate === 'Wednesday' ? 'selected' : '' ?>>Wednesday</option>
                        <option value="Thursday" <?= $scheduleDate === 'Thursday' ? 'selected' : '' ?>>Thursday</option>
                        <option value="Friday" <?= $scheduleDate === 'Friday' ? 'selected' : '' ?>>Friday</option>
                        <option value="Saturday" <?= $scheduleDate === 'Saturday' ? 'selected' : '' ?>>Saturday</option>
                        <option value="Sunday" <?= $scheduleDate === 'Sunday' ? 'selected' : '' ?>>Sunday</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <label class="form-label">Schedule Time</label>
                    <input type="time" name="schedule_time" class="form-control" value="<?= htmlspecialchars($scheduleTime) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required style="background-color: #fff;">
                    <option value="active" <?= $class['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $class['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div style="margin-top:24px;">
                <button type="submit" class="btn btn-primary">Update Class</button>
                <a href="<?= BASE_URL ?>/admin/classes" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
