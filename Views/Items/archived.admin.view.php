<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Entity\Shop\Categories\ShopCategoryEntity[] $categories */
/* @var CMW\Model\Shop\Item\ShopItemsModel $items */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-cubes-stacked"></i> <span class="m-lg-auto">Articles archivés</span></h3>
</div>
<div class="alert alert-warning">Les articles archivés sont souvent des articles qui ont déjà était commandé, vous en gardez une trace pour le suivie des commandes client, facture ...</div>

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
                        <th class="text-center">Avis</th>
                        <th class="text-center">Raison d'archivage</th>
                        <th class="text-center">Prix</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center"></th>
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
                                    <img style="width: 12rem; max-height: 9rem; object-fit: contain"
                                         src="<?= $defaultImage ?>" class="p-2 d-block"
                                         alt="..."/>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="review/<?= $item->getId() ?>">
                                    <?= $review->countTotalRatingByItemId($item->getId()) ?> avis<br>
                                    <?= $review->getStars($item->getId()) ?>
                                </a>
                            </td>
                            <td style="max-width: 5rem;">
                                <?= $item->getArchivedReason() ?>
                            </td>
                            <td class="text-center">
                                <?= $item->getPriceFormatted() ?>
                            </td>
                            <td class="text-center">
                                <?= $item->getFormatedStock() ?>
                            </td>
                            <td class="text-center">
                                <a type="button" data-bs-toggle="modal"  data-bs-target="#active-<?= $item->getId() ?>">
                                    <i data-bs-toggle="tooltip" title="Activer" class="text-success fas fa-rocket"></i>
                                </a>
                            </td>
                        </tr>

                        <!--
                             --MODAL SUPPRESSION ARTICLE--
                             -->
                        <div class="modal fade text-left" id="active-<?= $item->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                 role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title white" id="myModalLabel160">Désarchivage
                                            : <?= $item->getName() ?></h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Vos clients pourront à nouveau acheter cet article !</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">
                                            <i class="bx bx-x"></i>
                                            <span
                                                class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="activate/<?= $item->getId() ?>"
                                           class="btn btn-primary ml-1">
                                            <i class="bx bx-check"></i>
                                            <span
                                                class="">Confirmer</span>
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