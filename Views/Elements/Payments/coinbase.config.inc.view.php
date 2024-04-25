<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var string $varName */

?>

<form id="coinbase" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row">
        <div class="col-12 col-md-2">
            <div class="form-group">
                <label for="<?=$varName?>_fee">Frais :</label>
                <div class="input-group">
                    <input oninput="validateNumberInput(this)" required id="<?=$varName?>_fee" name="<?=$varName?>_fee" type="text" class="form-control" placeholder="5.00" aria-describedby="basic-addon2" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName.'_fee') ?? 0 ?>">
                    <span class="input-group-text" id="basic-addon2"><?= ShopSettingsModel::getInstance()->getSettingValue("symbol") ?></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="form-group">
                <label for="<?=$varName?>_api_key">Clé Secrète :</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName.'_api_key') ?>"
                       placeholder="SECRET_KEY"
                       type="text"
                       name="<?=$varName?>_api_key"
                       id="<?=$varName?>_api_key"
                       class="form-control"
                       required>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>
