<?php
require_once 'autoload.php';

$title = 'Вход';

$login = [];
$errors = [];
if ($_POST !== []) {
    $login = $_POST;
}
if ($login !== []) {
    $errors = validateLogin($login);
}
if ($errors === [] && $login !== []) {
    authorize($login);
    header("Location: /");
    exit;
} else {
    $page_content = renderTemplate('templates/login.php', [
        'login' => $login,
        'errors' => $errors,
        'categories' => getAllCategories(),
    ]);
    printLayout($title, $page_content);
}
