<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $discounts */

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
                <h4>Promotions</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">CODE</th>
                        <th class="text-center">Lié à</th>
                        <th class="text-center">Date de fin</th>
                        <th class="text-center">Date de début</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Gérer</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($discounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getDescription() ?></td>
                            <td><?= $discount->getCode() ?></td>
                            <td><?= $discount->getCategory() ?></td>
                            <td><?= $discount->getStartDate() ?></td>
                            <td><?= $discount->getEndDate() ?></td>
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
