<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user']['user_type']) || $_SESSION['user']['user_type'] !== 'partner' || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'Shipping Company') {
    header("Location: ../../login/login.php");
    exit;
}

$agent_name = $_SESSION['user']['full_name'] ?? 'Agent';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Port System — Shipping Company (Agent)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1 class="header-title">Port System — Shipping Company (Agent)</h1>
            <div class="header-purpose">
                Purpose: Shipping Company (Agent) — UI-only template showing the features you requested.
            </div>
        </div>
    </header>

    <main class="container">
        <div class="content-card">
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
        </div>

        <div class="content-card">
            <h2 class="card-title">My Ships / Requests</h2>
            <table class="data-table" id="requests-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Ship</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be populated by JavaScript -->
                    <tr>
                        <td colspan="3">Loading requests...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-card">
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
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
