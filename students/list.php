<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Students";
include "../includes/header.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

if ($search) {
    $like = "%$search%";
    $stmt = mysqli_prepare($conn, "SELECT s.*, p.full_name AS parent_name FROM students s LEFT JOIN parents p ON s.parent_id = p.id
        WHERE s.full_name LIKE ? OR s.admission_no LIKE ? OR s.grade LIKE ? ORDER BY s.id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT s.*, p.full_name AS parent_name FROM students s LEFT JOIN parents p ON s.parent_id = p.id ORDER BY s.id DESC");
}
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3>All Students</h3>
        <a href="add.php" class="btn">+ Add Student</a>
    </div>
    <form method="GET" style="margin-bottom:16px; display:flex; gap:10px;">
        <input type="text" name="search" placeholder="🔍 Search by name, admission no, or grade..." value="<?php echo htmlspecialchars($search); ?>"
            style="flex:1; padding:10px 14px; border:1px solid #dbe2f0; border-radius:8px; font-size:14px;">
        <button type="submit" class="btn">Search</button>
    </form>
    <table>
        <tr>
            <th>Admission No</th><th>Name</th><th>Grade</th><th>Section</th><th>Parent</th><th>Contact</th><th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr style="cursor:pointer;" onclick="if(event.target.tagName!=='A'){ window.location='view.php?id=<?php echo $row['id']; ?>'; }">
            <td><?php echo htmlspecialchars($row['admission_no']); ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['grade']); ?></td>
            <td><?php echo htmlspecialchars($row['section']); ?></td>
            <td><?php echo htmlspecialchars($row['parent_name'] ?? '-'); ?></td>
            <td><?php echo htmlspecialchars($row['contact']); ?></td>
            <td>
                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="event.stopPropagation(); return confirm('Delete this student?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
