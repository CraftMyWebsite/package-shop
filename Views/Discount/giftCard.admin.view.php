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
    <h3><i class="fa-solid fa-gift"></i> <span class="m-lg-auto">Carte cadeau</span></h3>
    <div class="buttons">
    </div>
</div>
<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Carte active</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Acheté le</th>
                        <th class="text-center">Terminé dans</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($ongoingDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getStartDateFormatted() ?></td>
                            <td><?= $discount->getDuration() ?></td>
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
                <h4>Carte passée ou utilisé</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table2">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">Nombre d'utilisation</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($pastDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getCode() ?></td>
                            <td><?= $discount->getCurrentUses() ?? "∞" ?>/<?= $discount->getMaxUses() ?? "∞" ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
