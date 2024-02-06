<?php

/* @var \CMW\Entity\Shop\Orders\ShopOrdersEntity $order */
/* @var \CMW\Entity\Shop\Orders\ShopOrdersItemsEntity [] $orderItems */
/* @var CMW\Model\Shop\Order\ShopOrdersItemsVariantesModel $itemsVariantes */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getNumber() ?></span></h3>
</div>

<div class="card">
    <div class="card-header">
        <h4>Articles à préparer</h4>
    </div>
        <div class="card-body row">
            <?php foreach ($orderItems as $orderItem):?>
                <div class="col-12 col-lg-3 mb-4">
                    <div class="card-in-card">
                        <div class="card-body">
                            <?php if ($orderItem->getFirstImageItemUrl() !== "/Public/Uploads/Shop/0"): ?>
                            <div class="text-center" >
                                <img style="width: 10rem; height: 10rem; object-fit: cover" src="<?= $orderItem->getFirstImageItemUrl() ?>" alt="Panier">
                            </div>
                            <?php else: ?>
                            <div class="text-center" >
                                <img style="width: 10rem; height: 10rem; object-fit: cover" src="<?= $defaultImage ?>" alt="..."/>
                            </div>
                            <?php endif; ?>
                            <h4 class="text-center"><?= $orderItem->getItem()->getName() ?></h4>
                            <br>
                            <p style="font-size: 1.2rem">
                                - Quantité : <b><?= $orderItem->getOrderItemQuantity() ?></b><br>
                            <?php foreach ($itemsVariantes->getShopItemVariantValueByOrderItemId($orderItem->getOrderItemId()) as $itemVariant): ?>
                                - <?= $itemVariant->getVariantValue()->getVariant()->getName() ?> : <b><?= $itemVariant->getVariantValue()->getValue() ?></b><br>
                            <?php endforeach; ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach;?>
        </div>
</div>

<div class="text-center d-flex justify-content-between">
    <form action="cancel/<?= $order->getOrderId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-danger">Cette commande n'est pas réalisable</button>
    </form>
    <form action="send/<?= $order->getOrderId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-primary">Commande prête pour l'envoie</button>
    </form>
</div>