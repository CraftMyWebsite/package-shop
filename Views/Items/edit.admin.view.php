<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Utils\Website;

$title = 'Boutique';
$description = '';

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */
/* @var CMW\Entity\Shop\Items\ShopItemEntity $item */
/* @var CMW\Entity\Shop\Images\ShopImageEntity[] $imagesItem */
/* @var \CMW\Entity\Shop\Items\ShopItemPhysicalRequirementEntity $physicalInfo */
/* @var CMW\Interface\Shop\IVirtualItems[] $virtualMethods */
/* @var CMW\Interface\Shop\IPriceTypeMethod[] $priceTypeMethods */
/* @var CMW\Entity\Shop\Items\ShopItemVariantEntity[] $itemVariants */
/* @var CMW\Model\Shop\Item\ShopItemVariantValueModel $variantValuesModel */

$virtualMethod = ShopItemsVirtualMethodModel::getInstance()?->getVirtualItemMethodByItemId($item->getId());
$virtualMethodName = $virtualMethod?->getVirtualMethod()->varName();

$existingVariants = [];
foreach ($itemVariants as $itemVariant) {
    $variantValues = [];
    foreach ($variantValuesModel->getShopItemVariantValueByVariantId($itemVariant->getId()) as $variantValue) {
        $variantValues[] = [
            'id' => $variantValue->getId(),
            'value' => $variantValue->getValue()
        ];
    }
    $existingVariants[] = [
        'name' => $itemVariant->getName(),
        'values' => $variantValues
    ];
}

?>
<div class="page-title">
    <h3><i class="fa-solid fa-cart-plus"></i> Édition de <?= $item->getName() ?></h3>

    <?php if ($item->getQuantityInCart()): ?>
    <button data-modal-toggle="modal-danger" class="btn-warning"><?= LangManager::translate('core.btn.edit') ?></button>
    <?php else: ?>
    <button form="editItem" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.edit') ?></button>
    <?php endif; ?>
</div>

<div id="modal-danger" class="modal-container">
    <div class="modal">
        <div class="modal-header-warning">
            <h6>Modifications</h6>
            <button type="button" data-modal-hide="modal-danger"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <p>Attention, certains utilisateurs ont déja ajouter cet article dans leur panier !<br>
                Si vous avez des variantes ou si le type d'article a changé (Physique -> Virtuel ou Virtuel -> Physique), cela supprimera automatiquement cet article des paniers qui le contiennent !</p>
        </div>
        <div class="modal-footer">
            <button form="editItem" type="submit" class="btn-warning"><?= LangManager::translate('core.btn.edit') ?></button>
        </div>
    </div>
</div>

