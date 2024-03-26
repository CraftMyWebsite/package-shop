<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

?>

<form id="coinbase" action="payments/settings" method="post">
    <?php (new SecurityManager())->insertHiddenToken(); ?>
    <div class="row">
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
    <div class="text-center">
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </div>
</form>
