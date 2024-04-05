<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $inProgressOrders */
/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $errorOrders */
/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersEntity [] $finishedOrders */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$title = "Commandes";
$description = "";

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-list-check"></i> <span class="m-lg-auto">Commandes</span></h3>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>En cours</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <p><i class="fa-solid fa-triangle-exclamation"></i> Vous n'avez pas encoré terminé la configuration des alertes pour les nouvelles commandes, rendez-vous dans <a href="settings">configuration</a> pour ne louper aucune commande !</p>
                </div>

                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Utilisateur</th>
                        <th class="text-center">N° de commande</th>
                        <th class="text-center">Montant</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Paiement</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Gérer</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($inProgressOrders as $inProgressOrder) : ?>
                        <tr>
                            <td><?= $inProgressOrder->getUserAddressMethod()->getUserFirstName() . " " . $inProgressOrder->getUserAddressMethod()->getUserLastName() ?></td>
                            <td>#<?= $inProgressOrder->getOrderNumber() ?></td>
                            <td>
                                <?= "<b style='color: #6f6fad'>" . $inProgressOrder->getOrderTotalFormatted() ."</b>" ?><br>
                            </td>
                            <td><?= $inProgressOrder->getAdminStatus() ?></td>
                            <?php $payment = ShopPaymentsController::getInstance()->getPaymentByName($inProgressOrder->getPaymentMethod()->getName()) ?>
                            <td><?php if ($payment->dashboardURL()) : ?><a target="_blank" href="<?=$payment->dashboardURL()?>"><?=$inProgressOrder->getPaymentMethod()->getName()?></a><?php else: ?> <?=$inProgressOrder->getPaymentMethod()->getName()?> <?php endif; ?>
                                <?="(". $inProgressOrder->getPaymentMethod()->getFeeFormatted().")" ?></td>
                            <td><?= $inProgressOrder->getCreated() ?></td>
                            <td>
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
    </div>
</section>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Terminé</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table2">
                    <thead>
                    <tr>
                        <th class="text-center">Utilisateur</th>
                        <th class="text-center">N° de commande</th>
                        <th class="text-center">Montant</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Consulter</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($finishedOrders as $finishedOrder) : ?>
                        <tr>
                            <td><?= $finishedOrder->getUserAddressMethod()->getUserFirstName() . " " . $finishedOrder->getUserAddressMethod()->getUserLastName() ?></td>
                            <td>#<?= $finishedOrder->getOrderNumber() ?></td>
                            <td>
                                <?= "<b style='color: #73ad6f'> +" . $finishedOrder->getOrderTotalFormatted() ."</b>" ?><br>
                            </td>
                            <td><?= $finishedOrder->getAdminStatus() ?></td>
                            <td>
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
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Terminé et remboursé</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table3">
                    <thead>
                    <tr>
                        <th class="text-center">Utilisateur</th>
                        <th class="text-center">N° de commande</th>
                        <th class="text-center">Montant</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Consulter</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($errorOrders as $errorOrder) : ?>
                        <tr>
                            <td><?= $errorOrder->getUserAddressMethod()->getUserFirstName() . " " . $errorOrder->getUserAddressMethod()->getUserLastName() ?></td>
                            <td>#<?= $errorOrder->getOrderNumber() ?></td>
                            <td>
                                <?= "<b style='color: #ad6f78'> -" . $errorOrder->getOrderTotalFormatted() ."</b>" ?><br>
                            </td>
                            <td><?= $errorOrder->getAdminStatus() ?></td>
                            <td>
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
    </div>
</section>