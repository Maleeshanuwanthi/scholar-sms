<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Parent";
$id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    $stmt = mysqli_prepare($conn, "UPDATE parents SET full_name=?, email=?, phone=?, address=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone, $address, $id);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}

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

include "../includes/header.php";
?>
<div class="panel">
    <h3>Edit Parent</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($parent['full_name']); ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($parent['email']); ?>">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($parent['phone']); ?>">
        <label>Address</label>
        <textarea name="address" rows="3"><?php echo htmlspecialchars($parent['address']); ?></textarea>
        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
