<?php
header('Content-Type: application/json');
require_once '../../config/db.php';

// Get the PDO connection object
$pdo = pms_pdo();

$action = isset($_GET['action']) ? $_GET['action'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

try {
    switch ($action) {
        case 'ship_overview':
            getShipOverview($pdo, $date);
            break;
        case 'berth_occupancy':
            getBerthOccupancy($pdo);
            break;
        case 'containers_in_yard':
            getContainersInYard($pdo);
            break;
        case 'arrivals_departures':
            getArrivalsAndDepartures($pdo, $date);
            break;
        case 'get_notices':
            getNotices($pdo);
            break;
        case 'add_notice':
            addNotice($pdo);
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
            break;
    }
} catch (PDOException $e) {
    // Catch any database errors and return a JSON error message
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

function getShipOverview($pdo, $date) {
    $start_date = $date . ' 00:00:00';
    $end_date = $date . ' 23:59:59';

    // Count of ships arriving on the selected date
    $stmt_arriving = $pdo->prepare("SELECT COUNT(*) as arriving_count FROM shipping_requests WHERE estimated_arrival_time BETWEEN ? AND ? AND status = 'approved'");
    $stmt_arriving->execute([$start_date, $end_date]);
    $result_arriving = $stmt_arriving->fetch();

    // Count of ship requests submitted for that date
    $stmt_requests = $pdo->prepare("SELECT COUNT(*) as requests_count FROM shipping_requests WHERE created_at BETWEEN ? AND ?");
    $stmt_requests->execute([$start_date, $end_date]);
    $result_requests = $stmt_requests->fetch();

    echo json_encode([
        'arriving_ships' => $result_arriving['arriving_count'] ?? 0,
        'ship_requests' => $result_requests['requests_count'] ?? 0
    ]);
}

function getBerthOccupancy($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as occupied_berths FROM berth_allocations WHERE status = 'docked'");
    $stmt->execute();
    $result = $stmt->fetch();
    echo json_encode($result);
}

function getContainersInYard($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_containers FROM cargo_assignments WHERE status = 'Assigned'");
    $stmt->execute();
    $result = $stmt->fetch();
    echo json_encode($result);
}

function getArrivalsAndDepartures($pdo, $date) {
    $start_date = $date . ' 00:00:00';
    $end_date = $date . ' 23:59:59';

    $stmt = $pdo->prepare("
        SELECT 
            sr.ship_name, 
            sr.estimated_arrival_time as eta, 
            b.berth_name as requested_berth, 
            sr.status 
        FROM shipping_requests sr
        LEFT JOIN berths b ON sr.requested_berth_id = b.id
        WHERE sr.estimated_arrival_time BETWEEN ? AND ?
        ORDER BY sr.estimated_arrival_time ASC
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetchAll();
    echo json_encode($result);
}

function getNotices($pdo) {
    $stmt = $pdo->query("SELECT * FROM notices ORDER BY created_at DESC");
    $notices = $stmt->fetchAll();
    echo json_encode($notices);
}

function addNotice($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? null;
    $message = $data['message'] ?? null;

    if (empty($title) || empty($message)) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Title and message are required']);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO notices (title, message) VALUES (?, ?)");
    if ($stmt->execute([$title, $message])) {
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to add notice']);
    }
}
?>
