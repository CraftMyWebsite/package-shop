<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discount */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
$title = LangManager::translate('shop.views.discount.edit.title', ['name' => $discount->getName()]);
$description = '';

?>

<div class="page-title">
    <div>
        <h3><i class="fa-solid fa-tag"></i> <?= LangManager::translate('shop.views.discount.edit.title', ['name' => $discount->getName()]) ?></h3>
        <small><?= LangManager::translate('shop.views.discount.add.infoTitle', ['symbol' => $symbol]) ?></small>
    </div>

    <button form="editDiscount" type="submit" class="btn-primary"><?= LangManager::translate('shop.views.discount.edit.edit') ?></button>
</div>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="alert-info">
    <?php if ($discount->getLinked() === 0): ?>
    <p><i class="fa-solid fa-circle-info"></i> <?= LangManager::translate('shop.views.discount.edit.warnAll') ?></p>
    <?php endif; ?>
    <?php if ($discount->getLinked() === 1): ?>
        <p><i class="fa-solid fa-circle-info"></i> <?= LangManager::translate('shop.views.discount.edit.warnItems') ?><br>
        <ul>
            <?php foreach (ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByDiscountId($discount->getId()) as $discountItem): ?>
                <li>- <a target="_blank" href="<?= $discountItem->getItem()->getItemLink() ?>"><?= $discountItem->getItem()->getName() ?> (<?= $discountItem->getItem()->getPrice() ?> <?= $symbol ?>)</a></li>
            <?php endforeach; ?>
        </ul>
        </p>
    <?php endif; ?>
    <?php if ($discount->getLinked() === 2): ?>
        <p><i class="fa-solid fa-circle-info"></i> <?= LangManager::translate('shop.views.discount.edit.warnCats') ?><br>
        <ul>
            <?php foreach (ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesByDiscountId($discount->getId()) as $discountCat): ?>
                <li>- <a target="_blank" href="<?= Website::getProtocol() ?>://<?= $_SERVER['SERVER_NAME'] ?><?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>shop/cat/<?= $discountCat->getCategory()->getSlug() ?>"><?= $discountCat->getCategory()->getName() ?> (<?= $discountCat->getCategory()->countItemsInCat() ?> articles)</a></li>
            <?php endforeach; ?>
        </ul>
        </p>
    <?php endif; ?>
</div>

<form id="editDiscount" method="post" class="space-y-6">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>

    <div class="grid-2">
        <div class="card">
            <h6><?= LangManager::translate('shop.views.discount.add.info') ?></h6>
                <div>
                    <label for="name"><?= LangManager::translate('shop.views.discount.add.name') ?><span class="text-danger">*</span> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" id="name" name="name" value="<?= $discount->getName() ?>"  required>
                    </div>
                </div>
        </div>
        <div class="card">
            <div class="flex gap-2">
                <h6><?= LangManager::translate('shop.views.discount.add.duration') ?></h6>
                <small><?= LangManager::translate('shop.views.discount.add.optional') ?></small>
            </div>
                <div>
                    <label for="endDate"><?= LangManager::translate('shop.views.discount.add.end') ?></label>
                    <div class="input-group">
                        <i class="fa-regular fa-clock"></i>
                        <input type="datetime-local" step="1" id="endDate" name="endDate" value="<?= $discount?->getEndDate() ? $discount->getEndDate() : null ?>">
                    </div>
                </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="flex gap-2">
                <h6><?= LangManager::translate('shop.views.discount.add.limit') ?></h6>
                <small><?= LangManager::translate('shop.views.discount.add.optional') ?></small>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="multiplePerUsers" <?= $discount->getUsesMultipleByUser() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="<?= LangManager::translate('shop.views.discount.add.tooltipUse') ?>">
                        <?= LangManager::translate('shop.views.discount.add.use') ?></p>
                </label>
            </div>
            <div>
                <label for="maxUses"><?= LangManager::translate('shop.views.discount.add.globalLimit') ?></label>
                <div class="input-group">
                    <i class="fa-solid fa-ban"></i>
                    <input type="number" id="maxUses" name="maxUses" placeholder="<?= LangManager::translate('shop.views.discount.add.noLimit') ?>" value="<?= $discount->getMaxUses() ?>" >
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h6><?= LangManager::translate('shop.views.discount.add.impact') ?><span class="text-danger">*</span></h6>
                <div class="space-y-4">
                    <div>
                        <select id="impact" name="impact">
                            <option <?= $discount->getPrice() ? 'selected' : '' ?> value="0"><?= $symbol ?> - <?= LangManager::translate('shop.views.discount.add.money') ?></option>
                            <option <?= $discount->getPercentage() ? 'selected' : '' ?> value="1">% - <?= LangManager::translate('shop.views.discount.add.percent') ?></option>
                        </select>
                    </div>
                    <div>
                        <div class="input-group" id="price-group">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <input type="text" value="<?= $discount->getPrice() ?>" id="price" name="price" required>
                        </div>
                        <div class="input-group hidden" id="percent-group">
                            <i class="fa-solid fa-percent"></i>
                            <input type="number" value="<?= $discount->getPercentage() ?>" id="percent" name="percent" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <h6><?= LangManager::translate('shop.views.discount.add.settings') ?></h6>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="test" <?= $discount->getTestMode() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="<?= LangManager::translate('shop.views.discount.add.tooltipTest') ?>">
                            <?= LangManager::translate('shop.views.discount.add.test') ?></p>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="needPurchase" <?= $discount->getUserHaveOrderBeforeUse() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="<?= LangManager::translate('shop.views.discount.add.tooltipBeforeBuy') ?>">
                            <?= LangManager::translate('shop.views.discount.add.beforeBuy') ?></p>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="applyQuantity" <?= $discount->getDiscountQuantityImpacted() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="<?= LangManager::translate('shop.views.discount.add.tooltipQuantity') ?>">
                            <?= LangManager::translate('shop.views.discount.add.quantity') ?>
                        </p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <?php if ($discount->getCode()): ?>
        <div class="card">
            <h6><?= LangManager::translate('shop.views.discount.add.code') ?></h6>
            <div>
                <div id="code-group">
                    <label for="code" title="Le CODE que vos clients doivent taper pour appliquer la réduction"><?= LangManager::translate('shop.views.discount.add.code') ?>
                        :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-rocket"></i>
                        <input type="text" id="code" name="code" value="<?= $discount->getCode() ?>" required>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const impactSelect = document.getElementById('impact');
        const priceGroup = document.getElementById('price-group');
        const priceInput = document.getElementById('price'); // Assurez-vous que cet élément existe
        const percentGroup = document.getElementById('percent-group');
        const percentInput = document.getElementById('percent'); // Assurez-vous que cet élément existe

        function toggleInputFields() {
            if (impactSelect.value === '0') {
                priceGroup.style.display = 'flex';
                percentGroup.style.display = 'none';
                priceInput.setAttribute('required', 'required');
                percentInput.removeAttribute('required');
            } else {
                priceGroup.style.display = 'none';
                percentGroup.style.display = 'flex';
                percentInput.setAttribute('required', 'required');
                priceInput.removeAttribute('required');
            }
        }

        impactSelect.addEventListener('change', toggleInputFields);

        // Initial call to set the correct fields on page load
        toggleInputFields();
    });
