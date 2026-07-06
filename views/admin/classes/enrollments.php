<?php
/**
 * @var array $class
 * @var array $subjects
 * @var array $tutors
 * @var array $enrollments
 * @var array $availableStudents
 */
$title = 'Class Enrollments';
require VIEW_PATH . '/partials/header.php';
?>

<div class="page-header">
    <h2>Enrollments for <?= htmlspecialchars($class['name']) ?></h2>
    <p>Manage student enrollments for this specific class.</p>
</div>

<?php if (!empty($success)): ?>
    <div class="alert-success">
        <span class="material-icons">check_circle</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert-error" style="background:#ffebee; color:#c62828; border:1px solid #ef9a9a; padding:12px; border-radius:8px; display:flex; align-items:center; gap:8px; margin-bottom:20px;">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="section-header" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
    <h3>Enrolled Students (<?= count($enrollments) ?>)</h3>
</div>

<div style="display:flex; gap:20px; align-items:flex-start;">
    
    <!-- Left: Table of current enrollments -->
    <div class="custom-card" style="flex:2;">
        <div class="custom-card-body" style="padding:0;">
            <table class="custom-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Student Name</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Student ID</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Enrollment Date</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Status</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrollments as $e): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;"><strong><?= htmlspecialchars($e['name']) ?></strong><br><span style="color:var(--muted); font-size:0.8rem;"><?= htmlspecialchars($e['email']) ?></span></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;">STU-<?= str_pad((string)$e['student_id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;"><?= date('M j, Y', strtotime($e['enrollment_date'])) ?></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;">
                                <?php if($e['enrollment_status'] === 'active'): ?>
                                    <span style="background:var(--success-bg);color:var(--success);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;">Active</span>
                                <?php else: ?>
                                    <span style="background:var(--danger-bg);color:var(--danger);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;"><?= ucfirst($e['enrollment_status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;">
                                <?php if($e['enrollment_status'] === 'active'): ?>
                                    <form action="<?= BASE_URL ?>/admin/classes/unenroll" method="POST" onsubmit="return confirm('Suspend this enrollment?');">
                                        <input type="hidden" name="enrollment_id" value="<?= $e['enrollment_id'] ?>">
                                        <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Suspend</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:var(--muted); padding:30px;">No students enrolled in this class.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Add new student -->
    <div class="custom-card" style="flex:1;">
        <div class="custom-card-body" style="padding:20px;">
            <h4 style="margin-bottom:15px; font-weight:600;">Enroll Student</h4>
            
            <?php if (empty($availableStudents)): ?>
                <p style="color:var(--muted); font-size:0.9rem;">No active un-enrolled students available.</p>
            <?php else: ?>
                <form action="<?= BASE_URL ?>/admin/classes/enroll" method="POST">
                    <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">
                    
                    <div class="form-group" style="margin-bottom:15px; position:relative;">
                        <label for="studentSearch" style="display:block; margin-bottom:5px; font-weight:500; font-size:0.9rem;">Search Student</label>
                        <input type="text" id="studentSearch" placeholder="Search by name, SCC ID or STU ID..." style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" autocomplete="off">
                        <input type="hidden" name="student_id" id="student_id" required>
                        
                        <div id="studentDropdown" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid var(--border); border-radius:8px; max-height:200px; overflow-y:auto; z-index:10; box-shadow:0 4px 12px rgba(0,0,0,0.1); margin-top:4px;">
                            <?php foreach ($availableStudents as $s): ?>
                                <?php $displayText = htmlspecialchars($s['name']) . ' (' . htmlspecialchars($s['scc_id'] ?? 'N/A') . ' / STU-' . str_pad((string)$s['student_id'], 4, '0', STR_PAD_LEFT) . ')'; ?>
                                <div class="student-option" data-id="<?= $s['student_id'] ?>" data-search="<?= strtolower($displayText) ?>" style="padding:10px 12px; cursor:pointer; border-bottom:1px solid #f0f2f5;">
                                    <?= $displayText ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; padding:10px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Add to Class</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>

<style>
.student-option:hover {
    background: var(--bg-light);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const dropdown = document.getElementById('studentDropdown');
    const hiddenInput = document.getElementById('student_id');
    const options = document.querySelectorAll('.student-option');

    if (!searchInput) return;

    searchInput.addEventListener('focus', () => {
        dropdown.style.display = 'block';
    });

    searchInput.addEventListener('input', function() {
        dropdown.style.display = 'block';
        const query = this.value.toLowerCase();
        let hasVisible = false;
        
        options.forEach(opt => {
            if (opt.dataset.search.includes(query)) {
                opt.style.display = 'block';
                hasVisible = true;
            } else {
                opt.style.display = 'none';
            }
        });
        
        // Reset hidden input if user changes text after selecting
        hiddenInput.value = ''; 
    });

    options.forEach(opt => {
        opt.addEventListener('click', function() {
            hiddenInput.value = this.dataset.id;
            searchInput.value = this.textContent.trim();
            dropdown.style.display = 'none';
        });
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    });
});
</script>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
