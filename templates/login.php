<?php
/**
 * @var array $categories
 * @var array $errors
 * @var array $login
 */
?>
<?= renderTemplate('templates/nav.php', ['categories' => $categories]) ?>
<form class="form container <?= ($errors !== []) ? 'form--invalid' : '' ?>" action="/login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <div class="form__item <?= isset($errors['email']) ? 'form__item--invalid' : '' ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" required
            <?= (isset($login['email'])) ? 'value="' . $login['email'] . '"' : '' ?>>
        <span class="form__error"><?= $errors['email'] ?? '' ?></span>
    </div>
    <div class="form__item form__item--last <?= isset($errors['password']) ? 'form__item--invalid' : '' ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" required>
        <span class="form__error"><?= $errors['password'] ?? '' ?></span>
    </div>
    <span class="form__error form__error--bottom"><?= $errors['main'] ?? 'Пожалуйста, исправьте ошибки в форме.' ?></span>
    <button type="submit" class="button">Войти</button>
</form>