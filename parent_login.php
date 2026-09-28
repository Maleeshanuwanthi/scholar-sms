<?php
session_start();
require "config/db.php";
require "includes/csrf.php";

if (isset($_SESSION['parent_id'])) {
    header("Location: parent_dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($conn, "SELECT id, username, password, full_name FROM parents WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($row['password'] && password_verify($password, $row['password'])) {
            $_SESSION['parent_id'] = $row['id'];
            $_SESSION['parent_name'] = $row['full_name'];
            header("Location: parent_dashboard.php");
            exit;
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Username not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Parent Login - Scholar SMS</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
        <div class="icon">👪</div>
        <h2>Parent Portal</h2>
        <p class="subtitle">Scholar SMS - Homework, Attendance & Payment Updates</p>

        <?php if ($error): ?>
            <div class="error-msg">❌ <?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit">🚀 Login</button>
        </form>
        <p class="hint"><a href="index.php" style="color:#4b6cb7;">School Staff Login →</a></p>
    </div>
</div>
</body>
</html>
