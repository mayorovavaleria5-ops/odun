<?php
session_start();
require_once '../db.php';

// Проверка: только админ или редактор
if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor')) {
    header("Location: admin.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        
        if ($stmt->execute()) {
            $message = "<div style='color:green;'>Новость успешно добавлена!</div>";
        } else {
            $message = "<div style='color:red;'>Ошибка: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='color:red;'>Заполните заголовок и текст.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить новость</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4EFE6; padding: 40px; }
        .container { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        textarea { height: 200px; resize: vertical; }
        button { background: #1B3A4B; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Добавить новость</h2>
        <?= $message ?>
        <p><a href="admin.php">← Назад в админку</a></p>
        <form method="POST">
            <label>Заголовок новости:</label>
            <input type="text" name="title" required>
            
            <label>Текст новости:</label>
            <textarea name="content" required></textarea>
            
            <button type="submit">Опубликовать</button>
        </form>
    </div>
</body>
</html>