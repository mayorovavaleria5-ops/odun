<?php
session_start();
require_once '../db.php';

// Защита
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit;
}

$userRole = $_SESSION['role'] ?? 'operator';
$username = $_SESSION['username'] ?? 'Пользователь';
$message = '';

// --- ДОБАВЛЕНИЕ НОВОСТИ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news']) && ($userRole === 'admin' || $userRole === 'editor')) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    if (!empty($title) && !empty($content)) {
        $stmt = $conn->prepare("INSERT INTO news (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        if ($stmt->execute()) {
            $message = "<div style='color:green; background:#e8f5e9; padding:10px; border-radius:6px; margin-bottom:15px;'>Новость опубликована!</div>";
        } else {
            $message = "<div style='color:red;'>Ошибка: " . $conn->error . "</div>";
        }
    } else {
        $message = "<div style='color:red;'>Заполните все поля.</div>";
    }
}

// --- УДАЛЕНИЕ НОВОСТИ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_news_id']) && $userRole === 'admin') {
    $delete_id = (int)$_POST['delete_news_id'];
    $conn->query("DELETE FROM news WHERE id = $delete_id");
    header("Location: admin.php");
    exit;
}

// --- УДАЛЕНИЕ ЗАЯВКИ ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id']) && $userRole === 'admin') {
    $delete_id = (int)$_POST['delete_id'];
    $conn->query("DELETE FROM leads WHERE id = $delete_id");
    header("Location: admin.php");
    exit;
}

// Получаем заявки
$result = $conn->query("SELECT * FROM leads ORDER BY created_at DESC");
// Получаем новости
$news_result = $conn->query("SELECT * FROM news ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка ОДУН ТУР</title>
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
        
        /* Стили для формы новостей */
        .news-form { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #eee; }
        .news-form input, .news-form textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; font-family: inherit; }
        .news-form textarea { height: 100px; resize: vertical; }
        .news-form button { background: #1B3A4B; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        
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
        .badge-editor { background: #E3F2FD; color: #1565C0; }
        h2 { color: #1B3A4B; margin-top: 40px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Панель управления</h1>
            <div class="user-info">
                Вы вошли как: <span><?= htmlspecialchars($username) ?></span> 
                <?php if ($userRole === 'admin'): ?>
                    <span class="badge badge-admin">Админ</span>
                <?php elseif ($userRole === 'editor'): ?>
                    <span class="badge badge-editor">Редактор</span>
                <?php else: ?>
                    <span class="badge badge-operator">Оператор</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="nav-links">
            <a href="../index.php" target="_blank">На сайт</a>
            
            <?php if ($userRole === 'admin'): ?>
                <a href="add_user.php">Добавить сотрудника</a>
                <a href="change_password.php">Сменить пароль</a>
            <?php endif; ?>
            
            <a href="logout.php" class="logout">Выйти</a>
        </div>

        <?= $message ?>

        <!-- БЛОК ДОБАВЛЕНИЯ НОВОСТИ (Виден только админу и редактору) -->
        <?php if ($userRole === 'admin' || $userRole === 'editor'): ?>
        <div class="news-form">
            <h3 style="margin-top:0; color:#1B3A4B;">Добавить новость на сайт</h3>
            <form method="POST">
                <input type="text" name="title" placeholder="Заголовок новости" required>
                <textarea name="content" placeholder="Текст новости..." required></textarea>
                <button type="submit" name="add_news">Опубликовать</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- ТАБЛИЦА ЗАЯВОК -->
        <h2>Заявки с сайта</h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата</th>
                        <th>Имя</th>
                        <th>Телефон</th>
                        <th>Тур</th>
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
                            <?php if ($userRole === 'admin'): ?>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Удалить заявку?');" style="margin:0;">
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
            <div class="empty">Пока нет ни одной заявки.</div>
        <?php endif; ?>

        <!-- ТАБЛИЦА НОВОСТЕЙ (Виден только админу) -->
        <?php if ($userRole === 'admin'): ?>
            <h2>Опубликованные новости</h2>
            <?php if ($news_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Дата</th>
                            <th>Заголовок</th>
                            <th>Действие</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($news = $news_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $news['id'] ?></td>
                                <td><?= date('d.m.Y', strtotime($news['created_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($news['title']) ?></strong></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Удалить новость?');" style="margin:0;">
                                        <input type="hidden" name="delete_news_id" value="<?= $news['id'] ?>">
                                        <button type="submit" class="btn-delete">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">Новостей пока нет.</div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>
</html>