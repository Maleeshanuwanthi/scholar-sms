<?php
// Database connection settings
// If you set a MySQL root password in XAMPP, put it below.
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "scholar_sms";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
