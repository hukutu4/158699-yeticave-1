<?php
/**
 * @var array $lot
 * @var array $bets
 * @var array $errors
 * @var array $new_bet
 * @var array $categories
 */
$min_cost = $lot['current_price'] + $lot['bet_step'];
$did_user_bet = in_array($_SESSION['user']['id'] ?? 0, array_column($bets, 'user_id'));
$show_add_bet_form = isset($_SESSION['user']) && $_SESSION['user']['id'] != $lot['author_id'] && time() < $lot['updated_at'] && !$did_user_bet;
?>
<?= renderTemplate('templates/nav.php', ['categories' => $categories]) ?>
<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['name']) ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $lot['image_url'] ?>" width="730" height="548" alt="<?= htmlspecialchars($lot['name']) ?>">
            </div>
            <p class="lot-item__category">Категория: <span><?= htmlspecialchars($lot['category_name']) ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description']) ?></p>
        </div>
        <div class="lot-item__right">
            <?php if ($show_add_bet_form): ?>
                <div class="lot-item__state">
                    <div class="lot-item__timer timer"><?= date_create()->diff(date_create()->setTimestamp($lot['updated_at']))->format('%d дн. %h:%I') ?></div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= rurNumberFormat($lot['current_price']) ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= rurNumberFormat($min_cost) ?></span>
                        </div>
                    </div>
                    <form class="lot-item__form <?= ($errors !== []) ? 'form--invalid' : '' ?>" action="/lot.php?id=<?= $lot['id'] ?>" method="post">
                        <p class="lot-item__form-item <?= isset($errors['cost']) ? 'form__item--invalid' : '' ?>">
                            <label for="cost">Ваша ставка</label>
                            <input id="cost" type="number" name="cost" <?= (isset($new_bet['cost'])) ? 'value="' . $new_bet['cost'] . '"' : '' ?>
                                   placeholder="<?= number_format($min_cost, 0, '.', ' ') ?>">
                            <span class="form__error"><?= $errors['cost'] ?? '' ?></span>
                        </p>
                        <input id="lot-id" type="hidden" name="lot_id" <?= (isset($lot['id'])) ? 'value="' . $lot['id'] . '"' : '' ?>>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                </div>
            <?php endif; ?>
            <div class="history">
                <h3>История ставок (<span><?= count($bets) ?></span>)</h3>
                <table class="history__list">
                    <?php foreach ($bets as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $bet['user_name'] ?></td>
                            <td class="history__price"><?= rurNumberFormat($bet['price']) ?></td>
                            <td class="history__time"><?= date('d.m.y в H:i', $bet['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</section>
