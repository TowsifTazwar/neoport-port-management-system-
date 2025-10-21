<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Ensure the user is logged in as a Customs Officer
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Customs & Compliance Officer') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$pdo = pms_pdo();
$out = ['success' => false, 'message' => 'Invalid request.'];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $action = $_GET['action'] ?? '';
        if ($action === 'get_importer_requests') {
            $stmt = $pdo->prepare("SELECT * FROM importer_documents ORDER BY created_at DESC");
            $stmt->execute();
            $out = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } else if ($action === 'get_exporter_requests') {
            $stmt = $pdo->prepare("SELECT * FROM exporter_documents ORDER BY created_at DESC");
            $stmt->execute();
            $out = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        }
        else if ($action === 'get_customs_data') {
            // show only Harbor Master approved requests
            $stmt = $pdo->prepare("SELECT id, ship_name, status, customs_status FROM shipping_requests WHERE status = 'approved'");
            $stmt->execute();
            $out = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        }
        elseif ($action === 'get_partner_data') {
            $request_id = (int)($_GET['request_id'] ?? 0);
            if (!$request_id) throw new Exception('Missing request id');

            $sql = "SELECT p.*
                      FROM partners p
                      JOIN shipping_requests sr ON sr.company_partner_id = p.id
                     WHERE sr.id = ?";
            $st = $pdo->prepare($sql);
            $st->execute([$request_id]);
            $partner = $st->fetch(PDO::FETCH_ASSOC);
            if (!$partner) throw new Exception('Partner data not found');
            $out = ['success' => true, 'partner' => $partner];
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = strtolower(trim($_POST['action'] ?? ''));
        $request_id = (int)($_POST['request_id'] ?? 0);
        $reason = $_POST['reason'] ?? null;

        // Determine the action
        if ($action === 'importer_approve') {
            $stmt = $pdo->prepare("UPDATE importer_documents SET status = 'approved' WHERE id = ?");
            $stmt->execute([$request_id]);
            $out = ['success' => true, 'message' => 'Document approved.'];
        } else if ($action === 'importer_reject') {
            $stmt = $pdo->prepare("UPDATE importer_documents SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$request_id]);
            $out = ['success' => true, 'message' => 'Document rejected.'];
        } else if ($action === 'send_to_logistics') {
            // Forward to logistics
            $stmt = $pdo->prepare("SELECT company_name, contact_no, container_name, batch_number, address FROM importer_documents WHERE id = ?");
            $stmt->execute([$request_id]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("INSERT INTO logistics_tasks (document_id, company_name, contact_no, container_name, batch_number, address) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$request_id, $doc['company_name'], $doc['contact_no'], $doc['container_name'], $doc['batch_number'], $doc['address']]);

            $out = ['success' => true, 'message' => 'Document forwarded to logistics.'];
        } else if ($action === 'exporter_approve') {
            $stmt = $pdo->prepare("UPDATE exporter_documents SET status = 'approved' WHERE id = ?");
            $stmt->execute([$request_id]);
            $out = ['success' => true, 'message' => 'Document approved.'];
        } else if ($action === 'exporter_reject') {
            $stmt = $pdo->prepare("UPDATE exporter_documents SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$request_id]);
            $out = ['success' => true, 'message' => 'Document rejected.'];
        } else if ($action === 'send_exporter_to_logistics') {
            // Forward to logistics
            $stmt = $pdo->prepare("SELECT company_name, contact_no, container_name, batch_number, address, ship_id FROM exporter_documents WHERE id = ?");
            $stmt->execute([$request_id]);
            $doc = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $pdo->prepare("INSERT INTO logistics_tasks (document_id, company_name, contact_no, container_name, batch_number, address, ship_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$request_id, $doc['company_name'], $doc['contact_no'], $doc['container_name'], $doc['batch_number'], $doc['address'], $doc['ship_id']]);

            $out = ['success' => true, 'message' => 'Document forwarded to logistics.'];
        } else {
            if ($action === 'approve') {
                $customs_status = 'approved_by_customs';
            } elseif ($action === 'reject') {
                $customs_status = 'rejected_by_customs';
            } elseif ($action === 'undo') {
                $customs_status = NULL;  // Undo to pending state
            } else {
                throw new Exception('Invalid action');
            }
    
            $stmt = $pdo->prepare("UPDATE shipping_requests SET customs_status = ?, rejection_reason = ? WHERE id = ?");
            $stmt->execute([$customs_status, $reason, $request_id]);
    
            $out = ['success' => true, 'message' => ucfirst($action) . 'd by Customs'];
        }
    }
} catch (Exception $e) {
    $out = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($out);
