<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

function get_dashboard_stats() {
    $pdo = pms_pdo();
    $stats = [
        'total_registered' => 0,
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'suspended' => 0,
        'roles' => []
    ];

    // Authority stats
    $auth_req_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM authority_requests GROUP BY status");
    while ($row = $auth_req_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($stats[$row['status']])) {
            $stats[$row['status']] += $row['count'];
        }
    }
    $auth_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM authorities GROUP BY status");
    while ($row = $auth_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($stats[$row['status']])) {
            $stats[$row['status']] += $row['count'];
        }
    }

    // Staff stats
    $staff_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM staff_users GROUP BY status");
    while ($row = $staff_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($stats[$row['status']])) {
            $stats[$row['status']] += $row['count'];
        }
    }

    // Partner stats
    $partner_req_stmt = $pdo->query("SELECT status, COUNT(*) as count FROM partner_requests GROUP BY status");
    while ($row = $partner_req_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($stats[$row['status']])) {
            $stats[$row['status']] += $row['count'];
        }
    }
    $partner_stmt = $pdo->query("SELECT COUNT(*) as count FROM partners");
    $stats['approved'] += $partner_stmt->fetchColumn();
    $suspended_partner_stmt = $pdo->query("SELECT COUNT(*) as count FROM suspended_users WHERE user_type = 'partner'");
    $stats['suspended'] += $suspended_partner_stmt->fetchColumn();

    $stats['total_registered'] = $stats['pending'] + $stats['approved'] + $stats['rejected'] + $stats['suspended'];

    // Role-wise stats
    $roles_stmt = $pdo->query("
        SELECT role, status, COUNT(*) as count FROM (
            SELECT role, status FROM authorities
            UNION ALL
            SELECT role, status FROM staff_users
            UNION ALL
            SELECT r.role_name as role, pr.status FROM partner_requests pr JOIN partner_roles r ON pr.role_id = r.id
            UNION ALL
            SELECT r.role_name as role, 'approved' as status FROM partners p JOIN partner_roles r ON p.role_id = r.id
        ) as all_users
        GROUP BY role, status
    ");

    while ($row = $roles_stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($stats['roles'][$row['role']])) {
            $stats['roles'][$row['role']] = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0, 'suspended' => 0];
        }
        $stats['roles'][$row['role']][$row['status']] = $row['count'];
        $stats['roles'][$row['role']]['total'] += $row['count'];
    }

    return ['success' => true, 'stats' => $stats];
}

$view = $_GET['view'] ?? '';

if ($view === 'dashboard_stats') {
    echo json_encode(get_dashboard_stats());
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid view.']);
}
