<?php
function flash($key,$msg=null){
  if($msg!==null){ $_SESSION['flash'][$key]=$msg; return; }
  if(!empty($_SESSION['flash'][$key])){ $m=$_SESSION['flash'][$key]; unset($_SESSION['flash'][$key]); return $m; }
  return null;
}
function redirect($path){ header("Location: $path"); exit; }
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES,'UTF-8'); }
function is_post(){ return ($_SERVER['REQUEST_METHOD']??'GET')==='POST'; }
function require_fields($arr,$fields){
  foreach($fields as $f){ if(!isset($arr[$f]) || trim($arr[$f])==='') return false; }
  return true;
}
