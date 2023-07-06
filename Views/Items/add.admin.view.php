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

<section class="row">
    <h4>Nouvelle article dans <?= $category->getName() ?></h4>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Informations</h4>
            </div>
            <div class="card-body">
                <form action="items/add_item" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="modal-body">
                        <input type="hidden" name="shop_category_id" value="<?= $category->getId() ?>">
                        <div class="row">
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Nom :</h6>
                                <input type="text" class="form-control" name="shop_item_name" required>
                            </div>
                            <div class="col-12 col-lg-6 mt-2">
                                <h6>Type :</h6>
                                <select class="form-select" name="shop_item_type" required>
                                    <option value="0">Physique</option>
                                    <option value="1">Virtuel</option>
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
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                            <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h4>Galerie</h4>
            </div>
            <div class="card-body">
                <form >
                    <div class="cursor-pointer">
                        <label for="imgInp" style="width: 200px; height: 200px">
                            <img class="cursor-pointer" style="width: 150px;border: solid 3px green;" id="blah" src="https://image.noelshack.com/fichiers/2023/27/4/1688654674-2023-07-06-16h44-02.png" alt="your image" />
                        </label>
                    </div>
                    <input hidden="" accept="image/*" type='file' id="imgInp" />
                </form>

            </div>
        </div>
    </div>

</section>

<script>
    imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
            blah.src = URL.createObjectURL(file)
        }
    }
</script>
