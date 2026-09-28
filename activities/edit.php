<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Activity";
$id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $activity_date = $_POST['activity_date'];

    $stmt = mysqli_prepare($conn, "UPDATE activities SET title=?, description=?, activity_date=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $activity_date, $id);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM activities WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$activity = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$activity) { header("Location: list.php"); exit; }

include "../includes/header.php";
?>
<div class="panel">
    <h3>Edit Activity</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($activity['title']); ?>" required>
        <label>Description</label>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($activity['description']); ?></textarea>
        <label>Date</label>
        <input type="date" name="activity_date" value="<?php echo $activity['activity_date']; ?>">
        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
