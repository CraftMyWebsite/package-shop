<?php

use CMW\Controller\Shop\Admin\Setting\ShopSettingsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentCurrency */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentSymbol */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentReviews */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentAfter */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Interface\Shop\IVirtualItems[] $virtualMethods */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> Configuration</h3>
    <button form="applyConfig" type="submit" class="btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
</div>

<form id="applyConfig" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
<div class="grid-2">
    <div>
        <div class="card">
            <label for="currency">Devise :</label>
            <select class="choices" id="currency" name="currency" required>
                <?php foreach (ShopSettingsController::$availableCurrencies as $code => $details): ?>
                    <option value="<?= $code ?>" <?= $code === $currentCurrency ? 'selected' : '' ?>>
                        <?= "{$details['symbol']} - $code ({$details['name']})" ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="showAfter">Affichage du symbole :</label>
            <select class="form-select" id="showAfter" name="showAfter" required>
                <option value="1" <?= $currentAfter == 1 ? 'selected' : '' ?>>Après le prix</option>
                <option value="0" <?= $currentAfter == 0 ? 'selected' : '' ?>>Avant le prix</option>
            </select>
            <label class="toggle">
                <p class="toggle-label">Autoriser les avis clients</p>
                <input type="checkbox" class="toggle-input" id="allowReviews" name="allowReviews" <?= $currentReviews ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
            </label>
        </div>


        <div class="card mt-6">
            <h6>Commandes</h6>
            ---------------TODO---------------
            <div class="card-body row">
                <div class="col-12 col-lg-6">
                    <div class="card-in-card">
                        <div class="card-body">
                            <div class="col-12 mt-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sendMail" name="" <?= $currentReviews ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sendMail"><h6>Email alerte</h6></label>
                                </div>
                                <div class="form-group">
                                    <label for="adminNotif">Mail administrateur(s) :</label>
                                    <input class="form-control" type="url" id="adminNotif" placeholder="my@mail.com,second@mail.com" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card-in-card">
                        <div class="card-body">
                            <h6>Discord Webhook alerte :</h6>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="support_settings_use_webhook_new_support" name="support_settings_use_webhook_new_support" >
                                <label class="form-check-label" for="support_settings_use_webhook_new_support">Nouvelle commande :</label>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="url" name="support_settings_webhook_new_support" placeholder="https://discord.com/api/webhooks/" value="">
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="support_settings_use_webhook_new_response" name="support_settings_use_webhook_new_response" >
                                <label class="form-check-label" for="support_settings_use_webhook_new_response">Changement d'état :</label>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="url" name="support_settings_webhook_new_response" placeholder="https://discord.com/api/webhooks/" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex justify-between">
            <h6>Image par défaut :</h6>
            <a type="button" class="btn-warning-sm" href="settings/reset_default_image">Réinitialiser</a>
        </div>
        <img width="60%" class="mx-auto" src="<?= $defaultImage ?>" alt="Default image">
        <div class="drop-img-area" data-input-name="defaultPicture"></div>
    </div>
</div>
</form>

<div class="tab-vertical-container">
    <div class="tab-vertical" data-tabs-toggle="#tab-content-2">
        <?php foreach ($virtualMethods as $method): ?>
            <button class="tab-button" data-tabs-target="#tab<?= $method->varName() ?>" role="tab"><?= $method->name() ?></button>
        <?php endforeach; ?>
    </div>
    <div id="tab-content-2" class="tab-container">
        <?php foreach ($virtualMethods as $method): ?>
        <div class="tab-content" id="tab<?= $method->varName() ?>">
            <div class="card">
                <h6><?= $method->name() ?></h6>
                <form id="virtualGlobal" action="settings/virtual" method="post">
                    <?php (new SecurityManager())->insertHiddenToken(); ?>
                    <?php $method->includeGlobalConfigWidgets(); ?>
                    <div class="d-flex justify-content-center mt-4">
                        <button form="virtualGlobal" type="submit"
                                class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
