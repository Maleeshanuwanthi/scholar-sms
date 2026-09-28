<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Mark Attendance";

$type = $_GET['type'] ?? 'student';
$date = $_GET['date'] ?? date('Y-m-d');
$grade = $_GET['grade'] ?? '';
$section = $_GET['section'] ?? '';
$saved = false;

// ---- Handle Save ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $postType = $_POST['type'];
    $postDate = $_POST['attendance_date'];
    $statuses = $_POST['status'] ?? [];

    if ($postType === 'student') {
        $alreadyMarked = [];
        $newlyMarked = [];
        $checkStmt = mysqli_prepare($conn, "SELECT status FROM attendance_students WHERE student_id = ? AND attendance_date = ?");
        $stmt = mysqli_prepare($conn, "INSERT INTO attendance_students (student_id, attendance_date, status) VALUES (?,?,?)");
        $notifyStmt = mysqli_prepare($conn, "INSERT INTO notifications (student_id, parent_id, type, message) VALUES (?,?,'attendance',?)");

        foreach ($statuses as $studentId => $status) {
            $sid = intval($studentId);

            // Check if this student already has an attendance record for this date
            mysqli_stmt_bind_param($checkStmt, "is", $sid, $postDate);
            mysqli_stmt_execute($checkStmt);
            $existing = mysqli_stmt_get_result($checkStmt)->fetch_assoc();

            $lookup = mysqli_prepare($conn, "SELECT s.full_name, s.parent_id, p.phone FROM students s LEFT JOIN parents p ON s.parent_id = p.id WHERE s.id = ?");
            mysqli_stmt_bind_param($lookup, "i", $sid);
            mysqli_stmt_execute($lookup);
            $st = mysqli_stmt_get_result($lookup)->fetch_assoc();

            if ($existing) {
                // Already marked today - locked, do not change
                $alreadyMarked[] = ($st['full_name'] ?? 'Student') . " (already marked: " . $existing['status'] . ")";
                continue;
            }

            // Not marked yet today - insert once
            mysqli_stmt_bind_param($stmt, "iss", $sid, $postDate, $status);
            mysqli_stmt_execute($stmt);
            $newlyMarked[] = $st['full_name'] ?? 'Student';

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
        $_SESSION['attendance_locked_notice'] = $alreadyMarked;
    } else {
        $alreadyMarkedT = [];
        $checkStmtT = mysqli_prepare($conn, "SELECT status FROM attendance_teachers WHERE teacher_id = ? AND attendance_date = ?");
        $stmt = mysqli_prepare($conn, "INSERT INTO attendance_teachers (teacher_id, attendance_date, status) VALUES (?,?,?)");
        foreach ($statuses as $teacherId => $status) {
            $tid = intval($teacherId);

            mysqli_stmt_bind_param($checkStmtT, "is", $tid, $postDate);
            mysqli_stmt_execute($checkStmtT);
            $existingT = mysqli_stmt_get_result($checkStmtT)->fetch_assoc();

            if ($existingT) {
                $nameLookup = mysqli_prepare($conn, "SELECT full_name FROM teachers WHERE id = ?");
                mysqli_stmt_bind_param($nameLookup, "i", $tid);
                mysqli_stmt_execute($nameLookup);
                $tName = mysqli_stmt_get_result($nameLookup)->fetch_assoc();
                $alreadyMarkedT[] = ($tName['full_name'] ?? 'Teacher') . " (already marked: " . $existingT['status'] . ")";
                continue;
            }

            mysqli_stmt_bind_param($stmt, "iss", $tid, $postDate, $status);
            mysqli_stmt_execute($stmt);
        }
        $_SESSION['attendance_locked_notice'] = $alreadyMarkedT;
    }
    $saved = true;
    $type = $postType;
    $date = $postDate;
    $grade = $_POST['grade'] ?? '';
    $section = $_POST['section'] ?? '';
}

// ---- Data for filters ----
$grades = mysqli_query($conn, "SELECT DISTINCT grade FROM students WHERE grade != '' ORDER BY grade");
$sections = mysqli_query($conn, "SELECT DISTINCT section FROM students WHERE section != '' ORDER BY section");

// ---- Load list to mark ----
$studentsToMark = [];
$existingStudentStatus = [];
if ($type === 'student' && $grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE grade = ? AND section = ? ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $studentsToMark = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    $stmt2 = mysqli_prepare($conn, "SELECT student_id, status FROM attendance_students WHERE attendance_date = ?");
    mysqli_stmt_bind_param($stmt2, "s", $date);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    while ($r = mysqli_fetch_assoc($res2)) { $existingStudentStatus[$r['student_id']] = $r['status']; }
}

$teachersToMark = [];
$existingTeacherStatus = [];
if ($type === 'teacher') {
    $teachersToMark = mysqli_query($conn, "SELECT * FROM teachers ORDER BY full_name")->fetch_all(MYSQLI_ASSOC);
    $stmt3 = mysqli_prepare($conn, "SELECT teacher_id, status FROM attendance_teachers WHERE attendance_date = ?");
    mysqli_stmt_bind_param($stmt3, "s", $date);
    mysqli_stmt_execute($stmt3);
    $res3 = mysqli_stmt_get_result($stmt3);
    while ($r = mysqli_fetch_assoc($res3)) { $existingTeacherStatus[$r['teacher_id']] = $r['status']; }
}

