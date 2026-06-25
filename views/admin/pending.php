<?php

/**
 * @var string $csrf
 * @var array $pendingUsers
 * @var array $user
 */
$title = 'Pending Approvals';
require VIEW_PATH . '/partials/header.php';
?>
        <div class="page-header">
            <h2>Pending Approvals</h2>
            <p>Review and approve or reject new registration requests.</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><span class="material-icons" style="font-size:18px">check_circle</span> <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($pendingUsers)): ?>
            <div class="pending-banner">
                <span class="material-icons" style="font-size:18px">hourglass_empty</span> <strong><?= count($pendingUsers) ?></strong> account<?= count($pendingUsers) !== 1 ? 's' : '' ?> awaiting review
            </div>
        <?php endif; ?>

        <div class="table-card">
            <?php if (empty($pendingUsers)): ?>
                <div class="empty-state">
                    <div class="material-icons">check_circle_outline</div>
                    <p>No pending approvals!</p>
                    <p class="sub">All registration requests have been processed.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Role Applied</th>
                        <th>Date Applied</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingUsers as $u): ?>
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
                                    default   => ucfirst($u['role'])
                                } ?>
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:0.8rem;">
                            <?= date('d M Y, H:i', strtotime($u['created_at'])) ?>
                        </td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="<?= BASE_URL ?>/admin/approve-user" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                     <button type="submit" class="btn-sm btn-approve" id="btn-approve-<?= $u['user_id'] ?>"><span class="material-icons" style="font-size:16px">check</span> Approve</button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>/admin/reject-user" style="display:inline">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                     <button type="submit" class="btn-sm btn-reject" id="btn-reject-<?= $u['user_id'] ?>" onclick="return confirm('Reject this application?')"><span class="material-icons" style="font-size:16px">close</span> Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
<?php require VIEW_PATH . '/partials/footer.php'; ?>
