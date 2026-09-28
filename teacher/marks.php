<?php
require "../includes/teacher_auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Enter Marks";

$grade = $_SESSION['teacher_grade'];
$section = $_SESSION['teacher_section'];
$teacherId = $_SESSION['teacher_id'];
$saved = false;

// Create a new exam
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_exam'])) {
    csrf_verify();
    $examName = trim($_POST['exam_name']);
    $examDate = $_POST['exam_date'];
    $term = trim($_POST['term']);
    $stmt = mysqli_prepare($conn, "INSERT INTO exams (name, grade, term, exam_date) VALUES (?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "ssss", $examName, $grade, $term, $examDate);
    mysqli_stmt_execute($stmt);
    $newExamId = mysqli_insert_id($conn);
    header("Location: marks.php?exam_id=" . $newExamId);
    exit;
}

// Save marks for a subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_marks'])) {
    csrf_verify();
    $examId = intval($_POST['exam_id']);
    $subject = trim($_POST['subject']);
    $maxMarks = floatval($_POST['max_marks']);
    $marksArr = $_POST['marks'] ?? [];

    $stmt = mysqli_prepare($conn, "INSERT INTO marks (exam_id, student_id, subject, marks_obtained, max_marks, entered_by) VALUES (?,?,?,?,?,?)
        ON DUPLICATE KEY UPDATE marks_obtained = VALUES(marks_obtained), max_marks = VALUES(max_marks)");
    foreach ($marksArr as $studentId => $val) {
        if ($val === '') continue;
        $sid = intval($studentId);
        $markVal = floatval($val);
        mysqli_stmt_bind_param($stmt, "iisddi", $examId, $sid, $subject, $markVal, $maxMarks, $teacherId);
        mysqli_stmt_execute($stmt);
    }
    header("Location: marks.php?exam_id=" . $examId . "&subject=" . urlencode($subject) . "&success=1");
    exit;
}

$examId = intval($_GET['exam_id'] ?? 0);
$subject = trim($_GET['subject'] ?? '');
$saved = isset($_GET['success']);

$examsStmt = mysqli_prepare($conn, "SELECT * FROM exams WHERE grade = ? ORDER BY exam_date DESC");
mysqli_stmt_bind_param($examsStmt, "s", $grade);
mysqli_stmt_execute($examsStmt);
$exams = mysqli_stmt_get_result($examsStmt)->fetch_all(MYSQLI_ASSOC);

require_once $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/subjects.php";
$subjectGroups = get_subject_groups($grade);

$students = [];
$existingMarks = [];
if ($examId && $subject !== '') {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE grade = ? AND section = ? ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);

    $mstmt = mysqli_prepare($conn, "SELECT student_id, marks_obtained, max_marks FROM marks WHERE exam_id = ? AND subject = ?");
    mysqli_stmt_bind_param($mstmt, "is", $examId, $subject);
    mysqli_stmt_execute($mstmt);
    $mres = mysqli_stmt_get_result($mstmt);
    while ($r = mysqli_fetch_assoc($mres)) { $existingMarks[$r['student_id']] = $r; }
}

include "../includes/teacher_header.php";
?>

<?php if ($saved): ?>
<div class="alert-success">✅ Marks saved.</div>
<?php endif; ?>

<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">1. Choose or Create an Exam</h3>
    <div style="display:flex; gap:30px; flex-wrap:wrap;">
        <div style="flex:1; min-width:220px;">
            <form method="GET">
                <label>Existing Exam</label>
                <select name="exam_id" onchange="this.form.submit()" style="width:100%; padding:9px 12px; border:1px solid #dbe2f0; border-radius:8px;">
                    <option value="">-- Select Exam --</option>
                    <?php foreach ($exams as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $examId==$e['id']?'selected':''; ?>><?php echo htmlspecialchars(($e['term'] ? $e['term'] . ' - ' : '') . $e['name'] . ' (' . $e['exam_date'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <div style="flex:1; min-width:220px;">
            <form method="POST" class="styled">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="create_exam" value="1">
                <label>Or Create New Exam</label>
                <input type="text" name="exam_name" placeholder="e.g. Term 1 Test" required>
                <select name="term" required style="margin-top:8px; width:100%; padding:9px 12px; border:1px solid #dbe2f0; border-radius:8px;">
                    <option value="">-- Select Term --</option>
                    <option value="Term 1">Term 1</option>
                    <option value="Term 2">Term 2</option>
                    <option value="Term 3">Term 3</option>
                </select>
                <input type="date" name="exam_date" style="margin-top:8px;" required>
                <button type="submit" class="btn" style="margin-top:8px;">+ Create Exam</button>
            </form>
        </div>
    </div>
</div>

<?php if ($examId): ?>
<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">2. Choose Subject</h3>
    <form method="GET" style="display:flex; gap:12px; align-items:end;">
        <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
        <div>
            <label>Subject</label>
            <select name="subject" required style="min-width:280px; padding:9px 12px; border:1px solid #dbe2f0; border-radius:8px;">
                <option value="">-- Select Subject --</option>
                <?php foreach ($subjectGroups as $groupLabel => $subjectList): ?>
                    <optgroup label="<?php echo htmlspecialchars($groupLabel); ?>">
                        <?php foreach ($subjectList as $subj): ?>
                            <option value="<?php echo htmlspecialchars($subj); ?>" <?php echo $subject==$subj?'selected':''; ?>><?php echo htmlspecialchars($subj); ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </div>
        <div><button type="submit" class="btn">Load Students</button></div>
    </form>
</div>
<?php endif; ?>

<?php if ($examId && $subject !== ''): ?>
<div class="panel">
    <h3>3. Enter Marks - <?php echo htmlspecialchars($subject); ?></h3>
    <?php if (empty($students)): ?>
        <p style="color:#777; margin-top:10px;">No students found in your class.</p>
    <?php else: ?>
    <form method="POST" class="styled">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="save_marks" value="1">
        <input type="hidden" name="exam_id" value="<?php echo $examId; ?>">
        <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
        <label>Maximum Marks for this subject</label>
        <input type="number" name="max_marks" value="<?php echo $existingMarks[$students[0]['id']]['max_marks'] ?? 100; ?>" style="max-width:150px;">
        <table style="margin-top:14px;">
            <tr><th>Student</th><th>Admission No</th><th>Marks Obtained</th></tr>
            <?php foreach ($students as $st): ?>
            <tr>
                <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                <td><?php echo htmlspecialchars($st['admission_no']); ?></td>
                <td><input type="number" step="0.5" name="marks[<?php echo $st['id']; ?>]" value="<?php echo $existingMarks[$st['id']]['marks_obtained'] ?? ''; ?>" style="width:120px;"></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <div class="actions"><button type="submit" class="btn">💾 Save Marks</button></div>
    </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>