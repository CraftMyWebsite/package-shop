<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var string $varName */

?>

<form id="coinbase" action="payments/settings" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
    <div class="grid-4">
        <div class="col-span-3">
            <label for="<?= $varName ?>_api_key"><?= LangManager::translate('shop.views.elements.payments.coinbase.key') ?></label>
            <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_api_key') ?>"
                   placeholder="SECRET_KEY"
                   type="text"
                   name="<?= $varName ?>_api_key"
                   id="<?= $varName ?>_api_key"
                   class="input"
                   required>
        </div>
        <div>
            <label for="<?= $varName ?>_fee"><?= LangManager::translate('shop.views.elements.payments.fee') ?></label>
            <div class="input-group">
                <i><?= ShopSettingsModel::getInstance()->getSettingValue('symbol') ?></i>
                <input type="text" oninput="validateNumberInput(this)" required id="<?= $varName ?>_fee" name="<?= $varName ?>_fee" placeholder="5.00" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_fee') ?? 0 ?>">
            </div>
        </div>
    </div>
    <button type="submit" class="btn-center btn-primary"><?= LangManager::translate('shop.views.elements.payments.save') ?></button>
</form>
