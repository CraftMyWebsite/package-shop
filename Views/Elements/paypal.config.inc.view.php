<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\ShopPaymentMethodSettingsModel;

?>

<section>
    <div class="card">
        <div class="card-header">
            <h4>Configuration</h4>
        </div>
        <div class="card-body">
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
                <p>
                    TODO: Documentation CMW
                </p>
                <p>En attendant : <a href="https://developer.paypal.com/dashboard/" target="_blank">Panel de gestion Paypal</a></p>

                <div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</section>