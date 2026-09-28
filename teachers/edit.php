<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Teacher";
$id = intval($_GET['id'] ?? 0);

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

    $formError = "";
    if ($usernameValue !== NULL) {
        $check = mysqli_prepare($conn, "SELECT id FROM teachers WHERE username = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $usernameValue, $id);
        mysqli_stmt_execute($check);
        if (mysqli_stmt_get_result($check)->num_rows > 0) {
            $formError = "This Login Username is already taken by another teacher.";
        }
    }

    if ($formError === "") {
        if ($rawPassword !== "") {
            $hashedPassword = password_hash($rawPassword, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE teachers SET full_name=?, subject=?, email=?, phone=?, qualification=?, joined_date=?, username=?, password=?, assigned_grade=?, assigned_section=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssssssssssi", $full_name, $subject, $email, $phone, $qualification, $joined_date, $usernameValue, $hashedPassword, $assigned_grade, $assigned_section, $id);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE teachers SET full_name=?, subject=?, email=?, phone=?, qualification=?, joined_date=?, username=?, assigned_grade=?, assigned_section=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssssssi", $full_name, $subject, $email, $phone, $qualification, $joined_date, $usernameValue, $assigned_grade, $assigned_section, $id);
        }
        mysqli_stmt_execute($stmt);

        header("Location: list.php?success=1");
        exit;
    }
}

    header("Location: list.php?success=1");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM teachers WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$teacher = mysqli_stmt_get_result($stmt)->fetch_assoc();
if (!$teacher) { header("Location: list.php"); exit; }

include "../includes/header.php";
?>
<div class="panel">
    <h3>Edit Teacher</h3>
    <?php if (!empty($formError)): ?>
        <div class="error-msg" style="margin-bottom:14px;">❌ <?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($teacher['full_name']); ?>" required>
        <label>Subject</label>
        <input type="text" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>">
        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($teacher['email']); ?>">
        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($teacher['phone']); ?>">
        <label>Qualification</label>
        <input type="text" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification']); ?>">
        <label>Joined Date</label>
        <input type="date" name="joined_date" value="<?php echo $teacher['joined_date']; ?>">
        <label>Assigned Class (Grade)</label>
        <input type="text" name="assigned_grade" value="<?php echo htmlspecialchars($teacher['assigned_grade'] ?? ''); ?>" placeholder="e.g. Grade 3">
        <label>Assigned Section</label>
        <input type="text" name="assigned_section" value="<?php echo htmlspecialchars($teacher['assigned_section'] ?? ''); ?>" placeholder="e.g. A">
        <label>Login Username (for Teacher Portal)</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($teacher['username'] ?? ''); ?>" placeholder="e.g. mrs_perera">
        <label>Login Password (leave blank to keep current)</label>
        <input type="password" name="password" placeholder="Enter a new password only if changing it">
        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>