<?php
// /pms/login/login.php
if (session_status() === PHP_SESSION_NONE) session_start();

$err = $_GET['err'] ?? '';
$msg = $_GET['msg'] ?? '';

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  // Validate input
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($pass)) {
    header('Location: /pms/login/login.php?err=invalid'); exit;
  }

  // Connect to the database
  require_once __DIR__ . '/../config/db.php';
  $pdo = pms_pdo();

  // --- STEP 2: Attempt to log in as a System Admin ---
  // We check the 'system_admin' table first.
  $stmt = $pdo->prepare("SELECT * FROM system_admin WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      // An account with this email exists in the system_admin table.
      // Now, we verify the password hash.
      if (password_verify($pass, $user['password'])) {
          // SUCCESS: The password is correct for the system admin.
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['role'] = $user['role'];
          $_SESSION['user'] = [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'user_type' => 'system_admin',
            'role'      => $user['role'],
          ];
          // Redirect to the system admin dashboard.
          header('Location: /pms/system_admin/system_admin.php');
          exit;
      }
      
      // Special handling for the initial admin user if the password is 'admin0000'
      // This is to fix the password hash issue from the initial setup.
      if ($user['email'] === 'admin@gmail.com' && $pass === 'admin0000') {
          // The password is the old plain-text one. Let's update the hash.
          $new_hash = password_hash('admin0000', PASSWORD_DEFAULT);
          $update_stmt = $pdo->prepare("UPDATE system_admin SET password = :hash WHERE id = :id");
          $update_stmt->execute([':hash' => $new_hash, ':id' => $user['id']]);

          // Now that the hash is updated, we can log the user in.
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['role'] = $user['role'];
          $_SESSION['user'] = [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'user_type' => 'system_admin',
            'role'      => $user['role'],
          ];
          header('Location: /pms/system_admin/system_admin.php');
          exit;
      }
  }

  // --- STEP 3: Attempt to log in as an Authority ---
  $stmt = $pdo->prepare("SELECT * FROM authorities WHERE email = :email LIMIT 1");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      // Check status before password verification
      if ($user['status'] === 'suspended') {
          header('Location: /pms/login/login.php?err=suspended');
          exit;
      }

      if (password_verify($pass, $user['password_hash'])) {
          $_SESSION['user_id'] = $user['id'];
          // If the role is empty, default it to 'Director' for the session
          $user_role = !empty($user['role']) ? $user['role'] : 'Director';
          $_SESSION['role'] = $user_role;
          $_SESSION['user'] = [
            'id'        => $user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'user_type' => 'authority',
            'role'      => $user_role,
          ];
          
          // Redirect based on authority role
          // Use stripos for a case-insensitive check to see if 'Director' is in the role name
          if (stripos($user_role, 'Director') !== false) {
              header('Location: /pms/dashboard/port_director/director.php');
          } else {
              // Other authorities are redirected to the main home page
              header('Location: /pms/home/index.php?msg=login_success');
          }
          exit;
      }
  }
  
  // --- NEW STEP: Attempt to log in as a Shipping Agent ---
  $stmt = $pdo->prepare("
    SELECT sa.*, p.company_name 
    FROM shipping_agents sa
    JOIN partners p ON sa.partner_id = p.id
    WHERE sa.email = :email 
    LIMIT 1
  ");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      if (password_verify($pass, $user['password_hash'])) {
          // Agent found, log them in
          $_SESSION['user_id'] = $user['id'];
          $_SESSION['role'] = 'Shipping Agent';
          $_SESSION['user'] = [
              'id'           => $user['id'],
              'email'        => $user['email'],
              'full_name'    => $user['full_name'],
              'partner_id'   => $user['partner_id'],
              'company_name' => $user['company_name'],
              'user_type'    => 'shipping_agent',
              'role'         => 'Shipping Agent',
              'agent_id'     => $user['id'],
          ];
          
          // Redirect to the new shipping agent dashboard
          header('Location: /pms/dashboard/shipping_agent/shipping.php');
          exit;
      }
  }

  // --- STEP 4: Attempt to log in as a Partner (Shipping Company, Importer, etc.) ---
  $stmt = $pdo->prepare("
      SELECT p.*, pr.role_name
      FROM partners p
      JOIN partner_roles pr ON p.role_id = pr.id
      WHERE p.contact_email = :email
      LIMIT 1
  ");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      if (password_verify($pass, $user['password_hash'])) {
          // Check if partner is suspended
          $susp_stmt = $pdo->prepare("SELECT 1 FROM suspended_users WHERE user_id = :user_id AND user_type = 'partner'");
          $susp_stmt->execute([':user_id' => $user['id']]);
          if ($susp_stmt->fetch()) {
              header('Location: /pms/login/login.php?err=suspended');
              exit;
          }

          $_SESSION['user_id'] = $user['id'];
          $_SESSION['role'] = $user['role_name'];
          
          $agent_id = 0;
          if ($user['role_name'] === 'Shipping Company') {
              // Prioritize the primary contact agent
              $stmt = $pdo->prepare("SELECT id FROM shipping_agents WHERE partner_id = ? AND is_primary_contact = 1");
              $stmt->execute([$user['id']]);
              $agent = $stmt->fetch(PDO::FETCH_ASSOC);

              if ($agent) {
                  $agent_id = $agent['id'];
              } else {
                  // If no primary, fall back to the first agent found
                  $stmt = $pdo->prepare("SELECT id FROM shipping_agents WHERE partner_id = ? ORDER BY id ASC LIMIT 1");
                  $stmt->execute([$user['id']]);
                  $agent = $stmt->fetch(PDO::FETCH_ASSOC);
                  if ($agent) {
                      $agent_id = $agent['id'];
                  }
              }
          }

          $_SESSION['user'] = [
              'id'           => $user['id'],
              'email'        => $user['contact_email'],
              'full_name'    => $user['contact_name'],
              'company_name' => $user['company_name'],
              'user_type'    => 'partner',
              'role'         => $user['role_name'],
              'partner_id'   => $user['id'],
              'agent_id'     => $agent_id,
          ];

          // Redirect based on role
          if ($user['role_name'] === 'Shipping Company') {
              header('Location: /pms/dashboard/shipping_agent/shipping.php');
          } else if ($user['role_name'] === 'Importer') {
              $_SESSION['user']['user_type'] = 'importer';
              header('Location: /pms/dashboard/importer/importer.php');
          } else if ($user['role_name'] === 'Exporter') {
              $_SESSION['user']['user_type'] = 'exporter';
              header('Location: /pms/dashboard/exporter/exporter.php');
          } else {
              // Redirect other partners to a general dashboard or home
              header('Location: /pms/home/index.php?msg=login_success');
          }
          exit;
      }
  }

  // --- STEP 5: Attempt to log in as Staff ---
  $stmt = $pdo->prepare("SELECT * FROM staff_users WHERE email = :email AND status = 'approved' LIMIT 1");
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
      if (password_verify($pass, $user['password_hash'])) {
          $_SESSION['user_id'] = $user['id'];

          // Redirect based on staff role
          if ($user['role'] === 'Harbor Master') {
              $_SESSION['role'] = 'Harbor Master'; // Standardized role
              $_SESSION['user'] = [
                'id'        => $user['id'],
                'email'     => $user['email'],
                'full_name' => $user['full_name'],
                'user_type' => 'staff',
                'role'      => 'Harbor Master',
                'department'=> $user['department'],
              ];
              header('Location: /pms/dashboard/harbor/harbor.php');
              exit;
          } else {
              // For other staff roles
              $_SESSION['role'] = $user['role'];
              $_SESSION['user'] = [
                'id'        => $user['id'],
                'email'     => $user['email'],
                'full_name' => $user['full_name'],
                'user_type' => 'staff',
                'role'      => $user['role'],
                'department'=> $user['department'],
              ];
              
              // Redirect staff to their specific dashboards
              $role_dashboard_map = [
                "Customs & Compliance Officer" => "/pms/dashboard/customs/customs.php",
                "Cargo & Warehouse Manager" => "/pms/dashboard/cargo/cargo.php",
                "Logistics & Transport Coordinator" => "/pms/dashboard/logistics/logistics.php",
                "Finance & Billing Officer" => "/pms/dashboard/finance/finance.php"
              ];

              if (isset($role_dashboard_map[$user['role']])) {
                  header('Location: ' . $role_dashboard_map[$user['role']]);
              } else {
                  header('Location: /pms/home/index.php?msg=login_success');
              }
              exit;
          }
      }
  }

  // --- STEP 6: Check for Pending Requests ---
  $stmt = $pdo->prepare("SELECT 1 FROM authority_requests WHERE email = :email AND status = 'pending'");
  if ($stmt->execute([':email' => $email]) && $stmt->fetch()) {
      header('Location: /pms/login/login.php?err=pending');
      exit;
  }
  
  $stmt = $pdo->prepare("SELECT 1 FROM staff_users WHERE email = :email AND status = 'pending'");
  if ($stmt->execute([':email' => $email]) && $stmt->fetch()) {
      header('Location: /pms/login/login.php?err=pending');
      exit;
  }
  

  // --- STEP 7: All Checks Failed ---
  // If no user was found or if passwords did not match, we return a generic error.
  header('Location: /pms/login/login.php?err=bad');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale-1" />
  <title>Login • neoport</title>
  <link rel="stylesheet" href="/pms/login/login.css?v=2">
