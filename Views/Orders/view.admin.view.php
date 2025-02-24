<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = LangManager::translate('shop.views.orders.view.title', ['number' => $order->getOrderNumber()]);
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> <?= LangManager::translate('shop.views.orders.view.title', ['number' => $order->getOrderNumber()]) ?></h3>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="alert-info mb-4">
    <h6><?= $order->getAdminStatus() ?></h6>
    <?php if ($order->getShippingMethod()): ?>
        <p><?= LangManager::translate('shop.views.orders.view.ship') ?> <?= $order->getShippingMethod()->getName() ?>
            (<?= $order->getShippingMethod()->getPriceFormatted() ?>)</p>
    <?php endif; ?>
    <p><?= LangManager::translate('shop.views.orders.view.total') ?> <b><?= $order->getOrderTotalFormatted() ?></b> <?= LangManager::translate('shop.views.orders.view.paid') ?> <?= $order->getPaymentMethod()->getName() ?>
        (<?= $order->getPaymentMethod()->getFeeFormatted() ?>)</p>
    <?php if ($order->getAppliedCartDiscount()): ?>
        <p><?= LangManager::translate('shop.views.orders.view.discount') ?> <b>-<?= $order->getAppliedCartDiscountTotalPriceFormatted() ?></b></p>
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
                        <b><?= $order->getUser()->getPseudo() ?></b> <?= LangManager::translate('shop.views.orders.view.not-reviewed') ?><br>
                        <a data-modal-toggle="modal-relance-<?= $orderItem->getItem()->getId() ?>" class="btn-success-sm flex justify-center cursor-pointer"><?= LangManager::translate('shop.views.orders.view.call') ?></a>
                        <!--MODAL SUCCESS-->
                        <div id="modal-relance-<?= $orderItem->getItem()->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-success">
                                    <h6><?= LangManager::translate('shop.views.orders.view.call-title', ['name' => $orderItem->getName()]) ?></h6>
                                    <button type="button" data-modal-hide="modal-relance-<?= $orderItem->getItem()->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                    <div class="modal-body">
                                        <div class="alert-warning"><?= LangManager::translate('shop.views.orders.view.call-text') ?></div>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="../../../shop/settings" type="button" class="btn-warning"><?= LangManager::translate('shop.views.orders.view.no') ?></a>
                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/shop/orders/<?= $order->getId() ?>/reviewReminder/<?= $orderItem->getItem()->getId() ?>/<?= $order->getUser()->getId() ?>" type="button" class="btn-success"><?= LangManager::translate('shop.views.orders.view.yes') ?></a>
                                    </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <div class="alert-success text-center">
                    <b><?= $order->getUser()->getPseudo() ?></b> <?= LangManager::translate('shop.views.orders.view.reviewed') ?><br>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
            <p style="font-size: 1.2rem">
                - <?= LangManager::translate('shop.views.orders.view.quantity') ?> <b><?= $orderItem->getQuantity() ?></b><br>
                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                    - <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b><br>
                <?php endforeach; ?>
            </p>
            <?php if ($orderItem->getDiscountName()): ?>
                <p><?= LangManager::translate('shop.views.orders.view.discount-applied') ?> <b><?= $orderItem->getDiscountName() ?></b>
                    (-<?= $orderItem->getPriceDiscountImpactFormatted() ?>)</p>
                <p><?= LangManager::translate('shop.views.orders.view.price') ?> <s><?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></s>
                    <b><?= $orderItem->getTotalPriceAfterDiscountFormatted() ?></b> | <?= LangManager::translate('shop.views.orders.view.quantity') ?> <?= $orderItem->getQuantity() ?></p>
            <?php else: ?>
                <p><?= LangManager::translate('shop.views.orders.view.price') ?> <b> <?= $orderItem->getTotalPriceBeforeDiscountFormatted() ?></b> | <?= LangManager::translate('shop.views.orders.view.quantity') ?> <?= $orderItem->getQuantity() ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>