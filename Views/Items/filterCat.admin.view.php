<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\ShopSettingsModel;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Entity\Shop\ShopCategoryEntity $thisCat */
/* @var CMW\Model\Shop\ShopItemsModel $items */
/* @var CMW\Model\Shop\ShopImagesModel $imagesItem */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <span class="m-lg-auto">Articles dans la catégorie <?= $thisCat->getName() ?></span></h3>
    <div class="buttons">
        <a href="items/add" type="submit"
           class="btn btn-primary"><?= LangManager::translate("core.btn.add") ?></a>
    </div>
</div>


<section>
    <div>
        <div class="card">
            <div class="card-body">
                <a href="../archived"><small>Voir les articles archivés</small></a>
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
                        <th class="text-center"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
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
                            <td class="text-center">
                                <a href="<?= $item->getItemLink() ?>" target="_blank">
                                    <i data-bs-toggle="tooltip" title="Voir le rendue" class="text-success me-3 fa-solid fa-up-right-from-square"></i>
                                </a>
                                <a href="edit">
                                    <i data-bs-toggle="tooltip" title="Éditer" class="me-3 fa-solid fa-edit"></i>
                                </a>
                                <a type="button" data-bs-toggle="modal"  data-bs-target="#delete-<?= $item->getId() ?>">
                                    <i data-bs-toggle="tooltip" title="Supprimé" class="text-danger fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

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
                                        <p>Cette supression est définitive<br><br>
                                            NB : <br>
                                            - Si cet article est utilisé dans un panier, il sera archivé<br>
                                            - Si cet article a déjà fait l'objet d'une commande, il sera archivé
                                        </p>
                                        </p>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>