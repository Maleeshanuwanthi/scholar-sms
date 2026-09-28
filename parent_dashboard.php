<?php
require "includes/parent_auth.php";
require "config/db.php";

$parentId = $_SESSION['parent_id'];

$stmt = mysqli_prepare($conn, "SELECT n.*, s.full_name AS student_name, s.grade, s.section
    FROM notifications n JOIN students s ON n.student_id = s.id
    WHERE n.parent_id = ? ORDER BY n.created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $parentId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

function typeIcon($type) {
    if ($type === 'attendance') return '🗓️';
    if ($type === 'payment') return '💳';
    return '📝';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Portal - Scholar SMS</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="layout">
<main class="content" style="max-width:900px;">
    <div class="topbar">
        <h1>👪 Welcome, <?php echo htmlspecialchars($_SESSION['parent_name']); ?></h1>
        <a href="parent_logout.php" class="btn btn-secondary">🚪 Logout</a>
    </div>

    <div class="panel">
        <h3>Updates for Your Child(ren)</h3>
        <p style="color:#777; font-size:13px; margin-bottom:16px;">
            Homework, attendance, and payment updates appear here as they happen.
        </p>
        <table>
            <tr><th>Date</th><th>Student</th><th>Class</th><th>Update</th></tr>
            <?php if (mysqli_num_rows($result) === 0): ?>
            <tr><td colspan="4" style="color:#999;">No updates yet.</td></tr>
            <?php endif; ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($row['created_at']))); ?></td>
                <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                <td><?php echo htmlspecialchars($row['grade'] . ' ' . $row['section']); ?></td>
                <td><?php echo typeIcon($row['type']); ?> <?php echo htmlspecialchars($row['message']); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>
</div>
</body>
</html>
