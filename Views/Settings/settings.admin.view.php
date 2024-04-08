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
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Configuration</span></h3>
</div>
<section class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-2">
                            <div class="list-group" role="tablist">
                                <?php $i = 1; foreach ($virtualMethods as $method): ?>
                                    <a class="list-group-item list-group-item-action <?= $i === 1 ? 'active' : '' ?>" id="list-settings-list"
                                       data-bs-toggle="list" href="#method-<?= $method->varName() ?>"
                                       role="tab" aria-selected="<?= $i === 1 ? 'true' : 'false' ?>">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <?= $method->name() ?>
                                            </div>
                                        </div>
                                    </a>
                                    <?php ++$i; endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12 col-sm-12 col-md-10">
                            <div class="tab-content text-justify" id="nav-tabContent">
                                <?php $i = 1; foreach ($virtualMethods as $method): ?>
                                    <div class="tab-pane <?= $i === 1 ? 'active show' : '' ?>"
                                         id="method-<?= $method->varName() ?>" role="tabpanel"
                                         aria-labelledby="list-settings-list">
                                        <section>
                                            <div class="card-in-card">
                                                    <div class="card-body">
                                                        <div class="">
                                                            <h4><?= $method->name() ?></h4>
                                                        </div>
                                                        <?php $method->includeGlobalConfigWidgets(); ?>
                                                    </div>
                                            </div>
                                        </section>
                                    </div>
                                    <?php ++$i; endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Commandes</h4>
            </div>
            <form class="card-body row" action="settings/apply_orders" method="post">
                <?php (new SecurityManager())->insertHiddenToken() ?>
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

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Configuration générale</h4>
            </div>
            <div class="card-body row">
                <div class="col-12 col-lg-5">
                    <div class="card-in-card ">
                        <div class="card-body">
                            <h6>Image par défaut des articles :</h6>
                            <form id="apply_default_image" action="settings/apply_default_image" method="post" enctype="multipart/form-data">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <div class="text-center">
                                    <img width="50%" src="<?= $defaultImage ?>" alt="Default image">
                                </div>
                                <input class="mt-2 form-control form-control-sm" type="file"
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

                <div class="col-12 col-lg-7">
                    <div class="card-in-card">
                        <div class="card-body">
                            <form id="Configuration" action="settings/apply_global" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowReviews" name="allowReviews" <?= $currentReviews ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="allowReviews">Autoriser les avis clients</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Devise :</h6>
                                        <fieldset class="form-group">
                                            <select class="choices" name="currency" required>
                                                <?php foreach (ShopSettingsController::$availableCurrencies as $code => $details): ?>
                                                    <option value="<?= $code ?>" <?= $code === $currentCurrency ? 'selected' : '' ?>>
                                                        <?= "{$details['symbol']} - $code ({$details['name']})" ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Affichage du symbole :</h6>
                                        <fieldset class="form-group">
                                            <select class="form-select" name="showAfter" required>
                                                <option value="1" <?= $currentAfter == 1 ? 'selected' : '' ?>>Après le prix</option>
                                                <option value="0" <?= $currentAfter == 0 ? 'selected' : '' ?>>Avant le prix</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                            <div class="d-flex justify-content-center mt-4">
                                <button form="Configuration" type="submit" class="btn btn-primary">
                                    <?= LangManager::translate("core.btn.save") ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
