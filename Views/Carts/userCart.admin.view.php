<?php

/* @var \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $carts */
/* @var \CMW\Entity\Users\UserEntity $user */

$title = 'Paniers de ' . $user->getPseudo();
$description = '';

?>

<h3>Panier de <?= $user->getPseudo() ?></h3>


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