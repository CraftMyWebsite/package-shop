<?php

/* @var \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $carts */

$title = 'Paniers de ' . $sessionId;
$description = '';

?>

<h4>Panier de <?= $sessionId ?></h4>

<div class="table-container table-container-striped">

        <table id="table1">
            <thead>
            <tr>
                <th>Article</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Prix total</th>
                <th>Date d'ajout</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($carts as $cart): ?>
                <tr>
                    <td>
                        <a class="link" target="_blank" href="/shop/cat/<?= $cart->getItem()->getCategory()->getSlug() ?>/item/<?= $cart->getItem()->getSlug() ?>"><?= $cart->getItem()->getName() ?></a>
                    </td>
                    <td>
                        <?= $cart->getQuantity() ?>
                    </td>
                    <td>
                        <?= $cart->getItem()->getPriceFormatted() ?>
                    </td>
                    <td>
                        <?= $cart->getItemTotalPriceFormatted() ?>
                    </td>
                    <td>
                        <?= $cart->getCreated() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
</div>