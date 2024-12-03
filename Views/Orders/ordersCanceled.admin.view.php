<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $errorOrders */
/* @var bool $notificationIsRefused */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$title = 'Commandes';
$description = '';

?>
<h3><i class="fa-solid fa-square-xmark text-danger"></i> Terminé et remboursé</h3>

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
        <table id="table3" data-load-per-page="10">
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
