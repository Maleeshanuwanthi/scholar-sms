<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Assignment";
$id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = trim($_POST['subject']);
    $grade = trim($_POST['grade']);
    $due_date = $_POST['due_date'];

    $stmt = mysqli_prepare($conn, "UPDATE assignments SET title=?, description=?, subject=?, grade=?, due_date=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssssi", $title, $description, $subject, $grade, $due_date, $id);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM assignments WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$assignment = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$assignment) { header("Location: list.php"); exit; }

include "../includes/header.php";
?>
<div class="panel">
    <h3>Edit Assignment</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
        <label>Description</label>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
        <label>Subject</label>
        <input type="text" name="subject" value="<?php echo htmlspecialchars($assignment['subject']); ?>">
        <label>Grade</label>
        <input type="text" name="grade" value="<?php echo htmlspecialchars($assignment['grade']); ?>">
        <label>Due Date</label>
        <input type="date" name="due_date" value="<?php echo $assignment['due_date']; ?>">
        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
