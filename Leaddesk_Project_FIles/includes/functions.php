<?php
/**
 * LeadDesk Mini - Core Helper Functions
 */

/**
 * Sanitizes raw string input to prevent XSS and clean data
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Validates lead submission fields according to PRD criteria
 */
function validate_lead_input($name, $email, $budget, $message) {
    $errors = [];
    $allowed_budgets = [
        'Under $500',
        '$500–$1,000',
        '$1,000–$5,000',
        'Over $5,000'
    ];

    // Name Validation (3-100 characters)
    $name_len = mb_strlen(trim($name));
    if (empty($name)) {
        $errors['name'] = 'Full Name is required.';
    } elseif ($name_len < 3 || $name_len > 100) {
        $errors['name'] = 'Full Name must be between 3 and 100 characters.';
    }

    // Email Validation
    if (empty($email)) {
        $errors['email'] = 'Email Address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    // Budget Validation
    if (empty($budget)) {
        $errors['budget'] = 'Please select a budget range.';
    } elseif (!in_array($budget, $allowed_budgets)) {
        $errors['budget'] = 'Selected budget option is invalid.';
    }

    // Message Validation (10-1000 characters)
    $msg_len = mb_strlen(trim($message));
    if (empty($message)) {
        $errors['message'] = 'Message is required.';
    } elseif ($msg_len < 10) {
        $errors['message'] = 'Message must be at least 10 characters long.';
    } elseif ($msg_len > 1000) {
        $errors['message'] = 'Message cannot exceed 1000 characters.';
    }

    return $errors;
}

/**
 * Renders modern badge HTML for lead status
 */
function render_status_badge($status) {
    $class_map = [
        'New' => 'badge-new',
        'Contacted' => 'badge-contacted',
        'Closed' => 'badge-closed'
    ];
    $icon_map = [
        'New' => 'fa-sparkles',
        'Contacted' => 'fa-paper-plane',
        'Closed' => 'fa-circle-check'
    ];

    $badgeClass = isset($class_map[$status]) ? $class_map[$status] : 'badge-secondary';
    $iconClass = isset($icon_map[$status]) ? $icon_map[$status] : 'fa-info-circle';

    return sprintf(
        '<span class="status-badge %s"><i class="fa-solid %s me-1"></i>%s</span>',
        htmlspecialchars($badgeClass),
        htmlspecialchars($iconClass),
        htmlspecialchars($status)
    );
}

/**
 * Returns formatted relative time or readable date
 */
function format_date($timestamp) {
    if (!$timestamp) return 'N/A';
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' mins ago';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' hours ago';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' days ago';
    } else {
        return date('M j, Y g:i A', $time);
    }
}

/**
 * Formats JSON response and exits
 */
function json_response($success, $message, $extra = [], $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array_merge([
        'success' => (bool)$success,
        'message' => $message
    ], $extra));
    exit;
}
