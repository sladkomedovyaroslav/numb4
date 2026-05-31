<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Лабораторная работа №4</title>

    <!-- Подключение стилей -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- Заголовок формы -->
    <h1>Анкета пользователя</h1>

    <!-- Информация об авторе -->
    <p class="author">
        Выполнил: Сладкомедов Ярослав, ПМИ 23
    </p>

    <!-- Сообщение об успешном сохранении -->
    <?php if (!empty($messages)): ?>

        <div class="success">

            <?php foreach ($messages as $message): ?>

                <?= htmlspecialchars($message) ?>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <!-- Форма отправки данных -->
    <form action="" method="POST">

        <!-- Поле ФИО -->
        <label>ФИО</label>

        <input
            type="text"
            name="full_name"
            class="<?= $errors['full_name'] ? 'error-field' : '' ?>"
            value="<?= htmlspecialchars($_COOKIE['full_name_value'] ?? '') ?>"
        >

        <!-- Сообщение об ошибке ФИО -->
        <?php if (!empty($error_messages['full_name'])): ?>

            <div class="error-message">
                <?= htmlspecialchars($error_messages['full_name']) ?>
            </div>

        <?php endif; ?>

        <!-- Поле телефона -->
        <label>Телефон</label>

        <!-- Аналогично для остальных полей -->

        <!-- Выбор пола -->
        <div class="radio-group">

            <!-- Мужской -->
            <label>
                <input
                    type="radio"
                    name="gender"
                    value="male"
                    <?= (!empty($_COOKIE['gender_value']) && $_COOKIE['gender_value'] == 'male') ? 'checked' : '' ?>
                >
                Мужской
            </label>

            <!-- Женский -->
            <label>
                <input
                    type="radio"
                    name="gender"
                    value="female"
                    <?= (!empty($_COOKIE['gender_value']) && $_COOKIE['gender_value'] == 'female') ? 'checked' : '' ?>
                >
                Женский
            </label>

        </div>

        <!-- Список языков программирования -->
        <select
            name="languages[]"
            multiple
            class="<?= $errors['languages'] ? 'error-field' : '' ?>"
        >

            <!-- Заполнение списка из БД -->
            <?php foreach ($languages as $language): ?>

                <option value="<?= $language['id'] ?>">

                    <?= htmlspecialchars($language['name']) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <!-- Поле биографии -->
        <textarea
            name="biography"
            rows="6"
        ><?= htmlspecialchars($_COOKIE['biography_value'] ?? '') ?></textarea>

        <!-- Чекбокс согласия -->
        <div class="checkbox">

            <label>

                <input
                    type="checkbox"
                    name="agreement"
                >

                С контрактом ознакомлен(а)

            </label>

        </div>

        <!-- Кнопка отправки формы -->
        <button type="submit">
            Сохранить
        </button>

    </form>

    <!-- Ссылка на просмотр анкет -->
    <div class="links">

        <a href="view.php">
            Просмотреть анкеты
        </a>

    </div>

</div>

</body>
</html>