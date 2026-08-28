<?php
/**
 * LeadDesk Mini - Logout Handler
 */

require_once __DIR__ . '/config/auth.php';

logout_admin();
session_start();
$_SESSION['flash_success'] = "You have been logged out successfully.";
header("Location: login.php");
exit;
