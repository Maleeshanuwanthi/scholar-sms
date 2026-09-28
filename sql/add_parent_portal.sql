USE scholar_sms;

ALTER TABLE parents ADD COLUMN username VARCHAR(50) UNIQUE NULL AFTER full_name;
ALTER TABLE parents ADD COLUMN password VARCHAR(255) NULL AFTER username;

ALTER TABLE notifications MODIFY assignment_id INT NULL;
ALTER TABLE notifications ADD COLUMN type VARCHAR(20) NOT NULL DEFAULT 'assignment' AFTER assignment_id;
