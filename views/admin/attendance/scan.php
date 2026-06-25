<?php require VIEW_PATH . '/partials/header.php'; ?>

<div class="page-header">
    <h2><span class="material-icons" style="vertical-align:bottom;">qr_code_scanner</span> Class Attendance Scanner</h2>
    <p>Scan student QR codes or enter ID manually to mark attendance.</p>
</div>

<?php if (isset($success)): ?>
    <div class="alert-success" style="background:#e8f5e9; color:#2e7d32; padding:12px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <span class="material-icons">check_circle</span>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<?php if (isset($error)): ?>
    <div class="alert-error" style="background:#ffebee; color:#c62828; padding:12px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<?php if (!empty($openSessions)): ?>
    <div style="display:flex; gap:16px; margin-bottom:24px; flex-wrap:wrap;">
        <?php foreach ($openSessions as $session): ?>
            <div class="custom-card" style="flex:1; min-width:250px; background:var(--surface); padding:16px; border-radius:12px; border:1px solid var(--border);">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div>
                        <h4 style="margin:0; font-size:1.1rem;"><?= htmlspecialchars($session['class_name']) ?></h4>
                        <p style="margin:4px 0 0; font-size:0.85rem; color:var(--muted);">Session Active</p>
                    </div>
                    <div style="background:var(--success-bg); color:var(--success); padding:4px 10px; border-radius:99px; font-weight:700; font-size:0.8rem;">
                        <?= $session['present_count'] ?> / <?= $session['enrolled_count'] ?> Present
                    </div>
                </div>
                <div style="margin-top:16px; text-align:right;">
                    <form action="<?= BASE_URL ?>/admin/attendance/sessions/close" method="POST" onsubmit="return confirm('Close this session? Attendance can no longer be recorded.');">
                        <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Close Session</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div style="display:flex; gap:24px; flex-wrap:wrap;">
        <!-- Scanner Area -->
        <div style="flex:2; min-width:300px;">
            <div class="custom-card">
                <div class="custom-card-header" style="display:flex; gap:16px;">
                    <a href="#" id="qr-tab" style="color:var(--accent); font-weight:700; text-decoration:none; padding-bottom:10px; border-bottom:2px solid var(--accent);">QR Scanner</a>
                    <a href="#" id="manual-tab" style="color:var(--muted); font-weight:600; text-decoration:none; padding-bottom:10px;">Manual Entry</a>
                </div>
                <div class="custom-card-body">
                    <!-- QR Scanner Tab -->
                    <div id="qr-pane">
                        <div id="reader" style="width: 100%; min-height: 400px; background: #000; border-radius:8px; overflow:hidden;"></div>
                    </div>
                    <!-- Manual Entry Tab -->
                    <div id="manual-pane" style="display:none; text-align:center; padding:40px 20px;">
                        <h4 style="margin-bottom:10px;">Manual ID Entry</h4>
                        <p style="color:var(--muted); margin-bottom:20px;">Enter the student's ID number if the QR code is unreadable.</p>
                        <div style="display:flex; justify-content:center; gap:8px;">
                            <input type="text" id="manualIdInput" class="form-control" placeholder="e.g. STU-0005" style="max-width:200px; padding:10px; border-radius:8px; border:1px solid var(--border);">
                            <button class="btn btn-primary" type="button" id="btnManualSubmit" style="padding:10px 20px; border-radius:8px; border:none; background:var(--accent); color:white; cursor:pointer;">Verify</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar / Confirmation Card Area -->
        <div style="flex:1; min-width:280px;">
            <!-- Default Sidebar State -->
            <div class="custom-card" id="waitingCard" style="text-align:center; padding:60px 20px;">
                <span class="material-icons" style="font-size:3rem; color:var(--muted);">video_camera_front</span>
                <h5 style="margin-top:16px; font-weight:700;">Waiting for Scan</h5>
                <p style="color:var(--muted); font-size:0.875rem; margin-top:8px;">Point a student's QR code at the camera to begin.</p>
            </div>

            <div class="custom-card" id="confirmationCard" style="display:none;">
                <div class="custom-card-header" style="background:var(--warning-bg); color:var(--warning); text-align:center;">
                    Pending Confirmation
                </div>
                <div class="custom-card-body" style="text-align:center;">
                    <img src="" id="studentPhoto" style="width:100px; height:100px; border-radius:50%; margin-bottom:16px; object-fit:cover; background:#e8eaed;" alt="Student Photo">
                    <h4 id="studentName" style="margin-bottom:4px; font-weight:700;">Name</h4>
                    <p id="studentIdCode" style="color:var(--muted); font-size:0.875rem; margin-bottom:24px;">ID</p>
                    
                    <div style="text-align:left; border:1px solid var(--border); border-radius:8px; overflow:hidden; margin-bottom:24px;">
                        <div style="padding:12px 16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; font-size:0.875rem;">
                            <span>Total Sessions</span>
                            <span id="statTotal" style="background:#e8eaed; padding:2px 8px; border-radius:99px; font-weight:700;">0</span>
                        </div>
                        <div style="padding:12px 16px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; font-size:0.875rem;">
                            <span>Past Attendance</span>
                            <span id="statPast" style="background:#e8eaed; padding:2px 8px; border-radius:99px; font-weight:700;">0</span>
                        </div>
                        <div style="padding:12px 16px; display:flex; justify-content:space-between; align-items:center; font-size:0.875rem;">
                            <span>Payment Status</span>
                            <span id="statPayment" style="background:var(--success-bg); color:var(--success); padding:2px 8px; border-radius:99px; font-weight:700;">Verified</span>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:12px;">
                        <button class="btn" style="background:var(--success); color:#fff; width:100%; justify-content:center; padding:12px; font-size:1rem; border:none; border-radius:8px; cursor:pointer;" id="btnConfirmAttendance">
                            <span class="material-icons" style="font-size:18px;">check_circle</span> Confirm Present
                        </button>
                        <button class="btn btn-secondary" style="width:100%; justify-content:center; padding:12px; border:1px solid var(--border); background:#fff; border-radius:8px; cursor:pointer;" id="btnCancelAttendance">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
