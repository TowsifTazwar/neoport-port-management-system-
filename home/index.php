<?php
// htdocs/pms/home/index.php
$brand = "neoport";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Home • <?= htmlspecialchars($brand) ?></title>
  <link rel="stylesheet" href="/pms/home/assets/css/index.css?v=15">
</head>
<body>
  <!-- Top Nav -->
  <header class="navbar">
    <div class="logo"><?= htmlspecialchars($brand) ?></div>
    <nav>
      <a href="/pms/home/index.php">Home</a>
      <a href="#">About</a>
      <a href="#">Contact</a>
      <a href="#" id="notices-btn">Notices</a>
      <a href="/pms/login/login.php">Login</a>
      <a class="btn-register" href="/pms/register/register.php">Register</a>
    </nav>
  </header>

  <!-- HERO -->
  <section class="hero">
    <div class="hero__bg"></div>
    <div class="hero__overlay"></div>

    <div class="hero__content">
      <h1>Smart Port Management System</h1>
      <p class="tagline">
        Streamline vessel traffic, cargo, customs, logistics, workforce and finance — all in one platform.
      </p>

      <div class="hero__actions">
        <a class="btn btn--primary" href="/pms/register/register.php">Get Started</a>
        <a class="btn btn--outline" href="#features">Learn More</a>
      </div>
    </div>

    <!-- Arrow only -->
    <button class="scroll-indicator" aria-label="Scroll to features" data-scroll="#features">▾</button>
  </section>

  <!-- FEATURES -->
  <section id="features" class="features">
    <div class="container">
      <article class="card">
        <div class="card__icon">⚓</div>
        <h3>Harbor Operations</h3>
        <p>Real-time berth & vessel allocation with conflict checks.</p>
      </article>

      <article class="card">
        <div class="card__icon">📦</div>
        <h3>Cargo & Logistics</h3>
        <p>Track containers end-to-end across customs, storage and dispatch.</p>
      </article>

      <article class="card">
        <div class="card__icon">💳</div>
        <h3>Finance & Compliance</h3>
        <p>Automated billing, payments, and audit-ready trails.</p>
      </article>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer__inner">
      <span>© <?= date('Y') ?> <?= htmlspecialchars($brand) ?></span>
    </div>
  </footer>

  <div id="notices-modal" class="modal">
    <div class="modal-content">
      <span class="close-btn">&times;</span>
      <h2>Notices</h2>
      <div id="notices-container"></div>
    </div>
  </div>

  <script src="/pms/home/assets/js/index.js?v=8" defer></script>
</body>
</html>
