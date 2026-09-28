<aside class="sidebar">
    <div class="brand">
        <svg width="52" height="52" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-bottom:6px;">
            <defs>
                <linearGradient id="logoGrad" x1="0" y1="0" x2="64" y2="64" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#4b6cb7"/>
                    <stop offset="1" stop-color="#d16ba5"/>
                </linearGradient>
            </defs>
            <circle cx="32" cy="32" r="30" fill="url(#logoGrad)"/>
            <path d="M32 16L54 25L32 34L10 25L32 16Z" fill="#fff"/>
            <path d="M18 29V39C18 39 24 44 32 44C40 44 46 39 46 39V29" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
            <line x1="54" y1="25" x2="54" y2="37" stroke="#fff" stroke-width="2.5" stroke-linecap="round"/>
        </svg>
        <span class="brand-text">Scholar SMS</span>
    </div>
    <nav>
        <a href="<?php echo $base; ?>dashboard.php">📊 Dashboard</a>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/students/')!==false)?'open':''; ?>>
            <summary>🧑‍🎓 Students</summary>
            <a href="<?php echo $base; ?>students/add.php" class="sub-link">➕ Add Student</a>
            <a href="<?php echo $base; ?>students/list.php" class="sub-link">📋 Student Details</a>
        </details>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/teachers/')!==false)?'open':''; ?>>
            <summary>👩‍🏫 Teachers</summary>
            <a href="<?php echo $base; ?>teachers/add.php" class="sub-link">➕ Add Teacher</a>
            <a href="<?php echo $base; ?>teachers/list.php" class="sub-link">📋 Teacher Details</a>
        </details>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/attendance/')!==false)?'open':''; ?>>
            <summary>📋 Attendance</summary>
            <a href="<?php echo $base; ?>attendance/mark.php" class="sub-link">✅ Mark Attendance</a>
            <a href="<?php echo $base; ?>attendance/report.php" class="sub-link">📋 Attendance Report</a>
        </details>

        <a href="<?php echo $base; ?>parents/list.php">👪 Parents</a>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/activities/')!==false)?'open':''; ?>>
            <summary>🏅 Activities</summary>
            <a href="<?php echo $base; ?>activities/add.php" class="sub-link">➕ Add Activities</a>
            <a href="<?php echo $base; ?>activities/list.php" class="sub-link">📋 Activity Details</a>
        </details>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/assignments/')!==false)?'open':''; ?>>
            <summary>📝 Assignments</summary>
            <a href="<?php echo $base; ?>assignments/add.php" class="sub-link">➕ Add Assignment</a>
            <a href="<?php echo $base; ?>assignments/list.php" class="sub-link">📋 Assignment Marks</a>
        </details>

        <a href="<?php echo $base; ?>notifications/list.php">🔔 Notifications</a>

        <a href="<?php echo $base; ?>reports/view.php">📄 Reports</a>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/payments/')!==false)?'open':''; ?>>
            <summary>💳 Payment</summary>
            <a href="<?php echo $base; ?>payments/list.php?category=Annual+Trip" class="sub-link">🚌 Annual Trip</a>
            <a href="<?php echo $base; ?>payments/list.php?category=School+Payments" class="sub-link">🏫 School Payments</a>
            <a href="<?php echo $base; ?>payments/list.php?category=Sports+Meet" class="sub-link">🏆 Sports Meet</a>
            <a href="<?php echo $base; ?>payments/list.php?category=Others" class="sub-link">📦 Others</a>
        </details>

        <details class="nav-group" <?php echo (strpos($_SERVER['PHP_SELF'],'/gallery/')!==false)?'open':''; ?>>
            <summary>🖼️ Gallery</summary>
            <a href="<?php echo $base; ?>gallery/list.php" class="sub-link">📋 All Photos</a>
            <a href="<?php echo $base; ?>gallery/list.php?category=School+Events" class="sub-link">🎉 School Events</a>
            <a href="<?php echo $base; ?>gallery/list.php?category=Functions" class="sub-link">🎤 Functions</a>
            <a href="<?php echo $base; ?>gallery/list.php?category=Sports+Meet" class="sub-link">🏆 Sports Meet</a>
            <a href="<?php echo $base; ?>gallery/list.php?category=Student+Intake" class="sub-link">🧒 Student Intake</a>
            <a href="<?php echo $base; ?>gallery/list.php?category=Others" class="sub-link">📦 Others</a>
        </details>

        <a href="<?php echo $base; ?>about.php">ℹ️ About Us</a>
        <a href="<?php echo $base; ?>logout.php" class="logout">🚪 Logout</a>
    </nav>
</aside>