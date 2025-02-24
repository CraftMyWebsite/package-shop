<?php

/* @var CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity $order */
/* @var CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var bool $reviewEnabled */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Review\ShopReviewsModel;

$title = LangManager::translate('shop.views.orders.manage.send.title', ['number' => $order->getOrderNumber()]);
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-list-check"></i> <?= LangManager::translate('shop.views.orders.manage.send.title', ['number' => $order->getOrderNumber()]) ?></h3>
    <div>
        <a type="button" href="../" class="btn btn-warning"><?= LangManager::translate('shop.views.orders.manage.send.later') ?></a>
        <button form="finish" type="submit" class="btn btn-primary"><?= LangManager::translate('shop.views.orders.manage.send.road') ?></button>
    </div>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="grid-2">
    <div class="card">
        <h5><?= LangManager::translate('shop.views.orders.manage.send.ship') ?></h5>
        <hr>
        <h6><?= LangManager::translate('shop.views.orders.manage.send.ship-type', ['type' => $order->getShippingMethod()->getShipping()->getFormattedType()]) ?></h6>
        <p><?= $order->getShippingMethod()->getName() ?> - <b><?= $order->getShippingMethod()->getPriceFormatted() ?></b></p>
        <hr>
        <h6><?= LangManager::translate('shop.views.orders.manage.send.shipped-to') ?></h6>
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
        <h6><?= LangManager::translate('shop.views.orders.manage.send.more') ?></h6>
        <p>
            <?= LangManager::translate('shop.views.orders.manage.send.phone') ?> <b><?= $order->getUserAddressMethod()->getUserPhone() ?></b><br>
            <?= LangManager::translate('shop.views.orders.manage.send.mail') ?> <b><?= $order->getUserAddressMethod()->getUserMail() ?></b>
        </p>
    </div>

    <div>
        <div class="card">
            <h6><?= LangManager::translate('shop.views.orders.manage.send.follow') ?></h6>
            <form id="finish" action="finish/<?= $order->getId() ?>" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <h6><?= LangManager::translate('shop.views.orders.manage.send.follow-link') ?></h6>
                <input type="text" class="input" name="shipping_link">
                <small><?= LangManager::translate('shop.views.orders.manage.send.follow-info') ?></small>
            </form>
        </div>
        <div class="card mt-6">
            <h6><?= LangManager::translate('shop.views.orders.manage.send.review') ?></h6>
            <div>
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
                            <?= LangManager::translate('shop.views.orders.manage.send.quantity') ?> <b><?= $orderItem->getQuantity() ?></b> <br>
                            <?php foreach ($order->getOrderedItemsVariantes($orderItem->getId()) as $itemVariant): ?>
                                <?= $itemVariant->getName() ?> : <b><?= $itemVariant->getValue() ?></b>
                            <?php endforeach; ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
