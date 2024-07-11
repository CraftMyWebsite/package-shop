<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getOrderNumber();
$description = "";

?>

<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <button form="cancel" type="submit" class="btn btn-danger">Cette commande n'est pas réalisable</button>
        <button form="send" type="submit" class="btn btn-primary">Commande prête pour l'envoie</button>
    </div>
</div>


<h6>Articles à préparer</h6>

<div class="grid-4">
    <?php foreach ($order->getOrderedItems() as $orderItem):?>
        <div class="card">
            <?php if ($orderItem->getFirstImg() !== "/Public/Uploads/Shop/0"): ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto" src="<?= $orderItem->getFirstImg() ?>" alt="Panier">
            <?php else: ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto"  src="<?= $defaultImage ?>" alt="..."/>
            <?php endif; ?>
            <h4 class="text-center"><?= $orderItem->getName() ?></h4>
            <br>
            <p style="font-size: 1.2rem">
                - Quantité : <b><?= $orderItem->getQuantity() ?></b><br>
                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                    - <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b><br>
                <?php endforeach; ?>
            </p>
            <?php if ($orderItem->getItem()->getType() == 1): ?>
                <p>Vous n'avez rien à faire pour celui-ci c'est un article virtuel, il est en attente de validation complète des article physique inclus dans cette commande avant de pouvoir executé les actions qui lui sont lié.</p>
            <?php endif; ?>
        </div>
    <?php endforeach;?>
</div>

<form id="cancel" action="cancel/<?= $order->getId() ?>" method="post">
    <?php (new SecurityManager())->insertHiddenToken() ?>
</form>
<form id="send" action="send/<?= $order->getId() ?>" method="post">
    <?php (new SecurityManager())->insertHiddenToken() ?>
</form>