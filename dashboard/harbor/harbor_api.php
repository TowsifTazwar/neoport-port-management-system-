<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Harbor Master') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../config/db.php';
$pdo = pms_pdo();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Invalid action.'];

    if ($action === 'approve' || $action === 'reject') {
        $request_id = intval($_POST['request_id'] ?? 0);
        $rejection_reason = $_POST['rejection_reason'] ?? null;
        
        if ($request_id > 0) {
            $new_status = ($action === 'approve') ? 'approved' : 'rejected';
            try {
                // First, get the harbor_master's specific ID from the harbor_masters table
                $hm_stmt = $pdo->prepare("SELECT id FROM harbor_masters WHERE user_id = ?");
                $hm_stmt->execute([$_SESSION['user_id']]);
                $harbor_master = $hm_stmt->fetch(PDO::FETCH_ASSOC);

                if (!$harbor_master) {
                    throw new Exception("Could not find the corresponding Harbor Master record for the logged-in user.");
                }
                $harbor_master_id = $harbor_master['id'];

                $stmt = $pdo->prepare("UPDATE shipping_requests SET status = ?, harbor_master_id = ?, rejection_reason = ? WHERE id = ?");
                $stmt->execute([$new_status, $harbor_master_id, $rejection_reason, $request_id]);

                if ($stmt->rowCount() > 0) {
                    $response = ['success' => true, 'message' => "Request has been {$new_status}."];
                } else {
                    $response['message'] = 'Request not found or status unchanged.';
                }
            } catch (Exception $e) {
                http_response_code(500);
                $response['message'] = "Error updating request: " . $e->getMessage();
            }
        } else {
            $response['message'] = 'Invalid request ID.';
        }
    } elseif ($action === 'allocate_berth') {
        $shipping_request_id = intval($_POST['shipping_request_id'] ?? 0);
        $berth_name = trim($_POST['berth_id'] ?? '');
        $docking_time = trim($_POST['docking_time'] ?? '');

        if ($shipping_request_id > 0 && !empty($berth_name) && !empty($docking_time)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM berths WHERE berth_name = ?");
                $stmt->execute([$berth_name]);
                $berth = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$berth) {
                    throw new Exception("Berth not found.");
                }
                $berth_id = $berth['id'];

                // Check if the berth is already allocated
                $stmt = $pdo->prepare("SELECT id FROM berth_allocations WHERE berth_id = ? AND status = 'docked'");
                $stmt->execute([$berth_id]);
                $existing_allocation = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing_allocation) {
                    throw new Exception("Berth is already allocated.");
                }

                $stmt = $pdo->prepare("INSERT INTO berth_allocations (shipping_request_id, berth_id, docking_time) VALUES (?, ?, ?)");
                $stmt->execute([$shipping_request_id, $berth_id, $docking_time]);
                $response = ['success' => true, 'message' => "Berth allocated successfully."];
            } catch (Exception $e) {
                http_response_code(500);
                $response['message'] = "Database error: " . $e->getMessage();
            }
        } else {
            $response['message'] = 'Missing required fields for berth allocation.';
        }
    }
    
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'get_berth_usage') {
        try {
            $stmt = $pdo->query("
                SELECT b.berth_name, sr.ship_name, ba.status
                FROM berth_allocations ba
                JOIN berths b ON ba.berth_id = b.id
                JOIN shipping_requests sr ON ba.shipping_request_id = sr.id
                WHERE ba.status = 'docked'
                ORDER BY b.berth_name
            ");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
exit();
