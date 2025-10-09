<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php';
$title='تسجيل الدخول'; $active='login';

if(is_post()){
  if(!require_fields($_POST,['email','password'])) flash('err','أدخلي البريد وكلمة المرور.');
  else{
    $stmt=$pdo->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->execute([trim($_POST['email'])]); $u=$stmt->fetch();
    if(!$u || !password_verify($_POST['password'],$u['password_hash'])) flash('err','بيانات الدخول غير صحيحة.');
    else{
      $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email']];
      flash('ok','أهلًا '.e($u['name']).'!'); redirect('rose_dashboard.php');
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
    <h1>تسجيل الدخول</h1>
    <?php foreach(['ok','warn','err'] as $k){ if($m=flash($k)) echo '<div class="alert '.($k==='ok'?'success':($k==='warn'?'warn':'danger')).'">'.e($m).'</div>'; } ?>
    <form method="post" class="auth-grid">
      <input class="input" type="email" name="email" placeholder="البريد الإلكتروني" required>
      <input class="input" type="password" name="password" placeholder="كلمة المرور" required>
      <button class="btn">دخول</button>
    </form>
    <div class="auth-alt">مستخدم جديد؟ <a href="rose_register.php">إنشاء حساب</a></div>
  </div>
</div>
</body>
</html>
