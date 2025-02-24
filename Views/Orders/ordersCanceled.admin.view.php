<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $errorOrders */
/* @var bool $notificationIsRefused */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.orders.order.canceled-title');
$description = '';

?>
<h3><i class="fa-solid fa-square-xmark text-danger"></i> <?= LangManager::translate('shop.views.orders.order.canceled-title') ?></h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="card">
    <?php if ($notificationIsRefused): ?>
        <div class="alert alert-warning">
            <p><i class="fa-solid fa-triangle-exclamation"></i> <?= LangManager::translate('shop.views.orders.order.notify') ?></p>
        </div>
    <?php endif; ?>
    <div class="table-container table-container-striped">
        <table id="table3" data-load-per-page="10">
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
            <?php foreach ($errorOrders as $errorOrder): ?>
                <tr>
                    <td><?= $errorOrder->getUserAddressMethod()->getUserFirstName() . ' ' . $errorOrder->getUserAddressMethod()->getUserLastName() ?></td>
                    <td>#<?= $errorOrder->getOrderNumber() ?></td>
                    <td>
                        <?= "<b style='color: #ad6f78'> -" . $errorOrder->getOrderTotalFormatted() . '</b>' ?><br>
                    </td>
                    <td><?= $errorOrder->getAdminStatus() ?></td>
                    <td><?= $errorOrder->getCreated() ?></td>
                    <td class="text-center">
                        <?php if ($errorOrder->getInvoiceLink()): ?>
                            <a class="mr-2" href="<?= $errorOrder->getInvoiceLink() ?>">
                                <i class="text-info fa-solid fa-file-invoice"></i>
                            </a>
                        <?php endif; ?>
                        <a href="canceled/view/<?= $errorOrder->getId() ?>">
                            <i class="text-primary fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