<form id="editItem" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="grid-4">
        <div class="col-span-3">
            <div class="card">
                <div class="grid-2">
                    <div>
                        <label for="shop_item_name">Nom<span style="color: red">*</span> :</label>
                        <input value="<?= $item->getName() ?>" type="text" class="input" id="shop_item_name" name="shop_item_name" maxlength="50" required>
                    </div>
                    <div>
                        <label for="shop_item_short_desc">Déscription courte<span style="color: red">*</span> :</label>
                        <input value="<?= $item->getShortDescription() ?>" type="text" id="shop_item_short_desc" class="input" name="shop_item_short_desc" required>
                    </div>
                </div>
                <div>
                    <label for="shop_item_description">Déscription détailler<span style="color: red">*</span> :</label>
                    <textarea id="shop_item_description" data-tiny-height="500px" class="tinymce" name="shop_item_description"><?= $item->getDescription() ?></textarea>
                </div>
            </div>

            <div class="card mt-6">
                <div class="card-body">
                    <div id="typePhysique" style="display:none;">
                        <label>Caractéristique physique du produit :
                            <button type="button"  data-tooltip-target="tooltip-top-physical" data-tooltip-placement="top"><i class="fa-solid fa-circle-question"></i></button>
                            <div id="tooltip-top-physical" role="tooltip" class="tooltip-content">
                                Changer le type d'articles pour passer en contenue virtuel
                            </div>
                        </label>

                        <div class="grid-4">
                            <div>
                                <label>Poids<span style='color: red'>*</span> : <small>(en gramme)</small></label>
                                <input value="<?= $physicalInfo?->getWeight() ?>" type="text" class="input" placeholder="150.00" name="shop_item_weight" required>
                            </div>
                            <div>
                                <label>Longueur : <small>(en cm)</small></label>
                                <input value="<?= $physicalInfo?->getLength() ?>" type="text" class="input" placeholder="150.00" name="shop_item_length">
                            </div>
                            <div>
                                <label>Largeur : <small>(en cm)</small></label>
                                <input value="<?= $physicalInfo?->getWidth() ?>" type="text" class="input" placeholder="150.00" name="shop_item_width">
                            </div>
                            <div>
                                <label>Hauteur : <small>(en cm)</small></label>
                                <input value="<?= $physicalInfo?->getHeight() ?>" type="text" class="input" placeholder="150.00" name="shop_item_height">
                            </div>
                        </div>

                    </div>
                    <div id="typeVirtuel" style="display:none;">
                        <div class="mb-4">
                            <label>Contenue virtuel<span style="color: red">*</span> :
                                <button type="button" data-tooltip-target="tooltip-top-virtual" data-tooltip-placement="top">
                                    <i class="fa-solid fa-circle-question"></i>
                                </button>
                                <div id="tooltip-top-virtual" role="tooltip" class="tooltip-content">
                                    Changer le type d'articles pour passer en contenue physique
                                </div>
                            </label>
                            <select class="form-select" name="shop_item_virtual_prefix" id="virtual_type_selected" required>
                                <?php foreach ($virtualMethods as $virtualMethod): ?>
                                    <option value="<?= $virtualMethod->varName() ?>"
                                        <?= $virtualMethodName === $virtualMethod->varName() ? 'selected' : '' ?>
                                    ><?= $virtualMethod->name() ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <?php $i = 1;
                            foreach ($virtualMethods as $virtualMethod): ?>
                                <div class="virtual-method" id="method-<?= $virtualMethod->varName() ?>">
                                    <?php if ($virtualMethod->documentationURL()): ?>
                                        <a href="<?= $virtualMethod->documentationURL() ?>" target="_blank" class="btn btn-primary btn-sm">Documentations</a><br>
                                    <?php endif; ?>
                                    <p><?= $virtualMethod->description() ?></p>
                                    <input type="hidden" name="shop_item_virtual_method_var_name" value="<?= $virtualMethod->varName() ?>">
                                    <?php $virtualMethod->includeItemConfigWidgets($item->getId()) ?>
                                </div>
                                <?php ++$i;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-6">
                <div class="flex justify-between">
                    <div><label>Variantes (optionnel) : </label></div>
                    <div class="buttons">
                        <button class="btn btn-primary" type="button" onclick="ajouterVariante()">Ajouter une variante</button>
                    </div>
                </div>
                <p>Les variantes sont assez pratique quand vous souhaitez ajouter un article qui peut avoir plusieurs configurations disponnible sans pour autant créer autant d'article que vous avez de variantes de celui-ci, à vous de créer autant de variantes que vous le souhaiter. (Couleur,Taille,Poids ...)<br> Cependant, noter bien que si vous utilisez les variantes celle-ci devienne obligatoire pour vos consommateurs.</p>
                <div id="variantsContainer"></div>
            </div>

        </div>
        <div>
            <div class="card">
                <div>
                    <div class="checkbox mb-4">
                        <input <?= $item->isDraft() ? 'checked' : '' ?> id="checkbox" name="shop_item_draft" type="checkbox">
                        <label data-tooltip-target="tooltip-top" data-tooltip-placement="top" for="checkbox">Publié en tant que brouillon / test</label>
                        <div id="tooltip-top" role="tooltip" class="tooltip-content">
                            L'article ne s'affichera que pour les administrateurs
                        </div>
                    </div>
                    <label>Catégorie<span style="color: red">*</span> :</label>
                    <select name="shop_category_id" class="form-select">
                        <?php foreach ($categoryModel->getShopCategories() as $cat): ?>
                            <option value="<?= $cat->getId() ?>"
                                <?= $cat->getId() === $item->getCategory()->getId() ? 'selected' : '' ?>
                            > <?= $cat->getName() ?> </option>
                            <?php foreach ($categoryModel->getSubsCat($cat->getId()) as $subCategory): ?>
                                <option value="<?= $subCategory->getSubCategory()->getId() ?>"
                                    <?= $subCategory->getId() === $item->getCategory()->getId() ? 'selected' : '' ?>> <?php echo str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $subCategory->getDepth()) . ' ↪ ' . $subCategory->getSubCategory()->getName() ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Tags :</label>
                    <input type="text" class="input" name="">
                </div>
                <div>
                    <label>Prix :</label>
                    <div class="flex justify-between">
                        <input value="<?= $item->getPrice() ?>" type="text" class="input" name="shop_item_price" placeholder="19.99">
                        <span>
                                    <!--TODO : Uniquement les articles virtuel pour le moment-->
                                <select id="payment" class="form-select" name="shop_item_price_type" required>
                                    <?php foreach ($priceTypeMethods as $priceTypeMethod): ?>
                                        <option value="<?= $priceTypeMethod->varName() ?>"
                                            <?= $priceTypeMethod->varName() === 'money' ? 'data-is-money="true"' : '' ?>
                                            <?= $priceTypeMethod->varName() === $item->getPriceType() ? 'selected' : '' ?>
                                        ><?= $priceTypeMethod->name() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </span>
                    </div>
                </div>
                <div>
                    <label>Type<span style="color: red">*</span> :</label>
                    <select id="type" class="form-select super-choice" name="shop_item_type" onchange="afficherChamps()" required>
                        <option value="1" <?= $item->getType() === 1 ? 'selected' : '' ?>>Virtuel</option>
                        <option value="0" <?= $item->getType() === 0 ? 'selected' : '' ?>>Physique</option>
                    </select>
                </div>
            </div>
            <div class="card mt-6">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <label>Stock : <small>(<?= $item->getFormattedStock() ?>)</small></label>
                            <input value="<?= $item->getCurrentStock() ?>" type="number" class="input" name="shop_item_default_stock"
                                   placeholder="Pas de limites">
                        </div>
                        <div class="col-12 mt-2">
                            <label>Limite d'achat global :</label>
                            <input value="<?= $item->getGlobalLimit() ?>" type="number" class="input" name="shop_item_global_limit"
                                   placeholder="Pas de limites">
                        </div>
                        <div class="col-12 mt-2">
                            <label>Limite d'achat par utilisateur :</label>
                            <input value="<?= $item->getUserLimit() ?>" type="number" class="input" name="shop_item_user_limit"
                                   placeholder="Pas de limites">
                        </div>
                        <div class="col-12 mt-2">
                            <label>Limite d'achat par commande :</label>
                            <input value="<?= $item->getByOrderLimit() ?>" type="number" class="input" name="shop_item_by_order_limit"
                                   placeholder="Pas de limites">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-6">
        <div class="card-header">
            <h4>Galerie</h4>
        </div>
        <div class="card-body">

            <div onclick="addImg();" style="cursor: pointer;">
                <div class="text-center border-dashed border-4 rounded-lg dark:border-gray-700" style="padding-top: 1rem">
                    <h2><i class="text-success fa-solid fa-circle-plus fa-xl"></i></h2>
                    <p class="mt-2">Ajouter une image</p>
                </div>
            </div>
            <div id="img_div" class="grid-3 gap-16 mt-4">
                <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId()); ?>
                <?php if ($getImagesItem): ?>
                    <?php foreach ($getImagesItem as $index => $imageUrl): ?>
                        <div class="col-12 col-lg-3 border-4 border-dashed rounded-lg dark:border-gray-700 relative h-fit" id="delete-<?= $index ?>">
                            <div class="card-in-card p-2">
                                <img src="<?= $imageUrl->getImageUrl() ?>" class="w-50 mx-auto" alt="..."/>
                                <input hidden="" name="image-existing-<?= $index ?>" id="image-existing-<?= $index ?>" value="<?= $imageUrl->getName() ?>">
                                <div>
                                    <button type="button" class="absolute bottom-0 left-0 bg-success text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex" onclick="moveImage(this.parentNode.parentNode.parentNode, 'up')">
                                        <i class="fa-solid fa-chevron-left"></i>
                                    </button>
                                    <button type="button" class="absolute top-0 right-0 bg-danger text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex" onclick="deleteImage(this.parentNode.parentNode.parentNode)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <button type="button" class="absolute bottom-0 right-0 bg-success text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex" onclick="moveImage(this.parentNode.parentNode.parentNode, 'down')">
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </button>
                                </div>
                                <span class="image-order-label absolute top-0 left-0 bg-blue-500 text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex"><?= $index + 1 ?></span>
                                <input type="hidden" name="order-<?= $index ?>" value="<?= $index ?>">
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <input hidden="" type="text" name="numberOfImage" id="numberOfImage" value="<?= isset($getImagesItem) ? count($getImagesItem) : 0 ?>">
        </div>
    </div>

