# Scholar SMS — Project Documentation

## 1. Introduction

**Scholar SMS** is a full-stack School Management System built with **PHP, MySQL, HTML and CSS**
as an HND final-year project. It replaces manual, paper-based school record keeping with a single
web-based dashboard for managing students, teachers, parents, attendance, activities, assignments,
and payments.

## 2. Objectives

- Centralize student, teacher, and parent records in one system
- Digitize daily attendance tracking, class-wise, for both students and teachers
- Track school payments by category (annual trip, school fees, sports meet, etc.)
- Provide a secure, role-protected login system for school staff
- Present data visually through dashboard charts for quick decision-making

## 3. System Modules

| Module       | Description                                                        |
|--------------|----------------------------------------------------------------------|
| Login        | Secure authentication with hashed passwords                         |
| Dashboard    | Overview stats + charts (school overview, payments by category)     |
| Students     | Add / edit / delete / search student records, linked to parents     |
| Teachers     | Add / edit / delete / search teacher records                        |
| Parents      | Add / edit / delete parent/guardian records                         |
| Attendance   | Mark attendance class-wise (students) or by date (teachers); reports |
| Activities   | Manage school events and extracurricular activities                 |
| Assignments  | Manage subject assignments and due dates                            |
| Payments     | Record and categorize payments; filter by category                  |
| About Us     | Project summary page                                                 |

## 4. Entity Relationship Overview

```
users (login accounts)

parents ─┬─< students >─┬─ attendance_students
         │               │
         │               └─< payments
         │
teachers ─< attendance_teachers

activities (standalone)
assignments (standalone)
```

- One **parent** can have many **students** (1-to-many)
- One **student** can have many **payments** and many **attendance_students** records
- One **teacher** can have many **attendance_teachers** records

## 5. Security Measures Implemented

1. **Password Hashing** — Passwords are stored using PHP's `password_hash()` (bcrypt) and verified
   with `password_verify()`. Plain-text passwords are never stored.
2. **Prepared Statements** — All database queries use `mysqli_prepare()` with bound parameters,
   preventing SQL Injection attacks.
3. **CSRF Protection** — Every form (Add/Edit across all modules) includes a unique, session-bound
   CSRF token that is verified on submission, preventing Cross-Site Request Forgery attacks.
4. **Session Timeout** — Users are automatically logged out after 15 minutes of inactivity,
   reducing the risk of unauthorized access on a shared/unattended computer.
5. **Access Control** — All internal pages check for an active login session (`auth.php`) before
   rendering any data; unauthenticated requests are redirected to the login page.
6. **Input Escaping** — All user-supplied data is escaped with `htmlspecialchars()` before being
   output, preventing Cross-Site Scripting (XSS).

## 6. Key Functionality Highlights

- **Search** — Students and Teachers lists support live search by name, admission number, grade,
  or subject.
- **Duplicate Validation** — The system prevents duplicate Admission Numbers when adding students.
- **Category-based Payments** — Payments are tagged into fixed categories (Annual Trip, School
  Payments, Sports Meet, Others) and can be filtered from the sidebar.
- **Class-wise Attendance** — Attendance can be marked per class (grade + section) for students,
  and per day for teachers, with historical reports filterable by date range.
- **Dashboard Analytics** — Bar charts (Chart.js) visualize student/teacher/parent counts and
  payment totals by category.

## 7. User Manual (Quick Start)

1. Start XAMPP (Apache + MySQL).
2. Import `sql/scholar_sms.sql` via phpMyAdmin to create the database and default admin account.
3. Open `http://localhost/scholar_sms` in a browser.
4. Log in with the default credentials: `admin` / `admin123`.
5. Use the sidebar to navigate between modules. Each module with sub-items (Students, Teachers,
   Attendance, Activities, Assignments, Payment) expands into an Add/List (or category) view.
6. To mark attendance: go to **Attendance → Mark Attendance**, choose Student or Teacher tab,
   select the date (and class, for students), then submit.

## 8. Testing (Sample Test Cases)

| Test Case                          | Expected Result                                  | Status |
|-------------------------------------|---------------------------------------------------|--------|
| Login with correct credentials      | Redirect to Dashboard                             | Pass   |
| Login with wrong password           | Show "Incorrect password!" error                  | Pass   |
| Add student with duplicate Adm. No  | Show validation error, record not inserted        | Pass   |
| Submit form without CSRF token      | Request blocked with "Invalid request" message    | Pass   |
| Idle for 15+ minutes                | Auto logout, redirected to login with notice      | Pass   |
| Search student by grade             | List filters to matching students only            | Pass   |
| Delete a student                    | Record removed, related payments cascade-deleted  | Pass   |

## 9. Technologies Used

- **Frontend:** HTML5, CSS3 (custom, no framework), Chart.js for graphs
- **Backend:** PHP (procedural, mysqli extension)
- **Database:** MySQL (via XAMPP / phpMyAdmin)
- **Security:** bcrypt password hashing, prepared statements, CSRF tokens, session timeout

## 10. Future Enhancements (Suggested)

- Role-based permissions (separate Teacher login with limited access)
- PDF/Excel export for reports
- Email notifications for payment reminders and assignment due dates
- Profile photo uploads for students and teachers
- Pagination for large data tables
