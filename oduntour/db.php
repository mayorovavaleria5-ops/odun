<?php
$host = 'localhost'; // На хостинге всегда localhost
$user = 'логин_от_базы'; // Выдаст хостинг
$pass = 'пароль_от_базы'; // Выдаст хостинг
$dbname = 'имя_базы'; // Название, которое вы создали на хостинге

// Сначала подключаемся к серверу без выбора базы
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Ошибка подключения к серверу: " . $conn->connect_error);
}

// Создаем базу данных, если её нет
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// Выбираем базу
if (!$conn->select_db($dbname)) {
    die("Ошибка выбора базы данных: " . $conn->error);
}
$conn->set_charset("utf8mb4");
?>