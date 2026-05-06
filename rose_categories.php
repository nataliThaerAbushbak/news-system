<?php

echo "<!-- categories reached -->";

require_once __DIR__.'/rose_guard.php';
require_once __DIR__.'/rose_helpers.php';
$title='إدارة الفئات'; $active='cats';


if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['save_cat'])) {
  $id   = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $name = trim($_POST['name'] ?? '');
  if ($name === '') {
    flash('err','أدخلي اسم الفئة.');
    header('Location: rose_categories.php'); exit;
  }
  if ($id > 0) {
    $st = $pdo->prepare("UPDATE categories SET name=? WHERE id=?");
    $st->execute([$name, $id]);
    flash('ok','تم تعديل الفئة.');
  } else {
    $st = $pdo->prepare("INSERT INTO categories(name, created_at) VALUES(?, NOW())");
    $st->execute([$name]);
    flash('ok','تمت إضافة الفئة.');
  }
  header('Location: rose_categories.php'); exit;
}

// 
if (isset($_GET['del'])) {
  $id=(int)$_GET['del'];
  $st=$pdo->prepare("UPDATE categories SET deleted_at=NOW() WHERE id=? AND deleted_at IS NULL");
  $st->execute([$id]);
  flash('ok','تم حذف الفئة (حذف لطيف).');
  header('Location: rose_categories.php'); exit;
}


if (isset($_GET['restore'])) {
  $id=(int)$_GET['restore'];
  $st=$pdo->prepare("UPDATE categories SET deleted_at=NULL WHERE id=?");
  $st->execute([$id]);
  flash('ok','تمت الاستعادة.');
  header('Location: rose_categories.php'); exit;
}

$cats = [];
try {
  $cats = $pdo->query("SELECT * FROM categories ORDER BY (deleted_at IS NULL) DESC, id DESC")->fetchAll();
} catch (Throwable $e) {
}

include __DIR__.'/rose_header.php';
?>
<div class="card">
  <h2>الفئات</h2>
  <form method="post" class="form-grid cat-form" autocomplete="off">
    <input type="hidden" name="id" id="cat_id">
    <input class="input" name="name" id="cat_name" placeholder="اسم الفئة">
    <button class="btn" name="save_cat">حفظ</button>
  </form>
</div>

<div class="card" style="margin-top:14px">
  <h3>القائمة</h3>
  <div class="cat-list">
    <?php if (!$cats): ?>
      <div class="alert warn">لا توجد فئات بعد — أضيفي أول فئة من النموذج بالأعلى.</div>
    <?php else: ?>
      <?php foreach($cats as $c): ?>
        <div class="item <?php echo $c['deleted_at']?'row-deleted':''?>">
          <span class="name"><?php echo e($c['name']);?></span>
          <div class="actions">
            <?php if(!$c['deleted_at']): ?>
              <button class="icon-btn edit" onclick="editCat(<?php echo (int)$c['id'];?>,'<?php echo e($c['name']);?>')">تعديل</button>
              <a class="icon-btn delete" href="?del=<?php echo (int)$c['id'];?>" onclick="return confirm('حذف الفئة؟')">حذف</a>
            <?php else: ?>
              <a class="icon-btn" href="?restore=<?php echo (int)$c['id'];?>">استعادة</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach;?>
    <?php endif; ?>
  </div>
</div>

<script>
function editCat(id,name){
  document.getElementById('cat_id').value=id;
  document.getElementById('cat_name').value=name;
  document.getElementById('cat_name').focus();
}
</script>

<?php include __DIR__.'/rose_footer.php'; ?>
