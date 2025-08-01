<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $finishedOrders */
/* @var bool $notificationIsRefused */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.orders.order.ended-title');
$description = '';

?>
<h3><i class="fa-solid fa-circle-check text-success"></i> <?= LangManager::translate('shop.views.orders.order.ended-title') ?></h3>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="card">
    <?php if ($notificationIsRefused): ?>
        <div class="alert alert-warning">
            <p><i class="fa-solid fa-triangle-exclamation"></i> <?= LangManager::translate('shop.views.orders.order.notify') ?></p>
        </div>
    <?php endif; ?>
    <div class="table-container table-container-striped">
        <table id="table2" data-load-per-page="10">
            <thead>
            <tr>
                <th><?= LangManager::translate('shop.views.orders.order.user') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.number') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.amount') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.status') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.date') ?></th>
                <th class="text-center"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finishedOrders as $finishedOrder): ?>
                <tr>
                    <td>
                        <?php if ($finishedOrder->getUserAddressMethod()): ?>
                            <?= $finishedOrder->getUserAddressMethod()->getUserFirstName() . ' ' . $finishedOrder->getUserAddressMethod()->getUserLastName() ?>
                        <?php else: ?>
                            <?= $finishedOrder->getUser()->getPseudo() ?>
                        <?php endif; ?>
                    </td>
                    <td>#<?= $finishedOrder->getOrderNumber() ?></td>
                    <td>
                        <?= "<b style='color: #73ad6f'> +" . $finishedOrder->getOrderTotalFormatted() . '</b>' ?><br>
                    </td>
                    <td><?= $finishedOrder->getAdminStatus() ?></td>
                    <td><?= $finishedOrder->getCreated() ?></td>
                    <td class="text-center">
                        <?php if ($finishedOrder->getInvoiceLink()): ?>
                            <a class="mr-2" href="<?= $finishedOrder->getInvoiceLink() ?>">
                                <i class="text-info fa-solid fa-file-invoice"></i>
                            </a>
                        <?php endif; ?>
                        <a href="ended/view/<?= $finishedOrder->getId() ?>">
                            <i class="text-primary fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>