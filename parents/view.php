<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Parent Details";

$id = intval($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM parents WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$parent = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$parent) { header("Location: list.php"); exit; }

if (empty($parent['access_token'])) {
    $newToken = bin2hex(random_bytes(16));
    $tokenStmt = mysqli_prepare($conn, "UPDATE parents SET access_token = ? WHERE id = ?");
    mysqli_stmt_bind_param($tokenStmt, "si", $newToken, $id);
    mysqli_stmt_execute($tokenStmt);
    $parent['access_token'] = $newToken;
}
$link = "http://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])) . "/parent_view.php?token=" . $parent['access_token'];

$childrenStmt = mysqli_prepare($conn, "SELECT * FROM students WHERE parent_id = ?");
mysqli_stmt_bind_param($childrenStmt, "i", $id);
mysqli_stmt_execute($childrenStmt);
$children = mysqli_stmt_get_result($childrenStmt);

include "../includes/header.php";
?>
<div class="panel">
    <div class="panel-header">
        <h3>👪 <?php echo htmlspecialchars($parent['full_name']); ?></h3>
        <div>
            <a href="edit.php?id=<?php echo $parent['id']; ?>" class="btn btn-secondary">Edit</a>
            <a href="list.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <table style="margin-bottom:20px;">
        <tr><th style="width:160px;">Full Name</th><td><?php echo htmlspecialchars($parent['full_name']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($parent['email']); ?></td></tr>
        <tr><th>Phone</th><td><?php echo htmlspecialchars($parent['phone']); ?></td></tr>
        <tr><th>Address</th><td><?php echo htmlspecialchars($parent['address']); ?></td></tr>
        <tr><th>Notification Link</th><td>
            <input type="text" readonly value="<?php echo htmlspecialchars($link); ?>" onclick="this.select(); document.execCommand('copy');"
                style="width:100%; max-width:420px; padding:6px 8px; border:1px solid #ddd; border-radius:6px; font-size:12px;" title="Click to copy">
        </td></tr>
    </table>

    <h3 style="margin-bottom:10px;">🧑‍🎓 Linked Students</h3>
    <table>
        <tr><th>Name</th><th>Admission No</th><th>Grade</th><th>Section</th></tr>
        <?php if (mysqli_num_rows($children) === 0): ?>
        <tr><td colspan="4" style="color:#999;">No students linked to this parent.</td></tr>
        <?php endif; ?>
        <?php while ($c = mysqli_fetch_assoc($children)): ?>
        <tr>
            <td><?php echo htmlspecialchars($c['full_name']); ?></td>
            <td><?php echo htmlspecialchars($c['admission_no']); ?></td>
            <td><?php echo htmlspecialchars($c['grade']); ?></td>
            <td><?php echo htmlspecialchars($c['section']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
