<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Model\Shop\ShopCategoriesModel $category */
/* @var CMW\Model\Shop\ShopItemsModel $items */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-envelope"></i> <span
                class="m-lg-auto">Ajouter un article dans <?= $category->getName() ?></span></h3>
    <div class="buttons">
        <button form="addItem" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.add") ?></button>
    </div>
</div>

<form id="addItem" action="<?= $category->getId() ?>" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <section class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Informations</h4>
                </div>
                <div class="card-body">
                        <input type="hidden" name="shop_category_id" value="<?= $category->getId() ?>">
                        <div class="row">
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Nom :</h6>
                                <input type="text" class="form-control" name="shop_item_name" required>
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Type :</h6>
                                <select class="form-select super-choice" name="shop_item_type" required>
                                    <option value="1">Virtuel</option>
                                    <option value="0">Physique</option>
                                </select>
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Stock :</h6>
                                <input type="number" class="form-control" name="shop_item_default_stock"
                                       placeholder="0">
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Limite d'achat :</h6>
                                <input type="number" class="form-control" name="shop_item_global_limit"
                                       placeholder="0">
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Limite d'achat par utilisateur :</h6>
                                <input type="number" class="form-control" name="shop_item_user_limit"
                                       placeholder="0">
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Prix :</h6>
                                <input type="text" class="form-control" name="shop_item_price" placeholder="19.99">
                            </div>
                            <div class="col-12 mt-2">
                                <h6>Description :</h6>
                                <textarea class="tinymce" name="shop_item_description"></textarea>
                            </div>
                        </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
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

                let fisrtDiv = document.getElementById('img_div').appendChild(div);
                fisrtDiv.appendChild(div_in_div);
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
        number_Image_post.value = i;
    }
</script>

