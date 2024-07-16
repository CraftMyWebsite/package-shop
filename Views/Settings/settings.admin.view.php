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
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $maintenance */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $maintenanceMessage */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $autoValidateVirtual */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $stockAlert */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Interface\Shop\IVirtualItems[] $virtualMethods */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> Configuration</h3>
    <button form="applyConfig" type="submit" class="btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
</div>

<form id="applyConfig" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
<div class="grid-3">
    <div class="card">
        <div class="flex justify-between">
            <h6>Image par défaut</h6>
            <a type="button" class="btn-warning" href="settings/reset_default_image">Réinitialiser</a>
        </div>
        <img width="60%" class="mx-auto" src="<?= $defaultImage ?>" alt="Default image">
        <div class="drop-img-area" data-input-name="defaultPicture"></div>
    </div>

    <div>
        <div class="card">
            <h6>Monnaie</h6>
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
        </div>
        <div class="card mt-6">
            <label class="toggle">
                <h6 class="toggle-label">Maintenance</h6>
                <input type="checkbox" class="toggle-input" name="maintenance" <?= $maintenance ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
            </label>
            <label>Message d'alerte :</label>
            <input class="input" name="maintenanceMessage" id="maintenanceMessage" value="<?= $maintenanceMessage ?>" placeholder="Boutique en cours de maintenance">
            <div class="alert-info">
                <p>Ceci désactive l'accès à toutes les URL publiques du shop (y compris les paramètres, l'historique des commandes, le panier, etc.), sauf pour les rôles ayant la permission "Bypass maintenance".</p>
            </div>
        </div>
    </div>
    <div>
        <div class="card">
            <h6>Général</h6>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="allowReviews" name="allowReviews" <?= $currentReviews ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label">Avis clients</p>
            </label>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="autoValidateVirtual" name="autoValidateVirtual" <?= $autoValidateVirtual ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label" title="Si cette option est désactivé, les paniers qui ne contienne que des articles virtuel sont taité automatiquement comm terminé et validé, sinon, vous devrez les valider vous même avant que les action lié à l'article ne s'applique. ex (Une carte cadeau ne sera pas créer tant que vous n'avez pas validé vous même la commande)">Validation automatique des articles virtuel</p>
            </label>

            <label for="stockAlert">Alerte stock :</label>
            <div class="input-group">
                <i class="fa-solid fa-percent"></i>
                <input type="number" id="stockAlert" name="stockAlert" value="<?= $stockAlert ?>">
            </div>
            <small>Recevez une alerte quand les stocks tombent en dessous de XX pourcents</small>

            <p>Todo :</p>
            <label for="itemPerPage">Articles par page :</label>
            <input type="number" class="input" id="itemPerPage" name="itemPerPage" value="">
        </div>
    </div>
</div>
</form>

<div class="tab-vertical-container mt-6">
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
