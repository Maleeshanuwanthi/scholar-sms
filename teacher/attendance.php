<?php
require "../includes/teacher_auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Mark Attendance";

$grade = $_SESSION['teacher_grade'];
$section = $_SESSION['teacher_section'];
$date = $_GET['date'] ?? date('Y-m-d');
$saved = false;
$lockedNotice = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $postDate = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? [];

    $checkStmt = mysqli_prepare($conn, "SELECT status FROM attendance_students WHERE student_id = ? AND attendance_date = ?");
    $stmt = mysqli_prepare($conn, "INSERT INTO attendance_students (student_id, attendance_date, status) VALUES (?,?,?)");
    $notifyStmt = mysqli_prepare($conn, "INSERT INTO notifications (student_id, parent_id, type, message) VALUES (?,?,'attendance',?)");

    foreach ($statuses as $studentId => $status) {
        $sid = intval($studentId);
        mysqli_stmt_bind_param($checkStmt, "is", $sid, $postDate);
        mysqli_stmt_execute($checkStmt);
        $existing = mysqli_stmt_get_result($checkStmt)->fetch_assoc();

        $lookup = mysqli_prepare($conn, "SELECT s.full_name, s.parent_id, p.phone FROM students s LEFT JOIN parents p ON s.parent_id = p.id WHERE s.id = ?");
        mysqli_stmt_bind_param($lookup, "i", $sid);
        mysqli_stmt_execute($lookup);
        $st = mysqli_stmt_get_result($lookup)->fetch_assoc();

        if ($existing) {
            $lockedNotice[] = ($st['full_name'] ?? 'Student') . " (already marked: " . $existing['status'] . ")";
            continue;
        }

        mysqli_stmt_bind_param($stmt, "iss", $sid, $postDate, $status);
        mysqli_stmt_execute($stmt);

        if ($st && $st['parent_id']) {
            $msg = "Attendance marked: " . $st['full_name'] . " was " . $status . " on " . $postDate . ".";
            mysqli_stmt_bind_param($notifyStmt, "iis", $sid, $st['parent_id'], $msg);
            mysqli_stmt_execute($notifyStmt);
            if (!empty($st['phone'])) {
                require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/sms.php";
                send_sms($st['phone'], "Scholar SMS: " . $msg);
            }
        }
    }
    $saved = true;
    $date = $postDate;
}

$studentsToMark = [];
$existingStatus = [];
if ($grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE grade = ? AND section = ? ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $studentsToMark = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    $stmt2 = mysqli_prepare($conn, "SELECT student_id, status FROM attendance_students WHERE attendance_date = ?");
    mysqli_stmt_bind_param($stmt2, "s", $date);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    while ($r = mysqli_fetch_assoc($res2)) { $existingStatus[$r['student_id']] = $r['status']; }
}

include "../includes/teacher_header.php";
?>

<?php if ($saved): ?>
<div class="alert-success">✅ Attendance saved for <?php echo htmlspecialchars($date); ?>.</div>
<?php if (!empty($lockedNotice)): ?>
<div class="error-msg" style="background:#fff6e5; color:#b8860b;">🔒 Already marked today: <?php echo htmlspecialchars(implode(', ', $lockedNotice)); ?></div>
<?php endif; ?>
<?php endif; ?>

<div class="panel" style="margin-bottom:20px;">
    <form method="GET" class="styled" style="display:flex; gap:12px; align-items:end;">
        <div><label>Date</label><input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>"></div>
        <div><button type="submit" class="btn">Load</button></div>
    </form>
</div>

<div class="panel">
    <h3>Mark Attendance - <?php echo htmlspecialchars($grade . ' ' . $section); ?> (<?php echo htmlspecialchars($date); ?>)</h3>
    <?php if (empty($studentsToMark)): ?>
        <p style="color:#777; margin-top:10px;">No students in your class, or class not assigned.</p>
    <?php else: ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($date); ?>">
        <table style="margin-top:14px;">
            <tr><th>Student</th><th>Admission No</th><th>Present</th><th>Absent</th><th>Status</th></tr>
            <?php foreach ($studentsToMark as $st):
                $current = $existingStatus[$st['id']] ?? null;
                $locked = $current !== null; ?>
            <tr>
                <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                <td><?php echo htmlspecialchars($st['admission_no']); ?></td>
                <td><input type="radio" name="status[<?php echo $st['id']; ?>]" value="Present" <?php echo $current=='Present'?'checked':''; ?> <?php echo $locked?'disabled':''; ?>></td>
                <td><input type="radio" name="status[<?php echo $st['id']; ?>]" value="Absent" <?php echo $current=='Absent'?'checked':''; ?> <?php echo $locked?'disabled':''; ?>></td>
                <td><?php echo $locked ? '🔒 Locked (' . htmlspecialchars($current) . ')' : '⬜ Not marked'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="actions"><button type="submit" class="btn">💾 Save Attendance</button></div>
    </form>
    <?php endif; ?>
</div>
<?php include "../includes/footer.php"; ?>
