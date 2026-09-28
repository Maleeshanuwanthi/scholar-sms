<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Edit Student";

$id = intval($_GET['id'] ?? 0);

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
    $check = mysqli_prepare($conn, "SELECT id FROM students WHERE admission_no = ? AND id != ?");
    mysqli_stmt_bind_param($check, "si", $admission_no, $id);
    mysqli_stmt_execute($check);
    if (mysqli_stmt_get_result($check)->num_rows > 0) {
        $formError = "This Admission No is already used by another student.";
    }

    if ($formError === "") {
        $stmt = mysqli_prepare($conn, "UPDATE students SET admission_no=?, full_name=?, dob=?, gender=?, grade=?, section=?, parent_id=?, contact=?, address=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssissi", $admission_no, $full_name, $dob, $gender, $grade, $section, $parent_id, $contact, $address, $id);
        mysqli_stmt_execute($stmt);

        header("Location: list.php?success=1");
        exit;
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$student = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$student) {
    header("Location: list.php");
    exit;
}

$parents = mysqli_query($conn, "SELECT id, full_name FROM parents ORDER BY full_name");
include "../includes/header.php";
?>

<div class="panel">
    <h3>Edit Student</h3>
    <?php if (!empty($formError)): ?>
        <div class="error-msg" style="margin-bottom:14px;">❌ <?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Admission No</label>
        <input type="text" name="admission_no" value="<?php echo htmlspecialchars($student['admission_no']); ?>" required>

        <label>Full Name</label>
        <input type="text" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?php echo $student['dob']; ?>">

        <label>Gender</label>
        <select name="gender">
            <?php foreach (['Male','Female','Other'] as $g): ?>
            <option value="<?php echo $g; ?>" <?php echo $student['gender']==$g?'selected':''; ?>><?php echo $g; ?></option>
            <?php endforeach; ?>
        </select>

        <label>Grade</label>
        <input type="text" name="grade" value="<?php echo htmlspecialchars($student['grade']); ?>">

        <label>Section</label>
        <input type="text" name="section" value="<?php echo htmlspecialchars($student['section']); ?>">

        <label>Parent</label>
        <select name="parent_id">
            <option value="">-- None --</option>
            <?php while ($p = mysqli_fetch_assoc($parents)): ?>
                <option value="<?php echo $p['id']; ?>" <?php echo $student['parent_id']==$p['id']?'selected':''; ?>><?php echo htmlspecialchars($p['full_name']); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Contact</label>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>">

        <label>Address</label>
        <textarea name="address" rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>

        <div class="actions">
            <button type="submit" class="btn">Update</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include "../includes/footer.php"; ?>