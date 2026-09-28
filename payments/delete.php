<?php
require "../includes/auth.php";
require "../config/db.php";
$id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "DELETE FROM payments WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
header("Location: list.php?success=1");
exit;
