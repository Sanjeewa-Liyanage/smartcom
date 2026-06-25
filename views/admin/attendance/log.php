<?php require VIEW_PATH . '/partials/header.php'; ?>

<div class="page-header">
    <h2><span class="material-icons" style="vertical-align:bottom;">calendar_month</span> Attendance Log</h2>
    <p>View historical attendance records for specific classes by date.</p>
</div>

<div class="custom-card" style="margin-bottom:24px;">
    <div class="custom-card-body" style="padding:20px;">
        <form action="<?= BASE_URL ?>/admin/attendance/log" method="GET" style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">
            
            <div class="form-group" style="flex:2; min-width:250px;">
                <label for="class_id" style="display:block; font-weight:600; margin-bottom:8px;">Select Class / Batch</label>
                <select id="class_id" name="class_id" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border);" required>
                    <option value="">-- Choose Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['class_id'] ?>" <?= $selectedClassId == $c['class_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['subject_name'] . ' - ' . $c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="flex:1; min-width:150px;">
                <label for="date" style="display:block; font-weight:600; margin-bottom:8px;">Date</label>
                <input type="date" id="date" name="date" value="<?= htmlspecialchars($selectedDate) ?>" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border);" required>
            </div>

            <button type="submit" class="btn btn-primary" style="padding:12px 24px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer;">Filter Log</button>
        </form>
    </div>
</div>

<?php if ($selectedClassId && $selectedDate): ?>
    <div class="custom-card">
        <div class="custom-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <span>Attendance Records for <strong><?= htmlspecialchars($selectedDate) ?></strong></span>
            <span style="background:var(--primary-bg); color:var(--primary); padding:4px 12px; border-radius:99px; font-weight:600; font-size:0.85rem;"><?= count($records) ?> Present</span>
        </div>
        <div class="custom-card-body" style="padding:0;">
            <table class="custom-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Student Name</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Student ID</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Scanned Time</th>
                        <th style="padding:12px; text-align:left; background:#f9fafb; border-bottom:1px solid #e8eaed;">Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $r): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;"><strong><?= htmlspecialchars($r['name']) ?></strong><br><span style="color:var(--muted); font-size:0.8rem;"><?= htmlspecialchars($r['email']) ?></span></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;">STU-<?= str_pad((string)$r['student_id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;"><?= date('h:i A', strtotime($r['scanned_at'])) ?></td>
                            <td style="padding:12px; border-bottom:1px solid #f0f2f5;">
                                <?php if($r['method'] === 'qr_scan'): ?>
                                    <span style="background:var(--success-bg);color:var(--success);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;">QR Scan</span>
                                <?php else: ?>
                                    <span style="background:var(--warning-bg);color:var(--warning);padding:3px 8px;border-radius:99px;font-size:0.7rem;font-weight:600;">Manual</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:var(--muted); padding:40px;">No attendance records found for this class on this date.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
