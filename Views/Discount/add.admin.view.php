<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity [] $categories */
/* @var \CMW\Entity\Shop\Items\ShopItemEntity [] $items */
$symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
$title = '';
$description = '';

?>

<div class="page-title">
    <div>
        <h3><i class="fa-solid fa-tag"></i> Nouvelle promotion</h3>
        <small>Seuls les articles en <?= $symbol ?> et supérieur à 0 sont applicables.</small>
    </div>

    <button form="addDiscount" type="submit" class="btn-primary">Ajouter</button>
</div>

<div class="alert-info">
        <p><i class="fa-solid fa-circle-info"></i> N'oubliez pas qu'aucune promotion n'est cumulable. <br>
            par ex, une promotion appliquée automatiquement sur un groupe d'article sera prioritaire sur une promotion par CODE sur ces mêmes articles.</p>
</div>

<form id="addDiscount" method="post" class="space-y-6">
    <?php (new SecurityManager())->insertHiddenToken() ?>

    <div class="grid-2">
        <div class="card">
            <h6>Informations</h6>
                <div>
                    <label for="name">Nom<span class="text-danger">*</span> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
        </div>
        <div class="card">
            <div class="flex gap-2">
                <h6>Durée</h6>
                <small>Non obligatoire !</small>
            </div>
            <div class="grid-2">
                <div>
                    <label for="startDate">Date de début :</label>
                    <div class="input-group">
                        <i class="fa-regular fa-clock"></i>
                        <input type="datetime-local" step="1" id="startDate" name="startDate" value="">
                    </div>
                </div>
                <div>
                    <label for="endDate">Date de fin :</label>
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
                <h6>Limites</h6>
                <small>Non obligatoire !</small>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="multiplePerUsers">
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
                    <input type="number" id="maxUses" name="maxUses" placeholder="Pas de limites">
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h6>Impacte<span class="text-danger">*</span></h6>
                <div class="space-y-4">
                    <div>
                        <select id="impact" name="impact">
                            <option value="0"><?= $symbol ?> - Monétaire</option>
                            <option value="1">% - Pourcentage</option>
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
                <h6>Code</h6>
                <div>
                    <div>
                        <label class="toggle">
                            <input type="checkbox" class="toggle-input" name="defaultActive" id="defaultActive">
                            <div class="toggle-slider"></div>
                            <p class="toggle-label"
                               title="Vos clients n'ont pas à rentrer de code si cet option est active">
                                S'applique automatiquement</p>
                        </label>
                    </div>
                    <div id="code-group">
                        <label for="code" title="Le CODE que vos clients doivent taper pour appliquer la réduction">Code
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
            <h6>Réglages</h6>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="test">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="Ceci permet de tester vos promotions avant qu'elle ne sois utilisable par vos clients si cet option est active">
                        Mode test</p>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="needPurchase">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="Vos clients ont déjà passer une commande avant de pouvoir bénéficier de ce code si cet option est active">
                        Doit avoir déjà acheté</p>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <input type="checkbox" class="toggle-input" name="applyQuantity">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"
                       title="La réduction s'applique sur la quantité dans le panier si cet option est active">Appliquer sur la
                        quantité</p>
                </label>
            </div>
        </div>
        <div class="card">
            <div class="space-y-2">
                <div>
                    <label for="link"><h6>Lié à<span class="text-danger">*</span> </h6></label>
                    <select id="link" name="link">
                        <option value="0">Tout les articles</option>
                        <option value="1">Un ou Des article(s)</option>
                        <option value="2">Une ou Des catégorie(s)</option>
                    </select>
                </div>
                <div id="linkedItems-group">
                    <label for="linkedItems">Article(s) lié(s)<span class="text-danger">*</span> :</label>
                    <select id="linkedItems" name="linkedItems[]" class="choices" multiple>
                        <?php foreach ($items as $item): ?>
                        <?php if ($item->getPriceType() == 'money' && $item->getPrice() > 0): ?>
                            <option value="<?= $item->getId() ?>"><?= $item->getName() ?> (<?= $item->getPrice() ?><?= $symbol ?>)</option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="linkedCats-group">
                    <label for="linkedCats">Catégorie(s) lié(s)<span class="text-danger">*</span> :</label>
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
                    message: "La date de fin ne peut pas être inférieure à la date actuelle.",
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
                        message: "La date de début doit être antérieure à la date de fin.",
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