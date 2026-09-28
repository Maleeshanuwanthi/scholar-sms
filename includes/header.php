<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? $pageTitle . " - Scholar SMS" : "Scholar SMS"; ?></title>
<link rel="stylesheet" href="<?php echo $base; ?>css/style.css">
</head>
<body>
<div class="layout">
<?php include $_SERVER['DOCUMENT_ROOT'] . "/scholar_sms/includes/sidebar.php"; ?>
<main class="content">
<div class="topbar">
    <div style="display:flex; align-items:center; gap:12px;">
        <svg width="34" height="34" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="topLogoGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4b6cb7"/>
                    <stop offset="1" stop-color="#d16ba5"/>
                </linearGradient>
            </defs>
            <circle cx="32" cy="32" r="30" fill="url(#topLogoGrad)"/>
            <path d="M32 16L54 25L32 34L10 25L32 16Z" fill="#fff"/>
            <path d="M18 29V39C18 39 24 44 32 44C40 44 46 39 46 39V29" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="54" y1="25" x2="54" y2="37" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        <h1><?php echo isset($pageTitle) ? $pageTitle : "Dashboard"; ?></h1>
    </div>
    <div class="user-badge">👤 <?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></div>
</div>
