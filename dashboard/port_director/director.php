<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['user']['role']) ||
    (stripos($_SESSION['user']['role'], 'Director') === false)
) {
    header("Location: ../../login/login.php");
    exit;
}

$director_name = $_SESSION['user']['full_name'] ?? 'Director';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Port Director — Dashboard</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <header class="topbar">
    <div class="title">
      <span class="brand">Port System</span>
      <span class="divider">—</span>
      <span class="role">Port Director</span>
    </div>
    <div class="who">Welcome, <?php echo htmlspecialchars($director_name); ?></div>
  </header>

  <main class="page">
    <div class="date-selector">
      <label for="date">Select Date:</label>
      <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
    </div>
    <!-- KPI cards -->
    <section class="kpis" aria-label="Key Performance Indicators">
      <article class="kpi-card" aria-live="polite">
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path d="M20 21H4l-2-4h3l1 2h12l1-2h3l-2 4zM4 13l8-3 8 3V8l-8-3-8 3v5z"/></svg>
        </div>
        <div class="kpi-meta">
          <h3>Ships Arriving Today</h3>
          <div class="kpi-value" id="kpi-ships-arriving">—</div>
        </div>
      </article>

      <article class="kpi-card" aria-live="polite">
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path d="M3 4h18v2H3V4zm0 5h18v6a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V9zm4 2v4h10v-4H7z"/></svg>
        </div>
        <div class="kpi-meta">
          <h3>Berths Occupied</h3>
          <div class="kpi-value" id="kpi-berths">—</div>
        </div>
      </article>

      <article class="kpi-card" aria-live="polite">
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path d="M3 7h18v10H3V7zm2 2v6h14V9H5zM8 9h2v6H8V9zm6 0h2v6h-2V9z"/></svg>
        </div>
        <div class="kpi-meta">
          <h3>Containers in Yard</h3>
          <div class="kpi-value" id="kpi-containers">—</div>
        </div>
      </article>

      <article class="kpi-card" aria-live="polite">
        <div class="kpi-icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" focusable="false"><path d="M9 2h6v2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3V2zm0 4H6v14h12V6h-3v1H9V6zm1 5h8v2h-8v-2zm0 4h6v2h-6v-2zM7.5 11l1.2 1.2 2.3-2.3-1.1-1.1-1.2 1.2-.2-.2-1 1.2z"/></svg>
        </div>
        <div class="kpi-meta">
          <h3>Ship Requests Today</h3>
          <div class="kpi-value" id="kpi-ship-requests">—</div>
        </div>
      </article>
    </section>

    <!-- Two-panel section -->
    <section class="grid-2">
      <article class="card">
        <div class="card-head">
          <h3>Arrivals & Departures</h3>
        </div>
        <div class="table-wrap">
          <table class="table" id="movementsTable">
            <thead>
              <tr>
                <th>Vessel</th>
                <th>ETA/ETD</th>
                <th>Berth</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody><!-- filled by JS --></tbody>
          </table>
        </div>
      </article>

      <article class="card">
        <div class="card-head"><h3>Notices</h3></div>
        <ul class="notices" id="noticeList"><!-- filled by JS --></ul>

        <form id="noticeForm" class="notice-form" autocomplete="off">
          <label for="noticeTitle">Title</label>
          <input id="noticeTitle" name="title" type="text" placeholder="e.g., Berth Maintenance Schedule" required />
          <label for="noticeMsg">Post a new notice</label>
          <div class="row">
            <textarea id="noticeMsg" name="message" placeholder="e.g., Berth 5 will be closed for maintenance from..." required></textarea>
            <button class="btn" type="submit">Post</button>
          </div>
        </form>
      </article>
    </section>
  </main>

  <div id="toast" class="toast" role="status" aria-live="polite"></div>
  <script src="script.js"></script>
</body>
</html>
