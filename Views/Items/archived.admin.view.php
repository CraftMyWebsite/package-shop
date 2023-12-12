<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\ShopSettingsModel;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Entity\Shop\ShopCategoryEntity[] $categories */
/* @var CMW\Model\Shop\ShopItemsModel $items */
/* @var CMW\Model\Shop\ShopImagesModel $imagesItem */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <span class="m-lg-auto">Articles archivés</span></h3>
</div>
<div class="alert alert-warning">Les articles archivés sont souvent des articles qui ont déjà était commandé, vous en gardez une trace pour le suivie des commandes client, facture ...<br>Une fois archivés les articles ne sont plus activable</div>

<section>
    <div>
        <div class="card">
            <div class="card-body">
                <a href="../items"><small>Voir les articles actifs</small></a>
                <table class="table table-bordered" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center">Nom</th>
                        <th class="text-center">Images</th>
                        <th class="text-center">Description</th>
                        <th class="text-center">Catégorie</th>
                        <th class="text-center">Prix</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">En panier</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items->getShopArchivedItems() as $item): ?>
                        <tr>
                            <td class="text-center" style="width: fit-content;">
                                <h5><?= $item->getName() ?></h5>
                            </td>
                            <td class="text-center" style="width: 12rem; height: 9rem;">
                                <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId());
                                $v = 0;
                                foreach ($getImagesItem as $countImage) {
                                    $v++;
                                } ?>
                                <?php if ($getImagesItem) : ?>
                                    <?php if ($v !== 1) : ?>
                                        <div id="carousel_<?= $item->getId() ?>" class="carousel slide"
                                             data-bs-ride="carousel">
                                            <ol class="carousel-indicators">
                                                <?php $i = 0;
                                                foreach ($getImagesItem as $imageId): ?>
                                                    <li data-bs-target="#carousel_<?= $item->getId() ?>"
                                                        data-bs-slide-to="<?= $i ?>"
                                                        <?php if ($i === 0): ?>class="active"><?php endif; ?></li>
                                                    <?php $i++; endforeach; ?>
                                            </ol>
                                            <div class="carousel-inner">
                                                <?php $x = 0;
                                                foreach ($getImagesItem as $imagesUrl): ?>
                                                    <div class="carousel-item <?php if ($x === 0): ?>active<?php endif; ?>">
                                                        <img style="width: 12rem; max-height: 9rem; object-fit: contain"
                                                             src="<?= $imagesUrl->getImageUrl() ?>"
                                                             class="p-2 d-block" alt="..."/>
                                                    </div>
                                                    <?php $x++; endforeach; ?>
                                            </div>
                                            <div class="mt-1">
                                                <a class="carousel-control-prev" href="#carousel_<?= $item->getId() ?>"
                                                   role="button" data-bs-slide="prev">
                                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Previous</span>
                                                </a>
                                                <a class="carousel-control-next" href="#carousel_<?= $item->getId() ?>"
                                                   role="button" data-bs-slide="next">
                                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                    <span class="visually-hidden">Next</span>
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($imagesItem->getShopImagesByItem($item->getId()) as $imageUrl): ?>
                                            <img style="width: 12rem; max-height: 9rem; object-fit: contain"
                                                 src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block"
                                                 alt="..."/>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p>Pas d'images pour cet article</p>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 5rem;">
                                <?= $item->getDescription() ?>
                            </td>
                            <td class="text-center" style="width: fit-content;">
                                <a data-bs-toggle="tooltip" title="Consulter cette catégorie" target="_blank" href="<?= $item->getCategory()->getCatLink() ?>"><h6 class="text-primary"><?= $item->getCategory()->getName() ?></h6></a>
                            </td>
                            <td class="text-center">
                                <?= $item->getPrice() ?> <?= ShopSettingsModel::getSettingValue('currency') ?>
                            </td>
                            <td class="text-center">
                                <?= $item->getFormatedStock() ?>
                            </td>
                            <td class="text-center">
                                <?= $item->getQuantityInCart() ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


