<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */

$title = '';
$description = '';

?>
<div class="page-title">
    <h3><i class="fa-solid fa-layer-group"></i> Catégories</h3>
    <button data-modal-toggle="modal-add-cat" class="btn-primary" type="button">Créer une catégorie</button>
</div>


<?php if ($categoryModel->getShopCategories()): ?>
<?php foreach ($categoryModel->getShopCategories() as $category): ?>
<div class="card mb-6">
    <div class="flex justify-between">
        <p>
            <small><i class="text-secondary fa-solid fa-circle-dot"></i></small>
            <?= $category->getFontAwesomeIcon() ?> <b><?= $category->getName() ?></b> <?= $category->getDescription() ?> (<?= $category->countItemsInCat() ?> articles)
        </p>
        <div class="space-x-2">
            <a href="items/cat/<?= $category->getId() ?>"><i data-bs-toggle="tooltip"
                                                             title="Voir les articles lié"
                                                             class="me-3 fa-solid fa-eye"></i></a>
            <a target="_blank"
               href="<?= $category->getCatLink() ?>"><i data-bs-toggle="tooltip"
                                                        title="Voir le rendue"
                                                        class="me-3 fa-solid fa-up-right-from-square"></i></a>
            <a href="cat/addSubCat/<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="Ajouter une sous catégorie"
                   class="text-success me-3 fa-solid fa-circle-plus"></i>
            </a>
            <a href="cat/edit/<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="Modifier la catégorie"
                   class="text-info me-3 fas fa-edit"></i>
            </a>
            <button type="button" data-modal-toggle="modal-delete-<?= $category->getId() ?>">
                <i data-bs-toggle="tooltip" title="Supprimé"
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
                <?= $subCategory->getSubCategory()->getFontAwesomeIcon() ?> <b><?= $subCategory->getSubCategory()->getName() ?></b> <?= $subCategory->getSubCategory()->getDescription() ?> (<?= $subCategory->getSubCategory()->countItemsInCat() ?> articles)
            </p>
            <div class="space-x-2">
                <a href="items/cat/<?= $subCategory->getSubCategory()->getId() ?>"><i
                        data-bs-toggle="tooltip" title="Voir les articles lié"
                        class="me-3 fa-solid fa-eye"></i></a>
                <a target="_blank"
                   href="<?= $subCategory->getSubCategory()->getCatLink() ?>"><i
                        data-bs-toggle="tooltip" title="Voir le rendue"
                        class="me-3 fa-solid fa-up-right-from-square"></i></a>
                <a href="cat/addSubCat/<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="Ajouter une sous catégorie"
                       class="text-success me-3 fas fa-circle-plus"></i>
                </a>
                <a href="cat/edit/<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="Modifier la catégorie"
                       class="text-info me-3 fas fa-edit"></i>
                </a>
                <button type="button" data-modal-toggle="modal-deletee-<?= $subCategory->getSubCategory()->getId() ?>">
                    <i data-bs-toggle="tooltip" title="Supprimé"
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
                    <h6>Suppression de : <?= $subCategory->getSubCategory()->getName() ?></h6>
                    <button type="button" data-modal-hide="modal-deletee-<?= $subCategory->getSubCategory()->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p>Cette suppression est définitive.</p>
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
                    <h6>Suppression de : <?= $category->getName() ?></h6>
                    <button type="button" data-modal-hide="modal-delete-<?= $category->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p>Cette suppression est définitive.</p>
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
    <div class="alert alert-info">Merci de créer une catégorie pour commencer à utiliser la Boutique
    </div>
</div>
<?php endif ?>

<!--
    --MODAL AJOUT CATEGORIE--
-->
<div id="modal-add-cat" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>Nouvelle catégorie</h6>
            <button type="button" data-modal-hide="modal-add-cat"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="cat/add">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="modal-body">
                <label for="name">Nom<span style="color: red">*</span> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-heading"></i>
                    <input type="text" id="name" name="name" placeholder="Pantalon">
                </div>
                <label>Icon : <small>(Optionnel)</small></label>
                <div class="icon-picker" data-id="icon" data-name="icon" data-label="" data-placeholder="Sélectionner un icon" data-value=""></div>
                <label for="description">Description : <small>(Optionnel)</small></label>
                <div class="input-group">
                    <i class="fa-solid fa-paragraph"></i>
                    <input type="text" id="description" name="description" placeholder="Des vêtements">
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