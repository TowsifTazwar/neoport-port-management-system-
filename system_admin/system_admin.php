<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

$pdo = pms_pdo();

// --- Helper Functions ---
function log_activity($message) {
    if (!isset($_SESSION['activity_log'])) $_SESSION['activity_log'] = [];
    $timestamp = date('Y-m-d H:i:s');
    array_unshift($_SESSION['activity_log'], "[$timestamp] $message");
    if (count($_SESSION['activity_log']) > 20) array_pop($_SESSION['activity_log']);
}

// --- Action Handling ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $type = $_POST['type'] ?? ''; // To distinguish between 'authority' and 'staff'

    if ($action === 'clear_activity') {
        unset($_SESSION['activity_log']);
        $_SESSION['flash_message'] = "Recent activity cleared.";
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    if ($id > 0) {
        if ($action === 'approve') {
            if ($type === 'authority') {
                // Existing logic for authorities
                $stmt = $pdo->prepare("SELECT * FROM authority_requests WHERE id=? AND status='pending'");
                $stmt->execute([$id]);
                $request = $stmt->fetch();
                if ($request) {
                    $pdo->beginTransaction();
                    try {
                        $insertStmt = $pdo->prepare("INSERT INTO authorities (full_name,email,phone,organization,nid,role,password_hash,approved_by) VALUES (?,?,?,?,?,?,?,?)");
                        $insertStmt->execute([$request['full_name'],$request['email'],$request['phone'],$request['organization'],$request['nid'],$request['role'],$request['password_hash'],1]);
                        $updateStmt = $pdo->prepare("UPDATE authority_requests SET status='approved' WHERE id=?");
                        $updateStmt->execute([$id]);
                        $pdo->commit();
                        $_SESSION['flash_message'] = "Authority user {$request['full_name']} approved.";
                        log_activity("Approved authority: {$request['full_name']} ({$request['email']})");
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $_SESSION['flash_error'] = "Error approving authority: " . $e->getMessage();
                    }
                }
            } elseif ($type === 'staff') {
                // New logic for staff
                $stmt = $pdo->prepare("UPDATE staff_users SET status='approved', approved_by=? WHERE id=? AND status='pending'");
                $stmt->execute([1, $id]); // Assuming admin ID 1
                if ($stmt->rowCount() > 0) {
                    $_SESSION['flash_message'] = "Staff user approved.";
                    log_activity("Approved staff user ID: $id");
                } else {
                    $_SESSION['flash_error'] = "Could not approve staff user.";
                }
            }
        } elseif ($action === 'reject') {
            if ($type === 'authority') {
                $stmt = $pdo->prepare("UPDATE authority_requests SET status='rejected' WHERE id=?");
                $stmt->execute([$id]);
                $_SESSION['flash_message'] = "Authority request rejected.";
                log_activity("Rejected authority request ID: $id");
            } elseif ($type === 'staff') {
                $stmt = $pdo->prepare("UPDATE staff_users SET status='rejected' WHERE id=?");
                $stmt->execute([$id]);
                $_SESSION['flash_message'] = "Staff request rejected.";
                log_activity("Rejected staff request ID: $id");
            }
        } elseif ($action === 'suspend') {
            if ($type === 'authority') {
                $stmt = $pdo->prepare("UPDATE authorities SET status='suspended' WHERE id=?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['flash_message'] = "Authority user suspended.";
                    log_activity("Suspended authority user ID: $id");
                } else {
                    $_SESSION['flash_error'] = "Could not suspend authority user.";
                }
            } elseif ($type === 'staff') {
                $stmt = $pdo->prepare("UPDATE staff_users SET status='suspended' WHERE id=?");
                $stmt->execute([$id]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['flash_message'] = "Staff user suspended.";
                    log_activity("Suspended staff user ID: $id");
                } else {
                    $_SESSION['flash_error'] = "Could not suspend staff user.";
                }
            } elseif ($type === 'partner') {
                $stmt = $pdo->prepare("INSERT INTO suspended_users (user_id, user_type, reason, suspended_by) VALUES (?, 'partner', 'Suspended by admin', ?)");
                $stmt->execute([$id, 1]);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['flash_message'] = "Partner user suspended.";
                    log_activity("Suspended partner user ID: $id");
                } else {
                    $_SESSION['flash_error'] = "Could not suspend partner user.";
                }
            }
        } elseif ($action === 'update_role') {
            $new_role = $_POST['role'] ?? '';
            if (!empty($new_role)) {
                $table = '';
                if ($type === 'authority') $table = 'authorities';
                elseif ($type === 'staff') $table = 'staff_users';

                if ($table) {
                    $stmt = $pdo->prepare("UPDATE $table SET role=? WHERE id=?");
                    $stmt->execute([$new_role, $id]);
                    $_SESSION['flash_message'] = "User role updated.";
                    log_activity("Updated role for $type user ID: $id to $new_role");
                }
            }
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Role Stats ---
$authority_roles = ['Port Director'];
$staff_roles = [
    "Harbor Master", "Customs & Compliance Officer", "Cargo & Warehouse Manager",
    "Logistics & Transport Coordinator"
];
$roles = array_merge($authority_roles, $staff_roles);

// Role stats will be loaded via JavaScript
$role_stats = [];

// --- Fetch Requests ---
// Authorities
$pending_authority_requests = $pdo->query("SELECT id, full_name, email, role, 'authority' as type, 'pending' as status FROM authority_requests WHERE status='pending' ORDER BY created_at DESC")->fetchAll();
$approved_authorities = $pdo->query("SELECT *, 'authority' as type FROM authorities WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll();
$rejected_authorities = $pdo->query("SELECT *, 'rejected' as status, 'authority' as type FROM authority_requests WHERE status = 'rejected' ORDER BY created_at DESC")->fetchAll();
$suspended_authorities = $pdo->query("SELECT *, 'authority' as type FROM authorities WHERE status = 'suspended' ORDER BY created_at DESC")->fetchAll();

// Staff
$pending_staff_requests = $pdo->query("SELECT id, full_name, email, role, 'staff' as type, 'pending' as status FROM staff_users WHERE status='pending' ORDER BY created_at DESC")->fetchAll();
$approved_staff = $pdo->query("SELECT *, 'staff' as type FROM staff_users WHERE status='approved' ORDER BY created_at DESC")->fetchAll();
$rejected_staff = $pdo->query("SELECT *, 'staff' as type FROM staff_users WHERE status='rejected' ORDER BY created_at DESC")->fetchAll();
$suspended_staff = $pdo->query("SELECT *, 'staff' as type FROM staff_users WHERE status='suspended' ORDER BY created_at DESC")->fetchAll();

// Partners
$pending_partner_requests = $pdo->query("
    SELECT pr.id, pr.company_name as full_name, pr.contact_email as email, r.role_name as role, 'partner' as type, pr.status
    FROM partner_requests pr 
    JOIN partner_roles r ON pr.role_id = r.id 
    WHERE pr.status='pending' 
    ORDER BY pr.created_at DESC
")->fetchAll();
$approved_partners = $pdo->query("SELECT p.id, p.company_name as full_name, p.contact_email as email, r.role_name as role, 'partner' as type, 'approved' as status, p.created_at FROM partners p JOIN partner_roles r ON p.role_id = r.id ORDER BY p.created_at DESC")->fetchAll();
$suspended_partners = $pdo->query("SELECT p.id, p.company_name as full_name, p.contact_email as email, r.role_name as role, 'partner' as type, 'suspended' as status, p.created_at FROM partners p JOIN partner_roles r ON p.role_id = r.id JOIN suspended_users s ON p.id = s.user_id WHERE s.user_type = 'partner' ORDER BY p.created_at DESC")->fetchAll();


// Combine all users for the "All Users" table
$all_users = array_merge(
    $approved_authorities, $pending_authority_requests, $rejected_authorities, $suspended_authorities,
    $approved_staff, $pending_staff_requests, $rejected_staff, $suspended_staff,
    $approved_partners, $suspended_partners, $pending_partner_requests
);


$flash_message = $_SESSION['flash_message'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>System Admin Dashboard</title>
<link rel="stylesheet" href="system_admin.css">
</head>
<body>
<header class="topbar">
    <div class="title">
        <span class="logo">neoport</span>
        <span class="divider">/</span>
        <span class="role">System Admin</span>
    </div>
    <div class="user-info">
        <a href="/pms/logout.php" class="btn ghost">Logout</a>
    </div>
</header>

<main class="page">
    <?php if ($flash_message): ?>
        <div class="alert ok"><?= htmlspecialchars($flash_message) ?></div>
    <?php endif; ?>
    <?php if ($flash_error): ?>
        <div class="alert error"><?= htmlspecialchars($flash_error) ?></div>
    <?php endif; ?>

    <!-- Dashboard Stats -->
    <section class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Registered</h3>
            <p id="total-registered">0</p>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <p id="total-pending">0</p>
        </div>
        <div class="stat-card">
            <h3>Approved</h3>
            <p id="total-approved">0</p>
        </div>
        <div class="stat-card">
            <h3>Rejected</h3>
            <p id="total-rejected">0</p>
        </div>
        <div class="stat-card">
            <h3>Suspended</h3>
            <p id="total-suspended">0</p>
        </div>
    </section>

    <!-- Role-wise Registration Status -->
    <section class="card">
        <div class="card-head">
            <h2>Role-wise Registration Status</h2>
        </div>
        <div class="role-grid" id="role-stats-grid">
            <!-- Role stats will be loaded here by JavaScript -->
        </div>
    </section>

    <!-- Pending Requests -->
    <section class="card">
        <div class="card-head">
            <h2>Pending Requests</h2>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $pending_requests = array_merge($pending_authority_requests, $pending_staff_requests, $pending_partner_requests);
                if(empty($pending_requests)): 
                ?>
                    <tr><td colspan="5">No pending requests.</td></tr>
                <?php else: ?>
                    <?php foreach($pending_requests as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td><?= htmlspecialchars($u['full_name']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td class="actions">
                                <form method="post" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="type" value="<?= $u['type'] ?>">
                                    <button class="btn approve" name="action" value="<?= $u['type'] === 'partner' ? 'approve_partner' : 'approve' ?>">Approve</button>
                                </form>
                                <form method="post" style="display: inline-block;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="type" value="<?= $u['type'] ?>">
                                    <button class="btn reject" name="action" value="<?= $u['type'] === 'partner' ? 'reject_partner' : 'reject' ?>">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- All Users -->
    <section class="card">
        <div class="card-head">
            <h2>All Users</h2>
        </div>
        <div class="filters">
            <input type="text" id="searchAll" class="search-bar" placeholder="Search by name/email...">
            <select id="roleFilterAll">
                <option value="">All Roles</option>
                <optgroup label="Authority Roles">
                    <?php foreach($authority_roles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <optgroup label="Staff Roles">
                    <?php foreach($staff_roles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                    <?php endforeach; ?>
                </optgroup>
            </select>
            <select id="statusFilterAll">
                <option value="">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
        <table id="allTable" class="table">
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Manage</th></tr></thead>
            <tbody>
            <?php if(empty($all_users)): ?>
                <tr><td colspan="6">No users found.</td></tr>
            <?php else: ?>
                <?php foreach($all_users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><span class="badge <?= htmlspecialchars($u['status']) ?>"><?= ucfirst(htmlspecialchars($u['status'])) ?></span></td>
                        <td class="actions">
                            <?php if ($u['type'] !== 'partner' && $u['status'] === 'approved'): ?>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="type" value="<?= $u['type'] ?>">
                                    <select name="role" class="role-select">
                                        <?php $role_list = ($u['type'] === 'authority') ? $authority_roles : $staff_roles; ?>
                                        <?php foreach($role_list as $role): ?>
                                        <option value="<?= htmlspecialchars($role) ?>" <?= ($u['role']==$role)?'selected':'' ?>><?= htmlspecialchars($role) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn save" name="action" value="update_role" data-confirm="Update role?">Save</button>
                                </form>
                                <form method="post">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <input type="hidden" name="type" value="<?= $u['type'] ?>">
                                    <button class="btn suspend" name="action" value="suspend" data-confirm="Suspend this user?">Suspend</button>
                                </form>
                            <?php else: ?>
                                <span>-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Recent Activity -->
    <section class="card">
        <div class="activity-header">
            <h2>Recent Activity</h2>
            <form method="post">
                <button name="action" value="clear_activity" class="clear-activity" data-confirm="Clear all recent activity?">Clear All</button>
            </form>
        </div>
        <ul>
            <?php if(empty($_SESSION['activity_log'])): ?>
                <li>No recent activity.</li>
            <?php else: ?>
                <?php foreach($_SESSION['activity_log'] as $log): ?>
                    <li><?= htmlspecialchars($log) ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>

</main>

<footer>
    <p>Port Management System © <?= date('Y') ?></p>
</footer>
<script src="system_admin.js"></script>
</body>
</html>
