<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Payment";
$id = intval($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $student_id = intval($_POST['student_id']);
    $amount = $_POST['amount'];
    $purpose = trim($_POST['purpose']);
    $payment_date = $_POST['payment_date'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn, "UPDATE payments SET student_id=?, amount=?, purpose=?, payment_date=?, status=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "idsssi", $student_id, $amount, $purpose, $payment_date, $status, $id);
    mysqli_stmt_execute($stmt);

    header("Location: list.php?success=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM payments WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$payment = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$payment) { header("Location: list.php"); exit; }

$students = mysqli_query($conn, "SELECT id, full_name FROM students ORDER BY full_name");
include "../includes/header.php";
?>
<div class="panel">
    <h3>Edit Payment</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Student</label>
        <select name="student_id" required>
            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                <option value="<?php echo $s['id']; ?>" <?php echo $payment['student_id']==$s['id']?'selected':''; ?>><?php echo htmlspecialchars($s['full_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <label>Amount (Rs.)</label>
        <input type="number" step="0.01" name="amount" value="<?php echo $payment['amount']; ?>" required>
        <label>Category</label>
        <select name="purpose" required>
            <?php foreach (["Annual Trip", "School Payments", "Sports Meet", "Others"] as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $payment['purpose']==$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Payment Date</label>
        <input type="date" name="payment_date" value="<?php echo $payment['payment_date']; ?>">
        <label>Status</label>
        <select name="status">
            <option value="Pending" <?php echo $payment['status']=='Pending'?'selected':''; ?>>Pending</option>
            <option value="Paid" <?php echo $payment['status']=='Paid'?'selected':''; ?>>Paid</option>
        </select>
        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
