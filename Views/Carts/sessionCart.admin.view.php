<?php

/* @var \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $carts */

$title = "Paniers de ". $sessionId;
$description = "";

?>


<div class="card">
    <div class="card-header">
        <h4>Panier de <?= $sessionId ?></h4>
    </div>
    <div class="card-body">
        <table class="table" id="table1">
            <thead>
            <tr>
                <th class="text-center">Article</th>
                <th class="text-center">Quantité</th>
                <th class="text-center">Prix unitaire</th>
                <th class="text-center">Prix total</th>
                <th class="text-center">Date d'ajout</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($carts as $cart) : ?>
                <tr class="text-center">
                    <td>
                        <?= $cart->getItem()->getName() ?>
                    </td>
                    <td>
                        <?= $cart->getQuantity() ?>
                    </td>
                    <td>
                        <?= $cart->getItem()->getPrice() ?> €
                    </td>
                    <td>
                        <?= $cart->getTotalPrice() ?> €
                    </td>
                    <td>
                        <?= $cart->getCreated() ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>