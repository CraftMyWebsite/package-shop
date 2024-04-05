<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getOrderNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getOrderNumber() ?></span></h3>
</div>

<div class="card">
    <div class="card-header">
        <h4>Articles à préparer</h4>
    </div>
        <div class="card-body row">
            <?php foreach ($order->getOrderedItems() as $orderItem):?>
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card">
                        <div class="card-body">
                            <?php if ($orderItem->getFirstImg() !== "/Public/Uploads/Shop/0"): ?>
                            <div class="text-center" >
                                <img style="width: 10rem; height: 10rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier">
                            </div>
                            <?php else: ?>
                            <div class="text-center" >
                                <img style="width: 10rem; height: 10rem; object-fit: cover" src="<?= $defaultImage ?>" alt="..."/>
                            </div>
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
                    </div>
                </div>
            <?php endforeach;?>
        </div>
</div>

<div class="text-center d-flex justify-content-between">
    <form action="cancel/<?= $order->getId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-danger">Cette commande n'est pas réalisable</button>
    </form>
    <form action="send/<?= $order->getId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-primary">Commande prête pour l'envoie</button>
    </form>
</div>