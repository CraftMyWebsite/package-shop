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
        <h4><?= $order->getAdminStatus() ?></h4>
        <?php if ($order->getShippingMethod()): ?>
            <p>Éxpédition : <?= $order->getShippingMethod()->getName() ?> (<?= $order->getShippingMethod()->getPriceFormatted() ?>)</p>
        <?php endif; ?>
        <p>Total : <b><?= $order->getOrderTotalFormatted() ?></b> payé avec <?= $order->getPaymentMethod()->getName() ?> (<?= $order->getPaymentMethod()->getFeeFormatted() ?>)</p>
        <?php if ($order->getAppliedCartDiscount()): ?>
            <p>Réduction appliquée : <b>-<?= $order->getAppliedCartDiscountTotalPriceFormatted() ?></b></p>
        <?php endif; ?>
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
                        <?php if ($orderItem->getDiscountName()): ?>
                            <p>Réduction appliquée : <b><?= $orderItem->getDiscountName() ?></b> (-<?= $orderItem->getPriceDiscountImpactFormatted() ?>)</p>
                            <p>Prix : <s><?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></s> <b><?= $orderItem->getTotalPriceAfterDiscountFormatted() ?></b> | Quantité : <?= $orderItem->getQuantity() ?></p>
                        <?php else: ?>
                            <p>Prix : <b> <?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></b> | Quantité : <?= $orderItem->getQuantity() ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>