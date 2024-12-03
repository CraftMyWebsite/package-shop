<?php

use CMW\Controller\Shop\Admin\Setting\ShopSettingsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = 'Configuration';
$description = '';

/* @var CMW\Interface\Shop\IGlobalConfig[] $globalConfigMethod */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> Configuration des méthodes</h3>
    <button id="submitVirtualGlobal" type="button" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

    <div class="tab-vertical-container mt-6">
        <div class="tab-vertical" data-tabs-toggle="#tab-content-2">
            <?php foreach ($globalConfigMethod as $method): ?>
                <button class="tab-button" data-tabs-target="#tab-<?= $method->varName() ?>" role="tab"><?= $method->name() ?></button>
            <?php endforeach; ?>
        </div>
        <div id="tab-content-2" class="tab-container">
            <form id="virtualGlobal" data-ajax="true">
                <?php SecurityManager::getInstance()->insertHiddenToken(); ?>
                <?php $i=0; foreach ($globalConfigMethod as $method): ?>
                    <div class="tab-content" id="tab-<?= $method->varName() ?>">
                        <div class="card">
                            <div class="card-title">
                                <h6><?= $method->name() ?></h6>
                                <a class="w-fit btn-warning" href="methods/reset/<?= $method->varName() ?>">Réinitialiser : <?= $method->name() ?></a>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('virtualGlobal');
        const submitButton = document.getElementById('submitVirtualGlobal');

        submitButton.addEventListener('click', async () => {
            const formData = new FormData(form);

            console.log('Données envoyées :', Array.from(formData.entries()));

            try {
                const response = await fetch('/cmw-admin/shop/settings/methods', {
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