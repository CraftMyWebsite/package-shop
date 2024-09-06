<?php

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity $category */
/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = 'Catégorie';
$description = 'Édition catégorie';
?>
<div class="page-title">
    <h3><i class="fa-solid fa-book"></i> Édition de <?= $category->getName() ?></h3>
</div>

<div class="center-flex">
    <div class="flex-content">
        <form class="card" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <label for="name">Nom<span style="color: red">*</span> :</label>
            <div class="input-group">
                <i class="fa-solid fa-heading"></i>
                <input type="text" id="name" name="name" placeholder="Pantalon" value="<?= $category->getName() ?>">
            </div>
            <label>Icon : <small>(Optionnel)</small></label>
            <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="Sélectionner un icon" data-value="<?= $category->getIcon() ?>"></div>
            <label for="description">Description : <small>(Optionnel)</small></label>
            <div class="input-group">
                <i class="fa-solid fa-paragraph"></i>
                <input type="text" id="description" name="description" placeholder="Des vêtements" value="<?= $category->getDescription() ?>">
            </div>
            <label for="move">Déplacer vers :</label>
            <select id="move" name="move" class="form-select">
                <?php if (!is_null($category->getParent())): ?><option value="">Catégorie principale</option><?php endif; ?>
                <?php foreach ($categoryModel->getShopCategories() as $cat): ?>
                    <option value="<?= $cat->getId() ?>" <?= ($cat->getName() === $category->getName() ? 'selected' : '') ?>> <?php if ($cat->getName() === $category->getName()) { echo 'Ne pas déplacer'; } else { echo $cat->getName(); } ?> </option>
                    <?php foreach ($categoryModel->getSubsCat($cat->getId()) as $subCategory): ?>
                        <option value="<?= $subCategory->getSubCategory()->getId() ?>" <?= ($subCategory->getSubCategory()->getName() === $category->getName() ? 'selected' : '') ?>> <?php if ($subCategory->getSubCategory()->getName() === $category->getName()) { echo str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $subCategory->getDepth()) . ' ↪ Ne pas déplacer'; } else { echo str_repeat("\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}\u{00A0}", $subCategory->getDepth()) . ' ↪ ' . $subCategory->getSubCategory()->getName(); } ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
            <div class="mt-6">
                <button type="submit" class="btn-center btn-primary">
                    <?= LangManager::translate('core.btn.edit') ?>
                </button>
            </div>
        </form>
    </div>
</div>