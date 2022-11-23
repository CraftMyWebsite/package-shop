<?php use CMW\Controller\Shop\ShopController;
use CMW\Entity\Shop\ShopConfigEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\SecurityService;

$title = "";
$description = "";

/* @var ShopConfigEntity $config */
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Configuration de votre boutique</span></h3>
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
                <form id="Configuration" action="" method="post">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Devises acceptées :</h6>
                            <fieldset class="form-group">
                                <select class="choices choices__list--multiple" name="currencies[]" multiple required>
                                    <?php foreach (ShopController::$availableCurrencies as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= array_key_exists($code, ShopController::getLocalCurrenciesCode()) ? 'selected' : '' ?>>
                                            <?= $name ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="1" id="enableDiscordWebhook"
                                       name="enableDiscordWebhook" <?= $config->isDiscordWebHookEnable() ? 'checked' : '' ?>>
                                <label class="form-check-label" for="enableDiscordWebhook">
                                    <h6>Activer le WebHook Discord :
                                        <i data-bs-toggle="tooltip"
                                           title="Vous pouvez entièrement customiser votre webhook."
                                           class="fa-sharp fa-solid fa-circle-question"></i>
                                    </h6>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</div>
<script>
    function toggleField(hideObj, showObj) {
        hideObj.disabled = true;
        hideObj.style.display = 'none';
        showObj.disabled = false;
        showObj.style.display = 'inline';
        showObj.focus();
    }
</script>