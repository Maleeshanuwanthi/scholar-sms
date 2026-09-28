<?php
// Generates (or reuses) a CSRF token for the current session
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verifies the submitted CSRF token matches the session token.
// Stops execution immediately if it doesn't match (prevents CSRF attacks).
function csrf_verify() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid or expired form submission. Please go back and try again.");
    }
}
