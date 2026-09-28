<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Attendance Report";

$type = $_GET['type'] ?? 'student';
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

if ($type === 'student') {
    $stmt = mysqli_prepare($conn, "SELECT a.attendance_date, a.status, s.full_name, s.grade, s.section
        FROM attendance_students a JOIN students s ON a.student_id = s.id
        WHERE a.attendance_date BETWEEN ? AND ? ORDER BY a.attendance_date DESC, s.full_name");
} else {
    $stmt = mysqli_prepare($conn, "SELECT a.attendance_date, a.status, t.full_name, t.subject
        FROM attendance_teachers a JOIN teachers t ON a.teacher_id = t.id
        WHERE a.attendance_date BETWEEN ? AND ? ORDER BY a.attendance_date DESC, t.full_name");
}
mysqli_stmt_bind_param($stmt, "ss", $from, $to);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

include "../includes/header.php";
?>

<div class="panel" style="margin-bottom:20px;">
    <div style="display:flex; gap:10px; margin-bottom:16px;">
        <a href="report.php?type=student&from=<?php echo $from; ?>&to=<?php echo $to; ?>" class="btn <?php echo $type=='student'?'':'btn-secondary'; ?>">🧑‍🎓 Students</a>
        <a href="report.php?type=teacher&from=<?php echo $from; ?>&to=<?php echo $to; ?>" class="btn <?php echo $type=='teacher'?'':'btn-secondary'; ?>">👩‍🏫 Teachers</a>
    </div>
    <form method="GET" class="styled" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
        <input type="hidden" name="type" value="<?php echo $type; ?>">
        <div><label>From</label><input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>"></div>
        <div><label>To</label><input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>"></div>
        <div><button type="submit" class="btn">Filter</button></div>
    </form>
</div>

<div class="panel">
    <h3><?php echo $type === 'student' ? 'Student' : 'Teacher'; ?> Attendance (<?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?>)</h3>
    <table style="margin-top:14px;">
        <?php if ($type === 'student'): ?>
        <tr><th>Date</th><th>Student</th><th>Grade</th><th>Section</th><th>Status</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['attendance_date']); ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['grade']); ?></td>
            <td><?php echo htmlspecialchars($row['section']); ?></td>
            <td><span class="<?php echo $row['status']=='Present'?'badge-paid':'badge-pending'; ?>"><?php echo $row['status']; ?></span></td>
        </tr>
        <?php endwhile; ?>
        <?php else: ?>
        <tr><th>Date</th><th>Teacher</th><th>Subject</th><th>Status</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['attendance_date']); ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><span class="<?php echo $row['status']=='Present'?'badge-paid':'badge-pending'; ?>"><?php echo $row['status']; ?></span></td>
        </tr>
        <?php endwhile; ?>
        <?php endif; ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
