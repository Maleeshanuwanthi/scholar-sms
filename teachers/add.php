<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Teacher";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $full_name = trim($_POST['full_name']);
    $subject = trim($_POST['subject']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $qualification = trim($_POST['qualification']);
    $joined_date = $_POST['joined_date'];
    $username = trim($_POST['username']);
    $rawPassword = $_POST['password'];
    $assigned_grade = trim($_POST['assigned_grade']);
    $assigned_section = trim($_POST['assigned_section']);

    $usernameValue = $username !== "" ? $username : NULL;
    $hashedPassword = $rawPassword !== "" ? password_hash($rawPassword, PASSWORD_DEFAULT) : NULL;

    $formError = "";
    if ($usernameValue !== NULL) {
        $check = mysqli_prepare($conn, "SELECT id FROM teachers WHERE username = ?");
        mysqli_stmt_bind_param($check, "s", $usernameValue);
        mysqli_stmt_execute($check);
        if (mysqli_stmt_get_result($check)->num_rows > 0) {
            $formError = "This Login Username is already taken. Please choose a different one.";
        }
    }

    if ($formError === "") {
        $stmt = mysqli_prepare($conn, "INSERT INTO teachers (full_name, subject, email, phone, qualification, joined_date, username, password, assigned_grade, assigned_section) VALUES (?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssssssss", $full_name, $subject, $email, $phone, $qualification, $joined_date, $usernameValue, $hashedPassword, $assigned_grade, $assigned_section);
        mysqli_stmt_execute($stmt);

        header("Location: list.php?success=1");
        exit;
    }
}
include "../includes/header.php";
?>
<div class="panel">
    <h3>Add New Teacher</h3>
    <?php if (!empty($formError)): ?>
        <div class="error-msg" style="margin-bottom:14px;">❌ <?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Full Name</label>
        <input type="text" name="full_name" required>
        <label>Subject</label>
        <input type="text" name="subject">
        <label>Email</label>
        <input type="email" name="email">
        <label>Phone</label>
        <input type="text" name="phone">
        <label>Qualification</label>
        <input type="text" name="qualification">
        <label>Joined Date</label>
        <input type="date" name="joined_date">
        <label>Assigned Class (Grade)</label>
        <input type="text" name="assigned_grade" placeholder="e.g. Grade 3">
        <label>Assigned Section</label>
        <input type="text" name="assigned_section" placeholder="e.g. A">
        <label>Login Username (for Teacher Portal)</label>
        <input type="text" name="username" placeholder="e.g. mrs_perera">
        <label>Login Password</label>
        <input type="password" name="password" placeholder="Set a password so this teacher can log in">
        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>