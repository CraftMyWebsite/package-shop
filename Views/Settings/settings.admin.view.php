<?php

use CMW\Controller\Shop\Admin\Setting\ShopSettingsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = 'Configuration';
$description = '';

/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentCurrency */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentSymbol */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentReviews */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $currentAfter */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $maintenance */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $maintenanceMessage */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $autoValidateVirtual */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $showPublicStock */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $stockAlert */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Interface\Shop\IGlobalConfig[] $globalConfigMethod */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> Configuration</h3>
    <button form="applyConfig" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="applyConfig" method="post" enctype="multipart/form-data">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
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
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="showPublicStock" name="showPublicStock" <?= $showPublicStock ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label">Afficher les stocks publiquement</p>
            </label>
            <label for="stockAlert">Alerte stock :</label>
            <div class="input-group">
                <i class="fa-solid fa-percent"></i>
                <input type="number" id="stockAlert" name="stockAlert" value="<?= $stockAlert ?>">
            </div>
            <small>Recevez une alerte quand les stocks tombent en dessous de XX pourcents</small>

        </div>
    </div>
</div>
</form>

<hr>

<div class="card mt-2">
    <div class="flex justify-between">
        <h6>Configuration des méthodes</h6>
        <button id="submitVirtualGlobal" type="button" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
    </div>
    <div class="tab-vertical-container mt-6">
        <div class="tab-vertical" data-tabs-toggle="#tab-content-2">
            <?php foreach ($globalConfigMethod as $method): ?>
                <button class="tab-button" data-tabs-target="#tab-<?= $method->varName() ?>" role="tab"><?= $method->name() ?></button>
            <?php endforeach; ?>
        </div>
        <div id="tab-content-2" class="tab-container border dark:border-gray-600 rounded-lg">
            <form id="virtualGlobal" data-ajax="true">
                <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
                <?php $i=0; foreach ($globalConfigMethod as $method): ?>
                    <div class="tab-content" id="tab-<?= $method->varName() ?>">
                        <div class="card">
                            <div class="card-title">
                                <h6><?= $method->name() ?></h6>
                                <a class="w-fit btn-warning" href="settings/reset/<?= $method->varName() ?>">Réinitialiser : <?= $method->name() ?></a>
                            </div>
                            <input type="hidden" name="methodVarName-<?= $i ?>" value="<?= $method->varName() ?>">
                            <?php $method->includeGlobalConfigWidgets(); ?>
                        </div>
                        <div class="flex justify-end">
                        </div>
                    </div>
                <?php $i++; endforeach; ?>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('virtualGlobal');
        const submitButton = document.getElementById('submitVirtualGlobal');

        submitButton.addEventListener('click', async () => {
            const formData = new FormData(form);

            console.log('Données envoyées :', Array.from(formData.entries()));

            try {
                const response = await fetch('/cmw-admin/shop/settings/global', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error(`Une erreur est survenue lors de l'envoi du formulaire. Code: ${response.status}`);
                }

                const data = await response.json();
                console.log('Réponse JSON :', data);
                const csrfTokenField = form.querySelector('[name="security-csrf-token"]');
                const csrfTokenIdField = form.querySelector('[name="security-csrf-token-id"]');
                if (data.success) {


                    if (csrfTokenField && csrfTokenIdField) {
                        csrfTokenField.setAttribute('value', data.new_csrf_token);
                        csrfTokenIdField.setAttribute('value', data.new_csrf_token_id);

                        // Force une relecture pour confirmer la mise à jour
                        console.log('Nouveau CSRF Token:', csrfTokenField.value);
                        console.log('Nouveau CSRF Token ID:', csrfTokenIdField.value);
                    } else {
                        console.error("Champs CSRF introuvables");
                    }


                    iziToast.show({
                        titleSize: '14',
                        messageSize: '12',
                        icon: 'fa-solid fa-check',
                        title: "<?= LangManager::translate('core.toaster.success') ?>",
                        message: "<?= LangManager::translate('core.toaster.config.success') ?>",
                        color: "#20b23a",
                        iconColor: '#ffffff',
                        titleColor: '#ffffff',
                        messageColor: '#ffffff',
                        balloon: false,
                        close: true,
                        pauseOnHover: true,
                        position: 'topCenter',
                        timeout: 4000,
                        animateInside: false,
                        progressBar: true,
                        transitionIn: 'fadeInDown',
                        transitionOut: 'fadeOut',
                    });
                } else {
                    iziToast.show({
                        titleSize: '14',
                        messageSize: '12',
                        icon: 'fa-solid fa-xmark',
                        title: "<?= LangManager::translate('core.toaster.error') ?>",
                        message: data.error || "<?= LangManager::translate('core.toaster.config.error') ?>",
                        color: "#ab1b1b",
                        iconColor: '#ffffff',
                        titleColor: '#ffffff',
                        messageColor: '#ffffff',
                        balloon: false,
                        close: true,
                        pauseOnHover: true,
                        position: 'topCenter',
                        timeout: 4000,
                        animateInside: false,
                        progressBar: true,
                        transitionIn: 'fadeInDown',
                        transitionOut: 'fadeOut',
                    });
                }
            } catch (error) {
                console.error('Erreur AJAX :', error);
                iziToast.show({
                    titleSize: '14',
                    messageSize: '12',
                    icon: 'fa-solid fa-xmark',
                    title: "<?= LangManager::translate('core.toaster.error') ?>",
                    message: data.error || "<?= LangManager::translate('core.toaster.config.error') ?>",
                    color: "#ab1b1b",
                    iconColor: '#ffffff',
                    titleColor: '#ffffff',
                    messageColor: '#ffffff',
                    balloon: false,
                    close: true,
                    pauseOnHover: true,
                    position: 'topCenter',
                    timeout: 4000,
                    animateInside: false,
                    progressBar: true,
                    transitionIn: 'fadeInDown',
                    transitionOut: 'fadeOut',
                });
            }
        });
    });
</script>