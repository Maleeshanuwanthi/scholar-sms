<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Student Reports";

$grades = mysqli_query($conn, "SELECT DISTINCT grade FROM students WHERE grade != '' ORDER BY grade");
$sections = mysqli_query($conn, "SELECT DISTINCT section FROM students WHERE section != '' ORDER BY section");

$grade = $_GET['grade'] ?? '';
$section = $_GET['section'] ?? '';
$studentId = intval($_GET['student_id'] ?? 0);

$students = [];
if ($grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE grade = ? AND section = ? ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

$marksByExam = [];
$termAverages = [];
$studentName = '';
if ($studentId) {
    $nameStmt = mysqli_prepare($conn, "SELECT full_name FROM students WHERE id = ?");
    mysqli_stmt_bind_param($nameStmt, "i", $studentId);
    mysqli_stmt_execute($nameStmt);
    $studentName = mysqli_stmt_get_result($nameStmt)->fetch_assoc()['full_name'] ?? '';

    $stmt = mysqli_prepare($conn, "SELECT e.name AS exam_name, e.exam_date, e.term, m.subject, m.marks_obtained, m.max_marks
        FROM marks m JOIN exams e ON m.exam_id = e.id
        WHERE m.student_id = ? ORDER BY e.exam_date DESC, m.subject");
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $termTotals = [];
    while ($r = mysqli_fetch_assoc($res)) {
        $marksByExam[$r['exam_name'] . ' (' . $r['exam_date'] . ')'][] = $r;
        if (!empty($r['term'])) {
            if (!isset($termTotals[$r['term']])) { $termTotals[$r['term']] = ['total' => 0, 'max' => 0]; }
            $termTotals[$r['term']]['total'] += $r['marks_obtained'];
            $termTotals[$r['term']]['max'] += $r['max_marks'];
        }
    }
    foreach (['Term 1', 'Term 2', 'Term 3'] as $t) {
        $termAverages[$t] = (isset($termTotals[$t]) && $termTotals[$t]['max'] > 0)
            ? round(($termTotals[$t]['total'] / $termTotals[$t]['max']) * 100, 1) : null;
    }
}

include "../includes/header.php";
?>

<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">Select Class & Student</h3>
    <form method="GET" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
        <div>
            <label>Grade</label>
            <select name="grade" onchange="this.form.submit()">
                <option value="">-- Grade --</option>
                <?php mysqli_data_seek($grades, 0); while ($g = mysqli_fetch_assoc($grades)): ?>
                    <option value="<?php echo htmlspecialchars($g['grade']); ?>" <?php echo $grade==$g['grade']?'selected':''; ?>><?php echo htmlspecialchars($g['grade']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div>
            <label>Section</label>
            <select name="section" onchange="this.form.submit()">
                <option value="">-- Section --</option>
                <?php mysqli_data_seek($sections, 0); while ($s = mysqli_fetch_assoc($sections)): ?>
                    <option value="<?php echo htmlspecialchars($s['section']); ?>" <?php echo $section==$s['section']?'selected':''; ?>><?php echo htmlspecialchars($s['section']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php if ($grade && $section): ?>
        <div>
            <label>Student</label>
            <input type="hidden" name="grade" value="<?php echo htmlspecialchars($grade); ?>">
            <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
            <select name="student_id" onchange="this.form.submit()">
                <option value="">-- Student --</option>
                <?php foreach ($students as $st): ?>
                    <option value="<?php echo $st['id']; ?>" <?php echo $studentId==$st['id']?'selected':''; ?>><?php echo htmlspecialchars($st['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
    </form>
</div>

<?php if ($studentId): ?>
<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">📊 Term Comparison - <?php echo htmlspecialchars($studentName); ?> (Average %)</h3>
    <canvas id="termChart" height="90"></canvas>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('termChart'), {
    type: 'bar',
    data: {
        labels: ['Term 1', 'Term 2', 'Term 3'],
        datasets: [{
            label: 'Average %',
            data: <?php echo json_encode(array_values($termAverages)); ?>,
            backgroundColor: ['#4b6cb7', '#8e6cc7', '#d16ba5']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, max: 100 } }
    }
});
</script>

<div class="panel">
    <h3>📄 Report Card - <?php echo htmlspecialchars($studentName); ?></h3>
    <?php if (empty($marksByExam)): ?>
        <p style="color:#777; margin-top:10px;">No marks recorded yet for this student.</p>
    <?php endif; ?>
    <?php foreach ($marksByExam as $examLabel => $rows):
        $total = 0; $max = 0;
        foreach ($rows as $r) { $total += $r['marks_obtained']; $max += $r['max_marks']; }
        $avg = $max > 0 ? round(($total / $max) * 100, 1) : 0;
    ?>
    <h4 style="margin:18px 0 8px; color:#2f3b52;"><?php echo htmlspecialchars($examLabel); ?></h4>
    <table style="margin-bottom:10px;">
        <tr><th>Subject</th><th>Marks Obtained</th><th>Out Of</th></tr>
        <?php foreach ($rows as $r): ?>
        <tr>
            <td><?php echo htmlspecialchars($r['subject']); ?></td>
            <td><?php echo htmlspecialchars($r['marks_obtained']); ?></td>
            <td><?php echo htmlspecialchars($r['max_marks']); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:bold;">
            <td>Total</td><td><?php echo $total; ?></td><td><?php echo $max; ?> (<?php echo $avg; ?>%)</td>
        </tr>
    </table>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include "../includes/footer.php"; ?>