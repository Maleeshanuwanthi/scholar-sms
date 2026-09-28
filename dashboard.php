<?php
require "includes/auth.php";
require "config/db.php";
$base = "";
$pageTitle = "Dashboard";

function countRows($conn, $table) {
    $r = mysqli_query($conn, "SELECT COUNT(*) as c FROM $table");
    $row = mysqli_fetch_assoc($r);
    return $row['c'];
}

$totalStudents = countRows($conn, "students");
$totalTeachers = countRows($conn, "teachers");
$totalParents = countRows($conn, "parents");
$totalAssignments = countRows($conn, "assignments");

// ---- Chart 1: Module counts (Column chart) ----
$moduleLabels = ["Students", "Teachers", "Parents", "Assignments"];
$moduleCounts = [$totalStudents, $totalTeachers, $totalParents, $totalAssignments];

// ---- Chart 2: Payments by category (Bar chart) ----
$payRes = mysqli_query($conn, "SELECT purpose, SUM(amount) as total FROM payments GROUP BY purpose");
$paymentLabels = [];
$paymentTotals = [];
while ($row = mysqli_fetch_assoc($payRes)) {
    $paymentLabels[] = $row['purpose'];
    $paymentTotals[] = $row['total'];
}

include "includes/header.php";
?>

<div class="stats">
    <div class="stat-card"><div class="num"><?php echo $totalStudents; ?></div><div class="label">Students</div></div>
    <div class="stat-card"><div class="num"><?php echo $totalTeachers; ?></div><div class="label">Teachers</div></div>
    <div class="stat-card"><div class="num"><?php echo $totalParents; ?></div><div class="label">Parents</div></div>
    <div class="stat-card"><div class="num"><?php echo $totalAssignments; ?></div><div class="label">Assignments</div></div>
</div>

<div class="panel">
    <h3 style="margin-bottom:10px;">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> 👋</h3>
    <p style="color:#666; font-size:14px;">Use the sidebar to manage Students, Teachers, Parents, Activities, Assignments and Payments.</p>
</div>

<div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:20px;">
    <div class="panel" style="flex:1; min-width:320px;">
        <h3 style="margin-bottom:16px;">📊 School Overview</h3>
        <canvas id="overviewChart" height="220"></canvas>
    </div>
    <div class="panel" style="flex:1; min-width:320px;">
        <h3 style="margin-bottom:16px;">📉 Payments by Category (Rs.)</h3>
        <canvas id="paymentsChart" height="220"></canvas>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('overviewChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($moduleLabels); ?>,
        datasets: [{
            label: 'Total Count',
            data: <?php echo json_encode($moduleCounts); ?>,
            backgroundColor: ['#4b6cb7', '#d16ba5', '#f4a261', '#2a9d8f']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

new Chart(document.getElementById('paymentsChart'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($paymentLabels); ?>,
        datasets: [{
            label: 'Amount (Rs.)',
            data: <?php echo json_encode($paymentTotals); ?>,
            backgroundColor: '#4b6cb7'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<?php include "includes/footer.php"; ?>
