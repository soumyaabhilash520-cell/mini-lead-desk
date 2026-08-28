<?php
/**
 * LeadDesk Mini - Delete Lead Handler
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $id = filter_var($_POST['id'] ?? 0, FILTER_VALIDATE_INT);

    if (verify_csrf_token($token) && $id) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM leads WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = "Lead #{$id} deleted successfully.";
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Database error while deleting lead.";
        }
    } else {
        $_SESSION['flash_error'] = "Invalid CSRF token or lead ID.";
    }
}

header('Location: dashboard.php');
exit;
