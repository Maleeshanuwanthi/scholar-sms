<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: parent_login.php");
    exit;
}
