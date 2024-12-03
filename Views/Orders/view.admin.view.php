<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Utils\Website;

$title = 'Commandes #' . $order->getOrderNumber();
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> Commandes #<?= $order->getOrderNumber() ?></h3>
</div>

<div class="alert-info mb-4">
    <h6><?= $order->getAdminStatus() ?></h6>
    <?php if ($order->getShippingMethod()): ?>
        <p>Éxpédition : <?= $order->getShippingMethod()->getName() ?>
            (<?= $order->getShippingMethod()->getPriceFormatted() ?>)</p>
    <?php endif; ?>
    <p>Total : <b><?= $order->getOrderTotalFormatted() ?></b> payé avec <?= $order->getPaymentMethod()->getName() ?>
        (<?= $order->getPaymentMethod()->getFeeFormatted() ?>)</p>
    <?php if ($order->getAppliedCartDiscount()): ?>
        <p>Réduction appliquée : <b>-<?= $order->getAppliedCartDiscountTotalPriceFormatted() ?></b></p>
    <?php endif; ?>
</div>

<div class="grid-4">
    <?php foreach ($order->getOrderedItems() as $orderItem): ?>
        <div class="card">
            <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                <div class="text-center">
                    <img class="mx-auto" style="width: 10rem; height: 10rem; object-fit: cover"
                         src="<?= $orderItem->getFirstImg() ?>" alt="Panier">
                </div>
            <?php else: ?>
                <div class="text-center">
                    <img class="mx-auto" style="width: 10rem; height: 10rem; object-fit: cover"
                         src="<?= $defaultImage ?>" alt="..."/>
                </div>
            <?php endif; ?>
            <h4 class="text-center"><?= $orderItem->getName() ?></h4>
            <br>
            <?php if ($reviewEnabled): ?>
            <div class="flex justify-center">
                <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?>
            </div>
            <?php if ($order->getStatusCode() !== -2): ?>
                <?php
                $hasReview = false;
                foreach (ShopReviewsModel::getInstance()->getShopReviewByItemId($orderItem->getItem()->getId()) as $review) {
                    if ($review->getUser()->getId() === $orderItem->getHistoryOrder()->getUser()->getId()) {
                        $hasReview = true;
                        break;
                    }
                }
                if (!$hasReview): ?>
                    <div class="alert-info">
                        <b><?= $order->getUser()->getPseudo() ?></b> n'as pas encore donné son avis sur cet article !<br>
                        <a data-modal-toggle="modal-relance-<?= $orderItem->getItem()->getId() ?>" class="btn-success-sm flex justify-center cursor-pointer">Envoyer une relance</a>
                        <!--MODAL SUCCESS-->
                        <div id="modal-relance-<?= $orderItem->getItem()->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-success">
                                    <h6>Relance d'avis pour <?= $orderItem->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-relance-<?= $orderItem->getItem()->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                    <div class="modal-body">
                                        <div class="alert-warning">Avez-vous configurer le mail d'envoie de relance dans l'onglet configuration de la boutique ?</div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="../../../shop/settings" type="button" class="btn-warning">Non, configurer</a>
                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/shop/orders/<?= $order->getId() ?>/reviewReminder/<?= $orderItem->getItem()->getId() ?>/<?= $order->getUser()->getId() ?>" type="button" class="btn-success">Oui, envoyer</a>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <div class="alert-success text-center">
                    <b><?= $order->getUser()->getPseudo() ?></b> à donner son avis !<br>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            <p style="font-size: 1.2rem">
                - Quantité : <b><?= $orderItem->getQuantity() ?></b><br>
                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                    - <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b><br>
                <?php endforeach; ?>
            </p>
            <?php if ($orderItem->getDiscountName()): ?>
                <p>Réduction appliquée : <b><?= $orderItem->getDiscountName() ?></b>
                    (-<?= $orderItem->getPriceDiscountImpactFormatted() ?>)</p>
                <p>Prix : <s><?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></s>
                    <b><?= $orderItem->getTotalPriceAfterDiscountFormatted() ?></b> | Quantité
                    : <?= $orderItem->getQuantity() ?></p>
            <?php else: ?>
                <p>Prix : <b> <?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></b> | Quantité
                    : <?= $orderItem->getQuantity() ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>