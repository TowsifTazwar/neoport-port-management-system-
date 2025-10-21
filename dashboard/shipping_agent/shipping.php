<?php
if (session_status() === PHP_SESSION_NONE) session_start();

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
    header("Location: ../../login/login.php");
    exit;
}

$agent_name = $_SESSION['user']['full_name'] ?? 'Agent';
$company_name = $_SESSION['user']['company_name'] ?? 'Shipping Company';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port System — <?php echo htmlspecialchars($company_name); ?> (Agent)</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1 class="header-title">
                <?php echo htmlspecialchars($company_name); ?>
            </h1>
            <div class="header-purpose">
                Welcome, <?php echo htmlspecialchars($agent_name); ?>!
            </div>
        </div>
        <a href="/pms/logout.php" class="logout-button">Logout</a>
    </header>

    <main class="container">
        <section class="content-card">
            <h2 class="card-title">Ship Arrival Request</h2>
            <form id="ship-arrival-form">
                <div class="form-group">
                    <label for="ship-name">Ship Name</label>
                    <input type="text" id="ship-name" name="ship_name" required>
                </div>
                <div class="form-group">
                    <label for="eta">ETA</label>
                    <input type="datetime-local" id="eta" name="eta" placeholder="mm/dd/yyyy --:-- --" required>
                </div>
                <div class="form-group">
                    <label for="cargo-type">Cargo Type</label>
                    <input type="text" id="cargo-type" name="cargo_type" required>
                </div>
                <div class="form-group">
                    <label for="requested-berth">Requested Berth</label>
                    <select id="requested-berth" name="requested_berth" required>
                        <option value="" disabled selected>Select a Berth</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Submit Request</button>
            </form>
        </section>

        <section class="content-card">
            <h2 class="card-title">Update Ship Location</h2>
            <form id="update-location-form">
                <div class="form-group">
                    <label for="ship-id">Ship ID</label>
                    <input type="text" id="ship-id" name="ship_id" required>
                </div>
                <div class="form-group">
                    <label for="latitude">Latitude</label>
                    <input type="text" id="latitude" name="latitude" required>
                </div>
                <div class="form-group">
                    <label for="longitude">Longitude</label>
                    <input type="text" id="longitude" name="longitude" required>
                </div>
                <div class="form-group">
                    <label for="location-eta">ETA</label>
                    <input type="datetime-local" id="location-eta" name="eta" required>
                </div>
                <button type="submit" class="btn-submit">Update Location</button>
            </form>
        </section>

        <section class="content-card">
            <h2 class="card-title">My Ships / Requests</h2>
            <table class="data-table" id="requests-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Ship</th>
                        <th>Status</th>
                        <th>Reason for Rejection</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by JavaScript -->
                    <tr>
                        <td colspan="4">Loading requests...</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="content-card">
            <h2 class="card-title">Invoices</h2>
            <table class="data-table" id="invoices-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by JavaScript -->
                     <tr>
                        <td colspan="4">Loading invoices...</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
