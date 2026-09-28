<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Teachers";
include "../includes/header.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
if ($search) {
    $like = "%$search%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM teachers WHERE full_name LIKE ? OR subject LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "ss", $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM teachers ORDER BY id DESC");
}
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3>All Teachers</h3>
        <a href="add.php" class="btn">+ Add Teacher</a>
    </div>
    <form method="GET" style="margin-bottom:16px; display:flex; gap:10px;">
        <input type="text" name="search" placeholder="🔍 Search by name or subject..." value="<?php echo htmlspecialchars($search); ?>"
            style="flex:1; padding:10px 14px; border:1px solid #dbe2f0; border-radius:8px; font-size:14px;">
        <button type="submit" class="btn">Search</button>
    </form>
    <table>
        <tr><th>Name</th><th>Subject</th><th>Email</th><th>Phone</th><th>Joined</th><th>Actions</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr style="cursor:pointer;" onclick="if(event.target.tagName!=='A'){ window.location='view.php?id=<?php echo $row['id']; ?>'; }">
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['joined_date']); ?></td>
            <td>
                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="event.stopPropagation(); return confirm('Delete this teacher?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
