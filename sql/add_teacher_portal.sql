USE scholar_sms;

-- Teacher login + class assignment
ALTER TABLE teachers ADD COLUMN username VARCHAR(50) UNIQUE NULL AFTER full_name;
ALTER TABLE teachers ADD COLUMN password VARCHAR(255) NULL AFTER username;
ALTER TABLE teachers ADD COLUMN assigned_grade VARCHAR(20) NULL AFTER password;
ALTER TABLE teachers ADD COLUMN assigned_section VARCHAR(20) NULL AFTER assigned_grade;

-- Exams (a test/term exam for a specific grade)
CREATE TABLE exams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    grade VARCHAR(20) NOT NULL,
    exam_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Marks (subject-wise marks per student per exam)
CREATE TABLE marks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    exam_id INT NOT NULL,
    student_id INT NOT NULL,
    subject VARCHAR(100) NOT NULL,
    marks_obtained DECIMAL(5,2) NOT NULL,
    max_marks DECIMAL(5,2) NOT NULL DEFAULT 100,
    entered_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_mark (exam_id, student_id, subject),
    FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);
