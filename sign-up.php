<?php
require_once 'autoload.php';

$title = 'Регистрация';

$new_user = [];
$errors = [];
if ($_POST !== []) {
    $new_user = $_POST;
}
if (isset($_FILES['avatar'])) {
    $new_user['avatar'] = $_FILES['avatar'];
}
if ($new_user !== []) {
    $errors = validateNewUser($new_user);
}
if ($errors === [] && $new_user !== []) {
    addNewUser($new_user);
    header("Location: /login.php");
    exit;
} else {
    $page_content = renderTemplate('templates/sign-up.php', [
        'new_user' => $new_user,
        'errors' => $errors,
        'categories' => getAllCategories(),
    ]);
    printLayout($title, $page_content);
}
