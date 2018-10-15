<?php
/** Шаблонизатор
 * @param string $filename
 * @param array $params
 * @return string
 */
function renderTemplate($filename, $params = []) {
    if (file_exists($filename)) {
        extract($params);
        ob_start();
        require $filename;
        return ob_get_clean();
    }
    return '';
}

/** Выводим шаблон страницы с хедером, футером и заданным контентом
 * @param string $title
 * @param string $page_content
 * @return void
 */
function printLayout($title = '', $page_content = ''): void {
    $result_title = 'YetiCave';
    if ($title !== '') {
        $result_title .= ' - ' . $title;
    }
    $layout_content = renderTemplate('templates/layout.php', [
        'title' => $result_title,
        'content' => $page_content,
        'categories' => getAllCategories(),
    ]);
    print($layout_content);
}

/** Форматирует целое число, представляя его в виде строки, с разделителем пробелом для тысяч, дополненным знаком рубля.
 * @param int $price
 * @return string
 */
function rurNumberFormat(int $price) {
    return number_format($price, 0, '.', ' ') . '<b class="rub">р</b>';
}

/** Авторизация пользователя
 * @param array $login
 * @return bool
 */
function authorize($login) {
    $user = getUserByEmail($login['email']);
    if (!empty($user)) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

/** Разлогиниваем пользователя
 * @return void
 */
function logout(): void {
    unset($_SESSION['user']);
}

// ------------------------ БЛОК ФУНКЦИЙ ДЛЯ РАБОТЫ С БД ------------------------ //

/** Получить коннект к базе
 * @return mysqli
 */
function getDbConnection() {
    static $db;
    // Проверяем, есть ли уже коннект к БД
    if (is_null($db)) {
        // Если коннекта нет - подключаемся к БД
        $db = mysqli_connect('localhost', 'yeti', 'yeti', 'yeti');
        if ($db === false) {
            print("Ошибка подключения: " . mysqli_connect_error());
            die();
        }
    }
    return $db;
}

/** Получаем категории
 * @return array|mixed
 */
function getAllCategories() {
    $db = getDbConnection();
    $sql = "SELECT * FROM categories";
    $mysqli_result = $db->query($sql);
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result;
}

/** Получаем категорию по id
 * @param int $id
 * @return array|mixed
 */
function getCategory($id) {
    $db = getDbConnection();
    $sql = "SELECT * FROM categories WHERE id = ?";
    $mysqli_stmt = $db->prepare($sql);
    $mysqli_stmt->bind_param('i', $id);
    $mysqli_stmt->execute();
    $mysqli_result = $mysqli_stmt->get_result();
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result[0] ?? [];
}

/** Получаем открытые лоты
 * @return array|mixed
 */
function getOpenLots() {
    $db = getDbConnection();
    $sql = "SELECT
      l.id,
      l.name,
      l.starting_price as price,
      l.image_url as url,
      IF(MAX(b.price) IS NOT NULL, MAX(b.price), l.starting_price) AS current_price,
      COUNT(distinct b.id) AS bets_count,
      c.name AS category
    FROM
      lots l
    LEFT JOIN
      bets b ON l.id = b.lot_id
    INNER JOIN
      categories c ON l.category_id = c.id
    WHERE
      l.winner_id IS NULL
    GROUP BY
      l.id,
      l.name,
      l.starting_price,
      l.image_url,
      c.name,
      l.created_at
    ORDER BY
      l.created_at DESC";
    $mysqli_result = $db->query($sql);
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result;
}

/** Показать лот (и его категорию) по id
 * @param int $id
 * @return array|mixed
 */
function getLot(int $id) {
    $db = getDbConnection();
    $sql = "SELECT
      l.*,
      c.name as category_name,
      IFNULL((select MAX(b.price) from bets b where b.lot_id = l.id), l.starting_price) AS current_price
    FROM
      lots l
    INNER JOIN
      categories c ON l.category_id = c.id
    WHERE
      l.id = ?";
    $mysqli_stmt = $db->prepare($sql);
    $mysqli_stmt->bind_param('i', $id);
    $mysqli_stmt->execute();
    $mysqli_result = $mysqli_stmt->get_result();
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result[0] ?? [];
}

/** Получаем ставки по id лота
 * @param int $lot_id
 * @return array|mixed
 */
function getBets(int $lot_id) {
    $db = getDbConnection();
    $sql = "SELECT
      u.name as 'user_name',
      b.user_id,
      b.price,
      b.created_at
    FROM
      bets b
    inner join
      users u on u.id = b.user_id
    WHERE
      lot_id = ?
    ORDER BY
      created_at DESC
    LIMIT 10";
    $mysqli_stmt = $db->prepare($sql);
    $mysqli_stmt->bind_param('i', $lot_id);
    $mysqli_stmt->execute();
    $mysqli_result = $mysqli_stmt->get_result();
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result;
}

/** Добавляем новый лот
 * @param array $lot
 * @return bool
 */
function addLot(array $lot) {
    $db = getDbConnection();
    $sql = "INSERT INTO lots(author_id, category_id, name, description, image_url, starting_price, bet_step, created_at, updated_at) 
      VALUES (?,?,?,?,?,?,?,UNIX_TIMESTAMP(),?)";
    $mysqli_stmt = $db->prepare($sql);
    $author_id = (int)$_SESSION['user']['id'];
    $lot_end_time = strtotime($lot['lot-date']);
    $mysqli_stmt->bind_param('iisssddi',
        $author_id,
        $lot['category'],
        $lot['lot_name'],
        $lot['message'],
        $lot['image_url'],
        $lot['lot_rate'],
        $lot['lot_step'],
        $lot_end_time
    );
    if ($mysqli_stmt->execute()) {
        $result = $db->insert_id;
    } else {
        $result = false;
    }
    return $result;
}

/** Получаем пользователя по email
 * @param string $email
 * @return array|mixed
 */
function getUserByEmail(string $email) {
    $db = getDbConnection();
    $sql = "SELECT * FROM users WHERE email = ?";
    $mysqli_stmt = $db->prepare($sql);
    $mysqli_stmt->bind_param('s', $email);
    $mysqli_stmt->execute();
    $mysqli_result = $mysqli_stmt->get_result();
    $result = [];
    if ($mysqli_result !== false) {
        $result = $mysqli_result->fetch_all(MYSQLI_ASSOC);
    }
    return $result[0] ?? [];
}

/** Добавляем нового пользователя
 * @param array $user
 * @return int|false
 */
function addNewUser(array $user) {
    $db = getDbConnection();
    $sql = "INSERT INTO users(email, name, password, avatar_url, contacts, created_at) VALUES (?,?,?,?,?,UNIX_TIMESTAMP())";
    $mysqli_stmt = $db->prepare($sql);
    $mysqli_stmt->bind_param('sssss',
        $user['email'],
        $user['name'],
        $user['password'],
        $user['image_url'],
        $user['message']
    );
    if ($mysqli_stmt->execute()) {
        $result = $db->insert_id;
    } else {
        $result = false;
    }
    return $result;
}

/** Добавляем новую ставку
 * @param array $bet
 * @return int|bool
 */
function addBet(array $bet) {
    $db = getDbConnection();
    $sql = "INSERT INTO bets(user_id, lot_id, price, created_at) VALUES (?,?,?,UNIX_TIMESTAMP())";
    $mysqli_stmt = $db->prepare($sql);
    $user_id = (int)$_SESSION['user']['id'];
    $mysqli_stmt->bind_param('iii',
        $user_id,
        $bet['lot_id'],
        $bet['cost']
    );
    if ($mysqli_stmt->execute()) {
        $result = $db->insert_id;
    } else {
        $result = false;
    }
    return $result;
}

// ------------------------ БЛОК ФУНКЦИЙ ВАЛИДАТОРОВ ФОРМ ------------------------ //

/** Валидация нового лота
 * @param array $lot
 * @return array
 */
function validateNewLot(array &$lot) {
    $errors = [];

    // Валидация категории
    if (!preg_match('/^\d+$/', $lot['category']) || empty(getCategory($lot['category']))) {
        $errors['category'] = 'Выберите категорию';
    } else {
        $lot['category'] = (int)$lot['category'];
    }

    // Валидация наименования
    if (!empty($lot['lot_name'])) {
        $lot['lot_name'] = filter_var($lot['lot_name'], FILTER_SANITIZE_STRING);
        // Обрезаем строку, если длина больше допустимой
        if (mb_strlen($lot['lot_name']) > 255) {
            $lot['lot_name'] = mb_substr($lot['lot_name'], 0, 255);
        }
    } else {
        $errors['lot_name'] = 'Введите наименование лота';
    }

    // Валидация описания
    if (!empty($lot['message'])) {
        $lot['message'] = filter_var($lot['message'], FILTER_SANITIZE_STRING);
        // Обрезаем строку, если длина больше допустимой
        if (mb_strlen($lot['message']) > 2047) {
            $lot['message'] = mb_substr($lot['message'], 0, 2047);
        }
    } else {
        $errors['message'] = 'Напишите описание лота';
    }

    // Валидация начальной цены
    if (!empty($lot['lot_rate'])) {
        $lot['lot_rate'] = filter_var($lot['lot_rate'], FILTER_SANITIZE_NUMBER_FLOAT);
        $lot['lot_rate'] = (double)$lot['lot_rate'];
    } else {
        $errors['lot_rate'] = 'Введите начальную цену';
    }

    // Валидация шага ставки
    if (!empty($lot['lot_step'])) {
        $lot['lot_step'] = filter_var($lot['lot_step'], FILTER_SANITIZE_NUMBER_FLOAT);
        $lot['lot_step'] = (double)$lot['lot_step'];
    } else {
        $errors['lot_step'] = 'Введите шаг ставки';
    }

    // Валидация даты завершения торгов
    if (!empty($lot['lot-date']) && ($date_end = date_create($lot['lot-date'])) instanceof DateTime) {
        if ($date_end->getTimestamp() < time()) {
            $errors['lot-date'] = 'Дата завершения торгов должна быть позже текущего дня';
        }
    } else {
        $errors['lot-date'] = 'Введите дату завершения торгов';
    }

    // Валидация файла аватара
    validateAvatarImage($lot, $errors);

    return $errors;
}

/** Валидация данных о новом пользователе
 * @param $new_user
 * @return array
 */
function validateNewUser(&$new_user) {
    $errors = [];

    // Валидация емейла
    if (filter_var($new_user['email'], FILTER_VALIDATE_EMAIL)) {
        $user = getUserByEmail($new_user['email']);
        if (!empty($user)) {
            $errors['email'] = 'Пользователь с указанным e-mail уже существует';
        }
        if (mb_strlen($new_user['email']) > 255) {
            $errors['email'] = 'E-mail не должен превышать 255 символов';
        }
    } else {
        $errors['email'] = 'Введите корректный e-mail';
    }

    // Хеширование пароля
    if (!empty($new_user['password'])) {
        $new_user['password'] = password_hash($new_user['password'], PASSWORD_DEFAULT);
    } else {
        $errors['password'] = 'Введите пароль';
    }

    // Фильтрация имени пользователя
    if (!empty($new_user['name'])) {
        $new_user['name'] = filter_var($new_user['name'], FILTER_SANITIZE_STRING);
        if (mb_strlen($new_user['name']) > 255) {
            $errors['name'] = 'Имя не должно превышать 255 символов';
        }
    } else {
        $errors['name'] = 'Укажите имя';
    }

    // Фильтрация контактных данных
    if (!empty($new_user['message'])) {
        $new_user['message'] = filter_var($new_user['message'], FILTER_SANITIZE_STRING);
        if (mb_strlen($new_user['message']) > 2047) {
            $errors['message'] = 'Кол-во знаков не должно превышать 2047 символов';
        }
    } else {
        $errors['message'] = 'Укажите контактную информацию для связи с Вами';
    }

    // Валидация файла аватара
    validateAvatarImage($new_user, $errors, false);

    return $errors;
}

/** Валидация файла аватара
 * @param $subject
 * @param $errors
 * @param bool $required
 * @return bool
 */
function validateAvatarImage(&$subject, &$errors, $required = true) {
    if (isset($subject['avatar']) && !empty($subject['avatar']['tmp_name'])) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_name = $subject['avatar']['tmp_name'];
        $file_size = $subject['avatar']['size'];
        $file_type = finfo_file($finfo, $file_name);
        finfo_close($finfo);
        if ($file_type !== 'image/jpeg') {
            $errors['avatar'] = "Загрузите картинку в формате jpeg";
        }
        if ($file_size > 5000000) {
            $errors['avatar'] = "Максимальный размер файла: 5Мб";
        }
        if (!isset($errors['avatar'])) {
            $file_name = $subject['avatar']['name'];
            $file_path = __DIR__ . '/img/';
            $file_url = '/img/' . $file_name;
            move_uploaded_file($subject['avatar']['tmp_name'], $file_path . $file_name);
            $subject['image_url'] = $file_url;
        }
    } elseif (empty($subject['image_url']) && $required) {
        $errors['avatar'] = 'Добавьте фотографию';
    }
    return empty($errors['avatar']);
}

