<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";

$category = isset($_GET['category']) ? trim($_GET['category']) : "";
$pageTitle = $category ? $category : "Gallery";

if ($category) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM gallery WHERE category = ? ORDER BY uploaded_at DESC");
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $result = mysqli_query($conn, "SELECT * FROM gallery ORDER BY uploaded_at DESC");
}

include "../includes/header.php";
?>
<?php if (isset($_GET['success'])): ?>
<div class="alert-success">✅ Photo uploaded successfully.</div>
<?php endif; ?>

<div class="panel">
    <div class="panel-header">
        <h3><?php echo htmlspecialchars($pageTitle); ?></h3>
        <a href="add.php<?php echo $category ? '?category='.urlencode($category) : ''; ?>" class="btn">+ Add Photo</a>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:18px; margin-top:16px;">
        <?php if (mysqli_num_rows($result) === 0): ?>
            <p style="color:#999; grid-column:1/-1;">No photos uploaded yet.</p>
        <?php endif; ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div style="background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <img src="<?php echo $base . htmlspecialchars($row['image_path']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>"
                style="width:100%; height:160px; object-fit:cover; display:block;">
            <div style="padding:10px 12px;">
                <div style="font-size:13px; font-weight:600; color:#2f3b52; margin-bottom:4px;"><?php echo htmlspecialchars($row['title']); ?></div>
                <div style="font-size:11px; color:#999; margin-bottom:8px;">
                    <?php echo htmlspecialchars($row['category']); ?> · <?php echo htmlspecialchars(date("Y-m-d", strtotime($row['uploaded_at']))); ?>
                </div>
                <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" style="font-size:11px; padding:5px 10px;"
                    onclick="return confirm('Delete this photo?')">Delete</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<?php include "../includes/footer.php"; ?>