<?php
require_once 'autoload.php';

$title = 'Добавление лота';

if (!isset($_SESSION['user'])) {
    return http_response_code(403);
}
$lot = [];
$errors = [];
if ($_POST !== []) {
    $lot = $_POST;
}
if (isset($_FILES['avatar'])) {
    $lot['avatar'] = $_FILES['avatar'];
}
if ($lot !== []) {
    $errors = validateNewLot($lot);
}
if ($errors === [] && $lot !== []) {
    $lot_id = addLot($lot);
    header("Location: /lot.php?id={$lot_id}");
    exit;
} else {
    $page_content = renderTemplate('templates/add.php', [
        'lot' => $lot,
        'categories' => getAllCategories(),
        'errors' => $errors,
    ]);
    printLayout($title, $page_content);
}
