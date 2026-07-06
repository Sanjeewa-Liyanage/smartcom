<?php
/**
 * @var string|null $title
 * @var array $payments
 */
require VIEW_PATH . '/partials/header.php';
?>
<div class="page-header">
    <h2>Payment Records</h2>
    <p>View all collected class fees.</p>
</div>

<div class="custom-card">
    <div class="custom-card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <span>Fee Collection History</span>
        <a href="<?= BASE_URL ?>/admin/finance/collect" class="btn btn-primary btn-sm">
            <span class="material-icons" style="font-size:18px;">add</span> Collect Fee
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>SCC ID</th>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Month Paid</th>
                    <th>Amount (Rs.)</th>
                    <th>Collected By</th>
                    <th>Ref</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding: 20px; color:var(--muted);">No payment records found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($payments as $payment): ?>
                        <?php
                            $parts = explode('-', $payment['month']);
                            $monthDisplay = $payment['month'];
                            if (count($parts) === 2) {
                                $monthNum = (int)$parts[1];
                                $monthName = date('F', mktime(0, 0, 0, $monthNum, 10));
                                $monthDisplay = $monthName . ' ' . $parts[0];
                            }
                        ?>
                        <tr style="cursor:pointer;" class="payment-row" 
                            data-date="<?= date('Y-m-d h:i A', strtotime($payment['payment_date'])) ?>"
                            data-scc="<?= htmlspecialchars($payment['scc_id'] ?? '-') ?>"
                            data-student="<?= htmlspecialchars($payment['student_name']) ?>"
                            data-class="<?= htmlspecialchars($payment['class_name']) ?>"
                            data-subject="<?= htmlspecialchars($payment['subject_name']) ?>"
                            data-month="<?= htmlspecialchars($monthDisplay) ?>"
                            data-amount="<?= number_format($payment['amount'], 2) ?>"
                            data-collector="<?= htmlspecialchars($payment['collected_by_name']) ?>"
                            data-ref="<?= htmlspecialchars($payment['transaction_ref']) ?>"
                        >
                            <td><?= date('Y-m-d h:i A', strtotime($payment['payment_date'])) ?></td>
                            <td><span class="badge" style="background:var(--bg-light); color:var(--text); border:1px solid var(--border);"><?= htmlspecialchars($payment['scc_id'] ?? '-') ?></span></td>
                            <td><?= htmlspecialchars($payment['student_name']) ?></td>
                            <td>
                                <div style="font-weight:600;"><?= htmlspecialchars($payment['class_name']) ?></div>
                                <div style="font-size:0.8rem; color:var(--muted);"><?= htmlspecialchars($payment['subject_name']) ?></div>
                            </td>
                            <td>
                                <?= htmlspecialchars($monthDisplay) ?>
                            </td>
                            <td style="font-weight:600; color:var(--success);"><?= number_format($payment['amount'], 2) ?></td>
                            <td style="font-size:0.9rem; color:var(--muted);"><?= htmlspecialchars($payment['collected_by_name']) ?></td>
                            <td style="font-size:0.85rem; font-family:monospace;"><?= htmlspecialchars(substr($payment['transaction_ref'], 4, 8) . '...') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Detail Modal -->
<div id="paymentModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="custom-card" style="width: 100%; max-width: 500px; margin: 20px; animation: modalIn 0.3s ease-out;">
        <div class="custom-card-header" style="display:flex; justify-content:space-between; align-items:center; background:var(--surface);">
            <h3 style="margin:0; font-size:1.2rem;">Payment Details</h3>
            <button type="button" id="closeModalBtn" style="background:none; border:none; cursor:pointer; color:var(--muted);">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="custom-card-body" style="padding: 24px;">
            <div style="text-align:center; margin-bottom:24px;">
                <span class="material-icons" style="font-size:3rem; color:var(--success);">check_circle</span>
                <h2 style="margin:8px 0 0; color:var(--success);">Rs. <span id="modalAmount"></span></h2>
                <p style="color:var(--muted); font-size:0.9rem; margin-top:4px;">Successfully Paid</p>
            </div>
            
            <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted); width:40%;">Transaction Ref</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600; font-family:monospace;" id="modalRef"></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Date & Time</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalDate"></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Student</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;">
                        <div id="modalStudent"></div>
                        <div style="font-size:0.8rem; color:var(--muted);" id="modalScc"></div>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Class</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;">
                        <div id="modalClass"></div>
                        <div style="font-size:0.8rem; color:var(--muted);" id="modalSubject"></div>
                    </td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Paid For Month</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalMonth"></td>
                </tr>
                <tr>
                    <td style="padding:12px 0; color:var(--muted);">Collected By</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalCollector"></td>
                </tr>
            </table>
        </div>
        <div style="padding:16px 24px; border-top:1px solid var(--border); text-align:right; background:var(--bg-light);">
            <button type="button" class="btn btn-secondary" id="closeModalBtn2">Close</button>
            <button type="button" class="btn btn-primary" onclick="window.print()" style="display:inline-flex; align-items:center; gap:4px;">
                <span class="material-icons" style="font-size:18px;">print</span> Print Receipt
            </button>
        </div>
    </div>
</div>

<style>
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.payment-row:hover {
    background: var(--bg-light);
}

@media print {
    body * {
        visibility: hidden;
    }
    #paymentModal, #paymentModal * {
        visibility: visible;
    }
    #paymentModal {
        position: absolute;
        left: 0;
        top: 0;
        background: transparent;
        display: block !important;
    }
    #paymentModal .custom-card {
        box-shadow: none;
        border: none;
        margin: 0;
        animation: none;
    }
    #closeModalBtn, #closeModalBtn2, .btn-primary {
        display: none !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('paymentModal');
    const closeBtn1 = document.getElementById('closeModalBtn');
    const closeBtn2 = document.getElementById('closeModalBtn2');
    
    // UI Elements
    const mAmount = document.getElementById('modalAmount');
    const mRef = document.getElementById('modalRef');
    const mDate = document.getElementById('modalDate');
    const mStudent = document.getElementById('modalStudent');
    const mScc = document.getElementById('modalScc');
    const mClass = document.getElementById('modalClass');
    const mSubject = document.getElementById('modalSubject');
    const mMonth = document.getElementById('modalMonth');
    const mCollector = document.getElementById('modalCollector');

    document.querySelectorAll('.payment-row').forEach(row => {
        row.addEventListener('click', function() {
            mAmount.textContent = this.dataset.amount;
            mRef.textContent = this.dataset.ref;
            mDate.textContent = this.dataset.date;
            mStudent.textContent = this.dataset.student;
            mScc.textContent = this.dataset.scc;
            mClass.textContent = this.dataset.class;
            mSubject.textContent = this.dataset.subject;
            mMonth.textContent = this.dataset.month;
            mCollector.textContent = this.dataset.collector;
            
            modal.style.display = 'flex';
        });
    });
    
    function closeModal() {
        modal.style.display = 'none';
    }
    
    closeBtn1.addEventListener('click', closeModal);
    closeBtn2.addEventListener('click', closeModal);
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
});
</script>

<?php require VIEW_PATH . '/partials/footer.php'; ?>
