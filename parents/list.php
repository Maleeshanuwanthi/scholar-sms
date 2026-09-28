<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Parents";
include "../includes/header.php";

$search = isset($_GET['search']) ? trim($_GET['search']) : "";
if ($search) {
    $like = "%$search%";
    $stmt = mysqli_prepare($conn, "SELECT * FROM parents WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY id DESC");
    mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM parents ORDER BY id DESC");
}
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>
<div class="panel">
    <div class="panel-header">
        <h3>All Parents</h3>
        <a href="add.php" class="btn">+ Add Parent</a>
    </div>
    <form method="GET" style="margin-bottom:16px; display:flex; gap:10px;">
        <input type="text" name="search" placeholder="🔍 Search by name, email, or phone..." value="<?php echo htmlspecialchars($search); ?>"
            style="flex:1; padding:10px 14px; border:1px solid #dbe2f0; border-radius:8px; font-size:14px;">
        <button type="submit" class="btn">Search</button>
    </form>
    <table>
        <tr><th>Name</th><th>Email</th><th>Phone</th><th>Notification Link</th><th>Actions</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)):
            if (empty($row['access_token'])) {
                $newToken = bin2hex(random_bytes(16));
                $tokenStmt = mysqli_prepare($conn, "UPDATE parents SET access_token = ? WHERE id = ?");
                mysqli_stmt_bind_param($tokenStmt, "si", $newToken, $row['id']);
                mysqli_stmt_execute($tokenStmt);
                $row['access_token'] = $newToken;
            }
            $link = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/parent_view.php?token=" . $row['access_token'];
        ?>
        <tr style="cursor:pointer;" onclick="if(event.target.tagName!=='A' && event.target.tagName!=='INPUT'){ window.location='view.php?id=<?php echo $row['id']; ?>'; }">
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td>
                <input type="text" readonly value="<?php echo htmlspecialchars($link); ?>" onclick="event.stopPropagation(); this.select(); document.execCommand('copy');"
                    style="width:220px; padding:6px 8px; border:1px solid #ddd; border-radius:6px; font-size:12px;" title="Click to copy">
            </td>
            <td>
                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn">View</a>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="event.stopPropagation(); return confirm('Delete this parent?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
