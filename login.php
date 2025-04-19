<?php
session_start();  // ログイン情報を覚えておくためのもの
require_once 'db_connect.php';  // データベース接続を読み込む

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // フォームが送られたとき
    $userId = $_POST['user_id'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: dashboard.php");  // 成功したら次のページへ
        exit;
    } else {
        $error = "IDかパスワードが間違ってるよ";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="Rx93style.css">
</head>
<body>
    <div class="login-container">
        <h2>ログイン</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>ID</label>
                <input type="text" name="user_id" required>
            </div>
            <div class="form-group">
                <label>パスワード</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">ログイン</button>
        </form>
        <p>アカウントをお持ちでない方は<a href="register.php">こちら</a>から登録してください</p>
    </div>
</body>
</html>