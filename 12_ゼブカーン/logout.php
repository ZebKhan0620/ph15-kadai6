<?php

session_start();

// セッションの保存してあるIDを削除
unset($_SESSION['id']);

// クッキーのIDも削除
setcookie('id', '', time() - 3600, '/');

// セッション全体を破棄
session_destroy();

// ログインページにリダイレクト
header('Location: ./login.php');
exit();

?>
