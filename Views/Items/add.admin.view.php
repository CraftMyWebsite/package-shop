<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-envelope"></i> <span
                class="m-lg-auto">Nouvel article</span></h3>
    <div class="buttons">
        <button form="addItem" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.add") ?></button>
    </div>
</div>

<form id="addItem" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="row">
                            <div class="col-12 col-lg-4">
                                <h6>Nom<span style="color: red">*</span> :</h6>
                                <input type="text" class="form-control" name="shop_item_name" required>
                            </div>
                            <div class="col-12 col-lg-8">
                                <h6>Déscription courte<span style="color: red">*</span> :</h6>
                                <input type="text" class="form-control" name="shop_item_short_desc" required>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Déscription détailler<span style="color: red">*</span> :</h6>
                            <textarea  class="tinymce" name="shop_item_description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div id="typeNeeds" class="row">
                    </div>
                    <hr>

                    <div class="d-flex flex-wrap justify-content-between">
                        <div><h6>Variantes (optionnel) : </h6></div>
                        <div class="buttons">
                            <button class="btn btn-primary" type="button" onclick="ajouterVariante()">Ajouter une variante</button>
                        </div>
                    </div>
                    <p>Les variantes sont assez pratique quand vous souhaitez ajouter un article qui peut avoir plusieurs configurations disponnible sans pour autant créer autant d'article que vous avez de variantes de celui-ci, à vous de créer autant de variantes que vous le souhaiter. (Couleur,Taille,Poids ...)<br> Cependant, noter bien que si vous utilisez les variantes celle-ci devienne obligatoire pour vos consommateurs.</p>
                    <div id="variantsContainer">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6>Catégorie<span style="color: red">*</span> :</h6>
                            <select name="shop_category_id" class="form-select">
                                <?php foreach ($categoryModel->getShopCategories() as $cat): ?>
                                    <option value="<?= $cat->getId() ?>"> <?= $cat->getName() ?> </option>
                                    <?php foreach ($categoryModel->getSubsCat($cat->getId()) as $subCategory): ?>
                                        <option value="<?= $subCategory->getSubCategory()->getId() ?>"> <?php echo str_repeat("      ", $subCategory->getDepth()) . " ↪ ". $subCategory->getSubCategory()->getName() ?></option>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Tags :</h6>
                            <input type="text" class="form-control" name="">
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Prix :</h6>
                            <input type="text" class="form-control" name="shop_item_price" placeholder="19.99">
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Type<span style="color: red">*</span> :</h6>
                            <select id="type" class="form-select super-choice" name="shop_item_type" onchange="afficherChamps()" required>
                                <option value="1">Virtuel</option>
                                <option selected value="0">Physique</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6>Stock :</h6>
                            <input type="number" class="form-control" name="shop_item_default_stock"
                                   placeholder="0">
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Limite d'achat global :</h6>
                            <input type="number" class="form-control" name="shop_item_global_limit"
                                   placeholder="0">
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Limite d'achat par utilisateur :</h6>
                            <input type="number" class="form-control" name="shop_item_user_limit"
                                   placeholder="0">
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Limite d'achat par commande :</h6>
                            <input type="number" class="form-control" name="shop_item_by_order_limit"
                                   placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Galerie</h4>
                </div>
                <div class="card-body">

                    <div onclick="addImg();" class="card-in-card mb-4" style="cursor: pointer">
                        <div class="text-center" style="padding-top: 1rem">
                            <h2><i class="text-success fa-solid fa-circle-plus fa-xl"></i></h2>
                            <p class="mt-2">Ajouter une image</p>
                        </div>
                    </div>

                    <div id="img_div" class="row"></div>

                    <input hidden="" type="text" name="numberOfImage" id="numberOfImage">
                </div>
            </div>
        </div>
    </section>
</form>

