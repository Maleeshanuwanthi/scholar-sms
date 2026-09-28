<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Teacher Details";

$id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM teachers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$teacher = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$teacher) { header("Location: list.php"); exit; }

include "../includes/header.php";
?>
<div class="panel">
    <div class="panel-header">
        <h3>👩‍🏫 <?php echo htmlspecialchars($teacher['full_name']); ?></h3>
        <div>
            <a href="edit.php?id=<?php echo $teacher['id']; ?>" class="btn btn-secondary">Edit</a>
            <a href="list.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
    <table>
        <tr><th style="width:160px;">Full Name</th><td><?php echo htmlspecialchars($teacher['full_name']); ?></td></tr>
        <tr><th>Subject</th><td><?php echo htmlspecialchars($teacher['subject']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($teacher['email']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($teacher['phone']); ?></td></tr>
        <tr><th>Qualification</th><td><?php echo htmlspecialchars($teacher['qualification']); ?></td></tr>
        <tr><th>Joined Date</th><td><?php echo htmlspecialchars($teacher['joined_date']); ?></td></tr>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
