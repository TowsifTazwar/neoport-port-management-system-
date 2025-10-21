
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

$brand = "neoport";

// Allowed roles for operations staff
$roles = [
    "Harbor Master",
    "Customs & Compliance Officer",
    "Cargo & Warehouse Manager",
    "Logistics & Transport Coordinator"
];

// Get flash messages from session
$success_message = $_SESSION['flash_success'] ?? null;
$errors = $_SESSION['flash_errors'] ?? [];
unset($_SESSION['flash_success'], $_SESSION['flash_errors']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = []; // Re-initialize errors for this request

    // Honeypot check
    if (!empty($_POST['website'])) die('Spam detected.');

    // Sanitize and validate inputs
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $nid = trim($_POST['nid'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    // Role-specific fields
    $harbor_license = trim($_POST['harbor_license'] ?? '');
    $customs_badge = trim($_POST['customs_badge'] ?? '');
    $tin = trim($_POST['tin'] ?? '');
    $it_clearance = trim($_POST['it_clearance'] ?? '');

    // Basic validation
    if (empty($full_name) || empty($email) || empty($department) || empty($role) || empty($password)) {
        $errors[] = "Please fill in all required fields.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please provide a valid email address.";
    if ($password !== $password2) $errors[] = "Passwords do not match.";
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters long.";
    if (!in_array($role, $roles)) $errors[] = "Invalid role selected.";

    // Database checks
    if (empty($errors)) {
        try {
            $pdo = pms_pdo();
            $stmt = $pdo->prepare("SELECT id FROM staff_users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) $errors[] = "An account with this email already exists.";
        } catch (PDOException $e) {
            $errors[] = "Database connection error.";
        }
    }

    // Process registration
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $pdo->beginTransaction();

            // Insert into staff_users as pending
            $stmt = $pdo->prepare(
                "INSERT INTO staff_users (full_name, email, phone, department, role, password_hash, nid, notes, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
            );
            $stmt->execute([$full_name, $email, $phone, $department, $role, $password_hash, $nid, $notes]);
            $user_id = $pdo->lastInsertId();

            // Insert into role-specific table
            switch ($role) {
                case 'Harbor Master':
                    $stmt = $pdo->prepare("INSERT INTO harbor_masters (user_id, harbor_license) VALUES (?, ?)");
                    $stmt->execute([$user_id, $harbor_license]);
                    break;
                case 'Customs & Compliance Officer':
                    $stmt = $pdo->prepare("INSERT INTO customs_officers (user_id, customs_badge_id) VALUES (?, ?)");
                    $stmt->execute([$user_id, $customs_badge]);
                    break;
                case 'Cargo & Warehouse Manager':
                    $stmt = $pdo->prepare("INSERT INTO warehouse_managers (user_id) VALUES (?)");
                    $stmt->execute([$user_id]);
                    break;
                case 'Logistics & Transport Coordinator':
                    $stmt = $pdo->prepare("INSERT INTO logistics_coordinators (user_id) VALUES (?)");
                    $stmt->execute([$user_id]);
                    break;
            }

            $pdo->commit();
            $_SESSION['flash_success'] = "Request submitted. Your account is pending approval. You can log in after approval using this email and password.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: Could not process registration. " . $e->getMessage();
        }
    }

    if (!empty($errors)) $_SESSION['flash_errors'] = $errors;

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Request Access — Port Authority • <?= htmlspecialchars($brand) ?></title>
  <link rel="stylesheet" href="register-operations.css" />
</head>
<body>
  <!-- Top bar -->
  <header class="nav">
    <div class="container nav__row">
      <a href="/pms/home/index.php" class="brand"><?= htmlspecialchars($brand) ?></a>
      <nav class="nav__links">
        <a href="/pms/home/index.php">Home</a>
        <a href="/pms/register/register.php">Back to Register</a>
        <a href="/pms/login/login.php" class="btn btn--login">Login</a>
      </nav>
    </div>
  </header>

  <main class="container page">
    <a href="/pms/register/register.php" class="back-link" style="display: block; margin-bottom: 1rem; color: var(--c-text-secondary); text-decoration: none;">← Back</a>
    <h1 class="page__title">Request Access — Port Authority</h1>
    <p class="page__lead">
        Port Director & Deputy Director accounts are <strong>invite-only</strong>. Submit your request and our System Admin will review and contact you. Your account will remain <strong>pending</strong> until approved.
    </p>

    <!-- Banners -->
    <?php if ($success_message): ?>
      <div class="alert alert--success">
        <?= htmlspecialchars($success_message) ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
      <div class="alert alert--error">
        <strong>Error:</strong>
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form id="opsForm" class="card form" method="post" action="register-operations.php">
      <!-- Row 1 -->
      <div class="grid2">
        <label class="field">
          <span>Full name</span>
          <input type="text" name="full_name" required autocomplete="name" />
        </label>

        <label class="field">
          <span>Official email</span>
          <input type="email" name="email" required autocomplete="email" />
        </label>
      </div>

      <!-- Row 2 -->
      <div class="grid2">
        <label class="field">
          <span>Phone (official)</span>
          <input type="tel" name="phone" placeholder="+880 1XXXXXXXXX" />
        </label>

        <label class="field">
          <span>Department / Unit</span>
          <input type="text" name="department" placeholder="e.g., Harbor Operations, Customs, Finance" required />
        </label>
      </div>

      <!-- Role -->
      <label class="field">
        <span>Role</span>
        <select name="role" id="role" required>
          <option value="">Select…</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <!-- Password row -->
      <div class="grid2">
        <label class="field">
          <span>Password</span>
          <div class="pass-row">
            <input id="pass" type="password" name="password" minlength="8" required />
            <button class="peek" type="button" aria-label="Show password" data-target="pass">👁</button>
          </div>
          <small class="hint">Minimum 8 characters.</small>
        </label>

        <label class="field">
          <span>Confirm password</span>
          <div class="pass-row">
            <input id="pass2" type="password" name="password2" minlength="8" required />
            <button class="peek" type="button" aria-label="Show password" data-target="pass2">👁</button>
          </div>
        </label>
      </div>

      <!-- Conditional fields (unchanged) -->
      <div id="conditionalFields" class="grid2">
        <label class="field cond cond-harbor" hidden>
          <span>Harbor Master License No.</span>
          <input type="text" name="harbor_license" placeholder="e.g., HM-2025-001" />
        </label>

        <label class="field cond cond-customs" hidden>
          <span>Customs Badge / Authorization</span>
          <input type="text" name="customs_badge" placeholder="e.g., CUST-78342" />
        </label>

        <label class="field cond cond-finance" hidden>
          <span>TIN / Finance Ref (optional)</span>
          <input type="text" name="tin" placeholder="e.g., 1234567890" />
        </label>

        <label class="field cond cond-it" hidden>
          <span>IT Clearance Code</span>
          <input type="text" name="it_clearance" placeholder="Provide internal clearance code" />
        </label>
      </div>

      <!-- NID -->
      <label class="field">
        <span>National ID (NID) — optional</span>
        <input type="text" name="nid" placeholder="Enter NID (optional)" />
      </label>

      <!-- Notes -->
      <label class="field">
        <span>Notes / Justification</span>
        <textarea name="notes" rows="4" placeholder="Briefly describe your responsibilities and why you need access" required></textarea>
      </label>

      <!-- Honeypot -->
      <input type="text" name="website" class="hp" autocomplete="off" tabindex="-1" aria-hidden="true" />

      <div class="form__actions">
        <button type="submit" class="btn btn--primary">Submit Registration</button>
        <!-- Removed the “I already have an account” link as requested -->
      </div>
    </form>
  </main>

  <footer class="footer">
    <div class="container footer__row">
      <span>© <?= date('Y') ?> neoport</span>
      <nav class="footer__links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </nav>
    </div>
  </footer>

</body>
</html>
