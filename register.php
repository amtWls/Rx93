<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $password = $_POST['password'];

    // IDが既に存在するかチェック
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    if ($stmt->fetch()) {
        $error = "このIDは既に使用されています";
    } else {
        // パスワードをハッシュ化
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // データベースに登録
        $stmt = $pdo->prepare("INSERT INTO users (user_id, password) VALUES (?, ?)");
        if ($stmt->execute([$userId, $hashedPassword])) {
            $success = "登録が完了しました。ログインしてください。";
        } else {
            $error = "登録に失敗しました";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
    <link rel="stylesheet" href="Rx93style.css">
</head>
<body>
    <div class="login-container">
        <h2>会員登録</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
            <p><a href="login.php">ログインページへ</a></p>
        <?php else: ?>
            <form method="POST">
                <div class="form-group">
                    <label>ID</label>
                    <input type="text" name="user_id" required>
                </div>
                <div class="form-group">
                    <label>パスワード</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit">登録</button>
            </form>
            <p>既にアカウントをお持ちの方は<a href="login.php">こちら</a>からログイン</p>
        <?php endif; ?>
    </div>
</body>
</html>