<?php
/**
 * LeadDesk Mini - AJAX Status Update Handler
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Enforce admin login
if (!is_admin_logged_in()) {
    json_response(false, 'Unauthorized access. Please log in.', [], 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method.', [], 405);
}

// Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    json_response(false, 'Security check failed (Invalid CSRF token).', [], 403);
}

$id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);
$status = sanitize_input($_POST['status'] ?? '');
$allowed_statuses = ['New', 'Contacted', 'Closed'];

if (!$id || !in_array($status, $allowed_statuses)) {
    json_response(false, 'Invalid lead ID or status value provided.', [], 400);
}

try {
    $pdo = getDBConnection();
    
    // Check if lead exists
    $checkStmt = $pdo->prepare("SELECT id FROM leads WHERE id = :id");
    $checkStmt->execute([':id' => $id]);
    if (!$checkStmt->fetch()) {
        json_response(false, "Lead #{$id} not found.", [], 404);
    }

    // Update status
    $updateStmt = $pdo->prepare("UPDATE leads SET status = :status, updated_at = NOW() WHERE id = :id");
    $updateStmt->execute([
        ':status' => $status,
        ':id' => $id
    ]);

    // Recalculate metrics for live dashboard dynamic updates
    $total = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
    $new = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'New'")->fetchColumn();
    $contacted = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Contacted'")->fetchColumn();
    $closed = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'Closed'")->fetchColumn();

    json_response(true, "Lead #{$id} status updated successfully to '{$status}'.", [
        'lead_id' => $id,
        'new_status' => $status,
        'metrics' => [
            'total' => (int)$total,
            'new' => (int)$new,
            'contacted' => (int)$contacted,
            'closed' => (int)$closed
        ]
    ]);

} catch (PDOException $e) {
    json_response(false, 'Database error while updating lead status.', [], 500);
}