</script>

<script>
    let inputElement = document.querySelector('input[name="price"]');

    inputElement.addEventListener('input', function() {
        let inputValue = this.value;
        inputValue = inputValue.replace(/,/g, '.');
        inputValue = inputValue.replace(/[^\d.]/g, '');
        if (/\.\d{3,}/.test(inputValue)) {
            let decimalIndex = inputValue.indexOf('.');
            inputValue = inputValue.substring(0, decimalIndex + 3);
        }
        this.value = inputValue;
    });
</script>



<script>
    document.getElementById('percent').addEventListener('keydown', function(e) {
        // Vérifier si la touche pressée est un point ou une virgule
        if (e.key === '.' || e.key === ',') {
            iziToast.show(
                {
                    titleSize: '16',
                    messageSize: '14',
                    icon: 'fa-solid fa-xmark',
                    title  : "Erreur",
                    message: "<?= LangManager::translate('shop.views.discount.add.warnVirg') ?>",
                    color: "#41435F",
                    iconColor: '#DE2B59',
                    titleColor: '#DE2B59',
                    messageColor: '#fff',
                    balloon: false,
                    close: false,
                    position: 'bottomRight',
                    timeout: 5000,
                    animateInside: false,
                    progressBar: false,
                    transitionIn: 'fadeInLeft',
                    transitionOut: 'fadeOutRight',
                });
            e.preventDefault();
        }
    });

    document.getElementById('percent').addEventListener('input', function(e) {
        // Récupérer la valeur actuelle de l'entrée
        var value = e.target.value;

        // Vérifier si la valeur dépasse 99
        if (value > 99) {
            iziToast.show(
                {
                    titleSize: '16',
                    messageSize: '14',
                    icon: 'fa-solid fa-xmark',
                    title  : "Erreur",
                    message: "<?= LangManager::translate('shop.views.discount.add.warn99') ?>",
                    color: "#41435F",
                    iconColor: '#DE2B59',
                    titleColor: '#DE2B59',
                    messageColor: '#fff',
                    balloon: false,
                    close: false,
                    position: 'bottomRight',
                    timeout: 5000,
                    animateInside: false,
                    progressBar: false,
                    transitionIn: 'fadeInLeft',
                    transitionOut: 'fadeOutRight',
                });
            e.target.value = 99;
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const endDateInput = document.getElementById('endDate');

        function validateDate() {
            if (!endDateInput.value) {
                return;
            }

            const now = new Date().toISOString().slice(0, 19);
            if (endDateInput.value < now) {
                iziToast.show(
                    {
                        titleSize: '16',
                        messageSize: '14',
                        icon: 'fa-solid fa-xmark',
                        title  : "Erreur",
                        message: "<?= LangManager::translate('shop.views.discount.add.warnEndDate') ?>",
                        color: "#41435F",
                        iconColor: '#DE2B59',
                        titleColor: '#DE2B59',
                        messageColor: '#fff',
                        balloon: false,
                        close: false,
                        position: 'bottomRight',
                        timeout: 5000,
                        animateInside: false,
                        progressBar: false,
                        transitionIn: 'fadeInLeft',
                        transitionOut: 'fadeOutRight',
                    });
                endDateInput.value = now;
            }
        }

        endDateInput.addEventListener('change', validateDate);

        // Initial check in case the default value is invalid
        validateDate();
    });
</script>