<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.discount.credits.title');
$description = '';

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $ongoingDiscounts */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $pastDiscounts */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-money-bill-transfer"></i> <?= LangManager::translate('shop.views.discount.credits.title') ?></h3>
    <button data-bs-toggle="modal" data-modal-toggle="modal-generate" class="btn-primary"><?= LangManager::translate('shop.views.discount.credits.generate') ?></button>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div id="modal-generate" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.discount.credits.generate') ?></h6>
            <button type="button" data-modal-hide="modal-generate"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="credits/generate">
            <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
        <div class="modal-body">
            <label for="name"><?= LangManager::translate('shop.views.discount.credits.name') ?></label>
            <input placeholder="<?= LangManager::translate('shop.views.discount.credits.placeholderName') ?>" type="text" name="name" id="name" class="input" required>
            <label for="amount"><?= LangManager::translate('shop.views.discount.credits.amount') ?></label>
            <input placeholder="<?= LangManager::translate('shop.views.discount.credits.placeholderAmount') ?>" type="text" name="amount" id="amount" class="input" required>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn-primary">
                <span class=""><?= LangManager::translate('shop.views.discount.credits.doGenerate') ?></span>
            </button>
        </div>
        </form>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate('shop.views.discount.credits.activ') ?></h4>
        </div>
        <div class="table-container">
            <table class="table" id="table1" data-load-per-page="10">
                <thead>
                <tr>
                    <th><?= LangManager::translate('shop.views.discount.credits.codeName') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.credits.code') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.credits.codeAmount') ?></th>
                    <th class="text-center"><?= LangManager::translate('shop.views.discount.credits.manage') ?></th>
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
                                    <h6><?= LangManager::translate('shop.views.discount.credits.deleteTitle', ['name' => $discount->getName()]) ?></h6>
                                    <button type="button" data-modal-hide="modal-delete-<?= $discount->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate('shop.views.discount.credits.deleteText') ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="discounts/delete/<?= $discount->getId() ?>" type="button" class="btn-danger"><?= LangManager::translate('shop.views.discount.credits.delete') ?></a>
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
            <h4><?= LangManager::translate('shop.views.discount.credits.used') ?></h4>
        </div>
        <div class="table-container">
            <table class="table" id="table2" data-load-per-page="10">
                <thead>
                <tr>
                    <th class="text-center"><?= LangManager::translate('shop.views.discount.credits.codeName') ?></th>
                    <th class="text-center"><?= LangManager::translate('shop.views.discount.credits.code') ?></th>
                    <th><?= LangManager::translate('shop.views.discount.credits.codeAmount') ?></th>
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