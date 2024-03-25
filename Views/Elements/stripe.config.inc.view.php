<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

?>

<form id="paypal" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row">
        <div class="form-group">
            <label for="stripe_secret_key">Clé Secrète :</label>
            <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting('stripe_secret_key') ?>"
                   placeholder="SECRET_KEY"
                   type="text"
                   name="stripe_secret_key"
                   id="stripe_secret_key"
                   class="form-control"
                   required>
        </div>
    </div>
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>
