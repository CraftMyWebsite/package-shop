<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */

$title = LangManager::translate('shop.views.cat.manage.cat');
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-layer-group"></i> <?= LangManager::translate('shop.views.cat.manage.cat') ?></h3>
    <button data-modal-toggle="modal-add-cat" class="btn-primary" type="button"><?= LangManager::translate('shop.views.cat.manage.create') ?></button>
</div>

<?php if (!MailModel::getInstance()->getConfig() !== null && !MailModel::getInstance()->getConfig()->isEnable()): ?>
    <div class="alert-danger">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif;?>


<?php if ($categoryModel->getShopCategories()): ?>
<?php foreach ($categoryModel->getShopCategories() as $category): ?>
<div class="card mb-6">
    <div class="flex justify-between">
        <p>
            <small><i class="text-secondary fa-solid fa-circle-dot"></i></small>
            <?= $category->getFontAwesomeIcon() ?> <b><?= $category->getName() ?></b> <?= $category->getDescription() ?> (<?= $category->countItemsInCat() ?> <?= LangManager::translate('shop.views.cat.manage.items') ?>)
        </p>
        <div class="space-x-2">
            <a target="_blank"
               href="<?= $category->getCatLink() ?>"><i data-bs-toggle="tooltip"
                                                        title="<?= LangManager::translate('shop.views.cat.manage.tooltip.render') ?>"
                                                        class="me-3 fa-solid fa-up-right-from-square"></i></a>
            <a href="cat/addSubCat/<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.subCat') ?>"
                   class="text-success me-3 fa-solid fa-circle-plus"></i>
            </a>
            <a href="cat/edit/<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.edit') ?>"
                   class="text-info me-3 fas fa-edit"></i>
            </a>
            <button type="button" data-modal-toggle="modal-delete-<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.delete') ?>"
                   class="text-danger fas fa-trash-alt"></i>
            </button>

        </div>
    </div>
    <?php foreach ($categoryModel->getSubsCat($category->getId()) as $subCategory): ?>
        <div class="flex justify-between">
            <p style="padding-left: <?= 1 + $subCategory->getDepth() * 1 ?>rem"
                class="text-bold-500">
                <small><i
                        class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small>
                <?= $subCategory->getSubCategory()->getFontAwesomeIcon() ?> <b><?= $subCategory->getSubCategory()->getName() ?></b> <?= $subCategory->getSubCategory()->getDescription() ?> (<?= $subCategory->getSubCategory()->countItemsInCat() ?> <?= LangManager::translate('shop.views.cat.manage.items') ?>)
            </p>
            <div class="space-x-2">
                <a href="items/cat/<?= $subCategory->getSubCategory()->getId() ?>"><i
                        data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.items') ?>"
                        class="me-3 fa-solid fa-eye"></i></a>
                <a target="_blank"
                   href="<?= $subCategory->getSubCategory()->getCatLink() ?>"><i
                        data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.render') ?>"
                        class="me-3 fa-solid fa-up-right-from-square"></i></a>
                <a href="cat/addSubCat/<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.subCat') ?>"
                       class="text-success me-3 fas fa-circle-plus"></i>
                </a>
                <a href="cat/edit/<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.edit') ?>"
                       class="text-info me-3 fas fa-edit"></i>
                </a>
                <button type="button" data-modal-toggle="modal-deletee-<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="<?= LangManager::translate('shop.views.cat.manage.tooltip.delete') ?>"
                       class="text-danger fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <!--
        --MODAL SUPPRESSION SOUS CAT--
        -->
        <div id="modal-deletee-<?= $subCategory->getSubCategory()->getId() ?>" class="modal-container">
            <div class="modal">
                <div class="modal-header-danger">
                    <h6><?= LangManager::translate('shop.views.cat.manage.modalDelete.title', ['cat_name' => $subCategory->getSubCategory()->getName()]) ?></h6>
                    <button type="button" data-modal-hide="modal-deletee-<?= $subCategory->getSubCategory()->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p><?= LangManager::translate('shop.views.cat.manage.modalDelete.text') ?></p>
                </div>
                <div class="modal-footer">
                    <a type="button" href="cat/delete/<?= $subCategory->getSubCategory()->getId() ?>"
                       class="btn-danger ml-1"><?= LangManager::translate('core.btn.delete') ?>
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
        <!--
        --MODAL SUPPRESSION CAT--
        -->
        <div id="modal-delete-<?= $category->getId() ?>" class="modal-container">
            <div class="modal">
                <div class="modal-header-danger">
                    <h6><?= LangManager::translate('shop.views.cat.manage.modalDelete.title', ['cat_name' => $category->getName()]) ?></h6>
                    <button type="button" data-modal-hide="modal-delete-<?= $category->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p><?= LangManager::translate('shop.views.cat.manage.modalDelete.text') ?></p>
                </div>
                <div class="modal-footer">
                    <a type="button" href="cat/delete/<?= $category->getId() ?>"
                       class="btn btn-danger ml-1"><?= LangManager::translate('core.btn.delete') ?>
                    </a>
                </div>
            </div>
        </div>
<?php endforeach; ?>
<?php else: ?>
<div class="card p-4">
    <div class="alert alert-info"><?= LangManager::translate('shop.views.cat.manage.createBefore') ?>
    </div>
</div>
<?php endif ?>

<!--
    --MODAL AJOUT CATEGORIE--
-->
<div id="modal-add-cat" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6><?= LangManager::translate('shop.views.cat.manage.modalAdd.title') ?></h6>
            <button type="button" data-modal-hide="modal-add-cat"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="cat/add">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="name"><?= LangManager::translate('shop.views.cat.addSubCat.name') ?><span style="color: red">*</span> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="name" name="name" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.placeholderName') ?>">
                </div>
                <label><?= LangManager::translate('shop.views.cat.addSubCat.icon') ?></label>
                <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.iconPlaceholder') ?>" data-value=""></div>
                <label for="description"><?= LangManager::translate('shop.views.cat.addSubCat.desc') ?></label>
                <div class="input-group">
                    <i class="fa-solid fa-paragraph"></i>
                    <input type="text" id="description" name="description" placeholder="<?= LangManager::translate('shop.views.cat.addSubCat.descPlaceholder') ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary">
                    <?= LangManager::translate('core.btn.add') ?>
                </button>
            </div>
        </form>
    </div>
</div>