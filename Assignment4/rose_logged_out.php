<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php';
$title = 'تم تسجيل خروجك';
$active = 'login'; 
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title><?php echo e($title); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/shared.css?v=6">
  <link rel="stylesheet" href="css/auth.css?v=6">
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card card" style="text-align:center">
      <h1>تم تسجيل خروجك بنجاح ✅</h1>
      <p class="helper">نراك لاحقًا! بإمكانك العودة لتسجيل الدخول متى شئتِ.</p>
      <div class="auth-actions" style="justify-content:center">
        <a class="btn" href="rose_login.php">العودة لتسجيل الدخول</a>
      </div>
      <div class="auth-alt" style="margin-top:10px">
        <a href="rose_register.php">إنشاء حساب جديد</a>
      </div>
    </div>
  </div>
</body>
</html>
