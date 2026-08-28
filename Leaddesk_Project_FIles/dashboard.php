<?php
/**
 * LeadDesk Mini - Main Admin Dashboard
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();

$pdo = getDBConnection();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$totalLeads     = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$newLeads       = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
$contactedLeads = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();
$closedLeads    = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Closed'")->fetchColumn();

$b1 = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE budget = 'Under \$500'")->fetchColumn();
$b2 = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE budget = '\$500–\$1,000'")->fetchColumn();
$b3 = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE budget = '\$1,000–\$5,000'")->fetchColumn();
$b4 = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE budget = 'Over \$5,000'")->fetchColumn();

$recentLeads = $pdo->query("SELECT * FROM leads ORDER BY id DESC LIMIT 15")->fetchAll();

$admin_email = $_SESSION['admin_email'] ?? 'admin@leaddesk.com';
$admin_name  = explode('@', $admin_email)[0];
$admin_name  = ucfirst($admin_name);

$pageTitle        = "Admin Dashboard — LeadDesk Mini";
$includeDashboardJs = true;

include __DIR__ . '/includes/header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-content">

        <!-- ── TOP BAR ─────────────────────────────────────────────────── -->
        <header class="admin-topbar">
            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Search leads, campaigns, or metrics..." id="topbarSearch">
            </div>

            <div class="topbar-spacer"></div>

            <div class="topbar-actions">
                <button class="topbar-icon-btn" title="Notifications">
                    <i class="fa-solid fa-bell"></i>
                </button>
                <button class="topbar-icon-btn" id="themeToggleBtn" title="Toggle Theme">
                    <i class="fa-solid fa-moon theme-toggle-icon"></i>
                </button>
                <div class="topbar-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
                <div>
                    <div class="topbar-user-name"><?= htmlspecialchars($admin_name) ?></div>
                    <div class="topbar-user-role">
                        <a href="logout.php" style="color:var(--text-muted);font-size:.7rem;">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── PAGE BODY ───────────────────────────────────────────────── -->
        <div class="admin-page-body">

            <?php if ($flash_success): ?>
                <div class="alert-dark-danger" style="border-color:rgba(52,211,153,.25);background:rgba(52,211,153,.1);color:#6ee7b7;margin-bottom:1.25rem;">
                    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($flash_success) ?>
                </div>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <div class="alert-dark-danger" style="margin-bottom:1.25rem;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($flash_error) ?>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <div class="page-title">Admin Dashboard</div>
                    <div class="page-subtitle">Welcome back, <?= htmlspecialchars($admin_name) ?>. Here's what's happening today.</div>
                </div>
                <a href="export-leads.php" class="btn-ghost">
                    <i class="fa-solid fa-file-csv"></i> Export Report
                </a>
            </div>

            <!-- ── METRIC CARDS ─────────────────────────────────────── -->
            <div class="metrics-grid">
                <!-- Total Leads -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-card-label">Total Leads</span>
                        <div class="metric-card-icon ic-blue"><i class="fa-solid fa-users"></i></div>
                    </div>
                    <div class="metric-card-value" id="metricTotal"><?= number_format($totalLeads) ?></div>
                    <span class="metric-card-trend trend-up">
                        <span class="trend-icon">▲</span> +12%
                    </span>
                </div>

                <!-- New Submissions -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-card-label">New Submissions</span>
                        <div class="metric-card-icon ic-orange"><i class="fa-regular fa-envelope"></i></div>
                    </div>
                    <div class="metric-card-value" id="metricNew"><?= number_format($newLeads) ?></div>
                    <span class="metric-card-trend trend-up">
                        <span class="trend-icon">▲</span> +5%
                    </span>
                </div>

                <!-- Contacted -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-card-label">Contacted</span>
                        <div class="metric-card-icon ic-teal"><i class="fa-solid fa-phone"></i></div>
                    </div>
                    <div class="metric-card-value" id="metricContacted"><?= number_format($contactedLeads) ?></div>
                    <span class="metric-card-trend trend-down">
                        <span class="trend-icon">▼</span> -2%
                    </span>
                </div>

                <!-- Closed Deals -->
                <div class="metric-card">
                    <div class="metric-card-header">
                        <span class="metric-card-label">Closed Deals</span>
                        <div class="metric-card-icon ic-green"><i class="fa-solid fa-circle-check"></i></div>
                    </div>
                    <div class="metric-card-value" id="metricClosed"><?= number_format($closedLeads) ?></div>
                    <span class="metric-card-trend trend-up">
                        <span class="trend-icon">▲</span> +18%
                    </span>
                </div>
            </div>

            <!-- ── CHARTS ───────────────────────────────────────────── -->
            <div class="charts-grid">
                <!-- Budget Distribution -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">Budget Distribution</span>
                        <span class="chart-card-badge">Quarterly View</span>
                    </div>
                    <div style="height:240px;position:relative;">
                        <canvas id="budgetChart"
                                data-b1="<?= $b1 ?>"
                                data-b2="<?= $b2 ?>"
                                data-b3="<?= $b3 ?>"
                                data-b4="<?= $b4 ?>"></canvas>
                    </div>
                </div>

                <!-- Status Breakdown -->
                <div class="chart-card">
                    <div class="chart-card-header">
                        <span class="chart-card-title">Status Breakdown</span>
                    </div>
                    <div style="height:200px;position:relative;">
                        <canvas id="statusChart"
                                data-new="<?= $newLeads ?>"
                                data-contacted="<?= $contactedLeads ?>"
                                data-closed="<?= $closedLeads ?>"></canvas>
                    </div>
                    <div class="text-center" style="font-size:1.3rem;font-weight:800;color:var(--text-primary);margin:.5rem 0 .1rem;">
                        <?= number_format($totalLeads) ?>
                        <div style="font-size:.75rem;color:var(--text-muted);font-weight:400;">Total</div>
                    </div>
                    <div class="chart-legend justify-content-center">
                        <span class="legend-item"><span class="legend-dot" style="background:var(--text-muted);"></span> Cold</span>
                        <span class="legend-item"><span class="legend-dot" style="background:var(--brand);"></span> Hot</span>
                        <span class="legend-item"><span class="legend-dot" style="background:var(--clr-contacted);"></span> Warm</span>
                        <span class="legend-item"><span class="legend-dot" style="background:#f87171;"></span> Lost</span>
                    </div>
                </div>
            </div>

            <!-- ── RECENT ACTIVITY ──────────────────────────────────── -->
            <div class="activity-card">
                <div class="activity-header">
                    <span class="activity-title">Recent Activity</span>
                    <div style="display:flex;align-items:center;gap:1rem;">
                        <a href="leads.php" class="activity-view-all">View All</a>
                        <a href="leads.php" class="fab-new-lead" title="New Lead" style="text-decoration:none;">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </div>
                </div>

                <?php if (empty($recentLeads)): ?>
                    <div class="text-center py-5" style="color:var(--text-muted);">
                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block" style="opacity:.4;"></i>
                        No leads yet. Submit a lead from the public page!
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($recentLeads, 0, 5) as $lead):
                        $initials = strtoupper(mb_substr($lead['name'], 0, 1));
                        $msgPreview = mb_strimwidth($lead['message'], 0, 90, '...');
                        $timeAgo = format_date($lead['created_at']);
                        $statusClass = 'badge-new';
                        if ($lead['status'] === 'Contacted') $statusClass = 'badge-contacted';
                        if ($lead['status'] === 'Closed') $statusClass = 'badge-closed';
                    ?>
                        <div class="activity-item">
                            <div class="activity-avatar">
                                <?= $initials ?>
                                <?php if ($lead['status'] === 'New'): ?>
                                    <span class="activity-online-dot"></span>
                                <?php endif; ?>
                            </div>
                            <div class="activity-body">
                                <div class="activity-text">
                                    <strong><?= htmlspecialchars($lead['name']) ?></strong>
                                    submitted a new lead for
                                    <span class="highlight-green"><?= htmlspecialchars($lead['budget']) ?></span>.
                                </div>
                                <?php if ($lead['message']): ?>
                                    <div class="activity-quote">"<?= htmlspecialchars($msgPreview) ?>"</div>
                                <?php endif; ?>
                                <div class="activity-tags">
                                    <span class="activity-tag badge-status <?= $statusClass ?>"><?= htmlspecialchars($lead['status']) ?></span>
                                    <span class="activity-tag tag-budget"><?= htmlspecialchars($lead['budget']) ?></span>
                                </div>
                            </div>
                            <div class="activity-time"><?= $timeAgo ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- ── LEADS TABLE (filter & search) ───────────────────── -->
            <div class="leads-table-card">
                <!-- Toolbar -->
                <div style="padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                        <button class="filter-pill active" data-filter="All">All Leads (<?= $totalLeads ?>)</button>
                        <button class="filter-pill" data-filter="New">New (<?= $newLeads ?>)</button>
                        <button class="filter-pill" data-filter="Contacted">Contacted (<?= $contactedLeads ?>)</button>
                        <button class="filter-pill" data-filter="Closed">Closed (<?= $closedLeads ?>)</button>
                    </div>
                    <div style="position:relative;min-width:220px;">
                        <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem;pointer-events:none;"></i>
                        <input type="text" id="searchLeadInput" class="form-control-custom"
                               style="padding-left:2.3rem;border-radius:var(--radius-pill);"
                               placeholder="Real-time search by name or email...">
                    </div>
                </div>

                <!-- Table -->
                <div class="custom-table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Budget</th>
                                <th>Preview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody">
                            <?php if (empty($recentLeads)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5" style="color:var(--text-muted);">
                                        <i class="fa-solid fa-inbox fa-2x mb-2 d-block" style="opacity:.4;"></i>
                                        No leads submitted yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentLeads as $lead):
                                    $leadJson   = htmlspecialchars(json_encode($lead), ENT_QUOTES, 'UTF-8');
                                    $msgPreview = htmlspecialchars(mb_strimwidth($lead['message'], 0, 50, '...'));
                                    $selClass   = 'badge-new';
                                    if ($lead['status'] === 'Contacted') $selClass = 'badge-contacted';
                                    if ($lead['status'] === 'Closed')    $selClass = 'badge-closed';
                                ?>
                                    <tr>
                                        <td style="color:var(--text-muted);font-size:.8rem;">#<?= $lead['id'] ?></td>
                                        <td class="td-name"><?= htmlspecialchars($lead['name']) ?></td>
                                        <td class="td-email">
                                            <a href="mailto:<?= htmlspecialchars($lead['email']) ?>">
                                                <?= htmlspecialchars($lead['email']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span style="background:var(--bg-surface-2);border:1px solid var(--border);border-radius:4px;padding:.2rem .55rem;font-size:.75rem;color:var(--text-secondary);">
                                                <?= htmlspecialchars($lead['budget']) ?>
                                            </span>
                                        </td>
                                        <td title="<?= htmlspecialchars($lead['message']) ?>"><?= $msgPreview ?></td>
                                        <td>
                                            <select class="status-select <?= $selClass ?>"
                                                    data-id="<?= $lead['id'] ?>"
                                                    data-original="<?= htmlspecialchars($lead['status']) ?>">
                                                <option value="New"       <?= ($lead['status'] === 'New')       ? 'selected' : '' ?>>New</option>
                                                <option value="Contacted" <?= ($lead['status'] === 'Contacted') ? 'selected' : '' ?>>Contacted</option>
                                                <option value="Closed"    <?= ($lead['status'] === 'Closed')    ? 'selected' : '' ?>>Closed</option>
                                            </select>
                                        </td>
                                        <td><?= format_date($lead['created_at']) ?></td>
                                        <td>
                                            <div style="display:flex;gap:.4rem;">
                                                <button type="button" class="btn-table-action primary"
                                                        onclick="openLeadModal('<?= $leadJson ?>')" title="View">
                                                    <i class="fa-regular fa-eye"></i>
                                                </button>
                                                <form action="delete-lead.php" method="POST" style="display:inline;"
                                                      onsubmit="return confirm('Delete lead #<?= $lead['id'] ?>?');">
                                                    <input type="hidden" name="csrf_token" value="<?= get_csrf_token() ?>">
                                                    <input type="hidden" name="id" value="<?= $lead['id'] ?>">
                                                    <button type="submit" class="btn-table-action danger" title="Delete">
                                                        <i class="fa-regular fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /.admin-page-body -->

        <!-- ── ADMIN FOOTER ─────────────────────────────────────────── -->
        <footer class="admin-footer">
            <div>
                <span class="af-brand">LeadDesk Mini</span>
                <div class="af-copy">&copy; 2024 LeadDesk Mini. Built for Digital Heroes Training Task</div>
            </div>
            <div class="af-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </footer>

    </div><!-- /.admin-content -->
</div><!-- /.admin-wrapper -->

<!-- ── LEAD DETAIL MODAL ─────────────────────────────────────────────── -->
<div class="modal fade" id="leadDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content-dark modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title">
                    Lead Details <span id="modalLeadId" style="color:var(--brand);"></span>
                </h5>
                <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Full Name</div>
                            <div class="modal-info-value" id="modalLeadName"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Email Address</div>
                            <div class="modal-info-value">
                                <a id="modalLeadEmailLink" href="#" style="color:var(--clr-new);"></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Budget Range</div>
                            <div class="modal-info-value" id="modalLeadBudget"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Current Status</div>
                            <div id="modalLeadStatus" class="mt-1"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Message</div>
                            <div id="modalLeadMessage" style="white-space:pre-wrap;color:var(--text-secondary);font-size:.88rem;margin-top:.25rem;"></div>
                        </div>
                    </div>
                    <div class="col-md-6" style="font-size:.75rem;color:var(--text-muted);">
                        Submitted: <span id="modalLeadCreated" style="color:var(--text-secondary);font-weight:600;"></span>
                    </div>
                    <div class="col-md-6 text-md-end" style="font-size:.75rem;color:var(--text-muted);">
                        Updated: <span id="modalLeadUpdated" style="color:var(--text-secondary);font-weight:600;"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-ghost" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
