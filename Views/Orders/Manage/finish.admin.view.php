<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = LangManager::translate('shop.views.orders.manage.finish.title', ['number' => $order->getOrderNumber()]);
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> <?= LangManager::translate('shop.views.orders.manage.finish.title', ['number' => $order->getOrderNumber()]) ?></h3>
    <div>
        <a href="../" type="button" class="btn btn-warning"><?= LangManager::translate('shop.views.orders.manage.finish.not-now') ?></a>
        <button data-modal-toggle="modal-finish-him" class="btn-success" type="button"><?= LangManager::translate('shop.views.orders.manage.finish.ended') ?></button>
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


<div id="modal-finish-him" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6><?= LangManager::translate('shop.views.orders.manage.finish.order-ended') ?></h6>
            <button type="button" data-modal-hide="modal-finish-him"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="end/<?= $order->getId() ?>" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <?= LangManager::translate('shop.views.orders.manage.finish.perfect') ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success"><?= LangManager::translate('shop.views.orders.manage.finish.order-ended') ?></button>
            </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div>
        <div class="card">
            <?php if ($order->getShippingMethod()->getShipping()->getType() === 0): ?>
                <h6><?= LangManager::translate('shop.views.orders.manage.finish.received') ?></h6>
                <p><?= LangManager::translate('shop.views.orders.manage.finish.received-text') ?></p>
                <?php if (!empty($order->getShippingLink())): ?>
                    <p><?= LangManager::translate('shop.views.orders.manage.finish.follow', ['link' => $order->getShippingLink()]) ?></p>
                <?php endif; ?>
            <?php else: ?>
                <h6><?= LangManager::translate('shop.views.orders.manage.finish.withdraw') ?></h6>
                <p><?= LangManager::translate('shop.views.orders.manage.finish.withdraw-text') ?></p>
            <?php endif; ?>
        </div>
        <div class="card mt-4">
            <h5><?= LangManager::translate('shop.views.orders.manage.finish.shipping') ?></h5>
            <hr>
            <h6><?= LangManager::translate('shop.views.orders.manage.finish.shipping-type', ['type' => $order->getShippingMethod()->getShipping()->getFormattedType()]) ?></h6>
            <p><?= $order->getShippingMethod()->getName() ?> - <b><?= $order->getShippingMethod()->getPriceFormatted() ?></b></p>
            <hr>
            <h6><?= LangManager::translate('shop.views.orders.manage.finish.shipped-to') ?></h6>
            <p>
                <?= $order->getUserAddressMethod()->getUserFirstName() ?>
                <?= $order->getUserAddressMethod()->getUserLastName() ?><br>
                <?= $order->getUserAddressMethod()->getUserLine1() ?><br>
                <?php if (!empty($order->getUserAddressMethod()->getUserLine2())) { echo $order->getUserAddressMethod()->getUserLine2() . '<br>'; } ?>
                <?= $order->getUserAddressMethod()->getUserPostalCode() ?>
                <?= $order->getUserAddressMethod()->getUserCity() ?><br>
                <?= $order->getUserAddressMethod()->getUserFormattedCountry() ?><br>
            </p>
            <hr>
            <h6><?= LangManager::translate('shop.views.orders.manage.finish.more') ?></h6>
            <p>
                <?= LangManager::translate('shop.views.orders.manage.finish.phone') ?> <b><?= $order->getUserAddressMethod()->getUserPhone() ?></b><br>
                <?= LangManager::translate('shop.views.orders.manage.finish.mail') ?> <b><?= $order->getUserAddressMethod()->getUserMail() ?></b>
            </p>
        </div>
    </div>
    <div class="card">
        <h6><?= LangManager::translate('shop.views.orders.manage.finish.review') ?></h6>
        <?php foreach ($order->getOrderedItems() as $orderItem): ?>
            <div class="flex items-start mb-2">
                <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                    <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $orderItem->getFirstImg() ?>" alt="Panier"></div>
                <?php else: ?>
                    <div class="me-2"><img style="width: 4rem; height: 4rem; object-fit: cover" src="<?= $defaultImage ?>" alt="Panier"></div>
                <?php endif; ?>
                <p><?= $orderItem->getName() ?> <br>
                    <?php if ($reviewEnabled): ?>
                        <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?><br>
                    <?php endif; ?>
                    <?= LangManager::translate('shop.views.orders.manage.finish.quantity') ?> <b><?= $orderItem->getQuantity() ?></b> <br>
                    <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                        <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                    <?php endforeach; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
</div>