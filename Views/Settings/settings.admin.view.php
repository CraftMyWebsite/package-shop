<?php

use CMW\Controller\Shop\Admin\Setting\ShopSettingsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.settings.settings.title');
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
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $perPage */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> <?= LangManager::translate('shop.views.settings.settings.title') ?></h3>
    <button form="applyConfig" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<form id="applyConfig" method="post" enctype="multipart/form-data">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
<div class="grid-3">
    <div class="card">
        <div class="flex justify-between">
            <h6><?= LangManager::translate('shop.views.settings.settings.default_image') ?></h6>
            <a type="button" class="btn-warning" href="global/reset_default_image"><?= LangManager::translate('shop.views.settings.settings.reset_image') ?></a>
        </div>
        <img width="60%" class="mx-auto" src="<?= $defaultImage ?>" alt="Default image">
        <div class="drop-img-area" data-input-name="defaultPicture"></div>
    </div>

    <div>
        <div class="card">
            <h6><?= LangManager::translate('shop.views.settings.settings.money') ?></h6>
            <label for="currency"><?= LangManager::translate('shop.views.settings.settings.currency') ?></label>
            <select class="choices" id="currency" name="currency" required>
                <?php foreach (ShopSettingsController::$availableCurrencies as $code => $details): ?>
                    <option value="<?= $code ?>" <?= $code === $currentCurrency ? 'selected' : '' ?>>
                        <?= "{$details['symbol']} - $code ({$details['name']})" ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <label for="showAfter"><?= LangManager::translate('shop.views.settings.settings.show_symbol') ?></label>
            <select class="form-select" id="showAfter" name="showAfter" required>
                <option value="1" <?= $currentAfter == 1 ? 'selected' : '' ?>><?= LangManager::translate('shop.views.settings.settings.after') ?></option>
                <option value="0" <?= $currentAfter == 0 ? 'selected' : '' ?>><?= LangManager::translate('shop.views.settings.settings.before') ?></option>
            </select>
        </div>
        <div class="card mt-6">
            <label class="toggle">
                <h6 class="toggle-label"><?= LangManager::translate('shop.views.settings.settings.maintenance') ?></h6>
                <input type="checkbox" class="toggle-input" name="maintenance" <?= $maintenance ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
            </label>
            <label><?= LangManager::translate('shop.views.settings.settings.maintenance_alert') ?></label>
            <input class="input" name="maintenanceMessage" id="maintenanceMessage" value="<?= $maintenanceMessage ?>" placeholder="<?= LangManager::translate('shop.views.settings.settings.maintenance_placeholder') ?>">
            <div class="alert-info">
                <p><?= LangManager::translate('shop.views.settings.settings.maintenance_explain') ?></p>
            </div>
        </div>
    </div>
    <div>
        <div class="card">
            <h6><?= LangManager::translate('shop.views.settings.settings.general') ?></h6>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="allowReviews" name="allowReviews" <?= $currentReviews ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label"><?= LangManager::translate('shop.views.settings.settings.reviews') ?></p>
            </label>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="autoValidateVirtual" name="autoValidateVirtual" <?= $autoValidateVirtual ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label" title="<?= LangManager::translate('shop.views.settings.settings.auto_virtual_tooltip') ?>"><?= LangManager::translate('shop.views.settings.settings.auto_virtual') ?></p>
            </label>
            <label class="toggle">
                <input type="checkbox" class="toggle-input" id="showPublicStock" name="showPublicStock" <?= $showPublicStock ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
                <p class="toggle-label"><?= LangManager::translate('shop.views.settings.settings.show_stock') ?></p>
            </label>
            <label for="perPage"><?= LangManager::translate('shop.views.settings.settings.per_page_item') ?></label>
            <input type="number" name="perPage" id="perPage" class="input" value="<?= $perPage ?>">
            <label for="stockAlert"><?= LangManager::translate('shop.views.settings.settings.stock_alert') ?></label>
            <div class="input-group">
                <i class="fa-solid fa-percent"></i>
                <input type="number" id="stockAlert" name="stockAlert" value="<?= $stockAlert ?>">
            </div>
            <small><?= LangManager::translate('shop.views.settings.settings.stock_alert_explain') ?></small>

        </div>
    </div>
</div>
</form>
