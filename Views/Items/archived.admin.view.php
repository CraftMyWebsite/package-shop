<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

$title = 'Boutique';
$description = '';

/* @var CMW\Entity\Shop\Categories\ShopCategoryEntity[] $categories */
/* @var CMW\Model\Shop\Item\ShopItemsModel $items */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */

?>
<div class="page-title">
    <h3><i class="fa-solid fa-cubes-stacked"></i> Articles archivés</h3>
    <a href="../items" type="submit" class="btn-primary">Retourner au articles</a>
</div>

<div class="alert alert-warning my-4">Les articles archivés sont souvent des articles qui ont déjà était commandé, vous en gardez une trace pour le suivie des commandes client, facture ...<br>Vous pouvez les réactiver, mais cette action est fortement déconseiller</div>

<div class="table-container">
    <table id="table1" data-load-per-page="10">
        <thead>
        <tr>
            <th>Images</th>
            <th>Nom</th>
            <?php if ($allowReviews): ?>
                <th class="text-center">Avis</th>
            <?php endif; ?>
            <th>Raison d'archivage</th>
            <th>Prix</th>
            <th>Stock</th>
            <th class="text-center"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items->getShopArchivedItems() as $item): ?>
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
                    <?= $item->getArchivedReason() ?>
                </td>
                <td>
                    <?= $item->getPriceFormatted() ?>
                </td>
                <td>
                    <?= $item->getAdminFormattedStock() ?>
                </td>
                <td class="text-center space-x-2">
                    <button data-modal-toggle="modal-push-<?= $item->getId() ?>" type="button">
                        <i data-bs-toggle="tooltip" title="Activer" class="text-success fas fa-rocket"></i>
                    </button>
                </td>
            </tr>
            <!--
             --MODAL PUSH ARTICLE--
             -->
            <div id="modal-push-<?= $item->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header">
                        <h6>Désarchivage : <?= $item->getName() ?></h6>
                        <button type="button" data-modal-hide="modal-push-<?= $item->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <p>Vos clients pourront à nouveau acheter cet article !</p>
                    </div>
                    <div class="modal-footer">
                        <a href="activate/<?= $item->getId() ?>"
                           class="btn-primary">
                            Confirmer
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
