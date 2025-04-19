<?php
$dbHost = 'localhost';  // サーバーの場所（普通はこれでOK）
$dbUser = 'root';       // MySQLのユーザー名（XAMPPならこれ）
$dbPass = '';           // パスワード（XAMPPなら空でOK）
$dbName = 'image_db';   // さっき作ったデータベース名

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("接続失敗: " . $e->getMessage());
}
?>