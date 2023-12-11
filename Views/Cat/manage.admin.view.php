<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Shop\ShopCategoriesModel $categoryModel */

$title = "";
$description = "";

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-layer-group"></i> <span class="m-lg-auto">Catégories</span></h3>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php if ($categoryModel->getShopCategories()): ?>
                    <?php foreach ($categoryModel->getShopCategories() as $category): ?>
                        <div class="card-in-card table-responsive mb-4">
                            <table class="table-borderless table table-hover mt-1">
                                <thead>
                                <tr>
                                    <th id="categorie-<?= $category->getId() ?>">
                                        <small><i class="text-secondary fa-solid fa-circle-dot"></i></small>
                                        <?= $category->getFontAwesomeIcon() ?> <?= $category->getName() ?> <?= $category->getDescription() ?>
                                    </th>
                                    <th class="text-end">
                                        <a href="item/cat/<?= $category->getSlug() ?>"><i data-bs-toggle="tooltip"
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
                                               class="text-primary me-3 fas fa-edit"></i>
                                        </a>
                                        <a type="button" data-bs-toggle="modal"
                                           data-bs-target="#delete-<?= $category->getId() ?>">
                                            <i data-bs-toggle="tooltip" title="Supprimé"
                                               class="text-danger fas fa-trash-alt"></i>
                                        </a>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($categoryModel->getSubsCat($category->getId()) as $subCategory): ?>
                                    <tr id="cat-<?= $subCategory->getSubCategory()->getId() ?>">
                                        <td style="padding-left: <?= 1 + $subCategory->getDepth() * 2 ?>rem"
                                            class="text-bold-500">
                                            <small><i
                                                    class="text-secondary fa-solid fa-turn-up fa-rotate-90"></i></small>
                                            <?= $subCategory->getSubCategory()->getFontAwesomeIcon() ?> <?= $subCategory->getSubCategory()->getName() ?> <?= $subCategory->getSubCategory()->getDescription() ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="item/cat/<?= $subCategory->getSubCategory()->getSlug() ?>"><i
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
                                                   class="text-primary me-3 fas fa-edit"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#deletee->">
                                                <i data-bs-toggle="tooltip" title="Supprimé"
                                                   class="text-danger fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">Merci de créer une catégorie pour commencer à utiliser la Boutique
                    </div>
                <?php endif ?>
                <div class="divider">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#add-cat">
                        <div class="divider-text"><i class="fa-solid fa-circle-plus"></i> Ajouter une catégorie</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!--
    --MODAL AJOUT CATEGORIE--
-->
<div class="modal fade " id="add-cat" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white"
                    id="myModalLabel160">Nouvelle catégorie</h5>
            </div>
            <form method="post" action="cat/add">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="modal-body">

                    <h6>Nom<span style="color: red">*</span> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="name" required placeholder="Vêtement">
                        <div class="form-control-icon">
                            <i class="fas fa-heading"></i>
                        </div>
                    </div>
                    <h6>Icon :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="icon" placeholder="fa-solid fa-shirt">
                        <div class="form-control-icon">
                            <i class="fas fa-icons"></i>
                        </div>
                        <small class="form-text">Retrouvez la liste des icones sur le site de <a
                                href="https://fontawesome.com/search?o=r&m=free"
                                target="_blank">FontAwesome.com</a></small>
                    </div>
                    <h6>Description :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control" name="description"
                               placeholder="Des vêtements">
                        <div class="form-control-icon">
                            <i class="fas fa-paragraph"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x"></i>
                        <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                    </button>
                    <button type="submit" class="btn btn-primary ml-1">
                        <i class="bx bx-check"></i>
                        <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>