<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = "Commandes #" . $order->getOrderNumber();
$description = "";

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes #<?= $order->getOrderNumber() ?> ANNULÉ</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Remboursement</h4>
            </div>
            <div class="card-body row">
                <p>Votre client a déjà payé l'intégralité de
                    <?= "<b style='color: #6f6fad'>" . $order->getOrderTotalFormatted() ."</b>" ?>
                     avec <?= $order->getPaymentMethod()->getName() ?></p>
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
                <?php foreach ($order->getOrderedItems() as $orderItem):?>
                    <div style="align-items: baseline" class="d-flex justify-between mb-2">
                        <?php if ($orderItem->getFirstImg()!== "/Public/Uploads/Shop/0"): ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                        <?php else: ?>
                            <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                        <?php endif; ?>
                        <p><?= $orderItem->getPriceFormatted() ?> - <?= $orderItem->getName() ?> |
                            Quantité : <b><?= $orderItem->getQuantity() ?></b> |
                            <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                                <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
</section>


<div class="text-center d-flex justify-content-between">
    <a href="../" class="btn btn-warning">Plus tard ...</a>
    <form action="refunded/<?= $order->getId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-success">TODO CREATE AVOIR</button>
    </form>
    <form action="refunded/<?= $order->getId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-success">Commande remboursé ! TODO MODAL</button>
    </form>
</div>

