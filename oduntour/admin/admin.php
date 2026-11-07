<?php
session_start();

// 1. ПРОВЕРКА АВТОРИЗАЦИИ
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

require_once '../db.php';

// Получаем роль текущего пользователя (если не указана, считаем оператором)
$userRole = $_SESSION['role'] ?? 'operator';
$username = $_SESSION['username'] ?? 'Пользователь';

// 2. ПОЛУЧЕНИЕ ЗАЯВОК ИЗ БАЗЫ
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");

// 3. ОБРАБОТКА УДАЛЕНИЯ (Только для главного админа)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $userRole === 'admin') {
    $delete_id = (int)$_POST['delete_id'];
    $conn->query("DELETE FROM leads WHERE id = $delete_id");
    header("Location: admin.php"); // Обновляем страницу после удаления
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка ОДУН ТУР</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #F4EFE6; padding: 40px 20px; color: #2C2C2C; margin: 0; }
        .container { max-width: 1100px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(27,58,75,.10); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
        h1 { color: #1B3A4B; margin: 0; font-size: 24px; }
        .user-info { background: #EDE6D8; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; }
        .user-info span { color: #C77B3F; font-weight: 700; }
        .nav-links { margin-bottom: 20px; display: flex; gap: 15px; flex-wrap: wrap; font-size: 15px; }
        .nav-links a { color: #1B3A4B; text-decoration: none; font-weight: 500; padding: 8px 16px; background: #F4EFE6; border-radius: 8px; transition: 0.2s; }
        .nav-links a:hover { background: #C77B3F; color: #fff; }
        .nav-links a.logout { color: #d32f2f; background: #ffebee; }
        .nav-links a.logout:hover { background: #d32f2f; color: #fff; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 15px; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid #eee; vertical-align: top; }
        th { background: #1B3A4B; color: #fff; font-weight: 500; letter-spacing: 0.5px; }
        tr:hover { background: #fafafa; }
        .empty { color: #888; font-style: italic; padding: 30px; text-align: center; background: #fafafa; border-radius: 8px; }
        .btn-delete { background: #ffebee; color: #d32f2f; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: 0.2s; }
        .btn-delete:hover { background: #d32f2f; color: #fff; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-admin { background: #E8F5E9; color: #2E7D32; }
        .badge-operator { background: #FFF3E0; color: #E65100; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Заявки с сайта</h1>
            <div class="user-info">
                Вы вошли как: <span><?= htmlspecialchars($username) ?></span> 
                <?php if ($userRole === 'admin'): ?>
                    <span class="badge badge-admin">Админ</span>
                <?php else: ?>
                    <span class="badge badge-operator">Оператор</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-links">
            <a href="../index.php" target="_blank">На сайт</a>
            <a href="index.php">Тестовая форма</a>
            
            <?php if ($userRole === 'admin'): ?>
                <!-- Кнопки только для Главного Админа -->
                <a href="add_user.php">Добавить сотрудника</a>
                <a href="change_password.php">Сменить пароль</a>
            <?php endif; ?>
            
            <a href="logout.php" class="logout">Выйти</a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Тур</th>
                        <th>Комментарий</th>
                        <?php if ($userRole === 'admin'): ?>
                            <th>Действие</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['id'] ?></td>
                            <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                            <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                            <td><a href="tel:<?= htmlspecialchars($row['phone']) ?>"><?= htmlspecialchars($row['phone']) ?></a></td>
                            <td><?= htmlspecialchars($row['tour'] ?: '—') ?></td>
                            <td><?= htmlspecialchars($row['comment'] ?: '—') ?></td>
                            
                            <?php if ($userRole === 'admin'): ?>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Удалить заявку #<?= $row['id'] ?>?');" style="margin:0;">
                                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn-delete">Удалить</button>
                                    </form>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">Пока нет ни одной заявки. Заполните форму на сайте!</div>
        <?php endif; ?>
    </div>
</body>
</html>