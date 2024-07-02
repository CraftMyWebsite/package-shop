<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $upcomingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-tag"></i> Promotions</h3>
    <button form="Configuration" type="submit"
            class="btn-primary">Nouvelle promotion
    </button>
</div>

<div class="card">
        <h6>Promotions en cours</h6>
    <div class="table-container">
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

<div class="grid-2 mt-6">
    <div class="card">
            <h6>Promotions à venir</h6>
        <div class="table-container">
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
    <div class="card">
        <h6>Promotions passées</h6>
        <div class="table-container">
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
