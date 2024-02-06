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

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Commande reçu ?</h4>
            </div>
            <div class="card-body row">
                <p>Si votre client a bien reçu sa commande, il est conseiller de la clôturer pour un meilleur suivie.</p>
                <?php if (!empty($order->getShippingLink())): ?>
                    <p>Vous pouvez suivre l'avancée de la livraison ici : <a href="<?= $order->getShippingLink() ?>" target="_blank" class="">Suivre le colis</a></p>
                <?php endif; ?>
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
                        <p><?= $orderItem->getItem()->getName() ?> |
                            Quantité : <b><?= $orderItem->getOrderItemQuantity() ?></b> |
                            <?php foreach ($itemsVariantes->getShopItemVariantValueByOrderItemId($orderItem->getOrderItemId()) as $itemVariant): ?>
                                <?= $itemVariant->getVariantValue()->getVariant()->getName() ?> : <b><?= $itemVariant->getVariantValue()->getValue() ?></b>
                            <?php endforeach; ?>
                        </p>
                    </div>

                <?php endforeach;?>
            </div>
        </div>
    </div>
</section>


<div class="text-center d-flex justify-content-between">
    <a href="../" class="btn btn-warning">Pas pour l'instant</a>
    <form action="end/<?= $order->getOrderId() ?>" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <button type="submit" class="btn btn-primary">Cette commande est terminé</button>
    </form>
</div>

