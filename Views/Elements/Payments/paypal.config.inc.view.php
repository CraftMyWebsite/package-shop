<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var string $varName */

?>

<form id="paypal" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row mt-4">
        <div class="col-12 col-md-2">
            <div class="form-group">
                <label for="<?=$varName?>_fee">Frais :</label>
                <div class="input-group">
                    <input oninput="validateNumberInput(this)" required id="<?=$varName?>_fee" name="<?=$varName?>_fee" type="text" class="form-control" placeholder="5.00" aria-describedby="basic-addon2" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName.'_fee') ?? 0 ?>">
                    <span class="input-group-text" id="basic-addon2"><?= ShopSettingsModel::getInstance()->getSettingValue("symbol") ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="form-group">
                <label for="<?=$varName?>_client_id">ClientID :</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName.'_client_id') ?>"
                       placeholder="CLIENT_ID"
                       type="text"
                       name="<?=$varName?>_client_id"
                       id="<?=$varName?>_client_id"
                       class="form-control"
                       required>
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="form-group">
                <label for="<?=$varName?>_client_secret">Client Secret :</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName.'_client_secret') ?>"
                       placeholder="CLIENT_SECRET"
                       type="text"
                       name="<?=$varName?>_client_secret"
                       id="<?=$varName?>_client_secret"
                       class="form-control"
                       required>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>


<script>
    function validateNumberInput(input) {
        let value = input.value.replace(',', '.');
        value = value.replace(/[^0-9.]/g, '');
        let parts = value.split('.');
        if (parts.length > 2) {
            value = parts.shift() + '.' + parts.join('');
        }
        if (parts.length > 1 && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        input.value = value;
    }
</script>