/** Аутентификация пользователя при входе
 * @param $login
 * @return array
 */
function validateLogin(&$login) {
    $main_err_message = 'Логин и(или) пароль указаны не корректно';
    $errors = [];
    if (filter_var($login['email'], FILTER_VALIDATE_EMAIL)) {
        $user = getUserByEmail($login['email']);
        if (empty($user)) {
            $errors['main'] = $main_err_message;
        } elseif (!password_verify($login['password'], $user['password'])) {
            $errors['main'] = $main_err_message;
        }
    } else {
        $errors['email'] = 'Введите корректный e-mail';
    }
    if (empty($login['password'])) {
        $errors['password'] = 'Укажите пароль';
    }
    return $errors;
}

/** Валидация добавления новой ставки
 * @param array $bet
 * @return array
 */
function validateNewBet(array &$bet) {
    $errors = [];

    // Валидация цены ставки
    if (!empty($bet['cost'])) {
        $bet['cost'] = filter_var($bet['cost'], FILTER_SANITIZE_NUMBER_INT);
        $bet['cost'] = (int)$bet['cost'];
        if (isset($bet['lot_id'])) {
            $lot = getLot($bet['lot_id']);
            if ($lot['current_price'] + $lot['bet_step'] > $bet['cost']) {
                $errors['cost'] = 'Цена ставки должна быть больше текущей цены лота';
            }
        }
        if (!isset($_SESSION['user'])) {
            $errors['cost'] = 'Только авторизованные пользователи могут делать ставки';
        }
    } else {
        $errors['cost'] = 'Введите цену ставки';
    }

    return $errors;
}
