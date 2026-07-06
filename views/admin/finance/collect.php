<?php
/**
 * @var string|null $title
 */
require VIEW_PATH . '/partials/header.php';
?>
<div class="page-header">
    <h2>Collect Fees</h2>
    <p>Scan student QR code or enter SCC-ID to process payments.</p>
</div>

<?php if (isset($_SESSION['_flash']['error'])): ?>
    <div class="alert-error">
        <span class="material-icons">error</span>
        <?= htmlspecialchars($_SESSION['_flash']['error']) ?>
        <?php unset($_SESSION['_flash']['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['_flash']['success'])): ?>
    <div class="alert-success">
        <span class="material-icons">check_circle</span>
        <?= htmlspecialchars($_SESSION['_flash']['success']) ?>
        <?php unset($_SESSION['_flash']['success']); ?>
    </div>
<?php endif; ?>

<div style="display:flex; gap:24px; flex-wrap:wrap;">
    <!-- Scanner Area -->
    <div style="flex:1; min-width:300px;">
        <div class="custom-card">
            <div class="custom-card-header" style="display:flex; gap:16px;">
                <a href="#" id="qr-tab" style="color:var(--accent); font-weight:700; text-decoration:none; padding-bottom:10px; border-bottom:2px solid var(--accent);">QR Scanner</a>
                <a href="#" id="manual-tab" style="color:var(--muted); font-weight:600; text-decoration:none; padding-bottom:10px;">Manual Entry</a>
            </div>
            <div class="custom-card-body">
                <!-- QR Scanner Tab -->
                <div id="qr-pane">
                    <div id="reader" style="width: 100%; min-height: 300px; background: #000; border-radius:8px; overflow:hidden;"></div>
                    <div style="text-align:center; margin-top:12px;">
                        <button class="btn btn-secondary" id="btnToggleCamera" type="button" style="display:inline-flex; align-items:center; gap:4px;">
                            <span class="material-icons" id="cameraIcon" style="font-size:18px;">videocam</span> 
                            <span id="cameraBtnText">Start Camera</span>
                        </button>
                    </div>
                </div>
                <!-- Manual Entry Tab -->
                <div id="manual-pane" style="display:none; text-align:center; padding:40px 20px;">
                    <h4 style="margin-bottom:10px;">Manual ID Entry</h4>
                    <p style="color:var(--muted); margin-bottom:20px;">Enter the student's ID number if the QR code is unreadable.</p>
                    <div style="display:flex; justify-content:center; gap:8px;">
                        <input type="text" id="sccIdInput" class="form-control" placeholder="e.g. SCC-000001" style="max-width:200px; padding:10px; border-radius:8px; border:1px solid var(--border);">
                        <button class="btn btn-primary" type="button" id="btnFetchStudent" style="padding:10px 20px; border-radius:8px; border:none; background:var(--accent); color:white; cursor:pointer;">Verify</button>
                    </div>
                </div>
                
                <!-- Loading Indicator -->
                <div id="loadingIndicator" style="display:none; text-align:center; padding: 20px;">
                    <span class="material-icons" style="animation: spin 1s linear infinite; font-size:32px; color:var(--accent);">autorenew</span>
                    <p>Fetching student details...</p>
                </div>
                
                <!-- Error Display -->
                <div id="errorDisplay" class="alert-error" style="display:none; margin-top:24px;"></div>
            </div>
        </div>
    </div>

    <!-- Payment Area -->
    <div style="flex:1; min-width:400px;">
        <div class="custom-card" id="waitingCard" style="text-align:center; padding:60px 20px;">
            <span class="material-icons" style="font-size:3rem; color:var(--muted);">payments</span>
            <h5 style="margin-top:16px; font-weight:700;">Waiting for Scan</h5>
            <p style="color:var(--muted); font-size:0.875rem; margin-top:8px;">Scan a student's QR code to begin payment collection.</p>
        </div>

        <div class="custom-card" id="paymentSection" style="display:none;">
            <div class="custom-card-header" style="background:var(--success-bg); color:var(--success); font-weight:bold;">
                Process Payment
            </div>
            <div class="custom-card-body">
                <div style="background:var(--bg-light); padding:16px; border-radius:8px; margin-bottom:24px;">
                    <h4 style="margin-top:0; margin-bottom:8px; color:var(--text); font-size:1.1rem;">Student Details</h4>
                    <div style="display:grid; grid-template-columns: 1fr; gap:8px;">
                        <div><strong>Name:</strong> <span id="studentName"></span></div>
                        <div><strong>SCC ID:</strong> <span id="studentSccId"></span></div>
                    </div>
                </div>
                
                <form action="<?= BASE_URL ?>/admin/finance/pay" method="POST" id="paymentForm">
                    <input type="hidden" name="student_id" id="hiddenStudentId" value="">
                    
                    <div class="form-group">
                        <label class="form-label">Select Class</label>
                        <select name="class_id" id="classSelect" class="form-control" required style="background-color: #fff;">
                            <option value="">-- Select Class --</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="monthSelectionGroup" style="display:none;">
                        <label class="form-label">Add Month to Pay</label>
                        <div style="display:flex; gap:8px;">
                            <select id="singleMonthSelect" class="form-control" style="background-color: #fff; flex:1;">
                                <?php
                                    $currentYear = (int)date('Y');
                                    $years = [$currentYear, $currentYear + 1];
                                    $months = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'];
                                    foreach($years as $yr) {
                                        foreach($months as $mNum => $mName) {
                                            $val = $yr . '-' . $mNum;
                                            echo "<option value=\"$val\">$mName $yr</option>";
                                        }
                                    }
                                ?>
                            </select>
                            <button type="button" class="btn btn-secondary" id="btnAddMonth">Add</button>
                        </div>
                        
                        <div style="margin-top: 16px;">
                            <label class="form-label">Selected Months</label>
                            <ul id="selectedMonthsList" style="list-style:none; padding:0; margin:0; border:1px solid var(--border); border-radius:8px; max-height: 150px; overflow-y:auto; background:#fff;">
                                <li id="emptyMonthMsg" style="padding:10px; color:var(--muted); font-size:0.9rem; text-align:center;">No months added yet.</li>
                            </ul>
                        </div>
                        <div id="hiddenInputsContainer"></div>
                    </div>
                    
                    <div id="feeCalculationBox" style="display:none; background: #e8f5e9; padding: 20px; border-radius: 8px; border: 1px solid #c8e6c9; margin-top: 24px;">
                        <div style="display:flex; justify-content: space-between; align-items:center;">
                            <div>
                                <div style="font-size: 0.9rem; color: #2e7d32; font-weight:600;">Monthly Fee: Rs. <span id="displayMonthlyFee">0.00</span></div>
                                <div style="font-size: 0.9rem; color: #2e7d32; font-weight:600;">Months: <span id="displayMonthCount">0</span></div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size: 0.9rem; color: #2e7d32; font-weight:600;">Total Payable</div>
                                <div style="font-size: 1.8rem; font-weight:700; color: #1b5e20;">Rs. <span id="displayTotal">0.00</span></div>
                                <input type="hidden" name="total_amount" id="hiddenTotalAmount" value="0">
                            </div>
                        </div>
                    </div>
                    
                    <div style="margin-top:24px; display:flex; gap:12px;">
                        <button type="submit" class="btn btn-primary" id="btnSubmitPayment" disabled style="flex:1;">Confirm Payment</button>
                        <button type="button" class="btn btn-secondary" id="btnCancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.month-list-item {
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.95rem;
}
.month-list-item:last-child {
    border-bottom: none;
}
.btn-remove-month {
    background: none;
    border: none;
    color: var(--danger);
    cursor: pointer;
    display: flex;
    align-items: center;
    padding: 4px;
    border-radius: 4px;
}
.btn-remove-month:hover {
    background: #ffebee;
}
</style>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sccIdInput = document.getElementById('sccIdInput');
    const btnFetchStudent = document.getElementById('btnFetchStudent');
    const loadingIndicator = document.getElementById('loadingIndicator');
    const errorDisplay = document.getElementById('errorDisplay');
    const paymentSection = document.getElementById('paymentSection');
    const waitingCard = document.getElementById('waitingCard');
    
    // UI Elements for Student Details
    const studentName = document.getElementById('studentName');
    const studentSccId = document.getElementById('studentSccId');
    const hiddenStudentId = document.getElementById('hiddenStudentId');
    
    // UI Elements for Payment form
    const classSelect = document.getElementById('classSelect');
    const monthSelectionGroup = document.getElementById('monthSelectionGroup');
    const singleMonthSelect = document.getElementById('singleMonthSelect');
    const btnAddMonth = document.getElementById('btnAddMonth');
    const selectedMonthsList = document.getElementById('selectedMonthsList');
    const emptyMonthMsg = document.getElementById('emptyMonthMsg');
    const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');
    
    const feeCalculationBox = document.getElementById('feeCalculationBox');
    const displayMonthlyFee = document.getElementById('displayMonthlyFee');
    const displayMonthCount = document.getElementById('displayMonthCount');
    const displayTotal = document.getElementById('displayTotal');
    const hiddenTotalAmount = document.getElementById('hiddenTotalAmount');
    
    const btnSubmitPayment = document.getElementById('btnSubmitPayment');
    const btnCancel = document.getElementById('btnCancel');
    
    let currentClasses = [];
    let selectedMonths = new Set();
    
    // Tabs logic
    const qrTab = document.getElementById('qr-tab');
    const manualTab = document.getElementById('manual-tab');
    const qrPane = document.getElementById('qr-pane');
    const manualPane = document.getElementById('manual-pane');
    
    // QR Scanner setup
    const html5QrCode = new Html5Qrcode("reader");
    let isScanningPaused = false;
    let isCameraOn = false;
    let currentCameraId = null;
    
    const btnToggleCamera = document.getElementById('btnToggleCamera');
    const cameraIcon = document.getElementById('cameraIcon');
    const cameraBtnText = document.getElementById('cameraBtnText');

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
        sccIdInput.focus();
    });

    function onScanSuccess(decodedText, decodedResult) {
        if (isScanningPaused) return;
        isScanningPaused = true;
        
        // Optionally stop the camera upon successful scan, or just leave it running
        // toggleCamera();
        
        fetchStudentData(decodedText);
    }
    
    function startCamera() {
        if (!currentCameraId) {
            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    currentCameraId = devices[0].id;
                    startCameraInternal();
                } else {
                    alert("No cameras found.");
                }
            }).catch(err => console.error("Error getting cameras", err));
        } else {
            startCameraInternal();
        }
    }
    
    function startCameraInternal() {
        html5QrCode.start(
            currentCameraId, 
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            (errorMessage) => { /* ignore */ }
        ).then(() => {
            isCameraOn = true;
            cameraIcon.textContent = 'videocam_off';
            cameraBtnText.textContent = 'Stop Camera';
        }).catch(err => console.error("Scanner failed", err));
    }
    
    function stopCamera() {
        if (isCameraOn) {
            html5QrCode.stop().then(() => {
                isCameraOn = false;
                cameraIcon.textContent = 'videocam';
                cameraBtnText.textContent = 'Start Camera';
            }).catch(err => console.error("Failed to stop scanner", err));
        }
    }
    
    function toggleCamera() {
        if (isCameraOn) {
            stopCamera();
        } else {
            startCamera();
        }
    }
    
    btnToggleCamera.addEventListener('click', toggleCamera);

    // Handle Enter key in input
    sccIdInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchStudentData(sccIdInput.value.trim());
        }
    });
    
    btnFetchStudent.addEventListener('click', () => {
        fetchStudentData(sccIdInput.value.trim());
    });
    
    btnCancel.addEventListener('click', function() {
        resetUI();
        isScanningPaused = false;
        waitingCard.style.display = 'block';
    });
    
    function resetUI() {
        paymentSection.style.display = 'none';
        errorDisplay.style.display = 'none';
        monthSelectionGroup.style.display = 'none';
        feeCalculationBox.style.display = 'none';
        classSelect.innerHTML = '<option value="">-- Select Class --</option>';
        selectedMonths.clear();
        renderMonthsList();
        btnSubmitPayment.disabled = true;
        currentClasses = [];
        sccIdInput.value = '';
    }
    
    function fetchStudentData(sccId) {
        if (!sccId) {
            isScanningPaused = false;
            return;
        }
        
        resetUI();
        waitingCard.style.display = 'none';
        loadingIndicator.style.display = 'block';
        
        fetch('<?= BASE_URL ?>/admin/finance/fetch-student', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ scc_id: sccId })
        })
        .then(response => response.json())
        .then(data => {
            loadingIndicator.style.display = 'none';
            if (data.success) {
                // Populate student details
                studentName.textContent = data.student.name;
                studentSccId.textContent = data.student.scc_id;
                hiddenStudentId.value = data.student.student_id;
                
                // Populate classes
                currentClasses = data.classes;
                if (currentClasses.length === 0) {
                    errorDisplay.textContent = "Student is not enrolled in any active classes.";
                    errorDisplay.style.display = 'block';
                    setTimeout(() => { isScanningPaused = false; }, 2000);
                    return;
                }
                
                currentClasses.forEach(cls => {
                    const opt = document.createElement('option');
                    opt.value = cls.class_id;
                    opt.textContent = cls.name + ' - ' + cls.subject_name;
                    opt.dataset.fee = cls.monthly_fee;
                    classSelect.appendChild(opt);
                });
                
                paymentSection.style.display = 'block';
            } else {
                errorDisplay.textContent = data.message || "Failed to fetch student details.";
                errorDisplay.style.display = 'block';
                waitingCard.style.display = 'block';
                setTimeout(() => { isScanningPaused = false; }, 2000);
            }
        })
        .catch(err => {
            console.error(err);
            loadingIndicator.style.display = 'none';
            errorDisplay.textContent = "A network error occurred.";
            errorDisplay.style.display = 'block';
            waitingCard.style.display = 'block';
            setTimeout(() => { isScanningPaused = false; }, 2000);
        });
    }
    
    // Handle class selection
    classSelect.addEventListener('change', function() {
        if (this.value) {
            monthSelectionGroup.style.display = 'block';
            calculateTotal();
        } else {
            monthSelectionGroup.style.display = 'none';
            feeCalculationBox.style.display = 'none';
            btnSubmitPayment.disabled = true;
        }
    });
    
    // Handle adding month
    btnAddMonth.addEventListener('click', function() {
        const val = singleMonthSelect.value;
        const text = singleMonthSelect.options[singleMonthSelect.selectedIndex].text;
        
        if (!selectedMonths.has(val)) {
            selectedMonths.add(val);
            renderMonthsList();
            calculateTotal();
        }
    });
    
    // Handle removing month
    selectedMonthsList.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-month')) {
            const btn = e.target.closest('.btn-remove-month');
            const val = btn.dataset.val;
            selectedMonths.delete(val);
            renderMonthsList();
            calculateTotal();
        }
    });
    
    function renderMonthsList() {
        hiddenInputsContainer.innerHTML = '';
        const items = Array.from(selectedMonths).sort();
        
        if (items.length === 0) {
            selectedMonthsList.innerHTML = '<li id="emptyMonthMsg" style="padding:10px; color:var(--muted); font-size:0.9rem; text-align:center;">No months added yet.</li>';
            return;
        }
        
        selectedMonthsList.innerHTML = '';
        items.forEach(val => {
            // Find text mapping
            const opt = Array.from(singleMonthSelect.options).find(o => o.value === val);
            const text = opt ? opt.text : val;
            
            // Create list item
            const li = document.createElement('li');
            li.className = 'month-list-item';
            li.innerHTML = `
                <span>${text}</span>
                <button type="button" class="btn-remove-month" data-val="${val}">
                    <span class="material-icons" style="font-size:18px;">close</span>
                </button>
            `;
            selectedMonthsList.appendChild(li);
            
            // Create hidden input
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'months[]';
            hidden.value = val;
            hiddenInputsContainer.appendChild(hidden);
        });
    }
    
    function calculateTotal() {
        const selectedClassId = classSelect.value;
        if (!selectedClassId) return;
        
        const option = classSelect.options[classSelect.selectedIndex];
        const monthlyFee = parseFloat(option.dataset.fee || 0);
        const selectedMonthsCount = selectedMonths.size;
        
        if (selectedMonthsCount > 0) {
            const total = monthlyFee * selectedMonthsCount;
            
            displayMonthlyFee.textContent = monthlyFee.toFixed(2);
            displayMonthCount.textContent = selectedMonthsCount;
            displayTotal.textContent = total.toFixed(2);
            hiddenTotalAmount.value = total.toFixed(2);
            
            feeCalculationBox.style.display = 'block';
            btnSubmitPayment.disabled = false;
        } else {
            feeCalculationBox.style.display = 'none';
            btnSubmitPayment.disabled = true;
        }
    }
});
</script>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
