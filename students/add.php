<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Student";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $admission_no = trim($_POST['admission_no']);
    $full_name = trim($_POST['full_name']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $grade = trim($_POST['grade']);
    $section = trim($_POST['section']);
    $parent_id = $_POST['parent_id'] ?: NULL;
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);

    $formError = "";
    if ($admission_no === "" || $full_name === "") {
        $formError = "Admission No and Full Name are required.";
    } else {
        $check = mysqli_prepare($conn, "SELECT id FROM students WHERE admission_no = ?");
        mysqli_stmt_bind_param($check, "s", $admission_no);
        mysqli_stmt_execute($check);
        if (mysqli_stmt_get_result($check)->num_rows > 0) {
            $formError = "This Admission No already exists. Please use a different one.";
        }
    }

    if ($formError === "") {
        $stmt = mysqli_prepare($conn, "INSERT INTO students (admission_no, full_name, dob, gender, grade, section, parent_id, contact, address) VALUES (?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssssiss", $admission_no, $full_name, $dob, $gender, $grade, $section, $parent_id, $contact, $address);
        mysqli_stmt_execute($stmt);

        header("Location: list.php?success=1");
        exit;
    }
}

$parents = mysqli_query($conn, "SELECT id, full_name FROM parents ORDER BY full_name");
include "../includes/header.php";
?>

<div class="panel">
    <h3>Add New Student</h3>
    <?php if (!empty($formError)): ?>
        <div class="error-msg" style="margin-bottom:14px;">❌ <?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Admission No</label>
        <input type="text" name="admission_no" required>

        <label>Full Name</label>
        <input type="text" name="full_name" required>

        <label>Date of Birth</label>
        <input type="date" name="dob">

        <label>Gender</label>
        <select name="gender">
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <label>Grade</label>
        <input type="text" name="grade" placeholder="e.g. Grade 10">

        <label>Section</label>
        <input type="text" name="section" placeholder="e.g. A">

        <label>Parent</label>
        <select name="parent_id">
            <option value="">-- None --</option>
            <?php while ($p = mysqli_fetch_assoc($parents)): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['full_name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Contact</label>
        <input type="text" name="contact">

        <label>Address</label>
        <textarea name="address" rows="3"></textarea>

        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>
