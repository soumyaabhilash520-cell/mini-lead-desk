<?php
/**
 * LeadDesk Mini - Admin Login Portal
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

if (is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error_msg = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token    = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($token)) {
        $error_msg = "Security token validation failed. Please try again.";
    } elseif (empty($email) || empty($password)) {
        $error_msg = "Please fill in both email and password.";
    } else {
        try {
            $pdo  = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                login_admin($admin['id'], $admin['email']);
                header('Location: dashboard.php');
                exit;
            } else {
                $error_msg = "Invalid admin email or password. Please try again.";
            }
        } catch (PDOException $e) {
            $error_msg = "Database connection error. Please try again.";
        }
    }
}

$pageTitle = "Admin Login — LeadDesk Mini";
include __DIR__ . '/includes/header.php';
?>

<div class="login-wrapper">
    <div class="login-bg-glow"></div>

    <div class="login-card">
        <!-- Brand -->
        <div class="login-brand">
            <div style="display:inline-flex;align-items:center;gap:.5rem;margin-bottom:.35rem;">
                <span class="brand-dot" style="width:10px;height:10px;border-radius:50%;background:var(--brand);box-shadow:0 0 10px var(--brand);display:inline-block;"></span>
                <span class="login-brand-name">LeadDesk Mini</span>
            </div>
            <div class="login-brand-sub">Admin Management Authentication Portal</div>
        </div>

        <!-- Error -->
        <?php if ($error_msg): ?>
            <div class="alert-dark-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="login.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">

            <div style="margin-bottom:1rem;">
                <label for="email" class="form-label-custom">Admin Email Address</label>
                <input type="email" class="form-control-custom" id="email" name="email"
                       value="admin@leaddesk.com" placeholder="admin@leaddesk.com" required>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label for="password" class="form-label-custom">Password</label>
                <input type="password" class="form-control-custom" id="password" name="password"
                       value="admin123" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-brand w-100" style="justify-content:center;padding:.85rem;">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In to Admin Panel
            </button>

            <!-- Demo credentials -->
            <div class="demo-creds">
                <div class="demo-creds-label">
                    <i class="fa-solid fa-key"></i> Default Demo Credentials
                </div>
                <div class="demo-creds-text">
                    Email: <code>admin@leaddesk.com</code><br>
                    Password: <code>admin123</code>
                </div>
            </div>
        </form>

        <!-- Back link -->
        <div style="text-align:center;margin-top:1.5rem;">
            <a href="index.php" style="color:var(--text-muted);font-size:.8rem;font-weight:500;">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Landing Page
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
