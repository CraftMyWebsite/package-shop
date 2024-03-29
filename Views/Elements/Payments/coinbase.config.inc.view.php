<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

?>

<form id="coinbase" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row">
        <div class="col-12 col-md-2">
            <div class="form-group">
                <label for="coinbase_fee">Frais :</label>
                <div class="input-group">
                    <input oninput="validateNumberInput(this)" required id="coinbase_fee" name="coinbase_fee" type="text" class="form-control" placeholder="5.00" aria-describedby="basic-addon2" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting('coinbase_fee') ?? 0 ?>">
                    <span class="input-group-text" id="basic-addon2">€</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-10">
            <div class="form-group">
                <label for="coinbase_api_key">Clé Secrète :</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting('coinbase_api_key') ?>"
                       placeholder="SECRET_KEY"
                       type="text"
                       name="coinbase_api_key"
                       id="coinbase_api_key"
                       class="form-control"
                       required>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>
