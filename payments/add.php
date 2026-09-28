<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Payment";
$preselect = isset($_GET['category']) ? $_GET['category'] : "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $student_id = intval($_POST['student_id']);
    $amount = $_POST['amount'];
    $purpose = trim($_POST['purpose']);
    $payment_date = $_POST['payment_date'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn, "INSERT INTO payments (student_id, amount, purpose, payment_date, status) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "idsss", $student_id, $amount, $purpose, $payment_date, $status);
    mysqli_stmt_execute($stmt);

    $lookup = mysqli_prepare($conn, "SELECT s.full_name, s.parent_id, p.phone FROM students s LEFT JOIN parents p ON s.parent_id = p.id WHERE s.id = ?");
    mysqli_stmt_bind_param($lookup, "i", $student_id);
    mysqli_stmt_execute($lookup);
    $st = mysqli_stmt_get_result($lookup)->fetch_assoc();
    if ($st && $st['parent_id']) {
        $msg = "Payment recorded: Rs. " . number_format($amount, 2) . " (" . $purpose . ") for " . $st['full_name'] . " - Status: " . $status . ".";
        $notifyStmt = mysqli_prepare($conn, "INSERT INTO notifications (student_id, parent_id, type, message) VALUES (?,?,'payment',?)");
        mysqli_stmt_bind_param($notifyStmt, "iis", $student_id, $st['parent_id'], $msg);
        mysqli_stmt_execute($notifyStmt);
        if (!empty($st['phone'])) {
            require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/sms.php";
            send_sms($st['phone'], "Scholar SMS: " . $msg);
        }
    }

    header("Location: list.php?success=1&category=" . urlencode($purpose));
    exit;
}

$categories = ["Annual Trip", "School Payments", "Sports Meet", "Others"];
$students = mysqli_query($conn, "SELECT id, full_name FROM students ORDER BY full_name");
include "../includes/header.php";
?>
<div class="panel">
    <h3>Add Payment</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Student</label>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($s = mysqli_fetch_assoc($students)): ?>
                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['full_name']); ?></option>
            <?php endwhile; ?>
        </select>
        <label>Amount (Rs.)</label>
        <input type="number" step="0.01" name="amount" required>
        <label>Category</label>
        <select name="purpose" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $preselect==$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Payment Date</label>
        <input type="date" name="payment_date">
        <label>Status</label>
        <select name="status">
            <option value="Pending">Pending</option>
            <option value="Paid">Paid</option>
        </select>
        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
