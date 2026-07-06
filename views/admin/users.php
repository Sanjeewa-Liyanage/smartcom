<?php

/**
 * @var string $csrf
 * @var array $users
 * @var array $user
 */
$title = 'Manage Users';
require VIEW_PATH . '/partials/header.php';
?>
        <div class="page-header">
            <h2>All Users</h2>
            <p>View and manage all registered users across roles.</p>
        </div>

        <!-- Filters -->
        <div class="filter-bar">
            <a href="<?= BASE_URL ?>/admin/users" class="filter-tab <?= ($roleFilter ?? '') === '' ? 'active' : '' ?>" id="filter-all">All</a>
            <a href="<?= BASE_URL ?>/admin/users?role=student" class="filter-tab <?= ($roleFilter ?? '') === 'student' ? 'active' : '' ?>" id="filter-students"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">person</span> Students</a>
            <a href="<?= BASE_URL ?>/admin/users?role=tutor"   class="filter-tab <?= ($roleFilter ?? '') === 'tutor'   ? 'active' : '' ?>" id="filter-tutors"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">school</span> Tutors</a>
            <a href="<?= BASE_URL ?>/admin/users?role=parent"  class="filter-tab <?= ($roleFilter ?? '') === 'parent'  ? 'active' : '' ?>" id="filter-parents"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">family_restroom</span> Parents</a>
            <a href="<?= BASE_URL ?>/admin/users?role=admin"   class="filter-tab <?= ($roleFilter ?? '') === 'admin'   ? 'active' : '' ?>" id="filter-admins"><span class="material-icons" style="font-size:16px;vertical-align:text-bottom">admin_panel_settings</span> Admins</a>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div style="display:flex; align-items:center; gap: 12px;">
                    <div class="th-title">Users</div>
                    <div class="th-count"><?= count($users) ?> record<?= count($users) !== 1 ? 's' : '' ?></div>
                </div>
                <?php if (($roleFilter ?? '') === 'tutor'): ?>
                    <a href="<?= BASE_URL ?>/admin/tutors/create" class="btn-sm btn-success" style="padding: 8px 14px; border-radius: 8px; font-size: 0.8rem;"><span class="material-icons" style="font-size:16px">person_add</span> Create Tutor</a>
                <?php endif; ?>
            </div>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <div class="material-icons">group</div>
                    <p>No users found for the selected filter.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr <?= $u['role'] === 'student' ? 'style="cursor:pointer;" class="student-row"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-scc="'.htmlspecialchars($u['scc_id'] ?? '-').'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-name="'.htmlspecialchars($u['name']).'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-email="'.htmlspecialchars($u['email']).'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-status="'.htmlspecialchars($u['status']).'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-parent="'.htmlspecialchars($u['parent_name'] ?? '-').'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-parentemail="'.htmlspecialchars($u['parent_email'] ?? '-').'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-created="'.date('d M Y', strtotime($u['created_at'])).'"' : '' ?>
                        <?= $u['role'] === 'student' ? 'data-qr="'.htmlspecialchars($u['qr_code'] ?? '').'"' : '' ?>
                    >
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar ua-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="user-name"><?= htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="user-email"><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= match($u['role']) {
                                    'student' => '<span class="material-icons" style="font-size:12px">person</span> Student',
                                    'tutor'   => '<span class="material-icons" style="font-size:12px">school</span> Tutor',
                                    'parent'  => '<span class="material-icons" style="font-size:12px">family_restroom</span> Parent',
                                    'admin'   => '<span class="material-icons" style="font-size:12px">admin_panel_settings</span> Admin',
                                    default   => ucfirst($u['role'])
                                } ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= htmlspecialchars($u['status'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= match($u['status']) {
                                    'active'    => '<span class="material-icons" style="font-size:12px">check_circle</span> Active',
                                    'pending'   => '<span class="material-icons" style="font-size:12px">hourglass_empty</span> Pending',
                                    'rejected'  => '<span class="material-icons" style="font-size:12px">cancel</span> Rejected',
                                    'suspended' => '<span class="material-icons" style="font-size:12px">block</span> Suspended',
                                    default     => ucfirst($u['status'])
                                } ?>
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:0.8rem;">
                            <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td>
                            <?php if ($u['role'] !== 'admin'): ?>
                            <div class="actions">
                                <?php if ($u['status'] === 'active'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/toggle-user" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <input type="hidden" name="action" value="suspend">
                                        <button type="submit" class="btn-sm btn-warning" title="Suspend account"><span class="material-icons" style="font-size:14px">block</span> Suspend</button>
                                    </form>
                                <?php elseif ($u['status'] === 'suspended'): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/toggle-user" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <input type="hidden" name="action" value="activate">
                                        <button type="submit" class="btn-sm btn-success" title="Re-activate account"><span class="material-icons" style="font-size:14px">check_circle</span> Activate</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($u['role'] === 'student' && empty($u['parent_id'])): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/admin/enable-parent-control" style="display:inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                        <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                        <button type="submit" class="btn-sm btn-info" title="Enable Parent Control"><span class="material-icons" style="font-size:14px">family_restroom</span> Enable Parent</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                                <span style="color:var(--muted);font-size:0.75rem;">Protected</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        </div>

<!-- Student Detail Modal -->
<div id="studentModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="custom-card" style="width: 100%; max-width: 500px; margin: 20px; animation: modalIn 0.3s ease-out;">
        <div class="custom-card-header" style="display:flex; justify-content:space-between; align-items:center; background:var(--surface);">
            <h3 style="margin:0; font-size:1.2rem;">Student Details</h3>
            <button type="button" id="closeStudentModalBtn" style="background:none; border:none; cursor:pointer; color:var(--muted);">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="custom-card-body" style="padding: 24px;">
            <div style="text-align:center; margin-bottom:24px;">
                <div class="user-avatar ua-student" style="width:64px; height:64px; font-size:2rem; margin:0 auto 12px;" id="modalAvatar"></div>
                <h2 style="margin:0;" id="modalName"></h2>
                <p style="color:var(--muted); font-size:0.9rem; margin-top:4px;" id="modalEmail"></p>
                <div style="margin-top:8px;" id="modalStatusBadge"></div>
            </div>
            
            <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted); width:40%;">SCC ID</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalSccId"></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Registered On</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalCreated"></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Parent Name</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalParentName"></td>
                </tr>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px 0; color:var(--muted);">Parent Email</td>
                    <td style="padding:12px 0; text-align:right; font-weight:600;" id="modalParentEmail"></td>
                </tr>
            </table>

            <div style="text-align:center; margin-top:24px;">
                <h4 style="margin:0 0 12px; color:var(--muted);">QR Attendance Code</h4>
                <div id="modalQrCode" style="display:inline-block; padding:12px; border:1px solid var(--border); border-radius:8px; background:#fff;">
                    <!-- QR code image will be inserted here -->
                </div>
            </div>
        </div>
        <div style="padding:16px 24px; border-top:1px solid var(--border); text-align:right; background:var(--bg-light);">
            <button type="button" class="btn btn-secondary" id="closeStudentModalBtn2">Close</button>
        </div>
    </div>
</div>

<style>
@keyframes modalIn {
    from { opacity: 0; transform: translateY(-20px) scale(0.95); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
.student-row:hover td {
    background: var(--bg-light);
}
/* Prevent action buttons from triggering the row click */
.student-row .actions {
    position: relative;
    z-index: 2;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('studentModal');
    const closeBtn1 = document.getElementById('closeStudentModalBtn');
    const closeBtn2 = document.getElementById('closeStudentModalBtn2');
    
    // UI Elements
    const mAvatar = document.getElementById('modalAvatar');
    const mName = document.getElementById('modalName');
    const mEmail = document.getElementById('modalEmail');
    const mStatusBadge = document.getElementById('modalStatusBadge');
    const mSccId = document.getElementById('modalSccId');
    const mCreated = document.getElementById('modalCreated');
    const mParentName = document.getElementById('modalParentName');
    const mParentEmail = document.getElementById('modalParentEmail');
    const mQrCode = document.getElementById('modalQrCode');

    document.querySelectorAll('.student-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // Don't open if clicking on an action button
            if (e.target.closest('.actions')) return;

            const name = this.dataset.name;
            const status = this.dataset.status;
            
            mAvatar.textContent = name ? name.substring(0, 1).toUpperCase() : '';
            mName.textContent = name;
            mEmail.textContent = this.dataset.email;
            
            // Set status badge
            let statusHtml = '';
            if (status === 'active') statusHtml = '<span class="badge badge-active"><span class="material-icons" style="font-size:12px">check_circle</span> Active</span>';
            else if (status === 'pending') statusHtml = '<span class="badge badge-pending"><span class="material-icons" style="font-size:12px">hourglass_empty</span> Pending</span>';
            else if (status === 'suspended') statusHtml = '<span class="badge badge-suspended"><span class="material-icons" style="font-size:12px">block</span> Suspended</span>';
            else statusHtml = '<span class="badge">' + status + '</span>';
            mStatusBadge.innerHTML = statusHtml;

            mSccId.textContent = this.dataset.scc;
            mCreated.textContent = this.dataset.created;
            mParentName.textContent = this.dataset.parent;
            mParentEmail.textContent = this.dataset.parentemail;
            
            const qrToken = this.dataset.qr;
            if (qrToken) {
                mQrCode.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrToken)}" alt="QR Code" style="display:block;">`;
            } else {
                mQrCode.innerHTML = `<span style="color:var(--muted); font-size:0.85rem;">No QR code generated.</span>`;
            }
            
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
