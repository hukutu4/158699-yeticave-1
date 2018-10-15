<?php
/**
 * @var array $errors
 * @var array $lot
 * @var array $categories
 */
?>
<?= renderTemplate('templates/nav.php', ['categories' => $categories]) ?>
<form class="form form--add-lot container <?= ($errors !== []) ? 'form--invalid' : '' ?>" action="/add.php"
      method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= isset($errors['lot_name']) ? 'form__item--invalid' : '' ?>"><!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot_name"
                   placeholder="Введите наименование лота" <?= (isset($lot['lot_name'])) ? 'value="' . $lot['lot_name'] . '"' : '' ?>>
            <span class="form__error"><?= $errors['lot_name'] ?? '' ?></span>
        </div>
        <div class="form__item <?= isset($errors['category']) ? 'form__item--invalid' : '' ?>">
            <label for="category">Категория</label>
            <select id="category" name="category">
                <option>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option <?= (isset($lot['category']) && $category['id'] == $lot['category']) ? 'selected' : '' ?>
                            value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <span class="form__error"><?= $errors['category'] ?? '' ?></span>
        </div>
    </div>
    <div class="form__item form__item--wide <?= isset($errors['message']) ? 'form__item--invalid' : '' ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="message" placeholder="Напишите описание лота"><?= $lot['message'] ?? '' ?></textarea>
        <span class="form__error"><?= $errors['message'] ?? '' ?></span>
    </div>
    <div class="form__item form__item--file <?= isset($errors['avatar']) ? 'form__item--invalid' : '' ?>
    <?= !empty($lot['image_url']) ? 'form__item--uploaded' : '' ?>"> <!-- form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="<?= $lot['image_url'] ?? '' ?>" width="113" height="113" alt="Изображение лота">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="avatar" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
            <input id="image-url" type="hidden" name="image_url" <?= (isset($lot['image_url'])) ? 'value="' . $lot['image_url'] . '"' : '' ?>>
        </div>
        <span class="form__error"><?= $errors['avatar'] ?? '' ?></span>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= isset($errors['lot_rate']) ? 'form__item--invalid' : '' ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="number" name="lot_rate" placeholder="0" <?= (isset($lot['lot_rate'])) ? 'value="' . $lot['lot_rate'] . '"' : '' ?>>
            <span class="form__error"><?= $errors['lot_rate'] ?? '' ?></span>
        </div>
        <div class="form__item form__item--small <?= isset($errors['lot_step']) ? 'form__item--invalid' : '' ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="number" name="lot_step" placeholder="0" <?= (isset($lot['lot_step'])) ? 'value="' . $lot['lot_step'] . '"' : '' ?>>
            <span class="form__error"><?= $errors['lot_step'] ?? '' ?></span>
        </div>
        <div class="form__item <?= isset($errors['lot-date']) ? 'form__item--invalid' : '' ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="date" name="lot-date" <?= (isset($lot['lot-date'])) ? 'value="' . $lot['lot-date'] . '"' : '' ?>>
            <span class="form__error"><?= $errors['lot-date'] ?? '' ?></span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>
