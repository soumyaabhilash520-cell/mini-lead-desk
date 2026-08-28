<?php
/**
 * LeadDesk Mini - Public Navbar Component
 */
?>
<nav class="app-navbar">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand" href="index.php">
            LeadDesk Mini
            <span class="brand-dot"></span>
        </a>

        <div class="d-flex align-items-center gap-2 gap-md-4">
            <ul class="nav d-none d-md-flex">
                <li class="nav-item"><a href="index.php" class="nav-link-dark nav-link">Home</a></li>
                <li class="nav-item"><a href="#features" class="nav-link-dark nav-link">Features</a></li>
                <li class="nav-item"><a href="#contact" class="nav-link-dark nav-link">Pricing</a></li>
            </ul>

            <?php if (is_admin_logged_in()): ?>
                <a href="dashboard.php" class="btn-outline-brand" style="font-size:.82rem;">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-outline-brand" style="font-size:.82rem;">
                    Admin Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>
