<?php
require_once 'autoload.php';

$title = 'Главная';

$page_content = renderTemplate('templates/index.php', ['lots' => getOpenLots(), 'categories' => getAllCategories()]);
printLayout($title, $page_content);