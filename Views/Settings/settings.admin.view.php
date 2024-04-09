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
    <div class="buttons">
        <button form="applyConfig" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
    </div>
</div>

<form id="applyConfig" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <section class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Configuration générale</h4>
                </div>
                <div class="card-body row" style="z-index: 20">
                    <div class="col-12 col-lg-6">
                        <div class="card-in-card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <h6>Devise :</h6>
                                        <fieldset class="form-group">
                                            <select class="choices"  name="currency" required>
                                                <?php foreach (ShopSettingsController::$availableCurrencies as $code => $details): ?>
                                                    <option value="<?= $code ?>" <?= $code === $currentCurrency ? 'selected' : '' ?>>
                                                        <?= "{$details['symbol']} - $code ({$details['name']})" ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-12">
                                        <h6>Affichage du symbole :</h6>
                                        <fieldset class="form-group">
                                            <select class="form-select" name="showAfter" required>
                                                <option value="1" <?= $currentAfter == 1 ? 'selected' : '' ?>>Après le prix</option>
                                                <option value="0" <?= $currentAfter == 0 ? 'selected' : '' ?>>Avant le prix</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allowReviews" name="allowReviews" <?= $currentReviews ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="allowReviews"><h6>Autoriser les avis clients</h6></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card-in-card ">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mt-2">
                                    <h6>Image par défaut :</h6>
                                    <a class="btn btn-sm btn-warning" href="settings/reset_default_image">Réinitialiser</a>
                                </div>
                                <div class="text-center">
                                    <img width="60%" src="<?= $defaultImage ?>" alt="Default image">
                                </div>
                                <input class="form-control form-control-sm" type="file" accept=".png, .jpg, .jpeg, .webp, .gif" name="defaultPicture">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Commandes</h4>
                </div>
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
    </section>
</form>

<section>
    <div class="col-lg-12">
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-2" style="z-index: 10">
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
</section>