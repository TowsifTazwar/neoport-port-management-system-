<?php
// pms/register/register-partners.php — External partner self‑registration
$brand = "Port Management System";
$roles = ["Shipping Company", "Importer", "Exporter", "Supplier / Vendor"];

$success = false;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Anti-honeypot
    if (!empty($_POST['hp'])) {
        http_response_code(400);
        die('Spam detected.');
    }

    // DB connection
    require_once '../config/db.php';
    $pdo = pms_pdo();

    // Validation
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $error_message = "Passwords do not match. Please try again.";
    } else {
        $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();

            // Get role_id from role_name
            $role_stmt = $pdo->prepare("SELECT id FROM partner_roles WHERE role_name = ?");
            $role_stmt->execute([$_POST['role']]);
            $role_id = $role_stmt->fetchColumn();

            if (!$role_id) {
                throw new Exception("Invalid role selected.");
            }

            // Insert directly into partners table
            $sql = "INSERT INTO partners (
                        role_id, company_name, trade_license, tax_id,
                        contact_name, contact_email, phone, website,
                        address, city, country, notes, password_hash
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $role_id,
                $_POST['company'],
                $_POST['trade_license'],
                $_POST['tax_id'],
                $_POST['contact_name'],
                $_POST['contact_email'],
                $_POST['phone'],
                !empty($_POST['website']) ? $_POST['website'] : null,
                $_POST['address'],
                $_POST['city'],
                $_POST['country'],
                !empty($_POST['notes']) ? $_POST['notes'] : null,
                $password_hash
            ]);
            $partner_id = $pdo->lastInsertId();

            // Insert into role-specific table
            $role = $_POST['role'];
            if ($role === 'Shipping Company' && !empty($_POST['scac_imo'])) {
                $stmt = $pdo->prepare("INSERT INTO shipping_companies (partner_id, scac_imo) VALUES (?, ?)");
                $stmt->execute([$partner_id, $_POST['scac_imo']]);
            } elseif ($role === 'Importer' && !empty($_POST['irc'])) {
                $stmt = $pdo->prepare("INSERT INTO importers (partner_id, irc) VALUES (?, ?)");
                $stmt->execute([$partner_id, $_POST['irc']]);
            } elseif ($role === 'Exporter' && !empty($_POST['erc'])) {
                $stmt = $pdo->prepare("INSERT INTO exporters (partner_id, erc) VALUES (?, ?)");
                $stmt->execute([$partner_id, $_POST['erc']]);
            } elseif ($role === 'Supplier / Vendor' && !empty($_POST['service_category'])) {
                $stmt = $pdo->prepare("INSERT INTO suppliers (partner_id, service_category) VALUES (?, ?)");
                $stmt->execute([$partner_id, $_POST['service_category']]);
            }

            $pdo->commit();
            $success = true;

        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() === '23000') { // 23000 = Integrity constraint violation (e.g. duplicate email)
                $error_message = "An account with this email address already exists.";
            } else {
                $error_message = "Database error: " . $e->getMessage();
            }
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register — Trade &amp; Service Partners • <?= htmlspecialchars($brand) ?></title>
  <link rel="stylesheet" href="register-partners.css" />
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
    <h1 class="page__title">Register — Trade &amp; Service Partners</h1>
    <p class="page__lead">
      Create a business account for Shipping Companies, Importers, Exporters, or Suppliers/Vendors.
    </p>

    <?php if ($success): ?>
      <!-- Success banner -->
      <div class="alert alert--success">
        ✅ Request granted.
      </div>
    <?php else: ?>
      <!-- Error banner -->
      <?php if (!empty($error_message)): ?>
        <div class="alert alert--danger">
          <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>

      <form id="partnerForm" class="card form" method="post" action="register-partners.php">
      <!-- Role + company -->
      <div class="grid2">
        <label class="field">
          <span>Registering as</span>
          <select name="role" id="role" required>
            <option value="">Select…</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?= htmlspecialchars($r) ?>"><?= htmlspecialchars($r) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label class="field">
          <span>Company / Business name</span>
          <input type="text" name="company" placeholder="Legal business name" required />
        </label>
      </div>

      <!-- Business identifiers -->
      <div class="grid2">
        <label class="field">
          <span>Trade License No.</span>
          <input type="text" name="trade_license" placeholder="e.g., TL-2025-12345" required />
        </label>

        <label class="field">
          <span>TIN / VAT / BIN</span>
          <input type="text" name="tax_id" placeholder="e.g., 1234567890" required />
        </label>
      </div>

      <!-- Conditional IDs based on role -->
      <div id="conditionalFields" class="grid2">
        <!-- Shipping Company -->
        <label class="field cond cond-ship" hidden>
          <span>SCAC / IMO / Agent Code (any)</span>
          <input type="text" name="scac_imo" placeholder="e.g., SCAC: ABCD / IMO: 1234567" />
        </label>

        <!-- Importer -->
        <label class="field cond cond-importer" hidden>
          <span>Importer Registration (IRC)</span>
          <input type="text" name="irc" placeholder="e.g., IRC-0001234" />
        </label>

        <!-- Exporter -->
        <label class="field cond cond-exporter" hidden>
          <span>Exporter Registration (ERC)</span>
          <input type="text" name="erc" placeholder="e.g., ERC-0005678" />
        </label>

        <!-- Supplier / Vendor -->
        <label class="field cond cond-vendor" hidden>
          <span>Service Category</span>
          <input type="text" name="service_category" placeholder="e.g., Cranes, Fuel, Repairs" />
        </label>
      </div>

      <!-- Contacts -->
      <div class="grid2">
        <label class="field">
          <span>Primary contact person</span>
          <input type="text" name="contact_name" placeholder="Full name" required />
        </label>

        <label class="field">
          <span>Contact email</span>
          <input type="email" name="contact_email" placeholder="name@company.com" required />
        </label>
      </div>

      <div class="grid2">
        <label class="field">
          <span>Contact phone</span>
          <input type="tel" name="phone" placeholder="+880 1XXXXXXXXX" required />
        </label>

        <label class="field">
          <span>Company website (optional)</span>
          <input type="url" name="website" placeholder="https://example.com" />
        </label>
      </div>

      <!-- Address -->
      <div class="grid3">
        <label class="field">
          <span>Address line</span>
          <input type="text" name="address" placeholder="Street, area" required />
        </label>

        <label class="field">
          <span>City</span>
          <input type="text" name="city" required />
        </label>

        <label class="field">
          <span>Country</span>
          <input type="text" name="country" value="Bangladesh" required />
        </label>
      </div>

      <!-- Passwords -->
      <div class="grid2">
        <label class="field">
          <span>Password</span>
          <div class="pw">
            <input type="password" id="password" name="password" minlength="8" placeholder="Minimum 8 characters" required />
            <button class="pw-toggle" type="button" aria-label="Show password" data-target="password">👁</button>
          </div>
          <small class="hint">Minimum 8 characters.</small>
        </label>

        <label class="field">
          <span>Confirm password</span>
          <div class="pw">
            <input type="password" id="password_confirm" name="password_confirm" minlength="8" required />
            <button class="pw-toggle" type="button" aria-label="Show password" data-target="password_confirm">👁</button>
          </div>
        </label>
      </div>

      <!-- Notes -->
      <label class="field">
        <span>Notes (optional)</span>
        <textarea name="notes" rows="4" placeholder="Anything the port should know (e.g., operating capacity, special handling, etc.)"></textarea>
      </label>

      <!-- Honeypot -->
      <input type="text" name="hp" class="hp" autocomplete="off" tabindex="-1" aria-hidden="true" />

      <div class="form__actions">
        <button type="submit" class="btn btn--primary">Submit Registration</button>
      </div>
    </form>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="container footer__row">
      <span>© <?= date('Y') ?> Port Management System</span>
      <nav class="footer__links">
        <a href="#">Privacy Policy</a>
        <a href="#">Terms of Service</a>
      </nav>
    </div>
  </footer>

  <script src="register-partners.js"></script>
</body>
</html>
