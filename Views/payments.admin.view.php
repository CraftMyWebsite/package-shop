<?php

use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\MailModel;

$title = 'Paiements';
$description = 'Gérez les méthodes de paiements';

/* @var $methods \CMW\Interface\Shop\IPaymentMethod[] */

?>

<h3><i class="fa-solid fa-cash-register"></i> Moyens de paiements</h3>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

<div class="tab-vertical-container">
    <div class="tab-vertical" data-tabs-toggle="#tab-payment-content">
        <?php foreach ($methods as $method): ?>
            <button class="tab-button" data-tabs-target="#tab-<?= $method->varName() ?>" role="tab">
                <div class="flex justify-between">
                    <span><?= $method->faIcon('fa-xl') ?> <?= $method->name() ?></span>
                    <?php if ($method->isActive()): ?>
                        <span class="ml-2 text-success"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Paiement atif." class="fa-solid fa-circle-check"></i></span>
                    <?php else: ?>
                        <span class="ml-2 text-warning"><i data-bs-toggle="tooltip" data-bs-placement="top" title="Paiement incatif." class="fa-solid fa-circle-xmark"></i></span>
                    <?php endif; ?>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
    <div id="tab-payment-content" class="tab-container">
        <?php foreach ($methods as $method): ?>
        <div class="tab-content" id="tab-<?= $method->varName() ?>">
            <div class="card">
                <?php if ($method->varName() == 'free'): ?>
                    <div class="card-body">
                        <p>Vous ne pouvez pas modifier cette méthode de paiement, car elle est obligatoire pour la vente d'articles gratuits. <br>
                            Ne vous inquiétez pas, cette méthode de paiement est entièrement automatique et ne sera disponible que si la totalité du contenu du panier est à 0.</p>
                    </div>
                <?php else: ?>
                    <div class="card-body">
                        <div>
                            <h4><?= $method->faIcon('fa-xl') ?> Configuration des paiements avec <?= $method->name() ?></h4>
                        <div class="mt-3.5">
                            <?php if ($method->isActive()): ?>
                                <a href="payments/disable/<?= $method->varName() ?>" class="btn btn-danger btn-sm me-2">Désactiver <?= $method->name() ?></a>
                            <?php else: ?>
                                <a href="payments/enable/<?= $method->varName() ?>" class="btn btn-success btn-sm me-2">Activer <?= $method->name() ?></a>
                            <?php endif; ?>
                            <?php if ($method->dashboardURL()): ?>
                                <a href="<?= $method->dashboardURL() ?>" target="_blank" class="btn btn-primary btn-sm me-2">Panel <?= $method->name() ?></a>
                            <?php endif; ?>
                            <?php if ($method->documentationURL()): ?>
                                <a href="<?= $method->documentationURL() ?>" target="_blank" class="btn btn-info btn-sm">Documentations</a>
                            <?php endif; ?>
                        </div>
                        </div>
                        <div class="mt-3.5">
                            <?php $method->includeConfigWidgets() ?>
                        </div>

                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function validateNumberInput(input) {
        let value = input.value.replace(',', '.');
        value = value.replace(/[^0-9.]/g, '');
        let parts = value.split('.');
        if (parts.length > 2) {
            value = parts.shift() + '.' + parts.join('');
        }
        if (parts.length > 1 && parts[1].length > 2) {
            value = parts[0] + '.' + parts[1].substring(0, 2);
        }
        input.value = value;
    }
</script>