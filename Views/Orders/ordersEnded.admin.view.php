<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $finishedOrders */
/* @var bool $notificationIsRefused */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$title = 'Commandes';
$description = '';

?>
<h3><i class="fa-solid fa-circle-check text-success"></i> Terminé</h3>

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
        <table id="table2" data-load-per-page="10">
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