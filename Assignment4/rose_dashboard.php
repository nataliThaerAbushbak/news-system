<?php
require_once __DIR__.'/rose_guard.php';
require_once __DIR__.'/rose_helpers.php';
$title='لوحة التحكم'; $active='dash';


$stats = [
  'cats' => (int)$pdo->query("SELECT COUNT(*) c FROM categories WHERE deleted_at IS NULL")->fetch()['c'],
  'news' => (int)$pdo->query("SELECT COUNT(*) c FROM news WHERE deleted_at IS NULL")->fetch()['c'],
  'pub'  => (int)$pdo->query("SELECT COUNT(*) c FROM news WHERE is_published=1 AND deleted_at IS NULL")->fetch()['c'],
  'draft'=> (int)$pdo->query("SELECT COUNT(*) c FROM news WHERE is_published=0 AND deleted_at IS NULL")->fetch()['c'],
];
$latest = $pdo->query("SELECT n.id,n.title, c.name cat, n.is_published, n.created_at FROM news n LEFT JOIN categories c ON c.id=n.category_id WHERE n.deleted_at IS NULL ORDER BY n.id DESC LIMIT 8")->fetchAll();
$active='dash'; include __DIR__.'/rose_header.php';
?>
<div class="dashboard">
  <aside class="sidebar">
    <div class="logo">🌷 News Admin</div>
    <div class="menu">
      <a class="active" href="rose_dashboard.php">الرئيسية</a>
      <a href="rose_categories.php">الفئات</a>
      <a href="rose_news.php">الأخبار</a>
    </div>
  </aside>
  <section class="main">
    <div class="toolbar">
      <h2>مرحبًا، <?php echo e($user['name']);?> 👋</h2>
      <a class="btn" href="rose_news.php">+ خبر جديد</a>
    </div>
    <div class="stats">
      <div class="stat"><div class="label">الفئات</div><div class="value"><?php echo $stats['cats'];?></div></div>
      <div class="stat"><div class="label">الأخبار</div><div class="value"><?php echo $stats['news'];?></div></div>
      <div class="stat"><div class="label">منشور</div><div class="value"><?php echo $stats['pub'];?></div></div>
      <div class="stat"><div class="label">مسوّدة</div><div class="value"><?php echo $stats['draft'];?></div></div>
    </div>

    <h3 style="margin-top:18px">آخر الأخبار</h3>
    <div class="card">
      <table class="table news-table">
        <thead><tr><th>العنوان</th><th>الفئة</th><th>الحالة</th><th>تاريخ</th></tr></thead>
        <tbody>
          <?php foreach($latest as $row): ?>
          <tr>
            <td><a href="rose_news.php?edit=<?php echo $row['id'];?>"><?php echo e($row['title']);?></a></td>
            <td><span class="badge pink"><?php echo e($row['cat'] ?? '—');?></span></td>
            <td><?php echo $row['is_published']?'<span class="badge green">منشور</span>':'<span class="badge pink">مسودة</span>';?></td>
            <td><?php echo e($row['created_at']);?></td>
          </tr>
          <?php endforeach;?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php include __DIR__.'/rose_footer.php'; ?>
