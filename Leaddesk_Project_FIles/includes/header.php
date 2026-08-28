<?php
/**
 * LeadDesk Mini - HTML Header Template Include
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/auth.php';
$pageTitle = isset($pageTitle) ? $pageTitle : 'LeadDesk Mini — Lead Capture & Management';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LeadDesk Mini — A production-ready Lead Capture and Admin Management application built with PHP and MySQL.">
    <meta name="csrf-token" content="<?= get_csrf_token() ?>">
    <title><?= htmlspecialchars($pageTitle) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom Design System CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
