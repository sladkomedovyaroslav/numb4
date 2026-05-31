<?php

// Подключение БД
require 'db.php';

// Соединение с БД
$pdo = connectDB();

// Получение всех анкет вместе с языками программирования
$stmt = $pdo->query("
    SELECT 
        a.*,
        GROUP_CONCAT(pl.name SEPARATOR ', ') AS languages
    FROM applications a

    LEFT JOIN application_languages al
        ON a.id = al.application_id

    LEFT JOIN programming_languages pl
        ON al.language_id = pl.id

    GROUP BY a.id

    ORDER BY a.id DESC
");

// Получение результата в массив
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Если анкет нет -->
<?php if (empty($applications)): ?>

<!-- Вывод таблицы анкет -->
<?php else: ?>

<!-- Перебор всех анкет -->
<?php foreach ($applications as $app): ?>

<!-- Безопасный вывод данных -->
<?= htmlspecialchars($app['full_name']) ?>

<!-- Ссылка назад к форме -->
<a href="index.php">
    Вернуться к форме
</a>