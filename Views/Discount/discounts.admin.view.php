<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $upcomingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-tag"></i> <span class="m-lg-auto">Promotions</span></h3>
    <div class="buttons">
        <button form="Configuration" type="submit"
                class="btn btn-primary">Nouvelle promotion
        </button>
    </div>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Promotions en cours</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">Lié à</th>
                        <th class="text-center">Début</th>
                        <th class="text-center">Fin</th>
                        <th class="text-center">Utilisation</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($ongoingDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getCode() ?></td>
                            <td><?= $discount->getLinkedFormatted() ?></td>
                            <td><?= $discount->getStartDateFormatted() ?></td>
                            <td><?= $discount->getDuration() ?></td>
                            <td><?= $discount->getCurrentUses() ?? "∞" ?>/<?= $discount->getMaxUses() ?? "∞" ?></td>
                            <td>
                                <a href="discounts/manage/<?= $discount->getId() ?>">
                                    <i class="text-success fa-solid fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Promotions à venir</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table2">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">Lié à</th>
                        <th class="text-center">Commence dans</th>
                        <th class="text-center">Utilisation</th>
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($upcomingDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getDescription() ?></td>
                            <td><?= $discount->getCode() ?></td>
                            <td><?= $discount->getLinkedFormatted() ?></td>
                            <td><?= $discount->getDuration() ?></td>
                            <td><?= $discount->getStatus() ?></td>
                            <td>
                                <a href="discounts/manage/<?= $discount->getId() ?>">
                                    <i class="text-success fa-solid fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Promotions passées</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table3">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">Lié à</th>
                        <th class="text-center">Nombre d'utilisation</th>
                        <th class="text-center">Gérer</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($pastDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getDescription() ?></td>
                            <td><?= $discount->getCode() ?></td>
                            <td><?= $discount->getLinkedFormatted() ?></td>
                            <td><?= $discount->getDuration() ?></td>
                            <td><?= $discount->getStatus() ?></td>
                            <td>
                                <a href="discounts/manage/<?= $discount->getId() ?>">
                                    <i class="text-success fa-solid fa-edit"></i>
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
