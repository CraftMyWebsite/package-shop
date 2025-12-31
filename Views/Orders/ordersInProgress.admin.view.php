<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $inProgressOrders */
/* @var bool $notificationIsRefused */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.orders.order.progress-title');
$description = '';

?>
<h3><i class="fa-solid fa-spinner fa-spin text-info"></i> <?= LangManager::translate('shop.views.orders.order.progress-title') ?></h3>

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
        <table class="table" id="table1" data-load-per-page="10">
            <thead>
            <tr>
                <th><?= LangManager::translate('shop.views.orders.order.user') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.number') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.amount') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.status') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.payment') ?></th>
                <th><?= LangManager::translate('shop.views.orders.order.date') ?></th>
                <th class="text-center"><?= LangManager::translate('shop.views.orders.order.manage') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inProgressOrders as $inProgressOrder): ?>
                <tr>
                    <td>
                        <?php if ($inProgressOrder->getUserAddressMethod()): ?>
                            <?= $inProgressOrder->getUserAddressMethod()->getUserFirstName() . ' ' . $inProgressOrder->getUserAddressMethod()->getUserLastName() ?>
                        <?php else: ?>
                            <?= $inProgressOrder->getUser()->getPseudo() ?>
                        <?php endif; ?>
                    </td>
                    <td>#<?= $inProgressOrder->getOrderNumber() ?></td>
                    <td>
                        <?= "<b style='color: #6f6fad'>" . $inProgressOrder->getOrderTotalFormatted() . '</b>' ?><br>
                    </td>
                    <td><?= $inProgressOrder->getAdminStatus() ?></td>
                    <?php $payment = ShopPaymentsController::getInstance()->getPaymentByVarName($inProgressOrder->getPaymentMethod()->getVarName()) ?>
                    <td>
                        <?php if ($payment): ?>
                            <a target="_blank" class="link"
                               href="<?= $payment->dashboardURL() ?>"><?= $inProgressOrder->getPaymentMethod()->getName() ?></a>
                        <?php else: ?>
                            <?= $inProgressOrder->getPaymentMethod()->getName() ?>
                        <?php endif; ?>
                        <?= '(' . $inProgressOrder->getPaymentMethod()->getFeeFormatted() . ')' ?></td>
                    <td><?= $inProgressOrder->getCreated() ?></td>
                    <td class="text-center">
                        <?php if ($inProgressOrder->getInvoiceLink()): ?>
                        <a class="mr-2" href="<?= $inProgressOrder->getInvoiceLink() ?>">
                            <i class="text-info fa-solid fa-file-invoice"></i>
                        </a>
                        <?php endif; ?>
                        <a href="inProgress/manage/<?= $inProgressOrder->getId() ?>">
                            <i class="text-success fa-solid fa-wand-magic-sparkles"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>