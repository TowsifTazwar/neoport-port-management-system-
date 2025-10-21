<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Finance & Billing Officer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$pdo = pms_pdo();
$finance_officer_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    try {
        if ($action === 'get_finance_data') {
            $stmt = $pdo->prepare("SELECT * FROM shipping_requests WHERE finance_officer_id = ?");
            $stmt->execute([$finance_officer_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $response = ['success' => true, 'data' => $data];
        } else {
            http_response_code(400);
            $response['message'] = 'Invalid GET action.';
        }
    } catch (Exception $e) {
        http_response_code(500);
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
