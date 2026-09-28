<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Notifications";
include "../includes/header.php";

$result = mysqli_query($conn, "SELECT n.*, s.full_name AS student_name, s.grade, s.section, p.full_name AS parent_name, p.phone, p.email
    FROM notifications n
    JOIN students s ON n.student_id = s.id
    LEFT JOIN parents p ON n.parent_id = p.id
    ORDER BY n.created_at DESC");
?>

<div class="panel">
    <div class="panel-header">
        <h3>🔔 Parent Homework Notifications (Primary Grades 1-5)</h3>
    </div>
    <p style="color:#777; font-size:13px; margin-bottom:16px;">
        These are automatically generated whenever a homework/assignment is added for a Primary grade (1-5).
        In this offline demo they are shown here; connect an SMTP/SMS service to deliver them automatically.
    </p>
    <table>
        <tr><th>Date</th><th>Student</th><th>Class</th><th>Parent</th><th>Contact</th><th>Message</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($row['created_at']))); ?></td>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td><?php echo htmlspecialchars($row['grade'] . ' ' . $row['section']); ?></td>
            <td><?php echo htmlspecialchars($row['parent_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['phone'] ?? $row['email'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['message']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