</head>
<body>
  <div class="split">
    <!-- Left Hero -->
    <aside class="hero">
      <img src="/pms/login/login-hero.png" alt="Port cranes and containers" />
      <div class="hero__overlay"></div>
      <div class="hero__copy">
        <h1>neoport</h1>
        <p>Secure access for Authority, Operations, and Partners.</p>
      </div>
    </aside>

    <!-- Right Form -->
    <main class="panel">
      <header class="panel__top">
        <a class="link" href="/pms/home/index.php">← Back to Home</a>
      </header>

      <section class="card">
        <h2>Sign in</h2>
        <p class="muted">Use your email and password to continue.</p>

        <?php if ($err === 'invalid'): ?>
          <div class="alert">Please enter a valid email and password.</div>
        <?php elseif ($err === 'bad'): ?>
          <div class="alert">Email or password is incorrect.</div>
        <?php elseif ($err === 'suspended'): ?>
          <div class="alert">You are suspended. Invalid requests.</div>
        <?php elseif ($err === 'pending'): ?>
          <div class="alert warn">Your account is pending approval.</div>
        <?php elseif ($msg): ?>
          <div class="alert info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form class="form" method="post" novalidate>
          <label class="field">
            <span>Email</span>
            <input type="email" name="email" placeholder="you@example.com" required />
          </label>

          <label class="field">
            <span>Password</span>
            <div class="pwd-row">
              <input id="password" type="password" name="password" placeholder="••••••••" minlength="8" required />
              <button type="button" class="pwd-toggle" aria-label="Show password">👁</button>
            </div>
          </label>

          <button class="btn-primary" type="submit">Sign in</button>
        </form>

        <div class="small-links">
          <a href="/pms/register/register.php">Create account</a>
          <span>•</span>
          <a href="#" onclick="alert('Ask the System Admin to reset your password.');return false;">Forgot password?</a>
        </div>
      </section>
    </main>
  </div>

  <script src="/pms/login/login.js?v=2" defer></script>
</body>
</html>