<section class="tab-content" id="myTabContent">
    <?php $i = 0;
    foreach ($categories as $category): ?>
        <div class="tab-pane fade <?php if ($i === 0): ?>show active<?php endif; ?>" id="cat_<?= $category->getId() ?>"
             role="tabpanel" aria-labelledby="cat_<?= $category->getId() ?>_1">
            <div class="mt-2">
                <a href="<?= $category->getCatLink() ?>" target="_blank" class="btn btn-sm btn-primary">Consulter cette
                    catégorie</a>
                <a data-bs-target="#delete-<?= $category->getId() ?>" type="button" data-bs-toggle="modal"
                   class="btn btn-sm btn-danger">Supprimé cette catégorie</a>
            </div>
            <div class="row mt-2">

                <?php foreach ($items->getShopItemByCat($category->getId()) as $item): ?>
                    <div class="col-12 col-lg-3">
                        <div class="card p-2">
                            <h6 class="text-center"><?= $item->getName() ?></h6>
                            <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId());
                            $v = 0;
                            foreach ($getImagesItem as $countImage) {
                                $v++;
                            } ?>
                            <?php if ($getImagesItem) : ?>
                                <?php if ($v !== 1) : ?>
                                    <div id="carousel_<?= $item->getId() ?>" class="carousel slide"
                                         data-bs-ride="carousel">
                                        <ol class="carousel-indicators">
                                            <?php $i = 0;
                                            foreach ($getImagesItem as $imageId): ?>
                                                <li data-bs-target="#carousel_<?= $item->getId() ?>"
                                                    data-bs-slide-to="<?= $i ?>"
                                                    <?php if ($i === 0): ?>class="active"><?php endif; ?></li>
                                                <?php $i++; endforeach; ?>
                                        </ol>
                                        <div class="carousel-inner">
                                            <?php $x = 0;
                                            foreach ($getImagesItem as $imagesUrl): ?>
                                                <div class="carousel-item <?php if ($x === 0): ?>active<?php endif; ?>">
                                                    <img style="width: 100px; height: 150px; object-fit: contain"
                                                         src="<?= $imagesUrl->getImageUrl() ?>"
                                                         class="px-5 d-block w-100" alt="..."/>
                                                </div>
                                                <?php $x++; endforeach; ?>
                                        </div>
                                        <div class="mt-5">
                                            <a class="carousel-control-prev" href="#carousel_<?= $item->getId() ?>"
                                               role="button" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </a>
                                            <a class="carousel-control-next" href="#carousel_<?= $item->getId() ?>"
                                               role="button" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </a>
                                        </div>

                                    </div>
                                <?php else: ?>
                                    <?php foreach ($imagesItem->getShopImagesByItem($item->getId()) as $imageUrl): ?>
                                        <img style="width: 100px; height: 150px; object-fit: contain"
                                             src="<?= $imageUrl->getImageUrl() ?>" class="px-5 d-block w-100"
                                             alt="..."/>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>

                            <p><small><?= $item->getCreated() ?></small></p>
                            <p><?= $item->getDescription() ?></p>
                            <p>Type
                                : <?php if ($item->getType() === 0): ?>Physique<?php else: ?>Virtuel<?php endif; ?></p>
                            <p>Stock
                                : <?php if ($item->getDefaultStock() === null): ?>Illimité<?php else: ?><?= $item->getCurrentStock() ?>/<?= $item->getDefaultStock() ?><?php endif; ?></p>
                            <p>
                                <a target="_blank" href="<?= $item->getItemLink() ?>">Voir
                                    l'article</a></p>
                            <p>
                                <a target="_blank" href="<?= $item->getAddToCartLink() ?>">Ajouter au panier</a></p>
                            <p>Limite globale
                                : <?php if ($item->getGlobalLimit() === null): ?>Pas de limite<?php else: ?><?= $item->getGlobalLimit() ?><?php endif; ?></p>
                            <p>Limite utilisateur
                                : <?php if ($item->getUserLimit() === null): ?>Pas de limite<?php else: ?><?= $item->getUserLimit() ?><?php endif; ?></p>
                            <h5 class="text-center"><?php if ($item->getPrice() === 0.00): ?>Gratuit<?php else: ?><?= $item->getPrice() ?>
                                    <small> <?= ShopSettingsModel::getInstance()->getSettingValue("currency") ?></small><?php endif; ?>
                            </h5>
                            <div class="text-center py-1">
                                <a type="button" data-bs-toggle="modal"
                                   data-bs-target="#delete-<?= $item->getId() ?>"><i
                                        class="text-danger fas fa-trash-alt"></i></a>
                            </div>
                        </div>
                    </div>
                    <!--
                     --MODAL SUPPRESSION ARTICLE--
                     -->
                    <div class="modal fade text-left" id="delete-<?= $item->getId() ?>" tabindex="-1"
                         role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                             role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title white" id="myModalLabel160">Supression de
                                        : <?= $item->getName() ?></h5>
                                </div>
                                <div class="modal-body">
                                    Cette supression est définitive
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary"
                                            data-bs-dismiss="modal">
                                        <i class="bx bx-x"></i>
                                        <span
                                            class=""><?= LangManager::translate("core.btn.close") ?></span>
                                    </button>
                                    <a href="items/delete/<?= $item->getId() ?>"
                                       class="btn btn-danger ml-1">
                                        <i class="bx bx-check"></i>
                                        <span
                                            class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="col-12 col-lg-3">
                    <a class="card" href="items/add_item/<?= $category->getId() ?>" style="cursor: pointer">
                        <div class="text-center" style="padding-top: 6rem;padding-bottom: 5.5rem">
                            <h2><i class="text-success fa-solid fa-circle-plus fa-xl"></i></h2>
                            <p class="mt-2">Ajouter un article</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <!--
        --MODAL SUPPRESSION CAT--
        -->
        <div class="modal fade text-left" id="delete-<?= $category->getId() ?>" tabindex="-1"
             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                 role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title white" id="myModalLabel160">Supression de
                            : <?= $category->getName() ?></h5>
                    </div>
                    <div class="modal-body">
                        Cette supression est définitive
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-secondary"
                                data-bs-dismiss="modal">
                            <i class="bx bx-x"></i>
                            <span
                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                        </button>
                        <a href="cat/delete/<?= $category->getId() ?>"
                           class="btn btn-danger ml-1">
                            <i class="bx bx-check"></i>
                            <span
                                class=""><?= LangManager::translate("core.btn.delete") ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php $i++; endforeach; ?>
</section>

<!--
----MODAL AJOUT CAT ----
-->
<div class="modal fade text-left" id="add-cat" tabindex="-1"
     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title white" id="myModalLabel160">Nouvelle catégorie</h5>
            </div>
            <div class="modal-body">
                <form id="add_cat" action="items/add_cat" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-12 col-lg-6 mt-2">
                            <h6>Nom :</h6>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-12 col-lg-6 mt-2">
                            <h6>Description :</h6>
                            <input type="text" class="form-control" name="description" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                </button>
                <button form="add_cat" type="submit" class="btn btn-primary" data-bs-dismiss="modal">
                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                </button>

            </div>
        </div>
    </div>
</div>