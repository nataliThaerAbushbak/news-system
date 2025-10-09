<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php';
$title='تسجيل حساب'; $active='register';

if(is_post()){
  if(!require_fields($_POST,['name','email','password'])){
    flash('err','الرجاء تعبئة جميع الحقول.'); 
  }else{
    $name=trim($_POST['name']); $email=trim($_POST['email']); $pass=$_POST['password'];
  
    $stmt=$pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1"); $stmt->execute([$email]);
    if($stmt->fetch()){ flash('err','البريد مستخدم مسبقًا.'); }
    else{
      $hash=password_hash($pass,PASSWORD_DEFAULT);
      $pdo->prepare("INSERT INTO users(name,email,password_hash,created_at) VALUES(?,?,?,NOW())")
          ->execute([$name,$email,$hash]);
      flash('ok','تم إنشاء الحساب. يمكنك تسجيل الدخول الآن.');
      redirect('rose_login.php');
    }
  }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title><?php echo e($title);?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/shared.css">
  <link rel="stylesheet" href="css/auth.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card card">
    <h1>إنشاء حساب</h1>
    <p class="helper">أنشئي حسابًا لإدارة الأخبار والفئات.</p>
    <?php foreach(['ok','warn','err'] as $k){ if($m=flash($k)) echo '<div class="alert '.($k==='ok'?'success':($k==='warn'?'warn':'danger')).'">'.e($m).'</div>'; } ?>
    <form method="post" class="auth-grid">
      <input class="input" name="name" placeholder="الاسم الكامل" required>
      <input class="input" type="email" name="email" placeholder="البريد الإلكتروني" required>
      <input class="input" type="password" name="password" placeholder="كلمة المرور" required>
      <button class="btn">تسجيل</button>
    </form>
    <div class="auth-alt">لديك حساب؟ <a href="rose_login.php">تسجيل الدخول</a></div>
  </div>
</div>
</body>
</html>
