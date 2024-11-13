<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var string $varName */

?>

<p>Gérez les méthodes de paiement que vous voulez autoriser directement dans stripe, tous les paiements actif et configuré correctement seront transmis automatiquement, votre client pourra ainsi choisir la méthode qu'il préfère.<br>
<a target="_blank" class="link" href="https://dashboard.stripe.com/settings/payment_methods?config_id=pmc_1NQztq2b9x8tnST4GWwYqyWt">Gérer mes moyens de paiement Stripe</a></p>

<form id="stripe" action="payments/settings" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
<div class="grid-4">
    <div class="col-span-3">
        <label for="<?= $varName ?>_secret_key">Clé Secrète :</label>
        <input value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_secret_key') ?>"
               placeholder="SECRET_KEY"
               type="text"
               name="<?= $varName ?>_secret_key"
               id="<?= $varName ?>_secret_key"
               class="input"
               required>
    </div>
    <div>
        <label for="<?= $varName ?>_fee">Frais :</label>
        <div class="input-group">
            <i><?= ShopSettingsModel::getInstance()->getSettingValue('symbol') ?></i>
            <input type="text" oninput="validateNumberInput(this)" required id="<?= $varName ?>_fee" name="<?= $varName ?>_fee" placeholder="5.00" value="<?= ShopPaymentMethodSettingsModel::getInstance()->getSetting($varName . '_fee') ?? 0 ?>">
        </div>
    </div>
</div>
    <button type="submit" class="btn-center btn-primary">Sauvegarder</button>
</form>