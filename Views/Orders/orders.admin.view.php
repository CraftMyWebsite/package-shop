<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $inProgressOrders */
/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $errorOrders */
/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $finishedOrders */
/* @var bool $notificationIsRefused */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$title = 'Commandes';
$description = '';

?>
<h3><i class="fa-solid fa-list-check"></i> Commandes</h3>

<div class="card">
    <h6><i class="fa-solid fa-spinner fa-spin text-info"></i> En cours</h6>
    <?php if ($notificationIsRefused): ?>
        <div class="alert alert-warning">
            <p><i class="fa-solid fa-triangle-exclamation"></i> Attention, vous avez désactivé les notifications du
                package Shop ! Vous ne serez pas alerté en cas de nouvelle commande. <br>Il est également recommandé
                d'activer les alertes mail / Discord dans les paramètres de notification. <a class="link"
                                                                                             href="/cmw-admin/notifications">Modifier
                    les réglages des notifications</a></p>
        </div>
    <?php endif; ?>
    <div class="table-container table-container-striped">
        <table class="table" id="table1">
            <thead>
            <tr>
                <th>Utilisateur</th>
                <th>N° de commande</th>
                <th>Montant</th>
                <th>Status</th>
                <th>Paiement</th>
                <th>Date</th>
                <th class="text-center">Gérer</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($inProgressOrders as $inProgressOrder): ?>
                <tr>
                    <td><?= $inProgressOrder->getUserAddressMethod()->getUserFirstName() . ' ' . $inProgressOrder->getUserAddressMethod()->getUserLastName() ?></td>
                    <td>#<?= $inProgressOrder->getOrderNumber() ?></td>
                    <td>
                        <?= "<b style='color: #6f6fad'>" . $inProgressOrder->getOrderTotalFormatted() . '</b>' ?><br>
                    </td>
                    <td><?= $inProgressOrder->getAdminStatus() ?></td>
                    <?php $payment = ShopPaymentsController::getInstance()->getPaymentByVarName($inProgressOrder->getPaymentMethod()->getVarName()) ?>
                    <td>
                        <?php if ($payment->dashboardURL()): ?>
                            <a target="_blank" class="link"
                               href="<?= $payment->dashboardURL() ?>"><?= $inProgressOrder->getPaymentMethod()->getName() ?></a>
                        <?php else: ?>
                            <?= $inProgressOrder->getPaymentMethod()->getName() ?>
                        <?php endif; ?>
                        <?= '(' . $inProgressOrder->getPaymentMethod()->getFeeFormatted() . ')' ?></td>
                    <td><?= $inProgressOrder->getCreated() ?></td>
                    <td class="text-center">
                        <a href="orders/manage/<?= $inProgressOrder->getId() ?>">
                            <i class="text-success fa-solid fa-wand-magic-sparkles"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-6">
    <h6><i class="fa-solid fa-circle-check text-success"></i> Terminé</h6>
    <div class="table-container table-container-striped">
        <table id="table2">
            <thead>
            <tr>
                <th>Utilisateur</th>
                <th>N° de commande</th>
                <th>Montant</th>
                <th>Status</th>
                <th>Date</th>
                <th class="text-center"></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($finishedOrders as $finishedOrder): ?>
                <tr>
                    <td><?= $finishedOrder->getUserAddressMethod()->getUserFirstName() . ' ' . $finishedOrder->getUserAddressMethod()->getUserLastName() ?></td>
                    <td>#<?= $finishedOrder->getOrderNumber() ?></td>
                    <td>
                        <?= "<b style='color: #73ad6f'> +" . $finishedOrder->getOrderTotalFormatted() . '</b>' ?><br>
                    </td>
                    <td><?= $finishedOrder->getAdminStatus() ?></td>
                    <td><?= $finishedOrder->getCreated() ?></td>
                    <td class="text-center">
                        <a href="orders/view/<?= $finishedOrder->getId() ?>">
                            <i class="text-primary fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-6">
    <h6><i class="fa-solid fa-square-xmark text-danger"></i> Terminé et remboursé</h6>
    <div class="table-container table-container-striped">
        <table id="table3">
            <thead>
            <tr>
                <th>Utilisateur</th>
                <th>N° de commande</th>
                <th>Montant</th>
                <th>Status</th>
                <th>Date</th>
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
                        <a href="orders/view/<?= $errorOrder->getId() ?>">
                            <i class="text-primary fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
