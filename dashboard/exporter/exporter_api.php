<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['user']['user_type']) ||
    $_SESSION['user']['user_type'] !== 'exporter'
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$pdo = pms_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload_document') {
        try {
            // Validate required fields
            $required_fields = ['company_name', 'contact_no', 'tax_id', 'trade_license', 'lc_number', 'container_name', 'batch_number', 'invoice_id', 'ship_id', 'address'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("'$field' is required.");
                }
            }
            if (empty($_FILES['document']['name'])) {
                throw new Exception("'document' is required.");
            }

            $exporter_id = $_SESSION['user_id'];

            // Handle file upload
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $target_file = $target_dir . basename($_FILES["document"]["name"]);
            move_uploaded_file($_FILES["document"]["tmp_name"], $target_file);

            $stmt = $pdo->prepare(
                'INSERT INTO exporter_documents (exporter_id, company_name, contact_no, tax_id, trade_license, lc_number, container_name, batch_number, invoice_id, ship_id, address, document_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $exporter_id,
                $_POST['company_name'],
                $_POST['contact_no'],
                $_POST['tax_id'],
                $_POST['trade_license'],
                $_POST['lc_number'],
                $_POST['container_name'],
                $_POST['batch_number'],
                $_POST['invoice_id'],
                $_POST['ship_id'],
                $_POST['address'],
                $target_file
            ]);
            $response = ['success' => true, 'message' => 'Document uploaded successfully!'];
        } catch (Exception $e) {
            http_response_code(500);
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        http_response_code(400);
        $response['message'] = 'Invalid POST action.';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    try {
        if ($action === 'get_dashboard_data') {
            // This is a placeholder for now
            $response = [
                'success' => true, 
                'data' => []
            ];
        } else if ($action === 'get_location') {
            $ship_id = (string)($_GET['ship_id'] ?? '');
            if ($ship_id) {
                $stmt = $pdo->prepare("SELECT latitude, longitude, eta, distance_to_port, last_updated FROM ship_locations WHERE ship_id = ? ORDER BY last_updated DESC LIMIT 1");
                $stmt->execute([$ship_id]);
                $location = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($location) {
                    $response = ['success' => true, 'data' => $location];
                } else {
                    $response['message'] = 'No location data found for this ship.';
                }
            } else {
                http_response_code(400);
                $response['message'] = 'Ship ID is required.';
            }
        } else if ($action === 'search_cargo') {
            $term = $_GET['term'] ?? '';
            if ($term) {
                $stmt = $pdo->prepare("SELECT shipment_status as status FROM transport_assignments WHERE container_name = ? OR batch_number = ?");
                $stmt->execute([$term, $term]);
                $cargo = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cargo) {
                    $response = ['success' => true, 'data' => $cargo];
                } else {
                    $response['message'] = 'No cargo status found for this container/batch.';
                }
            } else {
                http_response_code(400);
                $response['message'] = 'Search term is required.';
            }
        } else if ($action === 'search_delivery_info') {
            $term = $_GET['term'] ?? '';
            if ($term) {
                $stmt = $pdo->prepare("SELECT company_name, contact_no, container_name, batch_number, address, assigned_transport FROM transport_assignments WHERE container_name = ? OR batch_number = ?");
                $stmt->execute([$term, $term]);
                $delivery_info = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($delivery_info) {
                    $response = ['success' => true, 'data' => $delivery_info];
                } else {
                    $response['message'] = 'No delivery info found for this container/batch.';
                }
            } else {
                http_response_code(400);
                $response['message'] = 'Search term is required.';
            }
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
