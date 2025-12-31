USE perpustakaan_mvp;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

USE perpustakaan_mvp;
SHOW TABLES;

USE perpustakaan_mvp;
SELECT id, username, role, created_at FROM users;

USE perpustakaan_mvp;
UPDATE users SET username='devania' WHERE username='admin';

