<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (
  !isset($_SESSION['user_id']) ||
  !isset($_SESSION['user']['role']) ||
  $_SESSION['user']['role'] !== 'Logistics & Transport Coordinator'
) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$pdo = pms_pdo();
$logistics_coordinator_id = $_SESSION['user_id'];

$ALLOWED_STATUSES = [
  'Pending Pickup','Dispatched','En Route','Arrived at City',
  'Out for Delivery','Delivered','Completed'
];

/* ------------------------ GET actions ------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $action = $_GET['action'] ?? '';

  try {
    if ($action === 'get_logistics_data') {
      $stmt = $pdo->prepare("
        SELECT ca.id, ca.container_name, ca.storage_slot_name, ca.status,
               sr.id AS shipping_request_id, sr.ship_name
        FROM cargo_assignments ca
        JOIN shipping_requests sr ON sr.id = ca.shipping_request_id
        WHERE ca.status = 'Assigned'
        ORDER BY ca.id DESC
      ");
      $stmt->execute();
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $response = ['success' => true, 'data' => $data];

    } else if ($action === 'get_document_tasks') {
      $stmt = $pdo->prepare("SELECT * FROM logistics_tasks ORDER BY created_at DESC");
      $stmt->execute();
      $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $response = ['success' => true, 'data' => $data];

    } else if ($action === 'get_transport_assignments') {
      // Used by the “Status of Shipment” table
      $stmt = $pdo->prepare("
        SELECT id, company_name, contact_no, container_name, batch_number,
               address, assigned_transport, shipment_status, created_at, status_updated_at
        FROM transport_assignments
        ORDER BY id DESC
      ");
      $stmt->execute();
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

/* ------------------------ POST actions ----------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_GET['action'] ?? ($_POST['action'] ?? '');

  try {
    if ($action === 'assign_transport') {
      $company   = trim($_POST['company']   ?? '');
      $contact   = trim($_POST['contact']   ?? '');
      $container = trim($_POST['container'] ?? '');
      $batch     = trim($_POST['batch']     ?? '');
      $address   = trim($_POST['address']   ?? '');
      $transport = trim($_POST['transport'] ?? '');

      if ($company === '' || $container === '') {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Company and Container are required.']);
        exit;
      }

      $stmt = $pdo->prepare("
        INSERT INTO transport_assignments
          (company_name, contact_no, container_name, batch_number, address, assigned_transport, shipment_status, created_at)
        VALUES
          (:company_name, :contact_no, :container_name, :batch_number, :address, :assigned_transport, 'Pending Pickup', NOW())
      ");
      $stmt->execute([
        ':company_name'       => $company,
        ':contact_no'         => $contact,
        ':container_name'     => $container,
        ':batch_number'       => $batch,
        ':address'            => $address,
        ':assigned_transport' => $transport
      ]);

      $response = ['success' => true, 'message' => 'Transport assigned successfully.'];

    } else if ($action === 'update_shipment_status') {
      $id = (int)($_POST['id'] ?? 0);
      $status = trim($_POST['status'] ?? '');

      if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid assignment id.']);
        exit;
      }
      if (!in_array($status, $ALLOWED_STATUSES, true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        exit;
      }

      $stmt = $pdo->prepare("
        UPDATE transport_assignments
        SET shipment_status = :status, status_updated_at = NOW()
        WHERE id = :id
      ");
      $stmt->execute([':status' => $status, ':id' => $id]);

      $response = ['success' => true, 'message' => 'Shipment status updated.'];

    } else {
      http_response_code(400);
      $response['message'] = 'Invalid POST action.';
    }
  } catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Database error: ' . $e->getMessage();
  }
}

echo json_encode($response);
