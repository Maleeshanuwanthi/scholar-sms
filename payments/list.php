<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";

$category = isset($_GET['category']) ? trim($_GET['category']) : "";
$pageTitle = $category ? $category : "All Payments";

if ($category) {
    $stmt = mysqli_prepare($conn, "SELECT p.*, s.full_name AS student_name FROM payments p JOIN students s ON p.student_id = s.id WHERE p.purpose = ? ORDER BY p.id DESC");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT p.*, s.full_name AS student_name FROM payments p JOIN students s ON p.student_id = s.id ORDER BY p.id DESC");
}

include "../includes/header.php";
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Saved successfully.</div>
<?php endif; ?>
<div class="panel">
    <div class="panel-header">
        <h3><?php echo htmlspecialchars($pageTitle); ?></h3>
        <a href="add.php<?php echo $category ? '?category='.urlencode($category) : ''; ?>" class="btn">+ Add Payment</a>
    </div>
    <table>
        <tr><th>Student</th><th>Amount</th><th>Purpose</th><th>Date</th><th>Status</th><th>Actions</th></tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
            <td>Rs. <?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['purpose']); ?></td>
            <td><?php echo htmlspecialchars($row['payment_date']); ?></td>
            <td><span class="<?php echo $row['status']=='Paid' ? 'badge-paid' : 'badge-pending'; ?>"><?php echo $row['status']; ?></span></td>
            <td>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this payment?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
<?php include "../includes/footer.php"; ?>
