<?php

/* @var \CMW\Entity\Shop\ShopOrdersEntity $order */
/* @var \CMW\Entity\Shop\ShopOrdersItemsEntity [] $orderItems */
/* @var CMW\Model\Shop\ShopOrdersItemsVariantesModel $itemsVariantes */
/* @var \CMW\Model\Shop\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getNumber() ?> ANNULÉ</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Remboursement</h4>
            </div>
            <div class="card-body row">
                <p>Votre client a déjà payé l'intégralité de
                    <?php $totalPrice = 0;foreach ($orderItems as $orderedItem):$totalPrice += $orderedItem->getOrderItemPrice();endforeach; ?>
                    <?= "<b style='color: #6f6fad'>" . $totalPrice + $order->getShippingMethod()->getPrice() ." € </b>" ?>
                     avec <?= $order->getPaymentName() ?></p>
                <p>Il ne vous reste plus cas le rembourser pour terminer le traitement de cette commande.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Récap de commande</h4>
            </div>
            <div class="card-body row">
                <?php foreach ($orderItems as $orderItem):?>
                    <div style="align-items: baseline" class="d-flex justify-between mb-2">
                        <?php if ($orderItem->getFirstImageItemUrl() !== "/Public/Uploads/Shop/0"): ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImageItemUrl() ?>" alt="Panier"></div>
                        <?php else: ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                        <?php endif; ?>
                        <p><?= $orderItem->getOrderItemPrice() ?>€ - <?= $orderItem->getItem()->getName() ?> |
                            Quantité : <b><?= $orderItem->getOrderItemQuantity() ?></b> |
                            <?php foreach ($itemsVariantes->getShopItemVariantValueByOrderItemId($orderItem->getOrderItemId()) as $itemVariant): ?>
                                <?= $itemVariant->getVariantValue()->getVariant()->getName() ?> : <b><?= $itemVariant->getVariantValue()->getValue() ?></b>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endforeach;?>
                <p class="me-2"><b><?= $order->getShippingMethod()->getPrice() ?>€</b> de frais de livraison</p>
            </div>
        </div>
    </div>
</section>


<div class="text-center d-flex justify-content-between">
    <a href="../" class="btn btn-warning">Plus tard ...</a>
    <form action="refunded/<?= $order->getOrderId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-success">Commande remboursé !</button>
    </form>
</div>

