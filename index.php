<?php

// Подключение файла для работы с БД
require 'db.php';

// Подключение к базе данных
$pdo = connectDB();

// Получение списка языков программирования для формы
$stmt = $pdo->query("SELECT * FROM programming_languages ORDER BY name");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Обработка GET-запроса (отображение формы)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $messages = [];

    // Сообщение об успешном сохранении
    if (!empty($_COOKIE['save'])) {

        setcookie('save', '', 100000);

        $messages[] = 'Данные успешно сохранены.';
    }

    // Проверка наличия ошибок в Cookies
    $errors = [
        'full_name' => !empty($_COOKIE['full_name_error']),
        'phone' => !empty($_COOKIE['phone_error']),
        'email' => !empty($_COOKIE['email_error']),
        'birth_date' => !empty($_COOKIE['birth_date_error']),
        'gender' => !empty($_COOKIE['gender_error']),
        'languages' => !empty($_COOKIE['languages_error']),
        'agreement' => !empty($_COOKIE['agreement_error'])
    ];

    $error_messages = [];

    // Получение текста ошибок и удаление Cookies после чтения
    if ($errors['full_name']) {
        $error_messages['full_name'] = $_COOKIE['full_name_error'];
        setcookie('full_name_error', '', time() - 3600);
    }

    if ($errors['phone']) {
        $error_messages['phone'] = $_COOKIE['phone_error'];
        setcookie('phone_error', '', time() - 3600);
    }

    if ($errors['email']) {
        $error_messages['email'] = $_COOKIE['email_error'];
        setcookie('email_error', '', time() - 3600);
    }

    if ($errors['birth_date']) {
        $error_messages['birth_date'] = $_COOKIE['birth_date_error'];
        setcookie('birth_date_error', '', time() - 3600);
    }

    if ($errors['gender']) {
        $error_messages['gender'] = $_COOKIE['gender_error'];
        setcookie('gender_error', '', time() - 3600);
    }

    if ($errors['languages']) {
        $error_messages['languages'] = $_COOKIE['languages_error'];
        setcookie('languages_error', '', time() - 3600);
    }

    if ($errors['agreement']) {
        $error_messages['agreement'] = $_COOKIE['agreement_error'];
        setcookie('agreement_error', '', time() - 3600);
    }

    // Подключение HTML-формы
    include 'form.php';
    exit();
}

// Получение данных из формы
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$birth_date = trim($_POST['birth_date'] ?? '');
$gender = $_POST['gender'] ?? '';
$biography = trim($_POST['biography'] ?? '');
$agreement = isset($_POST['agreement']);
$selected_languages = $_POST['languages'] ?? [];

$hasErrors = false;

// Проверка ФИО
if (empty($full_name) || !preg_match('/^[а-яА-Яa-zA-Z\s\-]+$/u', $full_name)) {

    setcookie(
        'full_name_error',
        'Допустимы только буквы, пробелы и дефис.',
        0
    );

    $hasErrors = true;
}

// Проверка телефона
if (empty($phone) || !preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {

    setcookie(
        'phone_error',
        'Допустимы цифры, пробелы и символы + - ( )',
        0
    );

    $hasErrors = true;
}

// Проверка email
if (empty($email) || !preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {

    setcookie(
        'email_error',
        'Введите корректный email.',
        0
    );

    $hasErrors = true;
}

// Проверка даты рождения
if (empty($birth_date)) {

    setcookie(
        'birth_date_error',
        'Укажите дату рождения.',
        0
    );

    $hasErrors = true;
}

// Проверка пола
if (!in_array($gender, ['male', 'female'])) {

    setcookie(
        'gender_error',
        'Выберите пол.',
        0
    );

    $hasErrors = true;
}

// Проверка выбора языков
if (empty($selected_languages)) {

    setcookie(
        'languages_error',
        'Выберите хотя бы один язык.',
        0
    );

    $hasErrors = true;
}

// Проверка согласия
if (!$agreement) {

    setcookie(
        'agreement_error',
        'Необходимо согласие.',
        0
    );

    $hasErrors = true;
}

// Сохранение введённых значений на 1 год
setcookie('full_name_value', $full_name, time() + 60 * 60 * 24 * 365);
setcookie('phone_value', $phone, time() + 60 * 60 * 24 * 365);
setcookie('email_value', $email, time() + 60 * 60 * 24 * 365);
setcookie('birth_date_value', $birth_date, time() + 60 * 60 * 24 * 365);
setcookie('gender_value', $gender, time() + 60 * 60 * 24 * 365);
setcookie('biography_value', $biography, time() + 60 * 60 * 24 * 365);

// Если есть ошибки — возврат к форме
if ($hasErrors) {

    header('Location: index.php');
    exit();
}

try {

    // Начало транзакции
    $pdo->beginTransaction();

    // Сохранение основной анкеты
    $stmt = $pdo->prepare("
        INSERT INTO applications
        (full_name, phone, email, birth_date, gender, biography, agreement)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $full_name,
        $phone,
        $email,
        $birth_date,
        $gender,
        $biography,
        $agreement ? 1 : 0
    ]);

    // Получение ID новой записи
    $application_id = $pdo->lastInsertId();

    // Подготовка запроса для языков
    $stmt = $pdo->prepare("
        INSERT INTO application_languages
        (application_id, language_id)
        VALUES (?, ?)
    ");

    // Сохранение выбранных языков
    foreach ($selected_languages as $language_id) {

        $stmt->execute([
            $application_id,
            $language_id
        ]);
    }

    // Подтверждение транзакции
    $pdo->commit();

} catch (Exception $e) {

    die('Ошибка: ' . $e->getMessage());
}

// Cookie для сообщения об успехе
setcookie('save', '1');

// Перенаправление после сохранения
header('Location: index.php');
exit();
?>