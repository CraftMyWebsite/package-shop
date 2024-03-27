<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;

?>

<p>Gérez les méthodes de paiement que vous voulez autoriser directement dans stripe, tous les paiements actif et configuré correctement seront transmis automatiquement, votre client pourra ainsi choisir la méthode qu'il préfère.<br>
<a target="_blank" href="https://dashboard.stripe.com/settings/payment_methods?config_id=pmc_1NQztq2b9x8tnST4GWwYqyWt">Gérer mes moyens de paiement Stripe</a></p>
<form id="stripe" action="payments/settings" method="post">
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
