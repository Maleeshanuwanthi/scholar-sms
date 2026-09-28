USE scholar_sms;
ALTER TABLE parents ADD COLUMN access_token VARCHAR(64) UNIQUE NULL AFTER password;