</form>

<script>
    let existingVariants = <?= json_encode($existingVariants) ?>;

    document.addEventListener("DOMContentLoaded", function () {
        afficherChamps(); // Appeler au chargement de la page
        ajouterVarianteExistant(); // Ajouter les variantes existantes
    });

    function ajouterValeur(container, parentIndex) {
        let inputDiv = document.createElement("div");
        inputDiv.className = "flex gap-2 item-center mt-2";

        let inputValeur = document.createElement("input");
        inputValeur.className = "input";
        inputValeur.name = "shop_item_variant_value[" + parentIndex + "][]";
        inputValeur.placeholder = "Vert";
        inputValeur.required = true;

        let boutonSupprimerValeur = document.createElement("button");
        boutonSupprimerValeur.textContent = "x";
        boutonSupprimerValeur.className = "btn-danger-sm h-fit";
        boutonSupprimerValeur.type = "button";
        boutonSupprimerValeur.onclick = function () {
            container.removeChild(inputDiv);
            inputDiv.removeChild(inputValeur);
            inputDiv.removeChild(boutonSupprimerValeur);
        };

        container.appendChild(inputDiv);
        inputDiv.appendChild(inputValeur);
        inputDiv.appendChild(boutonSupprimerValeur);
    }


    function ajouterVarianteExistant() {
        existingVariants.forEach((variant, index) => {
            let inputNom = document.createElement("input");
            inputNom.type = "text";
            inputNom.className = "input";
            inputNom.placeholder = "Couleur, Taille, etc";
            inputNom.name = "shop_item_variant_name[" + index + "]";
            inputNom.value = variant.name;
            inputNom.required = true;

            let labelNom = document.createElement("label");
            labelNom.innerHTML = "Nom<span style='color: red'>*</span> : ";

            let boutonSupprimer = document.createElement("a");
            boutonSupprimer.textContent = "Supprimer la variante";
            boutonSupprimer.className = "btn-center btn-danger text-center";
            boutonSupprimer.style.cursor = "pointer";
            boutonSupprimer.onclick = function () {
                document.getElementById("variantsContainer").removeChild(varianteContainer);
            };

            let valueContainer = document.createElement("div");
            valueContainer.className = "col-span-3";

            let labelValeurActuel = document.createElement("label");
            labelValeurActuel.innerHTML = "Valeur actuel :";
            valueContainer.appendChild(labelValeurActuel);

            variant.values.forEach((value, valueIndex) => {
                let inputValeur = document.createElement("input");
                inputValeur.className = "input";
                inputValeur.name = "shop_item_variant_value[" + index + "][]";
                inputValeur.value = value.value;
                inputValeur.placeholder = "Rouge";
                inputValeur.required = true;

                let boutonSupprimerValeur = document.createElement("button");
                boutonSupprimerValeur.textContent = "x";
                boutonSupprimerValeur.className = "btn-danger-sm h-fit";
                boutonSupprimerValeur.type = "button";
                boutonSupprimerValeur.onclick = function () {
                    valueContainer.removeChild(inputDiv);
                    inputDiv.removeChild(inputValeur);
                    inputDiv.removeChild(boutonSupprimerValeur);
                };

                let inputDiv = document.createElement("div");
                inputDiv.className = "flex gap-2 item-center mt-2";
                inputDiv.appendChild(inputValeur);
                inputDiv.appendChild(boutonSupprimerValeur);
                valueContainer.appendChild(inputDiv);
            });

            let varianteContainer = document.createElement("div");
            varianteContainer.setAttribute("data-index", index);
            varianteContainer.className = "card-in-card p-3 mt-2 mb-4";
            let row = document.createElement("div");
            row.className = "grid-4 border-2 rounded-lg p-4 dark:border-gray-700";
            let nameContainer = document.createElement("div");

            varianteContainer.appendChild(row);
            row.appendChild(nameContainer);
            nameContainer.appendChild(labelNom);
            nameContainer.appendChild(inputNom);
            nameContainer.appendChild(boutonSupprimer);
            row.appendChild(valueContainer);

            let labelValeurDiv = document.createElement("div")
            labelValeurDiv.className = "flex justify-between";
            let labelValeur = document.createElement("label");
            labelValeur.innerHTML = "Nouvelle valeur : ";

            let boutonAjouterValeur = document.createElement("a");
            boutonAjouterValeur.textContent = "+ Ajouter une valeur";
            boutonAjouterValeur.className = "text-success cursor-pointer font-bold";
            boutonAjouterValeur.type = "button";
            boutonAjouterValeur.onclick = function () {
                ajouterValeur(valueContainer, index);
            };

            labelValeurDiv.appendChild(labelValeur);
            labelValeurDiv.appendChild(boutonAjouterValeur);
            valueContainer.appendChild(labelValeurDiv);

            document.getElementById("variantsContainer").appendChild(varianteContainer);
        });
    }


    function ajouterVariante() {
        let index = document.querySelectorAll(".card-in-card").length;

        let inputNom = document.createElement("input");
        inputNom.type = "text";
        inputNom.className = "input";
        inputNom.placeholder = "Couleur, Taille, etc";
        inputNom.name = "shop_item_variant_name[" + index + "]";
        inputNom.required = true;
        let labelNom = document.createElement("label");
        labelNom.innerHTML = "Nom<span style='color: red'>*</span> : ";

        // Créer un bouton de suppression
        let boutonSupprimer = document.createElement("a");
        boutonSupprimer.textContent = "Supprimer la variante";
        boutonSupprimer.className = "btn-center btn-danger text-center";
        boutonSupprimer.style.cursor = "pointer";
        boutonSupprimer.onclick = function() {
            // Supprimer le conteneur de variante lors du clic sur le bouton
            document.getElementById("variantsContainer").removeChild(varianteContainer);
        };

        let inputValeur = document.createElement("input");
        inputValeur.className = "input";
        inputValeur.name = "shop_item_variant_value[" + index + "][]";
        inputValeur.placeholder = "Rouge";
        inputValeur.required = true;

        let labelValeurDiv = document.createElement("div")
        labelValeurDiv.className = "flex justify-between";

        let labelValeur = document.createElement("label");
        labelValeur.innerHTML = "Valeur<span style='color: red'>*</span> : ";

        // Ajouter un bouton "Ajouter une valeur"
        let boutonAjouterValeur = document.createElement("a");
        boutonAjouterValeur.textContent = "+ Ajouter une valeur";
        boutonAjouterValeur.className = "text-success font-bold";
        boutonAjouterValeur.type = "button";
        boutonAjouterValeur.onclick = function() {
            ajouterValeur(valueContainer, index);
        };

        labelValeurDiv.appendChild(labelValeur);
        labelValeurDiv.appendChild(boutonAjouterValeur);

        // Créer un conteneur pour les champs de variante
        let varianteContainer = document.createElement("div");
        varianteContainer.setAttribute("data-index", index);
        varianteContainer.className = "card-in-card p-3 mt-2 mb-4";
        let row = document.createElement("div");
        row.className = "grid-4 border-2 rounded-lg p-4 dark:border-gray-700";
        let nameContainer = document.createElement("div");
        let valueContainer = document.createElement("div");
        valueContainer.className = "col-span-3";

        varianteContainer.appendChild(row);
        row.appendChild(nameContainer);
        nameContainer.appendChild(labelNom);
        nameContainer.appendChild(inputNom);
        nameContainer.appendChild(boutonSupprimer);
        row.appendChild(valueContainer);
        valueContainer.appendChild(labelValeurDiv);
        valueContainer.appendChild(inputValeur);

        // Ajouter le conteneur au conteneur principal
        document.getElementById("variantsContainer").appendChild(varianteContainer);

        // Ajouter le premier champ de valeur
        ajouterValeur(valueContainer, index);


        function ajouterValeur(container, parentIndex) {
            let inputDiv = document.createElement("div");
            inputDiv.className = "flex gap-2 item-center mt-2";

            let inputValeur = document.createElement("input");
            inputValeur.className = "input";
            inputValeur.name = "shop_item_variant_value[" + parentIndex + "][]";
            inputValeur.placeholder = "Vert";
            inputValeur.required = true;

            let boutonSupprimerValeur = document.createElement("button");
            boutonSupprimerValeur.textContent = "x";
            boutonSupprimerValeur.className = "btn-danger-sm h-fit";
            boutonSupprimerValeur.type = "button";
            boutonSupprimerValeur.onclick = function() {
                // Supprimer le champ de valeur lors du clic sur le bouton
                container.removeChild(inputDiv);
                inputDiv.removeChild(inputValeur);
                inputDiv.removeChild(boutonSupprimerValeur);
            };

            container.appendChild(inputDiv);
            inputDiv.appendChild(inputValeur);
            inputDiv.appendChild(boutonSupprimerValeur);
        }
    }
