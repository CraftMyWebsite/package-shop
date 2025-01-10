<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var string $varName */

?>

<form id="paypal" action="payments/settings" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
<div class="grid-5">
    <div class="col-span-4">
        <div class="grid-2">
            <div>
                <label for="<?= $varName ?>_client_id"><?= LangManager::translate('shop.views.elements.payments.paypal.client') ?></label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_client_id') ?>"
                       placeholder="CLIENT_ID"
                       type="text"
                       name="<?= $varName ?>_client_id"
                       id="<?= $varName ?>_client_id"
                       class="input"
                       required>
            </div>
            <div>
                <label for="<?= $varName ?>_client_secret"><?= LangManager::translate('shop.views.elements.payments.paypal.key') ?></label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_client_secret') ?>"
                       placeholder="CLIENT_SECRET"
                       type="text"
                       name="<?= $varName ?>_client_secret"
                       id="<?= $varName ?>_client_secret"
                       class="input"
                       required>
            </div>
        </div>
    </div>
    <div>
        <div>
            <label for="<?= $varName ?>_fee"><?= LangManager::translate('shop.views.elements.payments.fee') ?></label>
            <div class="input-group">
                <i><?= ShopSettingsModel::getInstance()->getSettingValue('symbol') ?></i>
                <input type="text" oninput="validateNumberInput(this)" required id="<?= $varName ?>_fee" name="<?= $varName ?>_fee" placeholder="5.00" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_fee') ?? 0 ?>">
            </div>
        </div>
    </div>
</div>
    <button type="submit" class="btn-center btn-primary"><?= LangManager::translate('shop.views.elements.payments.save') ?></button>
</form>