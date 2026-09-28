<?php
require "includes/teacher_auth.php";
require "config/db.php";
$base = "";
$pageTitle = "Dashboard";

$grade = $_SESSION['teacher_grade'];
$section = $_SESSION['teacher_section'];

$count = 0;
if ($grade && $section) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as c FROM students WHERE grade = ? AND section = ?");
    mysqli_stmt_bind_param($stmt, "ss", $grade, $section);
    mysqli_stmt_execute($stmt);
    $count = mysqli_stmt_get_result($stmt)->fetch_assoc()['c'];
}

include "includes/teacher_header.php";
?>

<div class="stats">
    <div class="stat-card"><div class="num"><?php echo $count; ?></div><div class="label">Students in My Class</div></div>
</div>

<div class="panel">
    <h3 style="margin-bottom:10px;">Welcome, <?php echo htmlspecialchars($_SESSION['teacher_name']); ?> 👋</h3>
    <?php if (!$grade || !$section): ?>
        <p style="color:#c0392b; font-size:14px;">⚠️ You are not assigned to a class yet. Please contact the school office.</p>
    <?php else: ?>
        <p style="color:#666; font-size:14px;">You are the class teacher for <b><?php echo htmlspecialchars($grade . ' ' . $section); ?></b>.
        Use the sidebar to mark attendance, view your students, enter exam marks, and view your class report.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
