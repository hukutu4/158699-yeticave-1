<?php
require_once 'autoload.php';

// Разлогиниваем пользователя
logout();
header("Location: /");
exit;
