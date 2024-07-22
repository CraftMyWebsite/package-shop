<?php

/* @var \CMW\Entity\Shop\HistoryOrders\ShopHistoryOrdersAfterSalesEntity [] $afterSales */

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;

$title = "Services après ventes";
$description = "SAV";

?>
<h3><i class="fa-solid fa-headset"></i> Services après-ventes</h3>

    <div class="card">
        <h6>En cours</h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>N° de commande</th>
                    <th>Raison</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale) : ?>
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
        <h6>Traité</h6>
        <div class="table-container table-container-striped">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>N° de commande</th>
                    <th>Raison</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody >
                <?php foreach ($afterSales as $afterSale) : ?>
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