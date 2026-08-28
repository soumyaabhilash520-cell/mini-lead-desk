<?php
/**
 * LeadDesk Mini - Lead Management View & AJAX Data Provider
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();

$pdo = getDBConnection();

$filter = sanitize_input($_GET['filter'] ?? 'All');
$search = sanitize_input($_GET['search'] ?? '');
$isAjax = (!empty($_GET['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'));

$whereClauses = [];
$params       = [];

if ($filter !== 'All' && in_array($filter, ['New', 'Contacted', 'Closed'])) {
    $whereClauses[] = "status = :status";
    $params[':status'] = $filter;
}
if (!empty($search)) {
    $whereClauses[] = "(name LIKE :search OR email LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

$sql = "SELECT * FROM leads";
if (!empty($whereClauses)) $sql .= " WHERE " . implode(" AND ", $whereClauses);
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$leads = $stmt->fetchAll();

$total     = (int) $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$new       = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
$contacted = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();
$closed    = (int) $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Closed'")->fetchColumn();

$metrics = ['total' => $total, 'new' => $new, 'contacted' => $contacted, 'closed' => $closed];

function renderLeadRowsHtml($leads) {
    if (empty($leads)) {
        return '<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">
            <i class="fa-solid fa-inbox" style="font-size:1.8rem;opacity:.4;display:block;margin-bottom:.75rem;"></i>
            No matching leads found.
        </td></tr>';
    }

    $html = '';
    $csrfToken = get_csrf_token();

    foreach ($leads as $lead) {
        $leadJson   = htmlspecialchars(json_encode($lead), ENT_QUOTES, 'UTF-8');
        $msgPreview = htmlspecialchars(mb_strimwidth($lead['message'], 0, 50, '...'));

        $selClass = 'badge-new';
        if ($lead['status'] === 'Contacted') $selClass = 'badge-contacted';
        if ($lead['status'] === 'Closed')    $selClass = 'badge-closed';

        $html .= '<tr>';
        $html .= '<td style="color:var(--text-muted);font-size:.8rem;">#' . htmlspecialchars($lead['id']) . '</td>';
        $html .= '<td class="td-name">' . htmlspecialchars($lead['name']) . '</td>';
        $html .= '<td class="td-email"><a href="mailto:' . htmlspecialchars($lead['email']) . '">' . htmlspecialchars($lead['email']) . '</a></td>';
        $html .= '<td><span style="background:var(--bg-surface-2);border:1px solid var(--border);border-radius:4px;padding:.2rem .55rem;font-size:.75rem;color:var(--text-secondary);">' . htmlspecialchars($lead['budget']) . '</span></td>';
        $html .= '<td title="' . htmlspecialchars($lead['message']) . '">' . $msgPreview . '</td>';

        $html .= '<td>';
        $html .= '<select class="status-select ' . $selClass . '" data-id="' . $lead['id'] . '" data-original="' . htmlspecialchars($lead['status']) . '">';
        foreach (['New', 'Contacted', 'Closed'] as $st) {
            $sel = ($lead['status'] === $st) ? 'selected' : '';
            $html .= '<option value="' . $st . '" ' . $sel . '>' . $st . '</option>';
        }
        $html .= '</select></td>';

        $html .= '<td>' . format_date($lead['created_at']) . '</td>';
        $html .= '<td><div style="display:flex;gap:.4rem;">';
        $html .= '<button type="button" class="btn-table-action primary" onclick="openLeadModal(\'' . $leadJson . '\')" title="View"><i class="fa-regular fa-eye"></i></button>';
        $html .= '<form action="delete-lead.php" method="POST" style="display:inline;" onsubmit="return confirm(\'Delete lead #' . $lead['id'] . '?\')">';
        $html .= '<input type="hidden" name="csrf_token" value="' . $csrfToken . '">';
        $html .= '<input type="hidden" name="id" value="' . $lead['id'] . '">';
        $html .= '<button type="submit" class="btn-table-action danger" title="Delete"><i class="fa-regular fa-trash-can"></i></button>';
        $html .= '</form></div></td></tr>';
    }

    return $html;
}

if ($isAjax) {
    json_response(true, 'Leads loaded.', [
        'html'    => renderLeadRowsHtml($leads),
        'count'   => count($leads),
        'metrics' => $metrics
    ]);
}

$pageTitle        = "Lead Management — LeadDesk Mini";
$includeDashboardJs = true;

$admin_email = $_SESSION['admin_email'] ?? 'admin@leaddesk.com';
$admin_name  = ucfirst(explode('@', $admin_email)[0]);

include __DIR__ . '/includes/header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>

    <div class="admin-content">

        <!-- TOP BAR -->
        <header class="admin-topbar">
            <div class="topbar-search">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Search leads, campaigns, or metrics..." id="topbarSearchGlobal">
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

        <div class="admin-page-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <div class="page-title">Lead Management</div>
                    <div class="page-subtitle">Search, filter, inspect, and update lead statuses in real-time.</div>
                </div>
                <a href="export-leads.php?filter=<?= urlencode($filter) ?>&search=<?= urlencode($search) ?>" class="btn-ghost">
                    <i class="fa-solid fa-file-csv"></i> Export CSV
                </a>
            </div>

            <!-- Leads Table Card -->
            <div class="leads-table-card">
                <!-- Filter + Search Toolbar -->
                <div style="padding:1.1rem 1.5rem;border-bottom:1px solid var(--border);display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:1rem;">
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                        <button class="filter-pill <?= ($filter==='All')       ? 'active':'' ?>" data-filter="All">All Leads (<?= $metrics['total'] ?>)</button>
                        <button class="filter-pill <?= ($filter==='New')       ? 'active':'' ?>" data-filter="New">New (<?= $metrics['new'] ?>)</button>
                        <button class="filter-pill <?= ($filter==='Contacted') ? 'active':'' ?>" data-filter="Contacted">Contacted (<?= $metrics['contacted'] ?>)</button>
                        <button class="filter-pill <?= ($filter==='Closed')    ? 'active':'' ?>" data-filter="Closed">Closed (<?= $metrics['closed'] ?>)</button>
                    </div>
                    <div style="position:relative;min-width:220px;">
                        <i class="fa-solid fa-magnifying-glass" style="position:absolute;left:.85rem;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:.8rem;pointer-events:none;"></i>
                        <input type="text" id="searchLeadInput" class="form-control-custom"
                               style="padding-left:2.3rem;border-radius:var(--radius-pill);"
                               placeholder="Search by name or email..."
                               value="<?= htmlspecialchars($search) ?>">
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
                                <th>Message Preview</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="leadsTableBody">
                            <?= renderLeadRowsHtml($leads) ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /.admin-page-body -->

        <!-- Admin Footer -->
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

<!-- Lead Detail Modal -->
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
                            <div class="modal-info-value"><a id="modalLeadEmailLink" href="#" style="color:var(--clr-new);"></a></div>
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
                            <div id="modalLeadStatus" style="margin-top:.25rem;"></div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="modal-info-block">
                            <div class="modal-info-label">Message</div>
                            <div id="modalLeadMessage" style="white-space:pre-wrap;color:var(--text-secondary);font-size:.88rem;margin-top:.25rem;"></div>
                        </div>
                    </div>
                    <div class="col-md-6" style="font-size:.75rem;color:var(--text-muted);">
                        Created: <span id="modalLeadCreated" style="color:var(--text-secondary);font-weight:600;"></span>
                    </div>
                    <div class="col-md-6 text-md-end" style="font-size:.75rem;color:var(--text-muted);">
                        Last Updated: <span id="modalLeadUpdated" style="color:var(--text-secondary);font-weight:600;"></span>
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
