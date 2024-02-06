<?php

use CMW\Controller\Shop\ShopSettingsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentCurrency */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Configuration</span></h3>
    <div class="buttons">
        <button form="Configuration" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?>
        </button>
    </div>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Commandes</h4>
            </div>
            <form class="card-body row" id="Configuration" action="settings/apply_orders" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="col-12 col-lg-3">
                        <div class="card-in-card">
                            <div class="card-body">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="order_settings_use_mail" name="order_settings_use_mail" >
                                    <label class="form-check-label" for="order_settings_use_mail">Articles virtuel validation manuelle</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <div class="card-in-card">
                            <div class="card-body">
                                <h6>Discord alert</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="support_settings_use_webhook_new_support" name="support_settings_use_webhook_new_support" >
                                    <label class="form-check-label" for="support_settings_use_webhook_new_support"><h6>Discord Webhook - Nouvelle commande :</h6></label>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="url" name="support_settings_webhook_new_support" placeholder="https://discord.com/api/webhooks/" value="">
                                </div>

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="support_settings_use_webhook_new_response" name="support_settings_use_webhook_new_response" >
                                    <label class="form-check-label" for="support_settings_use_webhook_new_response"><h6>Discord Webhook - Changement d'état :</h6></label>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="url" name="support_settings_webhook_new_response" placeholder="https://discord.com/api/webhooks/" value="">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <div class="card-in-card">
                            <div class="card-body">
                                - Email (nouvelle commande / changement état commande)
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <?= LangManager::translate("core.btn.save") ?>
                        </button>
                    </div>
            </form>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("core.config.title") ?></h4>
            </div>
            <div class="card-body">
                <form id="Configuration" action="settings/apply_currency" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Devise :</h6>
                            <fieldset class="form-group">
                                <select class="choices" name="code" required>
                                    <?php foreach (ShopSettingsController::$availableCurrencies as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= $code === $currentCurrency ? 'selected' : '' ?>>
                                            <?= "$code ($name)" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Image d'article par default</h4>
            </div>
            <div class="card-body">
                <form id="apply_default_image" action="settings/apply_default_image" method="post" enctype="multipart/form-data">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="text-center">
                        <img src="<?= $defaultImage ?>" alt="Default image">
                    </div>
                    <input class="mt-2 form-control form-control-lg" type="file"
                           accept=".png, .jpg, .jpeg, .webp, .gif"
                           name="defaultPicture">
                </form>
                <div class="d-flex justify-content-between mt-4">
                    <form action="settings/reset_default_image" method="post">
                        <?php (new SecurityManager())->insertHiddenToken() ?>
                        <button type="submit" class="btn btn-warning">
                            Reset
                        </button>
                    </form>
                    <button form="apply_default_image" type="submit" class="btn btn-primary">
                        <?= LangManager::translate("core.btn.save") ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
