<?php
require_once __DIR__.'/rose_guard.php';
require_once __DIR__.'/rose_helpers.php';
$title='إدارة الأخبار'; $active='news';


$catOptions = $pdo->query("SELECT id,name FROM categories WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();

if(is_post() && isset($_POST['save_news'])){
  if(!require_fields($_POST,['title','body'])){ flash('err','العنوان والمحتوى مطلوبان.'); redirect('rose_news.php'); }
  $id = (int)($_POST['id'] ?? 0);
  $titleV = trim($_POST['title']); $bodyV = trim($_POST['body']);
  $catId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
  $isPub = !empty($_POST['is_published']) ? 1 : 0;

  
  $thumbSql = ""; $thumbParam = [];
  if(!empty($_FILES['thumb']['name']) && is_uploaded_file($_FILES['thumb']['tmp_name'])){
    $ext = strtolower(pathinfo($_FILES['thumb']['name'], PATHINFO_EXTENSION));
    if(!in_array($ext,['jpg','jpeg','png','gif','webp'])){ flash('warn','امتداد الصورة غير مدعوم.'); }
    else{
      $new = 'thumb_'.time().'_'.mt_rand(1000,9999).'.'.$ext;
      @move_uploaded_file($_FILES['thumb']['tmp_name'], UPLOAD_DIR.$new);
      $thumbSql = ", thumb_path=?"; $thumbParam = [$new];
    }
  }

  if($id){ // edit
    $sql = "UPDATE news SET title=?, body=?, category_id=?, is_published=? $thumbSql WHERE id=?";
    $params = array_merge([$titleV,$bodyV,$catId,$isPub], $thumbParam, [$id]);
    $pdo->prepare($sql)->execute($params);
    flash('ok','تم تحديث الخبر.');
  }else{ // addition
    $sql = "INSERT INTO news(title,body,category_id,is_published,thumb_path,created_at) VALUES(?,?,?,?,?,NOW())";
    $thumb = $thumbParam[0] ?? null;
    $pdo->prepare($sql)->execute([$titleV,$bodyV,$catId,$isPub,$thumb]);
    flash('ok','تمت إضافة الخبر.');
  }
  redirect('rose_news.php');
}

//delete
if(isset($_GET['del'])){
  $id=(int)$_GET['del'];
  $pdo->prepare("UPDATE news SET deleted_at=NOW() WHERE id=? AND deleted_at IS NULL")->execute([$id]);
  flash('ok','تم حذف الخبر (حذف لطيف).'); redirect('rose_news.php');
}


if(isset($_GET['restore'])){
  $id=(int)$_GET['restore'];
  $pdo->prepare("UPDATE news SET deleted_at=NULL WHERE id=?")->execute([$id]);
  flash('ok','تمت الاستعادة.'); redirect('rose_news.php');
}

$editRow=null;
if(isset($_GET['edit'])){
  $stmt=$pdo->prepare("SELECT * FROM news WHERE id=?"); $stmt->execute([(int)$_GET['edit']]);
  $editRow=$stmt->fetch();
}

$filter = trim($_GET['q'] ?? '');
$where = "WHERE n.deleted_at IS NULL";
$params=[];
if($filter!==''){ $where.=" AND (n.title LIKE ? OR n.body LIKE ?)"; $params[]="%$filter%"; $params[]="%$filter%"; }

$rows = $pdo->prepare("SELECT n.*, c.name cat FROM news n LEFT JOIN categories c ON c.id=n.category_id $where ORDER BY n.id DESC");
$rows->execute($params); $rows=$rows->fetchAll();

include __DIR__.'/rose_header.php';
?>
<div class="news-grid">
  <div class="editor card">
    <h2><?php echo $editRow?'تعديل خبر':'إضافة خبر';?></h2>
    <form method="post" enctype="multipart/form-data" class="form-row">
      <input type="hidden" name="id" value="<?php echo (int)($editRow['id']??0);?>">
      <input class="input" name="title" placeholder="عنوان الخبر" value="<?php echo e($editRow['title']??'');?>" required>
      <select class="input" name="category_id">
        <option value="">— اختر الفئة (اختياري) —</option>
        <?php foreach($catOptions as $c): ?>
          <option value="<?php echo (int)$c['id'];?>" <?php echo (!empty($editRow['category_id']) && $editRow['category_id']==$c['id'])?'selected':''; ?>>
            <?php echo e($c['name']);?>
          </option>
        <?php endforeach;?>
      </select>
      <textarea class="input" name="body" placeholder="نص الخبر" required><?php echo e($editRow['body']??'');?></textarea>
      <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        <label class="switch <?php echo !empty($editRow['is_published'])?'on':'';?>">
          <input type="checkbox" name="is_published" style="display:none" onchange="this.parentElement.classList.toggle('on')" <?php echo !empty($editRow['is_published'])?'checked':'';?>>
        </label>
        <span>نشر الآن</span>
        <input type="file" name="thumb" accept=".jpg,.jpeg,.png,.gif,.webp">
        <?php if(!empty($editRow['thumb_path'])): ?>
          <img class="news-thumb" src="<?php echo 'uploads/'.e($editRow['thumb_path']);?>" alt="">
        <?php endif; ?>
      </div>
      <button class="btn" name="save_news"><?php echo $editRow?'تحديث':'حفظ';?></button>
    </form>
  </div>

  <div class="preview card">
    <h3>بحث</h3>
    <form method="get" class="form-row">
      <input class="input" name="q" value="<?php echo e($filter);?>" placeholder="ابحثي بعنوان/نص">
      <button class="btn outline">تصفية</button>
    </form>
    <h3 style="margin-top:12px">جميع الأخبار</h3>
    <table class="table news-table">
      <thead><tr><th>صورة</th><th>العنوان</th><th>الفئة</th><th>الحالة</th><th>إجراءات</th></tr></thead>
      <tbody>
      <?php foreach($rows as $r): ?>
        <tr class="<?php echo $r['deleted_at']?'row-deleted':''?>">
          <td>
            <?php if($r['thumb_path']): ?>
              <img class="news-thumb" src="<?php echo 'uploads/'.e($r['thumb_path']);?>" alt="">
            <?php else: ?> — <?php endif; ?>
          </td>
          <td><?php echo e($r['title']);?></td>
          <td><span class="badge pink"><?php echo e($r['cat']??'—');?></span></td>
          <td><?php echo $r['is_published']?'<span class="badge green">منشور</span>':'<span class="badge pink">مسودة</span>';?></td>
          <td class="actions">
            <a class="icon-btn edit" href="?edit=<?php echo (int)$r['id'];?>">تعديل</a>
            <?php if(!$r['deleted_at']): ?>
              <a class="icon-btn delete" href="?del=<?php echo (int)$r['id'];?>" onclick="return confirm('حذف الخبر؟')">حذف</a>
            <?php else: ?>
              <a class="icon-btn" href="?restore=<?php echo (int)$r['id'];?>">استعادة</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach;?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__.'/rose_footer.php'; ?>
