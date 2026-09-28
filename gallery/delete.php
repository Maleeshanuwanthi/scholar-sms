<?php
require "../includes/auth.php";
require "../config/db.php";

$id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT image_path FROM gallery WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$photo = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($photo) {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/" . $photo['image_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    $del = mysqli_prepare($conn, "DELETE FROM gallery WHERE id = ?");
    mysqli_stmt_bind_param($del, "i", $id);
    mysqli_stmt_execute($del);
}

header("Location: list.php?success=1");
exit;