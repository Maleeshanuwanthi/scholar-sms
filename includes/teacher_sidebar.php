<aside class="sidebar">
    <div class="brand">
        <svg width="52" height="52" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom:6px;">
            <defs>
                <linearGradient id="logoGrad2" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4b6cb7"/>
                    <stop offset="1" stop-color="#d16ba5"/>
                </linearGradient>
            </defs>
            <circle cx="32" cy="32" r="30" fill="url(#logoGrad2)"/>
            <path d="M32 16L54 25L32 34L10 25L32 16Z" fill="#fff"/>
            <path d="M18 29V39C18 39 24 44 32 44C40 44 46 39 46 39V29" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        <span class="brand-text">Teacher Portal</span>
    </div>
    <nav>
        <a href="<?php echo $base; ?>teacher_dashboard.php">📊 Dashboard</a>
        <a href="<?php echo $base; ?>teacher/students.php">🧑‍🎓 My Class Students</a>
        <a href="<?php echo $base; ?>teacher/attendance.php">✅ Mark Attendance</a>
        <a href="<?php echo $base; ?>teacher/marks.php">📝 Enter Marks</a>
        <a href="<?php echo $base; ?>teacher/report.php">📄 Class Report</a>
        <a href="<?php echo $base; ?>teacher_logout.php" class="logout">🚪 Logout</a>
    </nav>
</aside>
