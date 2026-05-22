<?php

require 'db.php';

$pdo = connectDB();

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

$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сохраненные анкеты</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h1>Сохраненные анкеты</h1>

    <p class="author">
        Выполнил: Сладкомедов Ярослав, ПМИ 23
    </p>

    <?php if (empty($applications)): ?>

        <p>Анкет пока нет.</p>

    <?php else: ?>

        <table>

            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Дата рождения</th>
                <th>Пол</th>
                <th>Биография</th>
                <th>Согласие</th>
                <th>Языки</th>
                <th>Дата создания</th>
            </tr>

            <?php foreach ($applications as $app): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($app['id']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['full_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['phone']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['email']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['birth_date']) ?>
                    </td>

                    <td>
                        <?= $app['gender'] === 'male' ? 'Мужской' : 'Женский' ?>
                    </td>

                    <td>
                        <?= nl2br(htmlspecialchars($app['biography'])) ?>
                    </td>

                    <td>
                        <?= $app['agreement'] ? 'Да' : 'Нет' ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['languages']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($app['created_at']) ?>
                    </td>

                </tr>

            <?php endforeach; ?>

        </table>

    <?php endif; ?>

    <div class="links">

        <a href="index.php">
            Вернуться к форме
        </a>

    </div>

</div>

</body>
</html>

?>
