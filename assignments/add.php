<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Assignment";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = trim($_POST['subject']);
    $grade = trim($_POST['grade']);
    $due_date = $_POST['due_date'];

    $stmt = mysqli_prepare($conn, "INSERT INTO assignments (title, description, subject, grade, due_date) VALUES (?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "sssss", $title, $description, $subject, $grade, $due_date);
    mysqli_stmt_execute($stmt);
    $assignmentId = mysqli_insert_id($conn);

    $gradeNumber = 0;
    if (preg_match("/([0-9]+)/", $grade, $matches)) {
        $gradeNumber = intval($matches[1]);
    }

    if ($gradeNumber >= 1 && $gradeNumber <= 5) {
        $stmt2 = mysqli_prepare($conn, "SELECT id, parent_id FROM students WHERE grade = ?");
        mysqli_stmt_bind_param($stmt2, "s", $grade);
        mysqli_stmt_execute($stmt2);
        $students = mysqli_stmt_get_result($stmt2);
        $message = "New homework assigned: " . $title . " (" . $subject . ") - Due: " . $due_date;
        $notifyStmt = mysqli_prepare($conn, "INSERT INTO notifications (assignment_id, student_id, parent_id, message) VALUES (?,?,?,?)");
        require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/sms.php";
        while ($st = mysqli_fetch_assoc($students)) {
            if ($st['parent_id']) {
                mysqli_stmt_bind_param($notifyStmt, "iiis", $assignmentId, $st['id'], $st['parent_id'], $message);
                mysqli_stmt_execute($notifyStmt);

                $phoneLookup = mysqli_prepare($conn, "SELECT phone FROM parents WHERE id = ?");
                mysqli_stmt_bind_param($phoneLookup, "i", $st['parent_id']);
                mysqli_stmt_execute($phoneLookup);
                $parentRow = mysqli_stmt_get_result($phoneLookup)->fetch_assoc();
                if ($parentRow && !empty($parentRow['phone'])) {
                    send_sms($parentRow['phone'], "Scholar SMS: " . $message);
                }
            }
        }
    }

    header("Location: list.php?success=1");
    exit;
}

include "../includes/header.php";
?>
<div class="panel">
    <h3>Add Assignment</h3>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Title</label>
        <input type="text" name="title" required>
        <label>Description</label>
        <textarea name="description" rows="3"></textarea>
        <label>Subject</label>
        <input type="text" name="subject">
        <label>Grade</label>
        <input type="text" name="grade" placeholder="e.g. Grade 1 to Grade 13">
        <label>Due Date</label>
        <input type="date" name="due_date">
        <div class="actions">
            <button type="submit" class="btn">Save</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>
