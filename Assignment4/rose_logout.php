<?php
require_once __DIR__.'/rose_config.php';
require_once __DIR__.'/rose_helpers.php';


$_SESSION = [];
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

redirect('rose_logged_out.php');
