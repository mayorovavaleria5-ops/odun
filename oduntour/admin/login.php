<?php
session_start();
require_once '../db.php'; // Подключаем нашу базу

if (isset($_SESSION['logged_in'])) {
    header("Location: admin.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Ищем пользователя в базе
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Проверяем пароль
if ($user && password_verify($password, $user['password'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role']; // Запоминаем роль!
    header("Location: admin.php");
        } else {
            $error = 'Неверный логин или пароль';
        }
    } else {
        $error = 'Заполните все поля';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход в админку</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4EFE6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 360px; }
        h2 { color: #1B3A4B; text-align: center; margin-top: 0; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Inter'; }
        button { width: 100%; padding: 12px; background: #C77B3F; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; font-family: 'Inter'; }
        button:hover { background: #a8622b; }
        .error { color: #d32f2f; text-align: center; margin-bottom: 15px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Вход в админку</h2>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>