include "../includes/header.php";
?>

<?php if ($saved): ?>
<div class="alert-success">✅ Attendance saved for <?php echo htmlspecialchars($date); ?>.</div>
<?php
$lockedNotice = $_SESSION['attendance_locked_notice'] ?? [];
unset($_SESSION['attendance_locked_notice']);
if (!empty($lockedNotice)):
?>
<div class="error-msg" style="background:#fff6e5; color:#b8860b;">
    🔒 Already marked today (not changed): <?php echo htmlspecialchars(implode(', ', $lockedNotice)); ?>
</div>
<?php endif; ?>
<?php endif; ?>

<div class="panel" style="margin-bottom:20px;">
    <div style="display:flex; gap:10px; margin-bottom:16px;">
        <a href="mark.php?type=student" class="btn <?php echo $type=='student'?'':'btn-secondary'; ?>">🧑‍🎓 Students</a>
        <a href="mark.php?type=teacher" class="btn <?php echo $type=='teacher'?'':'btn-secondary'; ?>">👩‍🏫 Teachers</a>
    </div>

    <?php if ($type === 'student'): ?>
        <form method="GET" class="styled" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
            <input type="hidden" name="type" value="student">
            <div>
                <label>Date</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            </div>
            <div>
                <label>Grade</label>
                <select name="grade">
                    <option value="">-- Select Grade --</option>
                    <?php mysqli_data_seek($grades, 0); while ($g = mysqli_fetch_assoc($grades)): ?>
                        <option value="<?php echo htmlspecialchars($g['grade']); ?>" <?php echo $grade==$g['grade']?'selected':''; ?>><?php echo htmlspecialchars($g['grade']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label>Section</label>
                <select name="section">
                    <option value="">-- Select Section --</option>
                    <?php mysqli_data_seek($sections, 0); while ($s = mysqli_fetch_assoc($sections)): ?>
                        <option value="<?php echo htmlspecialchars($s['section']); ?>" <?php echo $section==$s['section']?'selected':''; ?>><?php echo htmlspecialchars($s['section']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div><button type="submit" class="btn">Load Class</button></div>
        </form>
    <?php else: ?>
        <form method="GET" class="styled" style="display:flex; gap:12px; align-items:end;">
            <input type="hidden" name="type" value="teacher">
            <div>
                <label>Date</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
            </div>
            <div><button type="submit" class="btn">Load Teachers</button></div>
        </form>
    <?php endif; ?>
</div>

<?php if ($type === 'student' && $grade && $section): ?>
<div class="panel">
    <h3>Mark Attendance - <?php echo htmlspecialchars($grade . ' ' . $section); ?> (<?php echo htmlspecialchars($date); ?>)</h3>
    <?php if (empty($studentsToMark)): ?>
        <p style="color:#777; margin-top:10px;">No students found in this class.</p>
    <?php else: ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="type" value="student">
        <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($date); ?>">
        <input type="hidden" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
        <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
        <table style="margin-top:14px;">
            <tr><th>Student</th><th>Admission No</th><th>Present</th><th>Absent</th><th>Status</th></tr>
            <?php foreach ($studentsToMark as $st):
                $current = $existingStudentStatus[$st['id']] ?? null;
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
        <div class="actions">
            <button type="submit" class="btn">💾 Save Attendance</button>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($type === 'teacher'): ?>
<div class="panel">
    <h3>Mark Teacher Attendance (<?php echo htmlspecialchars($date); ?>)</h3>
    <?php if (empty($teachersToMark)): ?>
        <p style="color:#777; margin-top:10px;">No teachers found.</p>
    <?php else: ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="type" value="teacher">
        <input type="hidden" name="attendance_date" value="<?php echo htmlspecialchars($date); ?>">
        <table style="margin-top:14px;">
            <tr><th>Teacher</th><th>Subject</th><th>Present</th><th>Absent</th><th>Status</th></tr>
            <?php foreach ($teachersToMark as $t):
                $current = $existingTeacherStatus[$t['id']] ?? null;
                $locked = $current !== null; ?>
            <tr>
                <td><?php echo htmlspecialchars($t['full_name']); ?></td>
                <td><?php echo htmlspecialchars($t['subject']); ?></td>
                <td><input type="radio" name="status[<?php echo $t['id']; ?>]" value="Present" <?php echo $current=='Present'?'checked':''; ?> <?php echo $locked?'disabled':''; ?>></td>
                <td><input type="radio" name="status[<?php echo $t['id']; ?>]" value="Absent" <?php echo $current=='Absent'?'checked':''; ?> <?php echo $locked?'disabled':''; ?>></td>
                <td><?php echo $locked ? '🔒 Locked (' . htmlspecialchars($current) . ')' : '⬜ Not marked'; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="actions">
            <button type="submit" class="btn">💾 Save Attendance</button>
        </div>
    </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>
