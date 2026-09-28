<?php
require "../includes/auth.php";
require "../config/db.php";
$base = "../";
$pageTitle = "Add Photo";

$categories = ["School Events", "Functions", "Sports Meet", "Student Intake", "Others"];
$formError = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);

    if ($title === "" || $category === "") {
        $formError = "Title and Category are required.";
    } elseif (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $formError = "Please choose a photo to upload.";
    } else {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
        $mime = mime_content_type($_FILES['photo']['tmp_name']);

        if (!isset($allowed[$mime])) {
            $formError = "Only JPG, PNG, GIF, or WEBP images are allowed.";
        } else {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/uploads/gallery/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid('photo_') . '.' . $allowed[$mime];
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $filename)) {
                $imagePath = "uploads/gallery/" . $filename;
                $stmt = mysqli_prepare($conn, "INSERT INTO gallery (title, category, image_path) VALUES (?,?,?)");
                mysqli_stmt_bind_param($stmt, "sss", $title, $category, $imagePath);
                mysqli_stmt_execute($stmt);
                header("Location: list.php?success=1&category=" . urlencode($category));
                exit;
            } else {
                $formError = "Failed to save the uploaded photo. Check folder permissions.";
            }
        }
    }
}

include "../includes/header.php";
?>
<div class="panel">
    <h3>Add New Photo</h3>
    <?php if ($formError): ?>
        <div class="error-msg" style="margin-bottom:14px;">❌ <?php echo htmlspecialchars($formError); ?></div>
    <?php endif; ?>
    <form method="POST" class="styled" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <label>Title / Caption</label>
        <input type="text" name="title" placeholder="e.g. Annual Sports Meet 2026 - 100m Final" required>
        <label>Category</label>
        <select name="category" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <label>Photo</label>
        <input type="file" name="photo" accept="image/*" required>
        <div class="actions">
            <button type="submit" class="btn">Upload</button>
            <a href="list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include "../includes/footer.php"; ?>