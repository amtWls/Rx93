<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("画像IDが指定されていません");
}

// 画像情報を取得
$stmt = $pdo->prepare("SELECT * FROM images WHERE id = ?");
$stmt->execute([$_GET['id']]);
$image = $stmt->fetch();
if (!$image) {
    die("画像が見つかりません");
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_image'])) {
    $imageId = $_POST['image_id'];
    $userId = $_SESSION['user_id'];

    if ($image['user_id'] === $userId) {
        $filePath = 'Uploads/' . $image['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ? AND user_id = ?");
        if ($stmt->execute([$imageId, $userId])) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "画像の削除に失敗しました";
        }
    } else {
        $error = "削除権限がありません";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>画像詳細</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .detail-container {
            max-width: 600px;
            margin: 20px auto;
            text-align: center;
        }
        .detail-container img {
            max-width: 100%;
            border-radius: 8px;
        }
        .detail-container p {
            margin: 10px 0;
        }
        .delete-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="detail-container">
            <h2>画像詳細</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <img src="Uploads/<?php echo htmlspecialchars($image['filename']); ?>" alt="画像">
            <p><strong>投稿者:</strong> <?php echo htmlspecialchars($image['user_id']); ?></p>
            <p><strong>ファイル名:</strong> <?php echo htmlspecialchars($image['filename']); ?></p>
            <p><strong>タグ:</strong> <?php echo htmlspecialchars($image['tags']) ?: 'なし'; ?></p>
            <p><strong>投稿日時:</strong> <?php echo htmlspecialchars($image['created_at']); ?></p>
            <?php if ($image['user_id'] === $_SESSION['user_id']): ?>
                <form method="POST">
                    <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                    <button type="submit" name="delete_image" class="delete-btn">削除</button>
                </form>
            <?php endif; ?>
            <p><a href="dashboard.php">戻る</a></p>
        </div>
    </div>
</body>
</html>