<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php'; 
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title><?php echo e($title ?? 'لوحة الأخبار'); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/shared.css?v=4">
  <?php if (!empty($_SESSION['user'])): ?>
    <link rel="stylesheet" href="css/dashboard.css?v=4">
    <link rel="stylesheet" href="css/news.css?v=4">
    <link rel="stylesheet" href="css/categories.css?v=4">
  <?php else: ?>
    <link rel="stylesheet" href="css/auth.css?v=4">
  <?php endif; ?>
</head>
<body>
<header class="header">
  <div class="brand">🌸 نظام إدارة الأخبار</div>
  <nav class="nav">
    <?php $active = $active ?? ''; ?>
    <?php if(!empty($_SESSION['user'])): ?>
      <a class="<?php echo $active==='dash'?'active':''?>" href="rose_dashboard.php">الرئيسية</a>
      <a class="<?php echo $active==='cats'?'active':''?>" href="rose_categories.php">الفئات</a>
      <a class="<?php echo $active==='news'?'active':''?>" href="rose_news.php">الأخبار</a>
      <a href="rose_logout.php">خروج</a>
    <?php else: ?>
      <a class="<?php echo $active==='login'?'active':''?>" href="rose_login.php">دخول</a>
      <a class="<?php echo $active==='register'?'active':''?>" href="rose_register.php">تسجيل</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">
<?php
foreach(['ok','warn','err'] as $k){
  if($m = flash($k)){
    $cls = $k==='ok'?'success':($k==='warn'?'warn':'danger');
    echo '<div class="alert '.$cls.'">'.e($m).'</div>';
  }
}
