<?php

/* @var \CMW\Entity\Shop\ShopCategoryEntity $category */
/* @var CMW\Model\Shop\ShopCategoriesModel $categoryModel */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = "Catégorie";
$description = "Ajouter une sous-catégorie";
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-book"></i> <span
            class="m-lg-auto">Édition de <?= $category->getName() ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <form class="card" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="card-body">
                <h6>Nom<span style="color: red">*</span> :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="name" required
                           placeholder="Vêtement" value="<?= $category->getName() ?>">
                    <div class="form-control-icon">
                        <i class="fas fa-heading"></i>
                    </div>
                </div>
                <h6>Icon :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="icon"
                           placeholder="fa-solid fa-shirt" value="<?= $category->getIcon() ?>">
                    <div class="form-control-icon">
                        <i class="fas fa-icons"></i>
                    </div>
                    <small class="form-text">Retrouvez la liste des icones sur le
                        site de <a href="https://fontawesome.com/search?o=r&m=free"
                                   target="_blank">FontAwesome.com</a></small>
                </div>
                <h6>Déscription :</h6>
                <div class="form-group position-relative has-icon-left">
                    <input type="text" class="form-control" name="description"
                           placeholder="Des vêtements" value="<?= $category->getDescription() ?>">
                    <div class="form-control-icon">
                        <i class="fas fa-paragraph"></i>
                    </div>
                </div>
                <div class="col-12 mt-2 mb-4">
                    <h6>Déplacer vers :</h6>
                    <select name="move" class="form-select">
                        <?php if (!is_null($category->getParent())): ?><option value="">Catégorie principale</option><?php endif; ?>
                        <?php foreach ($categoryModel->getShopCategories() as $cat): ?>
                            <option value="<?= $cat->getId() ?>" <?= ($cat->getName() === $category->getName() ? "selected" : "") ?>> <?php if ($cat->getName() === $category->getName()) { echo "Ne pas déplacer";} else { echo $cat->getName();}?> </option>
                            <?php foreach ($categoryModel->getSubsCat($cat->getId()) as $subCategory): ?>
                                <option value="<?= $subCategory->getSubCategory()->getId() ?>" <?= ($subCategory->getSubCategory()->getName() === $category->getName() ? "selected" : "") ?>> <?php if ($subCategory->getSubCategory()->getName() === $category->getName()) {echo str_repeat("      ", $subCategory->getDepth()) . " ↪ Ne pas déplacer";} else {echo str_repeat("      ", $subCategory->getDepth()) . " ↪ ". $subCategory->getSubCategory()->getName();} ?></option>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-check"></i>
                        <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>