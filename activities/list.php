<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Activities";
include "../includes/header.php";
$result = mysqli_query($conn, "SELECT * FROM activities ORDER BY activity_date DESC");
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>
<div class="panel">
    <div class="panel-header">
        <h3>School Activities</h3>
        <a href="add.php" class="btn">+ Add Activity</a>
    </div>
    <table>
        <tr><th>Title</th><th>Description</th><th>Date</th><th>Actions</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['activity_date']); ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this activity?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
