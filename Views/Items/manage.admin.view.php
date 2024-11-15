<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

$title = 'Boutique';
$description = '';

/* @var \CMW\Entity\Shop\Items\ShopItemEntity [] $items */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-cubes-stacked"></i> Articles</h3>
    <div>
        <a href="items/archived" type="submit" class="btn-warning">Voir les archives</a>
        <a href="items/add" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.add') ?></a>
    </div>
</div>

<div class="table-container">
    <table id="table1" data-load-per-page="10">
        <thead>
        <tr>
            <th>Images</th>
            <th>Nom</th>
            <?php if ($allowReviews): ?>
                <th class="text-center">Avis</th>
            <?php endif; ?>
            <th>Description</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Stock</th>
            <th class="text-center">En panier</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td class="text-center" style="width: 6rem; height: 6rem;">
                    <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId());
                    $v = 0;
                    foreach ($getImagesItem as $countImage) {
                        $v++;
                    } ?>
                    <?php if ($getImagesItem): ?>
                        <?php if ($v !== 1): ?>
                            <div class="slider-container relative w-full max-w-2xl mx-auto" data-height="80px">
                            <?php foreach ($getImagesItem as $imagesUrl): ?>
                                <img src="<?= $imagesUrl->getImageUrl() ?>" alt="..">
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <?php foreach ($imagesItem->getShopImagesByItem($item->getId()) as $imageUrl): ?>
                                <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                                     src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block"
                                     alt="..."/>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <img style="width: 6rem; max-height: 6rem; object-fit: contain"
                             src="<?= $defaultImage ?>" class="p-2 d-block"
                             alt="..."/>
                    <?php endif; ?>
                </td>
                <td style="width: fit-content;">
                    <h6><?= mb_strimwidth($item->getName(), 0, 30, '...') ?></h6>
                    <?php if ($item->isDraft()): ?>
                        <small class="cursor-pointer" data-tooltip-target="tooltip-top" data-tooltip-placement="top"><i class="fa-solid fa-circle-info"></i> Brouillon</small>
                        <div id="tooltip-top" role="tooltip" class="tooltip-content">
                            Visible uniquement par les administrateurs
                        </div>
                    <?php endif; ?>
                </td>
                <?php if ($allowReviews): ?>
                    <td class="text-center">
                        <a href="items/review/<?= $item->getId() ?>">
                            <?= $review->countTotalRatingByItemId($item->getId()) ?> avis<br>
                            <?= $review->getStars($item->getId()) ?>
                        </a>
                    </td>
                <?php endif; ?>
                <td style="max-width: 5rem;">
                    <?= mb_strimwidth($item->getShortDescription(), 0, 60, '...') ?>
                </td>
                <td style="width: fit-content;">
                    <a class="link" data-bs-toggle="tooltip" title="Consulter cette catégorie" target="_blank" href="<?= $item->getCategory()->getCatLink() ?>"><?= $item->getCategory()->getName() ?></a>
                </td>
                <td>
                    <b><?= $item->getPriceFormatted() ?></b>
                </td>
                <td>
                    <?= $item->getAdminFormattedStock() ?>
                </td>
                <td class="text-center">
                    <?= $item->getQuantityInCart() ?>
                </td>
                <td class="text-center space-x-2">
                    <a href="<?= $item->getItemLink() ?>" target="_blank">
                        <i data-bs-toggle="tooltip" title="Voir le rendue" class="text-success me-3 fa-solid fa-up-right-from-square"></i>
                    </a>
                    <a href="items/edit/<?= $item->getId() ?>">
                        <i data-bs-toggle="tooltip" title="Éditer" class="text-info me-3 fa-solid fa-edit"></i>
                    </a>
                    <button data-modal-toggle="modal-delete-<?= $item->getId() ?>" type="button">
                        <i data-bs-toggle="tooltip" title="Supprimé" class="text-danger fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
            <!--
             --MODAL SUPPRESSION ARTICLE--
             -->
            <div id="modal-delete-<?= $item->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6>Suppression de : <?= $item->getName() ?></h6>
                        <button type="button" data-modal-hide="modal-delete-<?= $item->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>Cette supression est définitive<br><br>
                            NB : <br>
                            - Si cet article est utilisé dans un panier, il sera archivé<br>
                            - Si cet article a déjà fait l'objet d'une commande, il sera archivé
                        </p>
                    </div>
                    <div class="modal-footer">
                        <a href="items/delete/<?= $item->getId() ?>"
                           class="btn-danger"><?= LangManager::translate('core.btn.delete') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>