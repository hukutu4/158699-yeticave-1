<?php
require_once 'autoload.php';

$new_bet = [];
$errors = [];
if ($_POST !== [] && isset($_SESSION['user'])) {
    $new_bet = $_POST;
}
if ($new_bet !== []) {
    $errors = validateNewBet($new_bet);
}
if ($errors === [] && $new_bet !== []) {
    $lot_id = addBet($new_bet);
}
// Проверка на число
if (!preg_match('/^\d+$/', $_GET['id'])) {
    return http_response_code(404);
}
$lot = getLot((int)$_GET['id']);
$bets = getBets((int)$_GET['id']);
// Проверка существования лота в базе
if (!empty($lot)) {
    $title = $lot['name'];
    $page_content = renderTemplate('templates/lot.php', [
        'lot' => $lot,
        'bets' => $bets,
        'categories' => getAllCategories(),
        'new_bet' => $new_bet,
        'errors' => $errors,
    ]);
    printLayout($title, $page_content);
} else {
    return http_response_code(404);
}
