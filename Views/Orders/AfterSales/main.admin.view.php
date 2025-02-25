<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity [] $afterSales */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.orders.afterSales.main.title');
$description = '';

?>
<h3><i class="fa-solid fa-headset"></i> <?= LangManager::translate('shop.views.orders.afterSales.main.title') ?></h3>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

    <div class="card">
        <h6><?= LangManager::translate('shop.views.orders.afterSales.main.progress') ?></h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.user') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.order') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.reason') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.status') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.date') ?></th>
                    <th class="text-center"><?= LangManager::translate('shop.views.orders.afterSales.main.manage') ?></th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale): ?>
                    <?php if ($afterSale->getStatus() !== 2): ?>
                        <tr>
                            <td><?= $afterSale->getAuthor()->getPseudo() ?></td>
                            <td><a class="link" href="orders/view/<?= $afterSale->getOrder()->getId() ?>">#<?= $afterSale->getOrder()->getOrderNumber() ?></a></td>
                            <td><?= $afterSale->getFormattedReason() ?></td>
                            <td><?= $afterSale->getFormattedStatus() ?></td>
                            <td><?= $afterSale->getCreated() ?></td>
                            <td class="text-center">
                                <a href="afterSales/manage/<?= $afterSale->getId() ?>">
                                    <i class="text-success fa-solid fa-headset"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-6">
        <h6><?= LangManager::translate('shop.views.orders.afterSales.main.past') ?></h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.user') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.order') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.reason') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.status') ?></th>
                    <th><?= LangManager::translate('shop.views.orders.afterSales.main.date') ?></th>
                    <th class="text-center"><?= LangManager::translate('shop.views.orders.afterSales.main.manage') ?></th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale): ?>
                    <?php if ($afterSale->getStatus() === 2): ?>
                        <tr>
                            <td><?= $afterSale->getAuthor()->getPseudo() ?></td>
                            <td><a class="link" href="orders/view/<?= $afterSale->getOrder()->getId() ?>">#<?= $afterSale->getOrder()->getOrderNumber() ?></a></td>
                            <td><?= $afterSale->getFormattedReason() ?></td>
                            <td><?= $afterSale->getFormattedStatus() ?></td>
                            <td><?= $afterSale->getCreated() ?></td>
                            <td class="text-center">
                                <a href="afterSales/manage/<?= $afterSale->getId() ?>">
                                    <i class="text-info fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>