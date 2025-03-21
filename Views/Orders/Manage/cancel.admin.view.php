<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = LangManager::translate('shop.views.orders.manage.cancel.title', ['number' => $order->getOrderNumber()]);
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> <?= LangManager::translate('shop.views.orders.manage.cancel.title', ['number' => $order->getOrderNumber()]) ?></h3>
    <div>
        <a href="../" type="button" class="btn btn-warning"><?= LangManager::translate('shop.views.orders.manage.cancel.later') ?></a>
        <button data-modal-toggle="modal-avoir" class="btn-warning" type="button"><?= LangManager::translate('shop.views.orders.manage.cancel.credit') ?></button>
        <button data-modal-toggle="modal-refunded" class="btn-success" type="button"><?= LangManager::translate('shop.views.orders.manage.cancel.refunded') ?></button>
    </div>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div id="modal-avoir" class="modal-container">
    <div class="modal">
        <div class="modal-header-warning">
            <h6><?= LangManager::translate('shop.views.orders.manage.cancel.credit') ?></h6>
            <button type="button" data-modal-hide="modal-avoir"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="avoir" action="refunded/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="alert-info">
                    <?= LangManager::translate('shop.views.orders.manage.cancel.credit-text') ?>
                </div>
                <label for="name"><?= LangManager::translate('shop.views.orders.manage.cancel.name') ?><span style="color: red">*</span> :</label>
                <input type="text" id="name" name="name" class="input" placeholder="Credit : Avoir">
                <small><?= LangManager::translate('shop.views.orders.manage.cancel.name-info') ?></small>
            </div>
            <div class="modal-footer">
                <button form="avoir" type="submit" class="btn btn-warning-sm"><?= LangManager::translate('shop.views.orders.manage.cancel.credit') ?></button>
            </div>
        </form>
    </div>
</div>

<div id="modal-refunded" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6><?= LangManager::translate('shop.views.orders.manage.cancel.order-refunded') ?></h6>
            <button type="button" data-modal-hide="modal-refunded"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="refunded" action="endFailed/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div class="modal-body">
            <?= LangManager::translate('shop.views.orders.manage.cancel.refunded-text') ?>
        </div>
        <div class="modal-footer">
            <button form="refunded" type="submit" class="btn btn-success"><?= LangManager::translate('shop.views.orders.manage.cancel.order-refunded') ?></button>
        </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h6><?= LangManager::translate('shop.views.orders.manage.cancel.refund') ?></h6>
        <p> <?= LangManager::translate('shop.views.orders.manage.cancel.refund-info', ['price' => $order->getOrderTotalFormatted(), 'payment_name' => $order->getPaymentMethod()->getName()]) ?></p>
    </div>
    <div class="card">
            <h6><?= LangManager::translate('shop.views.orders.manage.cancel.review') ?></h6>
            <?php foreach ($order->getOrderedItems() as $orderItem): ?>
                <div class="flex items-start mb-2">
                    <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                    <?php else: ?>
                        <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                    <?php endif; ?>
                    <p><?= $orderItem->getName() ?> - <?= $orderItem->getPriceFormatted() ?> <br>
                        <?php if ($reviewEnabled): ?>
                            <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?><br>
                        <?php endif; ?>
                        <?= LangManager::translate('shop.views.orders.manage.cancel.quantity') ?> <b><?= $orderItem->getQuantity() ?></b> <br>
                        <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                            <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                        <?php endforeach; ?>
                    </p>
                </div>
            <?php endforeach; ?>
    </div>
</div>
