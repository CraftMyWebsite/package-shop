<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Type\Shop\Enum\Item\ShopItemType;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = LangManager::translate('shop.views.orders.manage.new.title', ['number' => $order->getOrderNumber()]);
$description = '';

?>

<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> <?= LangManager::translate('shop.views.orders.manage.new.title', ['number' => $order->getOrderNumber()]) ?></h3>
    <div>
        <button data-modal-toggle="modal-danger" type="button" class="btn btn-danger"><?= LangManager::translate('shop.views.orders.manage.new.unrealizable') ?></button>
        <button data-modal-toggle="modal-success" type="button" class="btn btn-primary"><?= LangManager::translate('shop.views.orders.manage.new.ready') ?></button>
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

<div id="modal-danger" class="modal-container">
    <div class="modal">
        <div class="modal-header-danger">
            <h6><?= LangManager::translate('shop.views.orders.manage.new.unrealizable') ?></h6>
            <button type="button" data-modal-hide="modal-danger"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <?= LangManager::translate('shop.views.orders.manage.new.refund') ?>
            </p>
        </div>
        <div class="modal-footer">
            <button form="cancel" type="submit" class="btn btn-danger"><?= LangManager::translate('shop.views.orders.manage.new.unrealizable-btn') ?></button>
        </div>
    </div>
</div>

<!--MODAL SUCCESS-->
<div id="modal-success" class="modal-container">
    <div class="modal">
        <div class="modal-header-success">
            <h6><?= LangManager::translate('shop.views.orders.manage.new.inbox') ?></h6>
            <button type="button" data-modal-hide="modal-success"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">
                <?php if ($order->getShippingMethod()?->getShipping()->getType() === 0): ?>
                    <?= LangManager::translate('shop.views.orders.manage.new.ship') ?>
                <?php elseif($order->getShippingMethod()?->getShipping()->getType() === 1): ?>
                    <?= LangManager::translate('shop.views.orders.manage.new.withdraw') ?>
                <?php else: ?>
                    <?= LangManager::translate('shop.views.orders.manage.new.virtual') ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="modal-footer">
            <button data-modal-hide="modal-success" type="button" class="btn-danger"><?= LangManager::translate('core.btn.close') ?></button>
            <button form="send" type="submit" class="btn btn-success"><?= LangManager::translate('shop.views.orders.manage.new.all-ready') ?></button>
        </div>
    </div>
</div>


<div class="alert-info">
    <?php if ($order->getShippingMethod()?->getShipping()->getType() === 0): ?>
        <p><?= LangManager::translate('shop.views.orders.manage.new.need-to-ship') ?></p>
    <?php elseif ($order->getShippingMethod()?->getShipping()->getType() === 1): ?>
        <p><?= LangManager::translate('shop.views.orders.manage.new.need-to-withdraw') ?></p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressLine() ?></p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressPostalCode() ?> <?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getAddressCity() ?></p>
        <p><?= $order->getShippingMethod()->getShipping()->getWithdrawPoint()->getFormattedCountry() ?></p>
    <?php else: ?>
        <p><?= LangManager::translate('shop.views.orders.manage.new.only-virtual') ?></p>
    <?php endif; ?>
</div>
<h6><?= LangManager::translate('shop.views.orders.manage.new.prepare') ?></h6>

<div class="grid-4">
    <?php foreach ($order->getOrderedItems() as $orderItem): ?>
        <div class="card">
            <?php if ($orderItem->getFirstImg() !== '/Public/Uploads/Shop/0'): ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto" src="<?= $orderItem->getFirstImg() ?>" alt="Panier">
            <?php else: ?>
                <img style="width: 10rem; height: 10rem; object-fit: cover" class="mx-auto"  src="<?= $defaultImage ?>" alt="..."/>
            <?php endif; ?>
            <h4 class="text-center"><?= $orderItem->getName() ?></h4>
            <?php if ($reviewEnabled): ?>
                <div class="flex justify-center">
                    <?= ShopReviewsModel::getInstance()->getStars($orderItem->getItem()->getId()) ?>
                </div>
            <?php endif; ?>
            <?php if ($orderItem->getItem()->getType() === ShopItemType::VIRTUAL):
                $virtualMethod = ShopItemsVirtualMethodModel::getInstance()?->getVirtualItemMethodByItemId($orderItem->getItem()->getId())->getVirtualMethod()->name(); ?>
                <p><?= LangManager::translate('shop.views.orders.manage.new.virtual-method', ['method' => $virtualMethod]) ?></p>
            <?php endif; ?>
            <?php if ($orderItem->getItem()->getType() === ShopItemType::PHYSICAL): ?>
                <p><?= LangManager::translate('shop.views.orders.manage.new.physical') ?></p>
            <?php endif; ?>
            <p style="font-size: 1.2rem">
                - <?= LangManager::translate('shop.views.orders.manage.new.quantity') ?> <b><?= $orderItem->getQuantity() ?></b><br>
                <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                    - <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b><br>
                <?php endforeach; ?>
            </p>
        </div>
    <?php endforeach; ?>
</div>

<form id="cancel" action="cancel/<?= $order->getId() ?>" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
</form>
<form id="send" action="send/<?= $order->getId() ?>" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
</form>