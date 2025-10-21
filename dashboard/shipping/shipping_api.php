<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['user_type']) || $_SESSION['user']['user_type'] !== 'partner' || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Shipping Company') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$pdo = pms_pdo();
$agent_id = $_SESSION['user_id'];
// Assuming company_id is stored in the session after login.
// This needs to be set correctly during the login process.
$company_id = $_SESSION['user']['id'] ?? 0; // The partner's ID is the company ID in this context


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    try {
        if ($action === 'get_berths') {
            $stmt = $pdo->query("SELECT id, berth_name FROM berths WHERE status = 'available' ORDER BY berth_name");
            $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($action === 'get_requests_and_invoices') {
            // Fetch shipping requests
            $req_stmt = $pdo->prepare(
                "SELECT id, ship_name, status, DATE_FORMAT(created_at, '%Y-%m-%d') AS request_date FROM shipping_requests WHERE company_id = ? ORDER BY created_at DESC"
            );
            $req_stmt->execute([$company_id]);
            $requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Fetch invoices
            $inv_stmt = $pdo->prepare(
                "SELECT i.invoice_number, i.amount, i.status, i.file_path 
                 FROM invoices i
                 JOIN shipping_requests sr ON i.request_id = sr.id
                 WHERE sr.company_id = ? ORDER BY i.created_at DESC"
            );
            $inv_stmt->execute([$company_id]);
            $invoices = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'success' => true, 
                'data' => [
                    'requests' => $requests,
                    'invoices' => $invoices
                ]
            ];
        } else {
            http_response_code(400);
            $response['message'] = 'Invalid GET action.';
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response['message'] = 'Database error: ' . $e->getMessage();
    }

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'submit_ship_request') {
        try {
            $stmt = $pdo->prepare(
                'INSERT INTO shipping_requests (agent_id, company_id, ship_name, cargo_type, requested_berth_id, estimated_arrival_time) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $agent_id,
                $company_id,
                $_POST['ship_name'],
                $_POST['cargo_type'],
                $_POST['requested_berth'],
                $_POST['eta']
            ]);
            $response = ['success' => true, 'message' => 'Ship arrival request submitted successfully!'];
        } catch (Exception $e) {
            http_response_code(500);
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        http_response_code(400);
        $response['message'] = 'Invalid POST action.';
    }
}

echo json_encode($response);
