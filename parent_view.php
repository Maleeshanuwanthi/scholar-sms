<?php
require "config/db.php";

$token = isset($_GET['token']) ? trim($_GET['token']) : "";
$parent = null;

if ($token !== "") {
    $stmt = mysqli_prepare($conn, "SELECT * FROM parents WHERE access_token = ?");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $parent = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

function typeIcon($type) {
    if ($type === 'attendance') return '🗓️';
    if ($type === 'payment') return '💳';
    return '📝';
}

if ($parent) {
    $stmt2 = mysqli_prepare($conn, "SELECT n.*, s.full_name AS student_name, s.grade, s.section
        FROM notifications n JOIN students s ON n.student_id = s.id
        WHERE n.parent_id = ? ORDER BY n.created_at DESC");
    mysqli_stmt_bind_param($stmt2, "i", $parent['id']);
    mysqli_stmt_execute($stmt2);
    $result = mysqli_stmt_get_result($stmt2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Updates - Scholar SMS</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrap" style="align-items:flex-start; padding:40px 16px;">
    <div style="width:100%; max-width:700px; background:#fff; border-radius:16px; padding:30px; box-shadow:0 20px 40px rgba(0,0,0,0.2);">
        <?php if (!$parent): ?>
            <h2 style="color:#c0392b;">❌ Invalid or expired link</h2>
            <p style="color:#777; margin-top:8px;">Please contact the school office for a new link.</p>
        <?php else: ?>
            <h2 style="color:#2f3b52;">👪 Hello, <?php echo htmlspecialchars($parent['full_name']); ?></h2>
            <p style="color:#777; font-size:13px; margin-bottom:20px;">Here are the latest updates for your child(ren).</p>
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
        <?php endif; ?>
    </div>
</div>
</body>
</html>
