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
    <h3><i class="fa-solid fa-gift"></i> Carte cadeau</h3>
    <button data-bs-toggle="modal" data-modal-toggle="modal-generate" class="btn-primary">Générer une carte</button>
</div>

<div id="modal-generate" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Générer une carte cadeau</h6>
            <button type="button" data-modal-hide="modal-generate"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="giftCard/generate">
            <?php (new SecurityManager())->insertHiddenToken(); ?>
        <div class="modal-body">
            <label for="amount">Montant :</label>
            <input placeholder="18.99" type="text" name="amount" id="amount" class="input" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-primary">
                <span class="">Générer</span>
            </button>
        </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h4>Carte active</h4>
        </div>
        <div class="table-container">
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
    <div class="card">
        <div class="card-header">
            <h4>Carte passée ou utilisé</h4>
        </div>
        <div class="table-container">
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