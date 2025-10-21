<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['user']['user_type']) ||
    $_SESSION['user']['user_type'] !== 'exporter'
) {
    header("Location: ../../login/login.php");
    exit;
}

$exporter_name = $_SESSION['user']['full_name'] ?? 'Exporter';
$company_name = $_SESSION['user']['company_name'] ?? 'Exporter Company';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($company_name); ?> Dashboard - Port Management System</title>
    <link rel="stylesheet" href="style.css?v=1.1">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>
    <div class="hero-section">
        <header class="header">
            <div class="header-content">
                <h1 class="header-title"><?php echo htmlspecialchars($company_name); ?></h1>
                <span>Welcome, <?php echo htmlspecialchars($exporter_name); ?>!</span>
            </div>
            <a href="/pms/logout.php" class="logout-button">Logout</a>
        </header>
        <div class="hero-cards">
            <a href="#upload-documents" class="hero-card">
                <h3>Upload Documents</h3>
                <span class="arrow">&darr;</span>
            </a>
            <a href="#cargo-status" class="hero-card">
                <h3>Cargo Status</h3>
                <span class="arrow">&darr;</span>
            </a>
            <a href="#delivery-info" class="hero-card">
                <h3>Delivery Info</h3>
                <span class="arrow">&darr;</span>
            </a>
            <a href="#ship-location" class="hero-card">
                <h3>Ship Location</h3>
                <span class="arrow">&darr;</span>
            </a>
        </div>
    </div>

    <main class="container">
        <section id="upload-documents" class="card">
            <h2>Upload Documents</h2>
            <form id="upload-form">
                <div class="form-group">
                    <label for="company-name">Company Name</label>
                    <input type="text" id="company-name" name="company_name" required>
                </div>
                <div class="form-group">
                    <label for="contact-no">Contact No.</label>
                    <input type="text" id="contact-no" name="contact_no" required>
                </div>
                <div class="form-group">
                    <label for="tax-id">Tax ID</label>
                    <input type="text" id="tax-id" name="tax_id" required>
                </div>
                <div class="form-group">
                    <label for="trade-license">Trade Liscence</label>
                    <input type="text" id="trade-license" name="trade_license" required>
                </div>
                <div class="form-group">
                    <label for="lc-number">LC Number</label>
                    <input type="text" id="lc-number" name="lc_number" required>
                </div>
                <div class="form-group">
                    <label for="container-name">Container Name/ID</label>
                    <input type="text" id="container-name" name="container_name" required>
                </div>
                <div class="form-group">
                    <label for="batch-number">Batch Number</label>
                    <input type="text" id="batch-number" name="batch_number" required>
                </div>
                <div class="form-group">
                    <label for="invoice-id">Invoice/Transaction ID</label>
                    <input type="text" id="invoice-id" name="invoice_id" required>
                </div>
                <div class="form-group">
                    <label for="ship-id">Ship ID/Ship Name</label>
                    <input type="text" id="ship-id" name="ship_id" required>
                </div>
                <div class="form-group">
                    <label for="address">Address (The shipment will be sent to this address)</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="document">Document</label>
                    <input type="file" id="document" name="document" required>
                </div>
                <button type="submit" class="btn">Upload</button>
            </form>
        </section>

        <section id="cargo-status" class="card">
            <h2>Cargo Status</h2>
            <form id="cargo-search-form">
                <div class="form-group">
                    <label for="cargo-search">Search by Container Name or Batch Number</label>
                    <input type="text" id="cargo-search" name="cargo_search" required>
                </div>
                <button type="submit" class="btn">Search</button>
            </form>
            <div id="cargo-status-result"></div>
        </section>

        <section id="delivery-info" class="card">
            <h2>Delivery Info</h2>
            <form id="delivery-search-form">
                <div class="form-group">
                    <label for="delivery-search">Search by Batch Number or Container Name</label>
                    <input type="text" id="delivery-search" name="delivery_search" required>
                </div>
                <button type="submit" class="btn">Search</button>
            </form>
            <div id="delivery-info-result"></div>
        </section>

        <section id="ship-location" class="card">
            <h2>Ship Location</h2>
            <div class="form-group">
                <label for="ship-id-selector">Select Ship:</label>
                <input type="text" id="ship-id-selector" placeholder="Enter Ship ID">
                <button id="track-ship-btn" class="btn">Track Ship</button>
            </div>
            <div id="map" style="height: 400px;"></div>
            <div id="ship-info"></div>
        </section>
    </main>

    <footer class="footer">
        <p>Port Management System © 2025</p>
    </footer>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="script.js"></script>
</body>
</html>
