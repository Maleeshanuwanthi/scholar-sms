<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Student Details";

$id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT s.*, p.full_name AS parent_name, p.phone AS parent_phone, p.email AS parent_email
    FROM students s LEFT JOIN parents p ON s.parent_id = p.id WHERE s.id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$student = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$student) { header("Location: list.php"); exit; }

include "../includes/header.php";
?>
<div class="panel">
    <div class="panel-header">
        <h3>🧑‍🎓 <?php echo htmlspecialchars($student['full_name']); ?></h3>
        <div>
            <a href="edit.php?id=<?php echo $student['id']; ?>" class="btn btn-secondary">Edit</a>
            <a href="list.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
    <table>
        <tr><th style="width:160px;">Admission No</th><td><?php echo htmlspecialchars($student['admission_no']); ?></td></tr>
        <tr><th>Full Name</th><td><?php echo htmlspecialchars($student['full_name']); ?></td></tr>
        <tr><th>Date of Birth</th><td><?php echo htmlspecialchars($student['dob']); ?></td></tr>
        <tr><th>Gender</th><td><?php echo htmlspecialchars($student['gender']); ?></td></tr>
        <tr><th>Grade</th><td><?php echo htmlspecialchars($student['grade']); ?></td></tr>
        <tr><th>Section</th><td><?php echo htmlspecialchars($student['section']); ?></td></tr>
        <tr><th>Contact</th><td><?php echo htmlspecialchars($student['contact']); ?></td></tr>
        <tr><th>Address</th><td><?php echo htmlspecialchars($student['address']); ?></td></tr>
        <tr><th>Parent</th><td><?php echo htmlspecialchars($student['parent_name'] ?? '-'); ?></td></tr>
        <tr><th>Parent Phone</th><td><?php echo htmlspecialchars($student['parent_phone'] ?? '-'); ?></td></tr>
        <tr><th>Parent Email</th><td><?php echo htmlspecialchars($student['parent_email'] ?? '-'); ?></td></tr>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
