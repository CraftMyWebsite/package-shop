<?php

use CMW\Controller\Shop\SettingsController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "";
$description = "";

/* @VAR \CMW\Model\Shop\SettingsModel\ $currentCurrency */

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
                                    <?php foreach (SettingsController::$availableCurrencies as $code => $name): ?>
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
</section>