</script>

<script>
    let inputElement = document.querySelector('input[name="shop_item_price"]');

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
    document.addEventListener("DOMContentLoaded", function() {
        const typeSelect = document.getElementById("type");
        const paymentSelect = document.getElementById("payment");
        const moneyOption = paymentSelect.querySelector('option[data-is-money="true"]');

        function updatePaymentOptions() {
            const selectedType = typeSelect.value;

            // Désactiver toutes les options
            for (let option of paymentSelect.options) {
                option.disabled = false;
                option.hidden = false;
            }

            if (selectedType === "0") { // Si le type est physique
                // Activer uniquement l'option "money"
                for (let option of paymentSelect.options) {
                    if (option !== moneyOption) {
                        option.disabled = true;
                        option.hidden = true;
                    }
                }
                paymentSelect.value = moneyOption.value; // Sélectionner l'option "money"
            }
        }

        typeSelect.addEventListener("change", updatePaymentOptions);
        updatePaymentOptions(); // Appeler la fonction au chargement de la page
    });
</script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        updateRewardMethods();
        afficherChamps();

        const selectTypeElement = document.getElementById('type');
        const selectVirtualElement = document.getElementById('virtual_type_selected');

        selectTypeElement.addEventListener('change', afficherChamps);
        selectVirtualElement.addEventListener('change', updateRewardMethods);
    });

    function afficherChamps() {
        const choix = document.getElementById("type").value;
        const typePhysique = document.getElementById("typePhysique");
        const typeVirtuel = document.getElementById("typeVirtuel");

        cacherEtDesactiver(typePhysique);
        cacherEtDesactiver(typeVirtuel);

        if (choix === "0") {
            montrerEtActiver(typePhysique);
        } else if (choix === "1") {
            montrerEtActiver(typeVirtuel);
            updateRewardMethods(); // Mettre à jour les méthodes de récompense seulement si le type est virtuel
        }
    }

    function cacherEtDesactiver(element) {
        element.style.display = "none";
        const champs = element.querySelectorAll("input, select, textarea");
        champs.forEach(champ => {
            champ.disabled = true;
        });
    }

    function montrerEtActiver(element) {
        element.style.display = "block";
        const champs = element.querySelectorAll("input, select, textarea");
        champs.forEach(champ => {
            champ.disabled = false;
        });
    }

    function updateRewardMethods() {
        const selectTypeElement = document.getElementById('type');
        const selectElement = document.getElementById('virtual_type_selected');
        const rewardMethods = document.querySelectorAll('.virtual-method');

        if (selectTypeElement.value !== "1") {
            return; // Ne rien faire si le type n'est pas virtuel
        }

        rewardMethods.forEach(method => {
            method.style.display = 'none';
            disableFormElements(method, true);
        });

        const selectedValue = selectElement.value;
        const selectedMethod = document.getElementById('method-' + selectedValue);
        if (selectedMethod) {
            selectedMethod.style.display = 'block';
            disableFormElements(selectedMethod, false);
        }
    }

    function disableFormElements(container, disable) {
        const elements = container.querySelectorAll('input, select, textarea, button, fieldset, optgroup, option, datalist, output');
        elements.forEach(element => {
            element.disabled = disable;
            if (disable && element.hasAttribute('required')) {
                element.setAttribute('data-required', 'true');
                element.required = false;
            } else if (!disable && element.getAttribute('data-required') === 'true') {
                element.setAttribute('required', 'true');
                element.removeAttribute('data-required');
            }
        });
    }
