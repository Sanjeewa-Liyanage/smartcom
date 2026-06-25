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
                    <tr>
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
<?php require VIEW_PATH . '/partials/footer.php'; ?>
