<?php
/**
 * LeadDesk Mini - Admin Sidebar Include
 */
$current_page = basename($_SERVER['PHP_SELF']);
$admin_email  = isset($_SESSION['admin_email']) ? $_SESSION['admin_email'] : 'admin@leaddesk.com';
$admin_initials = strtoupper(substr($admin_email, 0, 2));
?>
<aside class="admin-sidebar" id="adminSidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-name">
            <span class="brand-dot" style="width:8px;height:8px;border-radius:50%;background:var(--brand);box-shadow:0 0 8px var(--brand);"></span>
            LeadDesk <span class="text-gradient">Admin</span>
        </div>
        <div class="sidebar-role">Sales Manager</div>
    </div>

    <!-- Nav -->
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="dashboard.php" class="sidebar-nav-link <?= ($current_page === 'dashboard.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="leads.php" class="sidebar-nav-link <?= ($current_page === 'leads.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-users"></i>
                <span>Leads</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link <?= ($current_page === 'analytics.php') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
        </li>
        <li class="sidebar-nav-item">
            <a href="#" class="sidebar-nav-link">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>

    <!-- New Lead button -->
    <div class="sidebar-new-lead">
        <a href="index.php" target="_blank" class="sidebar-new-lead-btn">
            <i class="fa-solid fa-plus"></i> New Lead
        </a>
    </div>

</aside>
