<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . " - Teacher Portal" : "Teacher Portal"; ?></title>
<link rel="stylesheet" href="<?php echo $base; ?>css/style.css">
</head>
<body>
<div class="layout">
<?php include $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/teacher_sidebar.php"; ?>
<main class="content">
<div class="topbar">
    <h1><?php echo isset($pageTitle) ? $pageTitle : "Dashboard"; ?></h1>
    <div class="user-badge">👩‍🏫 <?php echo htmlspecialchars($_SESSION['teacher_name'] ?? ''); ?> — <?php echo htmlspecialchars(($_SESSION['teacher_grade'] ?? '') . ' ' . ($_SESSION['teacher_section'] ?? '')); ?></div>
</div>