<?php else: ?>
    <!-- Pre-Session State -->
    <div class="custom-card" style="max-width:500px; margin:40px auto; text-align:center; padding:40px 20px;">
        <span class="material-icons" style="font-size:3rem; color:var(--accent); margin-bottom:16px;">event_note</span>
        <h3 style="margin-bottom:8px;">Start Today's Session</h3>
        <p style="color:var(--muted); margin-bottom:24px;">You must start an attendance session before scanning students.</p>
        
        <?php if(empty($classes)): ?>
            <p style="color:var(--danger);">No active classes found. <a href="<?= BASE_URL ?>/admin/classes/create">Create a class first</a>.</p>
        <?php else: ?>
            <form action="<?= BASE_URL ?>/admin/attendance/sessions/start" method="POST">
                <div style="margin-bottom:20px; text-align:left;">
                    <label for="class_id" style="display:block; font-weight:600; margin-bottom:8px;">Select Class / Batch</label>
                    <select id="class_id" name="class_id" style="width:100%; padding:12px; border-radius:8px; border:1px solid var(--border);" required>
                        <option value="">-- Choose Class --</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?= $c['class_id'] ?>">
                                <?= htmlspecialchars($c['subject_name'] . ' - ' . $c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:1rem;">Start Session</button>
            </form>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Custom Toast -->
<div id="customToast" style="position:fixed; bottom:24px; right:24px; background:#333; color:#fff; padding:16px 24px; border-radius:8px; display:none; align-items:center; gap:12px; box-shadow:0 10px 30px rgba(0,0,0,0.15); z-index:9999;">
    <span class="material-icons" id="toastIcon">info</span>
    <div>
        <div id="toastTitle" style="font-weight:700; font-size:0.9rem;">Notification</div>
        <div id="toastMessage" style="font-size:0.8rem; opacity:0.9;">Message goes here</div>
    </div>
</div>

<?php if (!empty($openSessions)): ?>
<!-- Include html5-qrcode -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const html5QrCode = new Html5Qrcode("reader");
    let isScanningPaused = false;
    let pendingSessionId = null;
    let pendingUserId = null;
    let scanMethod = 'qr_scan';

    const confirmationCard = document.getElementById('confirmationCard');
    const waitingCard = document.getElementById('waitingCard');
    const toastEl = document.getElementById('customToast');
    let toastTimeout;

    // Tabs logic
    const qrTab = document.getElementById('qr-tab');
    const manualTab = document.getElementById('manual-tab');
    const qrPane = document.getElementById('qr-pane');
    const manualPane = document.getElementById('manual-pane');

    qrTab.addEventListener('click', (e) => {
        e.preventDefault();
        qrTab.style.color = 'var(--accent)';
        qrTab.style.borderBottom = '2px solid var(--accent)';
        manualTab.style.color = 'var(--muted)';
        manualTab.style.borderBottom = 'none';
        qrPane.style.display = 'block';
        manualPane.style.display = 'none';
    });
    
    manualTab.addEventListener('click', (e) => {
        e.preventDefault();
        manualTab.style.color = 'var(--accent)';
        manualTab.style.borderBottom = '2px solid var(--accent)';
        qrTab.style.color = 'var(--muted)';
        qrTab.style.borderBottom = 'none';
        manualPane.style.display = 'block';
        qrPane.style.display = 'none';
    });

    function showToast(title, message, isError = false) {
        clearTimeout(toastTimeout);
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMessage').textContent = message;
        
        toastEl.style.background = isError ? 'var(--danger)' : 'var(--success)';
        document.getElementById('toastIcon').textContent = isError ? 'error' : 'check_circle';
        
        toastEl.style.display = 'flex';
        toastTimeout = setTimeout(() => { toastEl.style.display = 'none'; }, 3000);
    }

    function resetUI() {
        confirmationCard.style.display = 'none';
        waitingCard.style.display = 'block';
        pendingSessionId = null;
        pendingUserId = null;
        document.getElementById('manualIdInput').value = '';
    }

    function handleVerificationResponse(data, methodType) {
        if (!data.success) {
            showToast('Verification Failed', data.message, true);
            setTimeout(() => { isScanningPaused = false; }, 2000);
            return;
        }

        scanMethod = methodType;
        const student = data.data;
        pendingSessionId = student.session_id;
        pendingUserId = student.user_id;

        document.getElementById('studentName').textContent = student.name;
        document.getElementById('studentPhoto').src = student.photo_url;
        document.getElementById('studentIdCode').textContent = student.id_code;
        document.getElementById('statTotal').textContent = student.total_sessions;
        document.getElementById('statPast').textContent = student.past_attendance;
        document.getElementById('statPayment').textContent = student.payment_status;

        waitingCard.style.display = 'none';
        confirmationCard.style.display = 'block';
        showToast('Student Found', 'Please confirm attendance.', false);
    }

    // Process a scanned QR code
    function onScanSuccess(decodedText, decodedResult) {
        if (isScanningPaused) return;
        isScanningPaused = true;
        
        showToast('Scanning...', 'Verifying QR code...', false);

        fetch('<?= BASE_URL ?>/admin/attendance/verify', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ qr_data: decodedText })
        })
        .then(response => response.json())
        .then(data => handleVerificationResponse(data, 'qr_scan'))
        .catch(err => {
            console.error(err);
            showToast('Error', 'Server error during verification.', true);
            setTimeout(() => { isScanningPaused = false; }, 2000);
        });
    }

    // Start scanner
    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length) {
            const cameraId = devices[0].id;
            html5QrCode.start(
                cameraId, 
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                (errorMessage) => { /* ignore normal scan errors */ }
            ).catch(err => console.error("Scanner failed to start", err));
        }
    }).catch(err => {
        console.error("No cameras found", err);
    });

    // Manual Entry Submit
    document.getElementById('btnManualSubmit').addEventListener('click', function() {
        if (isScanningPaused) return;
        
        const manualId = document.getElementById('manualIdInput').value;
        if (!manualId) return;

        isScanningPaused = true;
        fetch('<?= BASE_URL ?>/admin/attendance/verify', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ manual_code: manualId })
        })
        .then(response => response.json())
        .then(data => handleVerificationResponse(data, 'manual'))
        .catch(err => {
            console.error(err);
            showToast('Error', 'Server error during manual verification.', true);
            setTimeout(() => { isScanningPaused = false; resetUI(); }, 2000);
        });
    });

    // Confirm Attendance
    document.getElementById('btnConfirmAttendance').addEventListener('click', function() {
        if (!pendingSessionId || !pendingUserId) return;

        this.disabled = true;

        fetch('<?= BASE_URL ?>/admin/attendance/confirm', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                session_id: pendingSessionId,
                user_id: pendingUserId,
                method: scanMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message, false);
                // Dynamically update the present count
                const sessionDivs = document.querySelectorAll('.custom-card');
                // Normally we'd reload the page to get the updated counts reliably,
                // but for now we just show success and let the user keep scanning.
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast('Error', data.message, true);
                this.disabled = false;
                setTimeout(() => { isScanningPaused = false; }, 1500);
            }
            resetUI();
        })
        .catch(err => {
            console.error(err);
            showToast('Error', 'Server error during confirmation.', true);
            this.disabled = false;
        });
    });

    // Cancel Attendance
    document.getElementById('btnCancelAttendance').addEventListener('click', function() {
        resetUI();
        isScanningPaused = false;
    });
});
</script>
<?php endif; ?>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
