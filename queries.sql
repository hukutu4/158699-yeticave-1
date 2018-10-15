USE yeti;

# --------- ВСТАВКА ---------
# Пользователи
INSERT INTO `users`
(`id`, `email`, `name`, `password`, `avatar_url`, `contacts`, `created_at`)
VALUES
  (1, 'ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka', NULL, '+79995554433', 1526539847),
  (2, 'kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa', NULL, '+71111111111', 1526539999),
  (3, 'warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW', NULL, '+71111111111', 1526539999);

# Категории
INSERT INTO `categories`
(`id`, `name`)
VALUES
  (1, 'Доски и лыжи'),
  (2, 'Крепления'),
  (3, 'Ботинки'),
  (4, 'Одежда'),
  (5, 'Инструменты'),
  (6, 'Разное');

# Лоты (объявления)
INSERT INTO `lots`
(`id`, `author_id`, `winner_id`, `category_id`, `name`, `description`, `image_url`, `starting_price`, `bet_step`, `created_at`, `updated_at`)
VALUES
  (1, 1, 2, 1, '2014 Rossignol District Snowboard', 'Идеальный во всех отношениях сноуборд!', 'img/lot-1.jpg', 10999.00, 100.00, 1526500299, 1526542432),
  (2, 1, NULL, 1, 'DC Ply Mens 2016/2017 Snowboard', 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив снег мощным щелчкоми четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.', 'img/lot-2.jpg', 159999.00, 100.00, 1526511299, 1527861075),
  (3, 1, NULL, 2, 'Крепления Union Contact Pro 2015 года размер L/XL', 'Отличные крепления!', 'img/lot-3.jpg', 8000.00, 100.00, 1526516299, 1527961075),
  (4, 1, NULL, 3, 'Ботинки для сноуборда DC Mutiny Charocal', 'Суперские ботинки!', 'img/lot-4.jpg', 10999.00, 100.00, 1526520299, 1528061075),
  (5, 2, NULL, 4, 'Куртка для сноуборда DC Mutiny Charocal', 'Замечательная куртка!', 'img/lot-5.jpg', 7500.00, 100.00, 1526523299, 1528161075),
  (6, 2, NULL, 6, 'Маска Oakley Canopy', 'У вас есть маска? Теперь у вас будет действительно хорошая маска!', 'img/lot-6.jpg', 5400.00, 100.00, 1526540299, 1528261075);

# Ставки
INSERT INTO `bets`
  (`id`, `user_id`, `lot_id`, `price`, `created_at`)
VALUES
  (1, 2, 1, 11099.00, 1526542327),
  (2, 2, 3, 8200.00, 1526542364);

# --------- ВЫБОРКА ---------
# Получить все категории;
SELECT
  *
FROM
  categories
;

# Получить самые новые, открытые лоты.
# Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, количество ставок, название категории;
SELECT
  l.name,
  l.starting_price,
  l.image_url,
  IF(MAX(b.price) IS NOT NULL, MAX(b.price), l.starting_price) AS current_price,
  COUNT(distinct b.id) AS bets_count,
  c.name AS category_name
FROM
  lots l
LEFT JOIN
  bets b ON l.id = b.lot_id
INNER JOIN
  categories c ON l.category_id = c.id
WHERE
  l.winner_id IS NULL
GROUP BY
  l.name,
  l.starting_price,
  l.image_url,
  c.name,
  l.created_at
ORDER BY
  l.created_at DESC
;

# Показать лот по его id. Получите также название категории, к которой принадлежит лот
SELECT
  l.*,
  c.name as category_name,
  IFNULL((select MAX(b.price) from bets b where b.lot_id = l.id), l.starting_price) AS current_price
FROM
  lots l
  INNER JOIN
  categories c ON l.category_id = c.id
WHERE
  l.id = 3
;

# Получить список самых свежих ставок для лота по его идентификатору;
SELECT
  u.name as 'user_name',
  b.price,
  b.created_at
FROM
  bets b
inner join
  users u on u.id = b.user_id
WHERE
  lot_id = 3
ORDER BY
  created_at DESC
LIMIT 10;

# --------- ОБНОВЛЕНИЕ ---------
# Обновить название лота по его идентификатору;
UPDATE
  lots
SET
  name = 'Куртка для сноуборда GT Mount Pro' /* name = 'Куртка для сноуборда DC Mutiny Charocal' */
WHERE
  id = 5
;
