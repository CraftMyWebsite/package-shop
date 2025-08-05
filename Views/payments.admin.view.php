<?php

use CMW\Type\Shop\Const\Payment\PaymentMethodConst;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.payments.title');
$description = '';

/* @var $methods \CMW\Interface\Shop\IPaymentMethodV2[] */

?>

<h3><i class="fa-solid fa-cash-register"></i> <?= LangManager::translate('shop.views.payments.title') ?></h3>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="tab-vertical-container">
    <div class="tab-vertical" data-tabs-toggle="#tab-payment-content">
        <?php foreach ($methods as $method): ?>
            <button class="tab-button" data-tabs-target="#tab-<?= $method->varName() ?>" role="tab">
                <div class="flex justify-between">
                    <span><?= $method->faIcon('fa-xl') ?> <?= $method->name() ?></span>
                    <?php if ($method->isActive()): ?>
                        <span class="ml-2 text-success"><i data-bs-toggle="tooltip" data-bs-placement="top" title="<?= LangManager::translate('shop.views.payments.actif') ?>" class="fa-solid fa-circle-check"></i></span>
                    <?php else: ?>
                        <span class="ml-2 text-warning"><i data-bs-toggle="tooltip" data-bs-placement="top" title="<?= LangManager::translate('shop.views.payments.inactif') ?>" class="fa-solid fa-circle-xmark"></i></span>
                    <?php endif; ?>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
    <div id="tab-payment-content" class="tab-container">
        <?php foreach ($methods as $method): ?>
        <div class="tab-content" id="tab-<?= $method->varName() ?>">
            <div class="card">
                <?php if ($method->varName() === PaymentMethodConst::FREE): ?>
                    <div class="card-body">
                        <p><?= LangManager::translate('shop.views.payments.warn') ?></p>
                    </div>
                <?php else: ?>
                    <div class="card-body">
                        <div>
                            <h4><?= $method->faIcon('fa-xl') ?> <?= LangManager::translate('shop.views.payments.config', ['name' => $method->name()]) ?></h4>
                        <div class="mt-3.5">
                            <?php if ($method->isActive()): ?>
                                <a href="payments/disable/<?= $method->varName() ?>" class="btn btn-danger btn-sm me-2"><?= LangManager::translate('shop.views.payments.disable', ['name' => $method->name()]) ?></a>
                            <?php else: ?>
                                <a href="payments/enable/<?= $method->varName() ?>" class="btn btn-success btn-sm me-2"><?= LangManager::translate('shop.views.payments.enable', ['name' => $method->name()]) ?></a>
                            <?php endif; ?>
                            <?php if ($method->dashboardURL()): ?>
                                <a href="<?= $method->dashboardURL() ?>" target="_blank" class="btn btn-primary btn-sm me-2"><?= LangManager::translate('shop.views.payments.panel', ['name' => $method->name()]) ?></a>
                            <?php endif; ?>
                            <?php if ($method->documentationURL()): ?>
                                <a href="<?= $method->documentationURL() ?>" target="_blank" class="btn btn-info btn-sm"><?= LangManager::translate('shop.views.payments.docs') ?></a>
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