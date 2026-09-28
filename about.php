<?php
require "includes/auth.php";
$base = "";
$pageTitle = "About Us";
include "includes/header.php";
?>
<div class="panel" style="max-width: 780px; margin: 0 auto; padding: 32px 36px;">
    <h3 style="margin-bottom: 16px;">About Scholar SMS</h3>

    <p style="color:#555; line-height:1.8; font-size:14px; margin-bottom:16px;">
        <strong>Scholar SMS</strong> is a full-stack School Management System developed using
        PHP and MySQL as a final-year HND project. The system is designed to simplify and
        digitize the day-to-day administrative work of a school by bringing student, teacher,
        parent, and financial records together into one secure, easy-to-use platform.
    </p>

    <p style="color:#555; line-height:1.8; font-size:14px; margin-bottom:16px;">
        The application replaces manual, paper-based record keeping with a centralized web
        dashboard that school staff can access from any computer on the network. It focuses on
        clarity, ease of use, and reliable data management for everyday school operations.
    </p>

    <h4 style="margin: 24px 0 10px; color:#2f3b52;">Key Modules</h4>
    <ul style="color:#555; line-height:1.9; font-size:14px; padding-left: 20px; margin-bottom: 20px;">
        <li><strong>Students</strong> – Register and manage student profiles and class details</li>
        <li><strong>Teachers</strong> – Maintain teacher records and subject assignments</li>
        <li><strong>Parents</strong> – Link parents/guardians to their children</li>
        <li><strong>Attendance</strong> – Mark daily attendance class-wise for students and teachers, with attendance reports</li>
        <li><strong>Activities</strong> – Track school events and extracurricular activities</li>
        <li><strong>Assignments</strong> – Manage subject assignments and due dates</li>
        <li><strong>Payments</strong> – Record and track school payments by category (annual trip, school fees, sports meet, etc.)</li>
    </ul>

    <h4 style="margin: 24px 0 10px; color:#2f3b52;">Built With</h4>
    <p style="color:#555; line-height:1.8; font-size:14px;">
        HTML, CSS, PHP, and MySQL — with secure password hashing and a clean, responsive dashboard interface.
    </p>
</div>
<?php include "includes/footer.php"; ?>
