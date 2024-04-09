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
        <a data-bs-toggle="modal" data-bs-target="#generate" class="btn btn-primary">Générer une carte</a>
    </div>
</div>

<div class="modal fade text-left" id="generate" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
         role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="myModalLabel160">Générer une carte cadeau</h5>
            </div>
            <form method="post" action="giftCard/generate">
                <?php (new SecurityManager())->insertHiddenToken(); ?>
                <div class="modal-body text-left">
                    <label for="amount">Montant :</label>
                    <input placeholder="18.99" type="text" name="amount" id="amount" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary"
                            data-bs-dismiss="modal">
                        <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <span class="">Générer</span>
                    </button>
                </div>
            </form>
        </div>
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
                        <th class="text-center">CODE</th>
                        <th class="text-center">Terminé dans</th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($ongoingDiscounts as $discount):?>
                        <tr>
                            <td><?= $discount->getName() ?></td>
                            <td><?= $discount->getCode() ?></td>
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


<script>
    let inputElement = document.querySelector('input[name="amount"]');

    inputElement.addEventListener('input', function() {
        let inputValue = this.value;
        inputValue = inputValue.replace(/,/g, '.');
        inputValue = inputValue.replace(/[^\d.]/g, '');
        if (/\.\d{3,}/.test(inputValue)) {
            let decimalIndex = inputValue.indexOf('.');
            inputValue = inputValue.substring(0, decimalIndex + 3);
        }
        this.value = inputValue;
    });
</script>