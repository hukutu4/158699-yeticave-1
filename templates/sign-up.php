<?php
/**
 * @var array $errors
 * @var array $new_user
 * @var array $categories
 */
?>
<?= renderTemplate('templates/nav.php', ['categories' => $categories]) ?>
<form class="form container <?= ($errors !== []) ? 'form--invalid' : '' ?>" action="/sign-up.php" method="post"
      enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item <?= isset($errors['email']) ? 'form__item--invalid' : '' ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" required
            <?= (isset($new_user['email'])) ? 'value="' . $new_user['email'] . '"' : '' ?>>
        <span class="form__error"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="form__item <?= isset($errors['password']) ? 'form__item--invalid' : '' ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <span class="form__error"><?= $errors['password'] ?? '' ?></span>
    </div>
    <div class="form__item <?= isset($errors['name']) ? 'form__item--invalid' : '' ?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="name" placeholder="Введите имя" required
            <?= (isset($new_user['name'])) ? 'value="' . $new_user['name'] . '"' : '' ?>>
        <span class="form__error"><?= $errors['name'] ?? '' ?></span>
    </div>
    <div class="form__item <?= isset($errors['message']) ? 'form__item--invalid' : '' ?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"
                  required><?= $new_user['message'] ?? '' ?></textarea>
        <span class="form__error"><?= $errors['message'] ?? '' ?></span>
    </div>
    <div class="form__item form__item--file form__item--last <?= isset($errors['avatar']) ? 'form__item--invalid' : '' ?>
    <?= !empty($new_user['image_url']) ? 'form__item--uploaded' : '' ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="<?= $new_user['image_url'] ?? '' ?>" width="113" height="113" alt="Ваш аватар">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="avatar" value="">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
            <input id="image-url" type="hidden"
                   name="image_url" <?= (isset($new_user['image_url'])) ? 'value="' . $new_user['image_url'] . '"' : '' ?>>
        </div>
        <span class="form__error"><?= $errors['avatar'] ?? '' ?></span>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/login.php">Уже есть аккаунт</a>
</form>