<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Cargo & Warehouse Manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$pdo = pms_pdo();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
    if ($method === 'GET') {
        switch ($action) {
            // Top table: all customs-approved requests (informational only)
            case 'get_cleared':
                $sql = "SELECT id, ship_name, status
                        FROM shipping_requests
                        WHERE customs_status = 'approved_by_customs'
                        ORDER BY id DESC";
                $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                break;

            // Form dropdown: only customs-approved AND not yet assigned
            case 'get_form_options':
                $sql = "SELECT sr.id, sr.ship_name
                        FROM shipping_requests sr
                        LEFT JOIN cargo_assignments ca
                          ON ca.shipping_request_id = sr.id
                        WHERE sr.customs_status = 'approved_by_customs'
                          AND ca.id IS NULL
                        ORDER BY sr.id DESC";
                $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                break;

            // Bottom table: current occupancy list (assignments)
            case 'get_occupancy':
                $sql = "SELECT ca.id,
                               ca.shipping_request_id,
                               sr.ship_name,
                               ca.container_name,
                               ca.storage_slot_name,
                               ca.status
                        FROM cargo_assignments ca
                        JOIN shipping_requests sr ON sr.id = ca.shipping_request_id
                        ORDER BY ca.id DESC";
                $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $data]);
                break;

            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
        exit;
    }

    if ($method === 'POST') {
        switch ($action) {
            // Create one assignment row
            case 'assign_storage':
                $shipping_request_id = (int)($_POST['shipping_request_id'] ?? 0);
                $container_name      = trim($_POST['container_name'] ?? '');
                $storage_slot_name   = trim($_POST['storage_slot_name'] ?? '');

                if ($shipping_request_id <= 0 || $container_name === '' || $storage_slot_name === '') {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
                    exit;
                }

                // Validate the request exists and is customs-approved
                $stmt = $pdo->prepare("SELECT id FROM shipping_requests
                                       WHERE id = ? AND customs_status = 'approved_by_customs'");
                $stmt->execute([$shipping_request_id]);
                $sr = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$sr) {
                    http_response_code(422);
                    echo json_encode(['success' => false, 'message' => 'Request not found or not customs-approved.']);
                    exit;
                }

                // Insert assignment (status = Assigned)
                $stmt = $pdo->prepare(
                    "INSERT INTO cargo_assignments (shipping_request_id, container_name, storage_slot_name, status)
                     VALUES (?, ?, ?, 'Assigned')"
                );
                $stmt->execute([$shipping_request_id, $container_name, $storage_slot_name]);

                echo json_encode(['success' => true, 'message' => 'Storage assigned.']);
                break;

            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: '.$e->getMessage()]);
}
