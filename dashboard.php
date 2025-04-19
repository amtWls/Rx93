<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// 画像アップロード処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $userId = $_SESSION['user_id'];
    $tags = $_POST['tags'];
    $image = $_FILES['image'];

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($image['type'], $allowedTypes)) {
        $error = "画像ファイルのみアップロードできます";
    } else {
        $uploadDir = 'Uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $filename = uniqid() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $uploadPath = $uploadDir . $filename;
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $stmt = $pdo->prepare("INSERT INTO images (user_id, filename, tags) VALUES (?, ?, ?)");
            if ($stmt->execute([$userId, $filename, $tags])) {
                $success = "画像がアップロードされました";
            } else {
                $error = "データベースへの保存に失敗しました";
            }
        } else {
            $error = "ファイルのアップロードに失敗しました: コード " . $image['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
    <style>
        #image-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            padding: 10px;
        }
        .image-container {
            position: relative;
        }
        .image-container img {
            width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>ようこそ、<?php echo htmlspecialchars($_SESSION['user_id']); ?>さん！</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <h3>画像をアップロード</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>画像を選択</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>タグ（カンマ区切り）</label>
                <input type="text" name="tags" placeholder="例: 猫,動物">
            </div>
            <button type="submit">アップロード</button>
        </form>
        <h3>すべての画像</h3>
        <div id="image-list">
            <?php
            $stmt = $pdo->query("SELECT id, filename FROM images ORDER BY created_at DESC");
            $images = $stmt->fetchAll();
            foreach ($images as $image) {
                echo '<div class="image-container">';
                echo '<a href="image_detail.php?id=' . $image['id'] . '" target="_blank">';
                echo '<img src="Uploads/' . htmlspecialchars($image['filename']) . '" alt="投稿画像">';
                echo '</a>';
                echo '</div>';
            }
            ?>
        </div>
        <a href="logout.php">ログアウト</a>
    </div>
</body>
</html>