<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity [] $categories */
/* @var \CMW\Entity\Shop\Items\ShopItemEntity [] $items */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
$title = LangManager::translate('shop.views.discount.add.title');
$description = '';

?>

<div class="page-title">
    <div>
        <h3><i class="fa-solid fa-tag"></i> <?= LangManager::translate('shop.views.discount.add.title') ?></h3>
        <small><?= LangManager::translate('shop.views.discount.add.infoTitle', ['symbol' => $symbol]) ?></small>
    </div>

    <button form="addDiscount" type="submit" class="btn-primary"><?= LangManager::translate('shop.views.discount.add.add') ?></button>
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
        <p><i class="fa-solid fa-circle-info"></i> <?= LangManager::translate('shop.views.discount.add.warn1') ?> <br>
            <?= LangManager::translate('shop.views.discount.add.warn2') ?></p>
</div>

<form id="addDiscount" method="post" class="space-y-6">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>

    <div class="grid-2">
        <div class="card">
            <h6><?= LangManager::translate('shop.views.discount.add.info') ?></h6>
                <div>
                    <label for="name"><?= LangManager::translate('shop.views.discount.add.name') ?><span class="text-danger">*</span> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
        </div>
        <div class="card">
            <div class="flex gap-2">
                <h6><?= LangManager::translate('shop.views.discount.add.duration') ?></h6>
                <small><?= LangManager::translate('shop.views.discount.add.optional') ?></small>
            </div>
            <div class="grid-2">
                <div>
                    <label for="startDate"><?= LangManager::translate('shop.views.discount.add.start') ?></label>
                    <div class="input-group">
                        <i class="fa-regular fa-clock"></i>
                        <input type="datetime-local" step="1" id="startDate" name="startDate" value="">
                    </div>
                </div>
                <div>
                    <label for="endDate"><?= LangManager::translate('shop.views.discount.add.end') ?></label>
                    <div class="input-group">
                        <i class="fa-regular fa-clock"></i>
                        <input type="datetime-local" step="1" id="endDate" name="endDate" value="">
                    </div>
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
                    <input type="checkbox" class="toggle-input" name="multiplePerUsers">
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
                    <input type="number" id="maxUses" name="maxUses" placeholder="<?= LangManager::translate('shop.views.discount.add.noLimit') ?>">
                </div>
            </div>
        </div>



















        <div class="grid-2">
            <div class="card">
                <h6><?= LangManager::translate('shop.views.discount.add.impact') ?><span class="text-danger">*</span></h6>
                <div class="space-y-4">
                    <div>
                        <select id="impact" name="impact">
                            <option value="0"><?= $symbol ?> - <?= LangManager::translate('shop.views.discount.add.money') ?></option>
                            <option value="1">% - <?= LangManager::translate('shop.views.discount.add.percent') ?></option>
                        </select>
                    </div>
                    <div>
                        <div class="input-group" id="price-group">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <input type="text" id="price" name="price" required>
                        </div>
                        <div class="input-group hidden" id="percent-group">
                            <i class="fa-solid fa-percent"></i>
                            <input type="number" id="percent" name="percent" required>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card">
                <h6><?= LangManager::translate('shop.views.discount.add.code') ?></h6>
                <div>
                    <div>
                        <label class="toggle">
                            <input type="checkbox" class="toggle-input" name="defaultActive" id="defaultActive">
                            <div class="toggle-slider"></div>
                            <p class="toggle-label"
                               title="<?= LangManager::translate('shop.views.discount.add.tooltipAuto') ?>">
                                <?= LangManager::translate('shop.views.discount.add.auto') ?></p>
                        </label>
                    </div>
                    <div id="code-group">
                        <label for="code" title="<?= LangManager::translate('shop.views.discount.add.tooltipCode') ?>"><?= LangManager::translate('shop.views.discount.add.code') ?>
                            :</label>
                        <div class="input-group">
                            <i class="fa-solid fa-rocket"></i>
                            <input type="text" id="code" name="code" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h6><?= LangManager::translate('shop.views.discount.add.settings') ?></h6>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="test">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="<?= LangManager::translate('shop.views.discount.add.tooltipTest') ?>">
                        <?= LangManager::translate('shop.views.discount.add.test') ?></p>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="needPurchase">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="<?= LangManager::translate('shop.views.discount.add.tooltipBeforeBuy') ?>">
                        <?= LangManager::translate('shop.views.discount.add.beforeBuy') ?></p>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="applyQuantity">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="<?= LangManager::translate('shop.views.discount.add.tooltipQuantity') ?>">
                        <?= LangManager::translate('shop.views.discount.add.quantity') ?>
                    </p>
                </label>
            </div>
        </div>
        <div class="card">
            <div class="space-y-2">
                <div>
                    <label for="link"><h6><?= LangManager::translate('shop.views.discount.add.linked') ?><span class="text-danger">*</span> </h6></label>
                    <select id="link" name="link">
                        <option value="0"><?= LangManager::translate('shop.views.discount.add.allItems') ?></option>
                        <option value="1"><?= LangManager::translate('shop.views.discount.add.items') ?></option>
                        <option value="2"><?= LangManager::translate('shop.views.discount.add.cats') ?></option>
                    </select>
                </div>
                <div id="linkedItems-group">
                    <label for="linkedItems"><?= LangManager::translate('shop.views.discount.add.itemsLinked') ?><span class="text-danger">*</span> :</label>
                    <select id="linkedItems" name="linkedItems[]" class="choices" multiple>
                        <?php foreach ($items as $item): ?>
                        <?php if ($item->getPriceType() == 'money' && $item->getPrice() > 0): ?>
                            <option value="<?= $item->getId() ?>"><?= $item->getName() ?> (<?= $item->getPrice() ?><?= $symbol ?>)</option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="linkedCats-group">
                    <label for="linkedCats"><?= LangManager::translate('shop.views.discount.add.catsLinked') ?><span class="text-danger">*</span> :</label>
                    <select id="linkedCats" name="linkedCats[]" class="choices" multiple>
                        <?php foreach ($categories as $category): ?>
                            <?php
                            $catHaveItemCompatible = false;
                            foreach (ShopItemsModel::getInstance()->getAdminShopItemByCat($category->getId()) as $item):
                                if ($item->getPriceType() == 'money' && $item->getPrice() > 0) {
                                    $catHaveItemCompatible = true;
                                    break;
                                }
                            endforeach;
                            ?>
                            <?php if ($catHaveItemCompatible): ?>
                                <option value="<?= $category->getId() ?>"><?= $category->getName() ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const impactSelect = document.getElementById('impact');
        const priceGroup = document.getElementById('price-group');
        const priceInput = document.getElementById('price');
        const percentGroup = document.getElementById('percent-group');
        const percentInput = document.getElementById('percent');

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

        toggleInputFields();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultActiveCheckbox = document.getElementById('defaultActive');
        const codeGroup = document.getElementById('code-group');
        const codeInput = document.getElementById('code'); // Assurez-vous que cet élément existe

        function toggleCodeField() {
            if (defaultActiveCheckbox.checked) {
                codeGroup.classList.add('hidden');
                codeInput.removeAttribute('required');
            } else {
                codeGroup.classList.remove('hidden');
                codeInput.setAttribute('required', 'required');
            }
        }

        defaultActiveCheckbox.addEventListener('change', toggleCodeField);

        // Initial call to set the correct state on page load
        toggleCodeField();
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const linkSelect = document.getElementById('link');
        const linkedItemsGroup = document.getElementById('linkedItems-group');
        const linkedItemsInput = document.getElementById('linkedItems'); // Assurez-vous que cet élément existe
        const linkedCatsGroup = document.getElementById('linkedCats-group');
        const linkedCatsInput = document.getElementById('linkedCats'); // Assurez-vous que cet élément existe

        function toggleLinkedFields() {
            switch (linkSelect.value) {
                case '0':
                    linkedItemsGroup.classList.add('hidden');
                    linkedCatsGroup.classList.add('hidden');
                    linkedItemsInput.removeAttribute('required');
                    linkedCatsInput.removeAttribute('required');
                    break;
                case '1':
                    linkedItemsGroup.classList.remove('hidden');
                    linkedCatsGroup.classList.add('hidden');
                    linkedItemsInput.setAttribute('required', 'required');
                    linkedCatsInput.removeAttribute('required');
                    break;
                case '2':
                    linkedItemsGroup.classList.add('hidden');
                    linkedCatsGroup.classList.remove('hidden');
                    linkedItemsInput.removeAttribute('required');
                    linkedCatsInput.setAttribute('required', 'required');
                    break;
            }
        }

        linkSelect.addEventListener('change', toggleLinkedFields);

        // Initial call to set the correct fields on page load
        toggleLinkedFields();
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
    function validateDates() {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const currentDate = new Date();

        if (endDateInput.value && endDate < currentDate) {
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
            endDateInput.value = '';
            return false;
        }

        if (startDateInput.value && endDateInput.value) {
            if (startDate >= endDate) {
                iziToast.show(
                    {
                        titleSize: '16',
                        messageSize: '14',
                        icon: 'fa-solid fa-xmark',
                        title  : "Erreur",
                        message: "<?= LangManager::translate('shop.views.discount.add.warnStartDate') ?>",
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
                endDateInput.value = '';
                return false;
            }
        }

        return true;
    }

    window.addEventListener('load', function () {
        const startDateInput = document.getElementById('startDate');
        const endDateInput = document.getElementById('endDate');

        startDateInput.addEventListener('change', validateDates);
        endDateInput.addEventListener('change', validateDates);
    });
</script>