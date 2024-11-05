<?php
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = '';
$description = '';

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-money-bill-transfer"></i> Avoirs / Credits</h3>
    <button data-bs-toggle="modal" data-modal-toggle="modal-generate" class="btn-primary">Générer un avoir</button>
</div>

<div id="modal-generate" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Générer un avoir</h6>
            <button type="button" data-modal-hide="modal-generate"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="credits/generate">
            <?php (new SecurityManager())->insertHiddenToken(); ?>
        <div class="modal-body">
            <label for="name">Nom :</label>
            <input placeholder="Avoir pour X" type="text" name="name" id="name" class="input" required>
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
            <h4>Avoir actif</h4>
        </div>
        <div class="table-container">
            <table class="table" id="table1">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>CODE</th>
                    <th>Montant</th>
                    <th class="text-center">Gérer</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ongoingDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= $discount->getCode() ?></td>
                        <td><b><?= $discount->getPriceFormatted() ?></b></td>
                        <td class="text-center">
                            <button data-modal-toggle="modal-delete-<?= $discount->getId() ?>" title="Supprimé">
                                <i class="text-danger fa-solid fa-trash"></i>
                            </button>
                        </td>
                        <div id="modal-delete-<?= $discount->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6>Suppression de <?= $discount->getName() ?></h6>
                                    <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    Cette suppression est definitive.
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger">Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4>Avoir utilisé</h4>
        </div>
        <div class="table-container">
            <table class="table" id="table2">
                <thead>
                <tr>
                    <th class="text-center">Nom</th>
                    <th class="text-center">CODE</th>
                    <th>Montant</th>
                </tr>
                </thead>
                <tbody class="text-center">
                <?php foreach ($pastDiscounts as $discount): ?>
                    <tr>
                        <td><?= $discount->getName() ?></td>
                        <td><?= $discount->getCode() ?></td>
                        <td><b><?= $discount->getPriceFormatted() ?></b></td>
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