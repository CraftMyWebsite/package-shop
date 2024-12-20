<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity $discount */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
$title = '';
$description = '';

?>

<div class="page-title">
    <div>
        <h3><i class="fa-solid fa-tag"></i> Édition de <?= $discount->getName() ?></h3>
        <small>Seuls les articles en <?= $symbol ?> et supérieur à 0 sont applicables.</small>
    </div>

    <button form="editDiscount" type="submit" class="btn-primary">Éditer</button>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b>Important : Configuration des e-mails requise</b>
        <p>Les e-mails ne sont pas configurés sur votre site. Une configuration correcte est essentielle pour assurer le bon fonctionnement du package Shop.<br>
            Les notifications importantes, telles que les confirmations de commandes, les informations de suivi ..., dépendent d'un système d'e-mails fonctionnel.</p>
        <p>Veuillez <a class="link" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/mail/configuration">configurer les paramètres d'e-mails</a> dès que possible.</p>
    </div>
<?php endif;?>

<div class="alert-info">
    <?php if ($discount->getLinked() === 0): ?>
    <p><i class="fa-solid fa-circle-info"></i> Cette promotion s'applique à tous les articles de votre boutique.<br>Ceci n'est pas modifiable, supprimer et recréer la promotion pour changer ceci</p>
    <?php endif; ?>
    <?php if ($discount->getLinked() === 1): ?>
        <p><i class="fa-solid fa-circle-info"></i> Cette promotion s'applique à un ou plusieurs articles.<br>Ceci n'est pas modifiable, supprimer et recréer la promotion pour changer ceci<br>Voici la liste des articles pris en charge par cette promotion :<br>
        <ul>
            <?php foreach (ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByDiscountId($discount->getId()) as $discountItem): ?>
                <li>- <a target="_blank" href="<?= $discountItem->getItem()->getItemLink() ?>"><?= $discountItem->getItem()->getName() ?> (<?= $discountItem->getItem()->getPrice() ?> <?= $symbol ?>)</a></li>
            <?php endforeach; ?>
        </ul>
        </p>
    <?php endif; ?>
    <?php if ($discount->getLinked() === 2): ?>
        <p><i class="fa-solid fa-circle-info"></i> Cette promotion s'applique à une ou plusieurs catégories.<br>Ceci n'est pas modifiable, supprimer et recréer la promotion pour changer ceci<br>Voici la liste des catégories prise en charge par cette promotion :<br>
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
            <h6>Informations</h6>
                <div>
                    <label for="name">Nom<span class="text-danger">*</span> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" id="name" name="name" value="<?= $discount->getName() ?>"  required>
                    </div>
                </div>
        </div>
        <div class="card">
            <div class="flex gap-2">
                <h6>Durée</h6>
                <small>Non obligatoire !</small>
            </div>
                <div>
                    <label for="endDate">Date de fin :</label>
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
                <h6>Limites</h6>
                <small>Non obligatoire !</small>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="multiplePerUsers" <?= $discount->getUsesMultipleByUser() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="Le client peut utiliser le code sur plusieurs commandes différentes si cet option est active">
                        Utilisation multiple par clients</p>
                </label>
            </div>
            <div>
                <label for="maxUses">Limite global d'utilisation / stock :</label>
                <div class="input-group">
                    <i class="fa-solid fa-ban"></i>
                    <input type="number" id="maxUses" name="maxUses" placeholder="Pas de limites" value="<?= $discount->getMaxUses() ?>" >
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h6>Impacte<span class="text-danger">*</span></h6>
                <div class="space-y-4">
                    <div>
                        <select id="impact" name="impact">
                            <option <?= $discount->getPrice() ? 'selected' : '' ?> value="0"><?= $symbol ?> - Monétaire</option>
                            <option <?= $discount->getPercentage() ? 'selected' : '' ?> value="1">% - Pourcentage</option>
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
                <h6>Réglages</h6>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="test" <?= $discount->getTestMode() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="Ceci permet de tester vos promotions avant qu'elle ne sois utilisable par vos clients si cet option est active">
                            Mode test</p>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="needPurchase" <?= $discount->getUserHaveOrderBeforeUse() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="Vos clients ont déjà passer une commande avant de pouvoir bénéficier de ce code si cet option est active">
                            Doit avoir déjà acheté</p>
                    </label>
                </div>
                <div>
                    <label class="toggle">
                        <input type="checkbox" class="toggle-input" name="applyQuantity" <?= $discount->getDiscountQuantityImpacted() ? 'checked' : '' ?>>
                        <div class="toggle-slider"></div>
                        <p class="toggle-label"
                           title="La réduction s'applique sur la quantité dans le panier si cet option est active">Appliquer sur la
                            quantité</p>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <?php if ($discount->getCode()): ?>
        <div class="card">
            <h6>Code</h6>
            <div>
                <div id="code-group">
                    <label for="code" title="Le CODE que vos clients doivent taper pour appliquer la réduction">Code
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
                priceGroup.classList.remove('hidden');
                percentGroup.classList.add('hidden');
                priceInput.setAttribute('required', 'required');
                percentInput.removeAttribute('required');
            } else {
                priceGroup.classList.add('hidden');
                percentGroup.classList.remove('hidden');
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
                    message: "Les nombres à virgule ne sont pas autorisé !",
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
                    message: "Vous ne pouvez pas dépasser 99% !",
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
                        message: "La date de fin ne peut pas être antérieur à la date actuelle.",
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