<?php
// Shipping Agent Dashboard - corrected frontend styling
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Shipping Agent Dashboard</title>
  <link rel="stylesheet" href="/assets/css/importer-dashboard-styles.css?v=<?php echo time(); ?>">
</head>
<body>
  <div class="app-wrap">
    <aside class="sidebar">
      <div class="logo">Port Management</div>
      <ul class="nav">
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Shipping Requests</a></li>
        <li><a href="#">Shipments</a></li>
        <li><a href="#">Invoices</a></li>
        <li><a href="#">Profile</a></li>
      </ul>
    </aside>
    <main class="main">
      <header class="topbar">
        <div class="search">🔍 <input type="text" placeholder="Search requests" style="border:0;outline:none;margin-left:8px"></div>
        <div class="summary">Logged in as Shipping Agent</div>
      </header>

      <section class="cards">
        <div class="card">
          <h3>Total Requests</h3>
          <div class="value">12</div>
        </div>
        <div class="card">
          <h3>Approved</h3>
          <div class="value">8</div>
        </div>
        <div class="card">
          <h3>Pending</h3>
          <div class="value">3</div>
        </div>
        <div class="card">
          <h3>Rejected</h3>
          <div class="value">1</div>
        </div>
      </section>

      <section class="card">
        <div class="card-head">
            <h3>Recent Shipping Requests</h3>
        </div>
        <div class="table-wrap">
            <table class="table">
              <thead>
                <tr><th>ID</th><th>Ship Name</th><th>ETA</th><th>Status</th><th>Action</th></tr>
              </thead>
              <tbody>
                <tr><td>36</td><td>Ship-00879</td><td>2025-10-18 22:49</td><td><span class="badge pending">Pending</span></td><td><a class="btn" href="#">View</a></td></tr>
                <tr><td>35</td><td>abc-05</td><td>2025-10-01 12:34</td><td><span class="badge approved">Approved</span></td><td><a class="btn" href="#">View</a></td></tr>
                <tr><td>34</td><td>abc-47</td><td>2025-10-17 13:11</td><td><span class="badge approved">Approved</span></td><td><a class="btn" href="#">View</a></td></tr>
              </tbody>
            </table>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
