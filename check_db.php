<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__.'/rose_config.php';

echo "<h3>DB OK ✅</h3>";
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
echo "<pre>"; print_r($tables); echo "</pre>";
//http://localhost/iug/Assignment4/rose_register.php