<script>
    function ajouterVariante() {
        let index = document.querySelectorAll(".card-in-card").length;

        let inputNom = document.createElement("input");
        inputNom.type = "text";
        inputNom.className = "form-control";
        inputNom.placeholder = "Couleur, Taille, etc";
        inputNom.name = "shop_item_variant_name[" + index + "]";
        inputNom.required = true;
        let labelNom = document.createElement("h6");
        labelNom.innerHTML = "Nom<span style='color: red'>*</span> : ";

        // Créer un bouton de suppression
        let boutonSupprimer = document.createElement("a");
        boutonSupprimer.textContent = "Supprimer la variante";
        boutonSupprimer.className = "text-danger font-bold";
        boutonSupprimer.style.cursor = "pointer";
        boutonSupprimer.onclick = function() {
            // Supprimer le conteneur de variante lors du clic sur le bouton
            document.getElementById("variantsContainer").removeChild(varianteContainer);
        };

        let inputValeur = document.createElement("input");
        inputValeur.className = "form-control";
        inputValeur.name = "shop_item_variant_value[" + index + "][]";
        inputValeur.placeholder = "Rouge";
        inputValeur.required = true;

        let labelValeurDiv = document.createElement("div")
        labelValeurDiv.className = "d-flex justify-content-between";

        let labelValeur = document.createElement("h6");
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
        row.className = "row";
        let nameContainer = document.createElement("div");
        nameContainer.className = "col-12 col-lg-4";
        let valueContainer = document.createElement("div");
        valueContainer.className = "col-12 col-lg-8";

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
            inputDiv.className = "input-group mt-2";

            let inputValeur = document.createElement("input");
            inputValeur.className = "form-control";
            inputValeur.name = "shop_item_variant_value[" + parentIndex + "][]";
            inputValeur.placeholder = "Vert";
            inputValeur.required = true;

            let boutonSupprimerValeur = document.createElement("button");
            boutonSupprimerValeur.textContent = "X";
            boutonSupprimerValeur.className = "btn btn-sm btn-danger";
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

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function () {
        // Appeler la fonction au chargement de la page
        afficherChamps();
    });

    function afficherChamps() {
        let choix = document.getElementById("type").value;
        let champsDynamiquesDiv = document.getElementById("typeNeeds");

        // Effacer les champs précédents
        champsDynamiquesDiv.innerHTML = "";

        if (choix === "0") {
            let weightDiv = document.createElement("div");
            weightDiv.className = "col-12 col-lg-6";
            let lengthDiv = document.createElement("div");
            lengthDiv.className = "col-12 col-lg-6";
            let widthDiv = document.createElement("div");
            widthDiv.className = "col-12 col-lg-6";
            let heightDiv = document.createElement("div");
            heightDiv.className = "col-12 col-lg-6";

            let weightTitle = document.createElement("h6");
            weightTitle.innerHTML = "Poids<span style='color: red'>*</span> : <small>(en gramme)</small>";
            let weightInput = document.createElement("input");
            weightInput.type = "text";
            weightInput.required = true;
            weightInput.className = "form-control";
            weightInput.placeholder = "150.00";
            weightInput.name = "shop_item_weight";

            let lengthTitle = document.createElement("h6");
            lengthTitle.innerHTML = "Largeur : <small>(en cm)</small>";
            let lengthInput = document.createElement("input");
            lengthInput.type = "text";
            lengthInput.className = "form-control";
            lengthInput.placeholder = "150.00";
            lengthInput.name = "shop_item_length";

            let widthTitle = document.createElement("h6");
            widthTitle.innerHTML = "Longueur : <small>(en cm)</small>";
            let widthInput = document.createElement("input");
            widthInput.type = "text";
            widthInput.className = "form-control";
            widthInput.placeholder = "150.00";
            widthInput.name = "shop_item_width";

            let heightTitle = document.createElement("h6");
            heightTitle.innerHTML = "Hauteur : <small>(en cm)</small>";
            let heightInput = document.createElement("input");
            heightInput.type = "text";
            heightInput.className = "form-control";
            heightInput.placeholder = "150.00";
            heightInput.name = "shop_item_height";


            champsDynamiquesDiv.appendChild(weightDiv);
            weightDiv.appendChild(weightTitle);
            weightDiv.appendChild(weightInput);
            champsDynamiquesDiv.appendChild(lengthDiv);
            lengthDiv.appendChild(lengthTitle);
            lengthDiv.appendChild(lengthInput);
            champsDynamiquesDiv.appendChild(widthDiv);
            widthDiv.appendChild(widthTitle);
            widthDiv.appendChild(widthInput);
            champsDynamiquesDiv.appendChild(heightDiv);
            heightDiv.appendChild(heightTitle);
            heightDiv.appendChild(heightInput);
        } else if (choix === "1") {
            let label = document.createElement("label");
            label.innerHTML = "Champ Virtuel 1 : ";
            let input = document.createElement("input");
            input.type = "text";
            input.name = "virtuel1";
            champsDynamiquesDiv.appendChild(label);
            champsDynamiquesDiv.appendChild(input);
        }
    }
</script>

<script type="text/javascript">
    let i = 0;

    function addImg() {
        let input = document.createElement('input');
        let div = document.createElement('div');
        let div_in_div = document.createElement('div');
        let btn_div = document.createElement('div');
        let img = document.createElement('img');
        let btnDelete = document.createElement('button');
        let label = document.createElement('label');

        input.type = "file";
        input.accept = "image/png, image/jpg, image/jpeg, image/webp, image/gif"
        input.name = 'image-' + i;
        input.id = 'image-' + i;
        input.style.display = 'none';
        input.click();

        input.onchange = evt => {
            const [file] = input.files
            if (file) {
                img.src = URL.createObjectURL(file)
                div.className = "col-12 col-lg-6";
                div.id = 'delete-' + i;
                btn_div.className = "d-flex flex-wrap justify-content-between";
                div_in_div.className = "card-in-card p-2";
                img.className = "w-50 mx-auto";
                btnDelete.type = "button";
                btnDelete.innerText = "<?= LangManager::translate("core.btn.delete") ?>";
                btnDelete.className = "btn btn-danger mt-2";
                label.htmlFor = 'image-' + i;
                label.innerText = "<?= LangManager::translate("core.btn.edit") ?>"
                label.className = "btn btn-primary mt-2";

                let firstDiv = document.getElementById('img_div').appendChild(div);
                firstDiv.appendChild(div_in_div);
                div_in_div.appendChild(img);
                div_in_div.appendChild(input);
                div_in_div.appendChild(btn_div);
                btn_div.appendChild(label);
                btn_div.appendChild(btnDelete);
                btnDelete.onclick = evt => {
                    input.remove()
                    div.remove()
                    img.remove()
                    btnDelete.remove()
                }
                i++;
            }
        }

        let number_Image_post = document.getElementById('numberOfImage')
        number_Image_post.value = i + 1;
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