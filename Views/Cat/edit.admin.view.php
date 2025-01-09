<?php

/* @var \CMW\Entity\Shop\Categories\ShopCategoryEntity $category */
/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

$title = LangManager::translate('shop.views.cat.edit.title', ['cat_name' => $category->getName()]);
$description = '';
?>
<div class="page-title">
    <h3><i class="fa-solid fa-book"></i> <?= LangManager::translate('shop.views.cat.edit.title', ['cat_name' => $category->getName()]) ?></h3>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>

<div class="center-flex">
    <div class="flex-content">
        <form class="card" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <label for="name"><?= LangManager::translate('shop.views.cat.addSubCat.name') ?><span style="color: red">*</span> :</label>
            <div class="input-group">
                <i class="fa-solid fa-heading"></i>
                <input type="text" id="name" name="name" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.placeholderName') ?>" value="<?= $category->getName() ?>">
            </div>
            <label><?= LangManager::translate('shop.views.cat.addSubCat.icon') ?></label>
            <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.iconPlaceholder') ?>" data-value="<?= $category->getIcon() ?>"></div>
            <label for="description"><?= LangManager::translate('shop.views.cat.addSubCat.desc') ?></label>
            <div class="input-group">
                <i class="fa-solid fa-paragraph"></i>
                <input type="text" id="description" name="description" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.descPlaceholder') ?>" value="<?= $category->getDescription() ?>">
            </div>
            <label for="move"><?= LangManager::translate('shop.views.cat.edit.move') ?></label>
            <select id="move" name="move" class="form-select">
                <?php if (!is_null($category->getParent())): ?><option value=""><?= LangManager::translate('shop.views.cat.edit.to') ?></option><?php endif; ?>
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