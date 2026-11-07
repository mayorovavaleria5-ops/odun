<?php
session_start();
require_once '../db.php';

// Защита: только для главного админа
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'operator';

    if (!empty($username) && !empty($password)) {
        // Хешируем пароль
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Проверяем, нет ли уже такого логина
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $message = "<div style='color:red;'>Пользователь с таким логином уже существует.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hash, $role);
            if ($stmt->execute()) {
                $message = "<div style='color:green;'>Сотрудник успешно добавлен!</div>";
            } else {
                $message = "<div style='color:red;'>Ошибка: " . $conn->error . "</div>";
            }
        }
    } else {
        $message = "<div style='color:red;'>Заполните все поля.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить сотрудника</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4EFE6; padding: 40px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; }
        input, select { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { background: #1B3A4B; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Добавить сотрудника</h2>
        <?= $message ?>
        <p><a href="admin.php">← Назад в админку</a></p>
        <form method="POST">
            <label>Логин:</label>
            <input type="text" name="username" required>
            
            <label>Пароль:</label>
            <input type="password" name="password" required>
            
            <label>Роль:</label>
            <select name="role">
                <option value="operator">Оператор (только заявки)</option>
                <option value="admin">Главный администратор (полный доступ)</option>
            </select>
            
            <button type="submit">Создать</button>
        </form>
    </div>
</body>
</html>