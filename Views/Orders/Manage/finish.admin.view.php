<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

use CMW\Manager\Security\SecurityManager;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
    <div>
        <a href="../" type="button" class="btn btn-warning">Pas pour l'instant</a>
        <button data-modal-toggle="modal-finish-him" class="btn-success" type="button">Terminé</button>
    </div>
</div>

<div id="modal-finish-him" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6>Commande terminé</h6>
            <button type="button" data-modal-hide="modal-finish-him"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="end/<?= $order->getId() ?>" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="modal-body">
                Parfait ! bonne vente à vous.
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Commande terminé</button>
            </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h6>Commande reçu ?</h6>
        <p>Si votre client a bien reçu sa commande, il est conseiller de la clôturer pour un meilleur suivie.</p>
        <?php if (!empty($order->getShippingLink())): ?>
            <p>Vous pouvez suivre l'avancée de la livraison ici : <a href="<?= $order->getShippingLink() ?>" target="_blank" class="link">Suivre le colis</a></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h6>Récap de commande</h6>
        <?php foreach ($order->getOrderedItems() as $orderItem): ?>
            <div class="flex items-start mb-2">
                <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                    <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                <?php else: ?>
                    <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                <?php endif; ?>
                <p><?= $orderItem->getName() ?> <br>
                    Quantité : <b><?= $orderItem->getQuantity() ?></b> <br>
                    <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                        <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                    <?php endforeach; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>