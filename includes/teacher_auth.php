<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/csrf.php";
if (!isset($_SESSION['teacher_id'])) {
    $isSubPage = strpos($_SERVER['PHP_SELF'], '/teacher/') !== false;
    header("Location: " . ($isSubPage ? "../teacher_login.php" : "teacher_login.php"));
    exit;
}