</script>

<script type="text/javascript">
    let i = document.querySelectorAll('#img_div .col-12').length;

    function addImg() {
        let input = document.createElement('input');
        let div = document.createElement('div');
        let div_in_div = document.createElement('div');
        let btn_div = document.createElement('div');
        let img = document.createElement('img');
        let btnDelete = document.createElement('button');

        input.type = "file";
        input.accept = "image/png, image/jpg, image/jpeg, image/webp, image/gif";
        input.name = 'image-' + i;
        input.id = 'image-' + i;
        input.style.display = 'none';

        input.click();

        input.onchange = evt => {
            const [file] = input.files;
            if (file) {
                img.src = URL.createObjectURL(file);
                div.className = "col-12 col-lg-3 border-4 border-dashed rounded-lg dark:border-gray-700 relative h-fit";
                div.id = 'delete-' + i;
                div_in_div.className = "card-in-card p-2";
                img.className = "w-50 mx-auto";
                btnDelete.type = "button";
                btnDelete.innerHTML = '<i class="fa-solid fa-trash"></i>';
                btnDelete.className = "absolute top-0 right-0 bg-danger text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex";

                let firstDiv = document.getElementById('img_div').appendChild(div);
                firstDiv.appendChild(div_in_div);
                div_in_div.appendChild(img);
                div_in_div.appendChild(input);
                div_in_div.appendChild(btn_div);
                btn_div.appendChild(btnDelete);
                btnDelete.onclick = evt => {
                    let parent = div.parentNode;
                    div.remove();
                    updateOrderLabels(parent);

                    // Décrémenter numberOfImage immédiatement
                    let number_Image_post = document.getElementById('numberOfImage');
                    number_Image_post.value = parseInt(number_Image_post.value) - 1;
                }

                let orderLabel = document.createElement('span');
                orderLabel.innerText = i + 1;
                orderLabel.className = 'image-order-label absolute top-0 left-0 bg-blue-500 text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex';

                let btnUp = document.createElement('button');
                btnUp.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
                btnUp.className = 'absolute bottom-0 left-0 bg-success text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex';
                btnUp.onclick = () => moveImage(div, 'up');
                btnUp.type = "button";

                let btnDown = document.createElement('button');
                btnDown.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
                btnDown.className = 'absolute bottom-0 right-0 bg-success text-white font-bold text-xl border-2 rounded-full justify-center items-center w-10 h-10 inline-flex';
                btnDown.onclick = () => moveImage(div, 'down');
                btnDown.type = "button";

                let inputOrder = document.createElement('input');
                inputOrder.type = 'hidden';
                inputOrder.name = 'order-' + i;
                inputOrder.value = i;

                div_in_div.appendChild(orderLabel);
                div_in_div.appendChild(inputOrder);
                btn_div.appendChild(btnUp);
                btn_div.appendChild(btnDelete);
                btn_div.appendChild(btnDown);

                // Incrémenter numberOfImage immédiatement après ajout
                let number_Image_post = document.getElementById('numberOfImage');
                number_Image_post.value = parseInt(number_Image_post.value) + 1;

                i++;
            }
        }
    }

    function moveImage(imageDiv, direction) {
        let parent = imageDiv.parentNode;
        if (direction === 'up' && imageDiv.previousElementSibling) {
            parent.insertBefore(imageDiv, imageDiv.previousElementSibling);
        } else if (direction === 'down' && imageDiv.nextElementSibling) {
            parent.insertBefore(imageDiv.nextElementSibling, imageDiv);
        }
        updateOrderLabels(parent);
    }

    function deleteImage(imageDiv) {
        let parent = imageDiv.parentNode;
        imageDiv.remove();
        updateOrderLabels(parent);

        // Décrémenter numberOfImage immédiatement
        let number_Image_post = document.getElementById('numberOfImage');
        number_Image_post.value = parseInt(number_Image_post.value) - 1;
    }

    function updateOrderLabels(parentDiv) {
        let children = parentDiv.children;
        for (let j = 0; j < children.length; j++) {
            let orderLabel = children[j].querySelector('.image-order-label');
            let inputOrder = children[j].querySelector('input[type="hidden"]');
            let inputFile = children[j].querySelector('input[type="file"]');
            let existingImage = children[j].querySelector('input[name^="image-existing-"]');

            if (orderLabel) {
                orderLabel.innerText = j + 1;
            }
            if (inputOrder) {
                inputOrder.name = 'order-' + j;
                inputOrder.value = j;
            }
            if (inputFile) {
                inputFile.name = 'image-' + j;
                inputFile.id = 'image-' + j;
            }
            if (existingImage) {
                existingImage.name = 'image-existing-' + j;
                existingImage.id = 'image-existing-' + j;
            }
        }
        i = children.length; // Mettre à jour le compteur `i` après réordonnancement
    }
</script>