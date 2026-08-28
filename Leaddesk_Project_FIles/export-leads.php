<?php
/**
 * LeadDesk Mini - Export Leads to CSV
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();

try {
    $pdo = getDBConnection();

    $filter = sanitize_input($_GET['filter'] ?? 'All');
    $search = sanitize_input($_GET['search'] ?? '');

    $whereClauses = [];
    $params = [];

    if ($filter !== 'All' && in_array($filter, ['New', 'Contacted', 'Closed'])) {
        $whereClauses[] = "status = :status";
        $params[':status'] = $filter;
    }

    if (!empty($search)) {
        $whereClauses[] = "(name LIKE :search OR email LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    $sql = "SELECT id, name, email, budget, message, status, created_at, updated_at FROM leads";
    if (!empty($whereClauses)) {
        $sql .= " WHERE " . implode(" AND ", $whereClauses);
    }
    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $leads = $stmt->fetchAll();

    // Set CSV download headers
    $filename = "leaddesk_leads_" . date('Y-m-d_His') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // CSV Header row
    fputcsv($output, ['Lead ID', 'Full Name', 'Email Address', 'Budget Range', 'Message', 'Status', 'Date Created', 'Last Updated']);

    // CSV Data rows
    foreach ($leads as $lead) {
        fputcsv($output, [
            $lead['id'],
            $lead['name'],
            $lead['email'],
            $lead['budget'],
            $lead['message'],
            $lead['status'],
            $lead['created_at'],
            $lead['updated_at']
        ]);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Error generating CSV export: " . htmlspecialchars($e->getMessage()));
}
