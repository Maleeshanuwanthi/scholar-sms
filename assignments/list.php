<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Assignments";
include "../includes/header.php";
$result = mysqli_query($conn, "SELECT * FROM assignments ORDER BY due_date DESC");
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>
<div class="panel">
    <div class="panel-header">
        <h3>All Assignments</h3>
        <a href="add.php" class="btn">+ Add Assignment</a>
    </div>
    <table>
        <tr><th>Title</th><th>Subject</th><th>Grade</th><th>Due Date</th><th>Actions</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['grade']); ?></td>
            <td><?php echo htmlspecialchars($row['due_date']); ?></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this assignment?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
