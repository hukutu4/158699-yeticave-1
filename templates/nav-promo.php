<?php
/**
 * @var array $categories
 */
?>
<ul class="promo__list">
    <?php foreach ($categories as $category): ?>
        <li class="promo__item promo__item--<?= $category['class'] ?>">
            <a class="promo__link" href="all-lots.html"><?= $category['name'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>