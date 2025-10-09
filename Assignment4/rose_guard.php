<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php'; // ⬅️ ضروري

if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user'])) {
  flash('warn','الرجاء تسجيل الدخول أولاً.');
  redirect('rose_login.php');
}

$user = $_SESSION['user'];
