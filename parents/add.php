<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Parent";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $accessToken = bin2hex(random_bytes(16));

    $stmt = mysqli_prepare($conn, "INSERT INTO parents (full_name, email, phone, address, access_token) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $full_name, $email, $phone, $address, $accessToken);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}
include "../includes/header.php";
?>
<div class="panel">
    <h3>Add New Parent</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Full Name</label>
        <input type="text" name="full_name" required>
        <label>Email</label>
        <input type="email" name="email">
        <label>Phone</label>
        <input type="text" name="phone">
        <label>Address</label>
        <textarea name="address" rows="3"></textarea>
        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
