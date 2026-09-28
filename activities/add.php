<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Activity";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $activity_date = $_POST['activity_date'];

    $stmt = mysqli_prepare($conn, "INSERT INTO activities (title, description, activity_date) VALUES (?,?,?)");
    mysqli_stmt_bind_param($stmt, "sss", $title, $description, $activity_date);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}
include "../includes/header.php";
?>
<div class="panel">
    <h3>Add Activity</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Title</label>
        <input type="text" name="title" required>
        <label>Description</label>
        <textarea name="description" rows="3"></textarea>
        <label>Date</label>
        <input type="date" name="activity_date">
        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
