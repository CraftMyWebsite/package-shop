<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $inProgressOrders */
/* @var bool $notificationIsRefused */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = 'Commandes';
$description = '';

?>
<h3><i class="fa-solid fa-spinner fa-spin text-info"></i> En cours</h3>

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
            <p><i class="fa-solid fa-triangle-exclamation"></i> Attention, vous avez désactivé les notifications du
                package Shop ! Vous ne serez pas alerté en cas de nouvelle commande. <br>Il est également recommandé
                d'activer les alertes mail / Discord dans les paramètres de notification. <a class="link"
                                                                                             href="/cmw-admin/notifications">Modifier
                    les réglages des notifications</a></p>
        </div>
    <?php endif; ?>
    <div class="table-container table-container-striped">
        <table class="table" id="table1" data-load-per-page="10">
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