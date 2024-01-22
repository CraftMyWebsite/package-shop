<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Shop\ShopOrdersEntity [] $inProgressOrders */
/* @var \CMW\Entity\Shop\ShopOrdersEntity [] $errorOrders */
/* @var \CMW\Entity\Shop\ShopOrdersEntity [] $finishedOrders */
/* @var \CMW\Model\Shop\ShopOrdersItemsModel $orderItemsModel */

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
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Utilisateur</th>
                        <th class="text-center">N° de commande</th>
                        <th class="text-center">Montant</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Gérer</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($inProgressOrders as $inProgressOrder) : ?>
                        <tr>
                            <td><?= $inProgressOrder->getUser()->getPseudo() ?></td>
                            <td>#<?= $inProgressOrder->getNumber() ?></td>
                            <td>
                                <?php $totalPrice = 0;foreach ($orderItemsModel->getOrdersItemsByOrderId($inProgressOrder->getOrderId()) as $orderItem):$totalPrice += $orderItem->getItem()->getPrice();endforeach; ?>
                                <?= "<b style='color: #6f6fad'>" . $totalPrice ." € </b>" ?>
                            </td>
                            <td><?= $inProgressOrder->getOrderStatus() ?></td>
                            <td><?= $inProgressOrder->getOrderCreated() ?></td>
                            <td>
                                <a href="">
                                    <i class="text-primary fa-solid fa-gears"></i>
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
                <table class="table" id="table1">
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
                                <?php $totalPrice = 0;foreach ($orderItemsModel->getOrdersItemsByOrderId($finishedOrder->getOrderId()) as $orderItem):$totalPrice += $orderItem->getItem()->getPrice();endforeach; ?>
                                <?= "<b style='color: #6f6fad'>" . $totalPrice ." € </b>" ?>
                            </td>
                            <td><?= $finishedOrder->getOrderStatus() ?></td>
                            <td><?= $finishedOrder->getOrderCreated() ?></td>
                            <td>
                                <a href="">
                                    <i class="text-primary fa-solid fa-gears"></i>
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
                <h4>Terminé avec problème</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
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
                            <td><?= $errorOrder->getOrderStatus() ?></td>
                            <td><?= $errorOrder->getOrderCreated() ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>