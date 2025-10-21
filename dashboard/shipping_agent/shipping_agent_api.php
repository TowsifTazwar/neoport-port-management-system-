<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['user']['user_type']) || 
    (
        $_SESSION['user']['user_type'] !== 'partner' && 
        $_SESSION['user']['user_type'] !== 'shipping_agent'
    ) || 
    !isset($_SESSION['user']['role']) || 
    (
        $_SESSION['user']['role'] !== 'Shipping Company' &&
        $_SESSION['user']['role'] !== 'Shipping Agent'
    )
) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$response = ['success' => false, 'message' => 'Invalid request.'];
$pdo = pms_pdo();

// Get the agent_id and partner_id from the session.
$user_type = $_SESSION['user']['user_type'];
$agent_id = 0;
$company_id = 0;

if ($user_type === 'shipping_agent') {
    // The user is a specific agent.
    $agent_id = $_SESSION['user']['id'];
    $company_id = $_SESSION['user']['partner_id'];
} elseif ($user_type === 'partner') {
    // The user is a partner (company account). Find the primary agent for this company.
    $company_id = $_SESSION['user']['id'];
    
    // Look for the agent marked as the primary contact.
    $stmt = $pdo->prepare("SELECT id FROM shipping_agents WHERE partner_id = ? AND is_primary_contact = 1");
    $stmt->execute([$company_id]);
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($agent) {
        $agent_id = $agent['id'];
    } else {
        // If no primary contact is found, fall back to the first agent associated with the company.
        $stmt = $pdo->prepare("SELECT id FROM shipping_agents WHERE partner_id = ? ORDER BY id ASC LIMIT 1");
        $stmt->execute([$company_id]);
        $agent = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($agent) {
            $agent_id = $agent['id'];
        } else {
            // If no agent is found at all, then there is a data problem.
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: No shipping agent is associated with your company account.']);
            exit;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    try {
        if ($action === 'get_berths') {
            $stmt = $pdo->query("SELECT id, berth_name FROM berths WHERE status = 'available' ORDER BY berth_name");
            $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($action === 'get_importers') {
            $stmt = $pdo->query(
                "SELECT p.id, p.company_name 
                 FROM partners p
                 JOIN partner_roles pr ON p.role_id = pr.id
                 WHERE pr.role_name = 'Importer'
                 ORDER BY p.company_name"
            );
            $response = ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } elseif ($action === 'get_requests_and_invoices') {
            $user_type = $_SESSION['user']['user_type'];
            $requests = [];
            $invoices = [];

            if ($user_type === 'shipping_agent') {
                // AGENT: Fetch only their own requests and invoices
                $req_stmt = $pdo->prepare(
                    "SELECT id, ship_name, status, rejection_reason, DATE_FORMAT(created_at, '%Y-%m-%d') AS request_date FROM shipping_requests WHERE agent_id = ? ORDER BY created_at DESC"
                );
                $req_stmt->execute([$agent_id]);
                $requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

                $inv_stmt = $pdo->prepare(
                    "SELECT i.invoice_number, i.amount, i.status, i.file_path 
                     FROM invoices i
                     JOIN shipping_requests sr ON i.request_id = sr.id
                     WHERE sr.agent_id = ? ORDER BY i.created_at DESC"
                );
                $inv_stmt->execute([$agent_id]);
                $invoices = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);

            } else { // 'partner'
                // COMPANY: Fetch all requests and invoices for the company
                $req_stmt = $pdo->prepare(
                    "SELECT id, ship_name, status, rejection_reason, DATE_FORMAT(created_at, '%Y-%m-%d') AS request_date FROM shipping_requests WHERE company_partner_id = ? ORDER BY created_at DESC"
                );
                $req_stmt->execute([$company_id]);
                $requests = $req_stmt->fetchAll(PDO::FETCH_ASSOC);

                $inv_stmt = $pdo->prepare(
                    "SELECT i.invoice_number, i.amount, i.status, i.file_path 
                     FROM invoices i
                     JOIN shipping_requests sr ON i.request_id = sr.id
                     WHERE sr.company_partner_id = ? ORDER BY i.created_at DESC"
                );
                $inv_stmt->execute([$company_id]);
                $invoices = $inv_stmt->fetchAll(PDO::FETCH_ASSOC);
            }

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
                'INSERT INTO shipping_requests (agent_id, company_partner_id, ship_name, cargo_type, requested_berth_id, estimated_arrival_time) VALUES (?, ?, ?, ?, ?, ?)'
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
    } else if ($action === 'update_location') {
        try {
            $ship_id = (string)($_POST['ship_id'] ?? '');
            $latitude = $_POST['latitude'];
            $longitude = $_POST['longitude'];
            $eta = $_POST['eta'];

            // Chittagong Port coordinates
            $port_latitude = 22.3333;
            $port_longitude = 91.8333;

            $distance_to_port = calculate_distance($latitude, $longitude, $port_latitude, $port_longitude);

            // Check if a record for this ship already exists
            $stmt = $pdo->prepare("SELECT id FROM ship_locations WHERE ship_id = ?");
            $stmt->execute([$ship_id]);
            $existing_location = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_location) {
                // Update existing record
                $update_stmt = $pdo->prepare(
                    "UPDATE ship_locations SET latitude = ?, longitude = ?, eta = ?, distance_to_port = ? WHERE ship_id = ?"
                );
                $update_stmt->execute([$latitude, $longitude, $eta, $distance_to_port, $ship_id]);
            } else {
                // Insert new record
                $insert_stmt = $pdo->prepare(
                    "INSERT INTO ship_locations (ship_id, latitude, longitude, eta, distance_to_port) VALUES (?, ?, ?, ?, ?)"
                );
                $insert_stmt->execute([$ship_id, $latitude, $longitude, $eta, $distance_to_port]);
            }

            $response = ['success' => true, 'message' => 'Ship location updated successfully!'];
        } catch (Exception $e) {
            http_response_code(500);
            $response['message'] = 'Database error: ' . $e->getMessage();
        }
    } else {
        http_response_code(400);
        $response['message'] = 'Invalid POST action.';
    }
}

function calculate_distance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // in kilometers

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earth_radius * $c;
}

echo json_encode($response);
