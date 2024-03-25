<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

?>

<form id="paypal" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="paypal_client_id">ClientID:</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_id') ?>"
                       placeholder="CLIENT_ID"
                       type="text"
                       name="paypal_client_id"
                       id="paypal_client_id"
                       class="form-control"
                       required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="paypal_client_secret">Client Secret :</label>
                <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_secret') ?>"
                       placeholder="CLIENT_SECRET"
                       type="text"
                       name="paypal_client_secret"
                       id="paypal_client_secret"
                       class="form-control"
                       required>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>
