<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// News Management System by Natali Thaer Abu Shbak (ID 220230818)
if (session_status() === PHP_SESSION_NONE) session_start();

define('DB_HOST','localhost');
define('DB_NAME','rose_db');   
define('DB_USER','root');
define('DB_PASS','');

try {
 
  $pdoServer = new PDO(
    "mysql:host=".DB_HOST.";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
  );

  $pdoServer->exec(
    "CREATE DATABASE IF NOT EXISTS `".DB_NAME."`
     CHARACTER SET utf8mb4
     COLLATE utf8mb4_unicode_ci"
  );


  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );


  $pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(120) NOT NULL,
      email VARCHAR(160) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(120) NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      deleted_at DATETIME NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS news (
      id INT AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(255) NOT NULL,
      body MEDIUMTEXT NOT NULL,
      category_id INT NULL,
      is_published TINYINT(1) NOT NULL DEFAULT 0,
      thumb_path VARCHAR(255) NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      deleted_at DATETIME NULL,
      CONSTRAINT fk_news_cat FOREIGN KEY (category_id)
        REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");

} catch (Exception $e) {
  die("Database connection failed: ".$e->getMessage());
}


define('UPLOAD_DIR', __DIR__ . "/uploads/");
if (!is_dir(UPLOAD_DIR)) {
  @mkdir(UPLOAD_DIR, 0777, true);
}


$BASE = '';
