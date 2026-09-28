<?php
require "../includes/teacher_auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "My Class Students";

$grade = $_SESSION['teacher_grade'];
$section = $_SESSION['teacher_section'];

$students = [];
if ($grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT s.*, p.full_name AS parent_name FROM students s LEFT JOIN parents p ON s.parent_id = p.id WHERE s.grade = ? AND s.section = ? ORDER BY s.full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

include "../includes/teacher_header.php";
?>
<div class="panel">
    <h3>My Class: <?php echo htmlspecialchars($grade . ' ' . $section); ?></h3>
    <table style="margin-top:14px;">
        <tr><th>Admission No</th><th>Name</th><th>Contact</th><th>Parent</th></tr>
        <?php if (empty($students)): ?>
        <tr><td colspan="4" style="color:#999;">No students found in your class.</td></tr>
        <?php endif; ?>
        <?php foreach ($students as $st): ?>
        <tr>
            <td><?php echo htmlspecialchars($st['admission_no']); ?></td>
            <td><?php echo htmlspecialchars($st['full_name']); ?></td>
            <td><?php echo htmlspecialchars($st['contact']); ?></td>
            <td><?php echo htmlspecialchars($st['parent_name'] ?? '-'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
