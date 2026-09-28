<?php
require "../includes/teacher_auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Class Report";

$grade = $_SESSION['teacher_grade'];
$section = $_SESSION['teacher_section'];

$students = [];
if ($grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM students WHERE grade = ? AND section = ? ORDER BY full_name");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $students = mysqli_stmt_get_result($stmt)->fetch_all(MYSQLI_ASSOC);
}

$studentId = intval($_GET['student_id'] ?? 0);
$marksByExam = [];
$termAverages = [];
if ($studentId) {
    $stmt = mysqli_prepare($conn, "SELECT e.name AS exam_name, e.exam_date, e.term, m.subject, m.marks_obtained, m.max_marks
        FROM marks m JOIN exams e ON m.exam_id = e.id
        WHERE m.student_id = ? ORDER BY e.exam_date DESC, m.subject");
    mysqli_stmt_bind_param($stmt, "i", $studentId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $termTotals = []; // term => [total, max]
    while ($r = mysqli_fetch_assoc($res)) {
        $marksByExam[$r['exam_name'] . ' (' . $r['exam_date'] . ')'][] = $r;
        if (!empty($r['term'])) {
            if (!isset($termTotals[$r['term']])) { $termTotals[$r['term']] = ['total' => 0, 'max' => 0]; }
            $termTotals[$r['term']]['total'] += $r['marks_obtained'];
            $termTotals[$r['term']]['max'] += $r['max_marks'];
        }
    }
    foreach (['Term 1', 'Term 2', 'Term 3'] as $t) {
        if (isset($termTotals[$t]) && $termTotals[$t]['max'] > 0) {
            $termAverages[$t] = round(($termTotals[$t]['total'] / $termTotals[$t]['max']) * 100, 1);
        } else {
            $termAverages[$t] = null;
        }
    }
}

include "../includes/teacher_header.php";
?>

<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">Select Student</h3>
    <form method="GET">
        <select name="student_id" onchange="this.form.submit()" style="width:100%; max-width:400px; padding:9px 12px; border:1px solid #dbe2f0; border-radius:8px;">
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $st): ?>
                <option value="<?php echo $st['id']; ?>" <?php echo $studentId==$st['id']?'selected':''; ?>><?php echo htmlspecialchars($st['full_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if ($studentId): ?>
<div class="panel" style="margin-bottom:20px;">
    <h3 style="margin-bottom:14px;">📊 Term Comparison (Average %)</h3>
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
    <h3>📄 Report Card</h3>
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