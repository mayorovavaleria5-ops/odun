<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_pass = $_POST['new_password'] ?? '';
    
    if (strlen($new_pass) >= 5) {
        // Хешируем новый пароль
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        // Обновляем в базе
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hash, $_SESSION['username']);
        if ($stmt->execute()) {
            $message = "<div style='color:green;'>Пароль успешно изменён!</div>";
        } else {
            $message = "<div style='color:red;'>Ошибка: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='color:red;'>Пароль должен быть не менее 5 символов.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена пароля</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4EFE6; padding: 40px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #1B3A4B; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Смена пароля</h2>
        <?= $message ?>
        <p><a href="admin.php">← Назад в админку</a></p>
        <form method="POST">
            <label>Новый пароль:</label>
            <input type="password" name="new_password" required>
            <button type="submit">Сохранить новый пароль</button>
        </form>
    </div>
</body>
</html>