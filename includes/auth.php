<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/csrf.php";

$isSubPage = strpos($_SERVER['PHP_SELF'], '/students/') || strpos($_SERVER['PHP_SELF'], '/teachers/') ||
             strpos($_SERVER['PHP_SELF'], '/parents/') || strpos($_SERVER['PHP_SELF'], '/activities/') ||
             strpos($_SERVER['PHP_SELF'], '/assignments/') || strpos($_SERVER['PHP_SELF'], '/payments/') ||
             strpos($_SERVER['PHP_SELF'], '/attendance/');
$loginPath = $isSubPage ? "../index.php" : "index.php";

// ---- Auto logout after 15 minutes of inactivity ----
$timeoutSeconds = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSeconds)) {
    session_unset();
    session_destroy();
    header("Location: " . $loginPath . "?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// ---- Require login ----
if (!isset($_SESSION['user_id'])) {
    header("Location: " . $loginPath);
    exit;
}
