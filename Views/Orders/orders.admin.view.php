<?php

/* @var \CMW\Entity\Shop\Orders\ShopOrdersEntity [] $inProgressOrders */
/* @var \CMW\Entity\Shop\Orders\ShopOrdersEntity [] $errorOrders */
/* @var \CMW\Entity\Shop\Orders\ShopOrdersEntity [] $finishedOrders */
/* @var \CMW\Model\Shop\Order\ShopOrdersItemsModel $orderItemsModel */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$payementMethod =

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
                    <?php $payementMethod = ShopPaymentsController::getInstance()->getPaymentByName($inProgressOrder->getPaymentName()); ?>
                        <tr>
                            <td><?= $inProgressOrder->getUser()->getPseudo() ?></td>
                            <td>#<?= $inProgressOrder->getNumber() ?></td>
                            <td>
                                <?php $totalPrice = 0;foreach ($orderItemsModel->getOrdersItemsByOrderId($inProgressOrder->getOrderId()) as $orderItem):$totalPrice += $orderItem->getItem()->getPrice();endforeach; ?>
                                <?= "<b style='color: #6f6fad'>" . $totalPrice ." € </b>" ?>
                            </td>
                            <td><?= $inProgressOrder->getAdminStatus() ?></td>
                            <td>
                                <?php if ($payementMethod->dashboardURL()): ?>
                                <a target="_blank" href="<?= $payementMethod->dashboardURL() ?>"><?= $payementMethod->name() ?></a>
                                <?php else: ?>
                                <?= $payementMethod->name() ?>
                                <?php endif; ?>
                            </td>
                            <td><?= $inProgressOrder->getOrderCreated() ?></td>
                            <td>
                                <a href="orders/manage/<?= $inProgressOrder->getOrderId() ?>">
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
                        <th class="text-center">Date</th>
                        <th class="text-center">Consulter</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($finishedOrders as $finishedOrder) : ?>
                        <tr>
                            <td><?= $finishedOrder->getUser()->getPseudo() ?></td>
                            <td>#<?= $finishedOrder->getNumber() ?></td>
                            <td>
                                <?php $totalPrice = 0;foreach ($orderItemsModel->getOrdersItemsByOrderId($finishedOrder->getOrderId()) as $orderItem):$totalPrice += $orderItem->getOrderItemPrice();endforeach; ?>
                                <?= "<b style='color: #6f6fad'>" . $totalPrice ." € </b>" ?>
                            </td>
                            <td><?= $finishedOrder->getAdminStatus() ?></td>
                            <td><?= $finishedOrder->getOrderCreated() ?></td>
                            <td>
                                <a href="">
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
                        <th class="text-center">Date</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($errorOrders as $errorOrder) : ?>
                        <tr>
                            <td><?= $errorOrder->getUser()->getPseudo() ?></td>
                            <td>#<?= $errorOrder->getNumber() ?></td>
                            <td>
                                <?php $totalPrice = 0;foreach ($orderItemsModel->getOrdersItemsByOrderId($errorOrder->getOrderId()) as $orderItem):$totalPrice += $orderItem->getItem()->getPrice();endforeach; ?>
                                <?= "<b style='color: #6f6fad'>" . $totalPrice ." € </b>" ?>
                            </td>
                            <td><?= $errorOrder->getAdminStatus() ?></td>
                            <td><?= $errorOrder->getOrderCreated() ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>