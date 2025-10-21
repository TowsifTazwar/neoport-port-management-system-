<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$brand = "neoport";

$err = '';
$ok  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Gather fields first (so the form can re-fill on error)
  $full_name   = trim($_POST['full_name'] ?? '');
  $email       = trim($_POST['email'] ?? '');
  $phone       = trim($_POST['phone'] ?? '');
  $org         = trim($_POST['organization'] ?? '');
  $role        = trim($_POST['role'] ?? '');
  $nid         = trim($_POST['nid'] ?? '');
  $password    = $_POST['password']  ?? '';
  $password2   = $_POST['password2'] ?? '';

  // Basic validations
  if ($full_name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $role === '') {
    $err = 'Please complete name, a valid email, and select a role.';
  } elseif (strlen($password) < 8) {
    $err = 'Password must be at least 8 characters.';
  } elseif ($password !== $password2) {
    $err = 'Passwords do not match.';
  } else {
    // Only now try to load DB config
    $dbFile = __DIR__ . '/../config/db.php';
    if (!is_file($dbFile)) {
      // Friendly message if DB config is missing
      $err = 'Registration is temporarily unavailable (database configuration missing). Please contact the system administrator.';
    } else {
      // Load the DB file; it might expose either pms_pdo() or a $pdo instance
      require_once $dbFile;

      try {
        // Prefer helper if present, otherwise fall back to a provided $pdo
        if (function_exists('pms_pdo')) {
          $pdo = pms_pdo();
        } elseif (!isset($pdo) || !($pdo instanceof PDO)) {
          throw new RuntimeException('Database connection is not available.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
          INSERT INTO authority_requests
            (full_name, email, phone, organization, nid, role, password_hash)
          VALUES
            (:full_name, :email, :phone, :organization, :nid, :role, :password_hash)
        ");
        $stmt->execute([
          ':full_name'     => $full_name,
          ':email'         => $email,
          ':phone'         => $phone,
          ':organization'  => $org,
          ':nid'           => $nid,
          ':role'          => $role,
          ':password_hash' => $hash,
        ]);

        $ok = 'Request submitted. Your account is pending approval. You can log in after approval using this email and password.';
        // clear the form values after success
        $full_name = $email = $phone = $org = $role = $nid = '';
      } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
          $err = 'This email is already registered.';
        } else {
          $err = 'Could not submit request. Please try again.';
        }
      } catch (Throwable $e) {
        $err = 'Could not submit request. Please try again.';
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Access — Port Authority • <?= htmlspecialchars($brand) ?></title>
  <link rel="stylesheet" href="/pms/register/register-authority.css?v=4">
</head>
<body>
  <header class="topbar">
    <div class="wrap">
      <div class="brand"><?= htmlspecialchars($brand) ?></div>
      <nav>
        <a href="/pms/home/index.php">Home</a>
        <a href="/pms/register/register.php">Back to Register</a>
        <a class="btn" href="/pms/login/login.php">Login</a>
      </nav>
    </div>
  </header>

  <main class="page">
    <a class="back" href="/pms/register/register.php">← Back</a>
    <h1>Request Access — <span>Port Authority</span></h1>
    <p class="sub">
      Port Director accounts are <strong>invite-only</strong>.
      Submit your request and our System Admin will review and contact you.
      Your account will remain <strong>pending</strong> until approved.
    </p>

    <?php if (!empty($err)): ?>
      <div class="alert error"><?= htmlspecialchars($err) ?></div>
    <?php elseif (!empty($ok)): ?>
      <div class="alert ok"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <form class="card form" method="post" novalidate>
      <div class="grid">
        <div class="field">
          <label>Full name</label>
          <input type="text" name="full_name" value="<?= htmlspecialchars($full_name ?? '') ?>" required>
        </div>
        <div class="field">
          <label>Official email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <div class="field">
          <label>Phone (optional)</label>
          <input type="text" name="phone" placeholder="+880 1XXXXXXXXX" value="<?= htmlspecialchars($phone ?? '') ?>">
        </div>
        <div class="field">
          <label>Organization / Authority</label>
          <input type="text" name="organization" placeholder="e.g., Chittagong Port Authority" value="<?= htmlspecialchars($org ?? '') ?>">
        </div>

        <div class="field">
          <label>Requested role</label>
          <select name="role" required>
            <option value="" <?= empty($role) ? 'selected' : '' ?>>Select...</option>
            <option value="Port Director" <?= (isset($role) && $role==='Port Director')?'selected':'' ?>>Port Director</option>
          </select>
        </div>
        <div class="field">
          <label>National ID (NID)</label>
          <input type="text" name="nid" placeholder="Enter your NID number" value="<?= htmlspecialchars($nid ?? '') ?>">
        </div>

        <!-- Passwords -->
        <div class="field">
          <label>Password</label>
          <div class="password-row">
            <input id="pass" type="password" name="password" minlength="8" required>
            <button class="toggle" type="button" aria-label="Show password">👁</button>
          </div>
          <small class="hint">Minimum 8 characters.</small>
        </div>
        <div class="field">
          <label>Confirm password</label>
          <div class="password-row">
            <input id="pass2" type="password" name="password2" minlength="8" required>
            <button class="toggle" type="button" aria-label="Show password">👁</button>
          </div>
          <small id="matchMsg" class="hint"></small>
        </div>
      </div>

      <div class="actions">
        <button class="btn-primary" type="submit">Submit Request</button>
      </div>
    </form>
  </main>

  <script src="/pms/register/register-authority.js?v=4" defer></script>
</body>
</html>
