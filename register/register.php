<?php
// pms/register/register.php — Step‑1 selector (3 boxes)
$brand = "Port Management System";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register • <?= htmlspecialchars($brand) ?></title>
  <link rel="stylesheet" href="register.css?v=12">
</head>
<body>
  <!-- Top Nav -->
  <header class="navbar">
    <div class="logo"><?= htmlspecialchars($brand) ?></div>
    <nav>
      <a href="/pms/home/index.php">Home</a>
      <a href="/pms/home/about.php">About</a>
      <a href="/pms/home/contact.php">Contact</a>
      <!-- FIX: point to the actual login page -->
      <a href="/pms/login/login.php">Login</a>
      <a href="/pms/register/register.php" class="btn-register">Register</a>
    </nav>
  </header>

  <!-- Register Container -->
  <main class="register-container">
    <h1>Create your account</h1>
    <p>Choose the category that best describes you. You can request access for Authority/Operations, or self‑register as a Partner.</p>

    <div class="register-options">
      <!-- Port Authority -->
      <a class="register-card" href="/pms/register/register-authority.php">
        <div class="icon">🏛️</div>
        <h2>Port Authority</h2>
        <p>Director &amp; Deputy level. Invite‑only; submit an access request.</p>
        <span class="cta-text">Request Access →</span>
      </a>

      <!-- Operations & Compliance -->
      <a class="register-card" href="/pms/register/register-operations.php">
        <div class="icon">⚒️</div>
        <h2>Operations &amp; Compliance</h2>
        <p>Harbor, Customs, Warehouse, Logistics, Workforce, Finance, IT.</p>
        <span class="cta-text">Register as Staff →</span>
      </a>

      <!-- Trade & Service Partners -->
      <a class="register-card" href="/pms/register/register-partners.php">
        <div class="icon">🌐</div>
        <h2>Trade & Service Partners</h2>
        <p>Shipping Companies, Exporters, Importers, Suppliers/Vendors.</p>
        <span class="cta-text">Register as Partner →</span>
      </a>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <p>© <?= date('Y') ?> <?= htmlspecialchars($brand) ?></p>
    <nav>
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
    </nav>
  </footer>

  <!-- Keep a JS file, but make it safe -->
  <script src="register.js?v=12" defer></script>
</body>
</html>
