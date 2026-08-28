<?php
/**
 * LeadDesk Mini - Public Lead Form Handler
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Check if request is AJAX
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) {
        json_response(false, 'Invalid request method.', [], 45);
    } else {
        header('Location: index.php');
        exit;
    }
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    if ($isAjax) {
        json_response(false, 'Invalid or expired CSRF token. Please refresh the page and try again.', [], 403);
    } else {
        $_SESSION['flash_error'] = 'Security validation failed (CSRF token invalid). Please try again.';
        header('Location: index.php#contact');
        exit;
    }
}

// 2. Extract and Sanitize Inputs
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$budget = sanitize_input($_POST['budget'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

// 3. Server-Side Validation
$errors = validate_lead_input($name, $email, $budget, $message);

if (!empty($errors)) {
    if ($isAjax) {
        json_response(false, 'Please correct the highlighted validation errors.', ['errors' => $errors], 422);
    } else {
        $_SESSION['flash_error'] = reset($errors);
        header('Location: index.php#contact');
        exit;
    }
}

// 4. Insert into MySQL Database using Prepared Statement
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        INSERT INTO leads (name, email, budget, message, status, created_at)
        VALUES (:name, :email, :budget, :message, 'New', NOW())
    ");

    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':budget' => $budget,
        ':message' => $message
    ]);

    $leadId = $pdo->lastInsertId();

    if ($isAjax) {
        json_response(true, 'Lead submitted successfully! Our team will contact you shortly.', [
            'lead_id' => $leadId
        ]);
    } else {
        $_SESSION['flash_success'] = 'Thank you! Your lead information has been submitted successfully.';
        header('Location: index.php#contact');
        exit;
    }

} catch (PDOException $e) {
    if ($isAjax) {
        json_response(false, 'A database error occurred while saving your submission. Please try again later.', [], 500);
    } else {
        $_SESSION['flash_error'] = 'Database error: Unable to process submission at this time.';
        header('Location: index.php#contact');
        exit;
    }
}
