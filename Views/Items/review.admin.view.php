<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = "Boutique";
$description = "";

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */
/* @var CMW\Entity\Shop\Items\ShopItemEntity $item */
/* @var CMW\Entity\Shop\Images\ShopImageEntity[] $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */

?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-star"></i> <span
            class="m-lg-auto">Avis de <?= $item->getName() ?></span></h3>
</div>
    <section class="row">
        <div class="col-12 col-lg-3">
            <?php
            $v = 0;
            foreach ($imagesItem as $countImage) {
                $v++;
            } ?>
            <?php if ($imagesItem) : ?>
                <?php if ($v !== 1) : ?>
                    <div id="carousel_<?= $item->getId() ?>" class="carousel slide"
                         data-bs-ride="carousel">
                        <ol class="carousel-indicators">
                            <?php $i = 0;
                            foreach ($imagesItem as $imageId): ?>
                                <li data-bs-target="#carousel_<?= $item->getId() ?>"
                                    data-bs-slide-to="<?= $i ?>"
                                    <?php if ($i === 0): ?>class="active"><?php endif; ?></li>
                                <?php $i++; endforeach; ?>
                        </ol>
                        <div class="carousel-inner">
                            <?php $x = 0;
                            foreach ($imagesItem as $imagesUrl): ?>
                                <div class="carousel-item <?php if ($x === 0): ?>active<?php endif; ?>">
                                    <img style="width: 80%; object-fit: contain"
                                         src="<?= $imagesUrl->getImageUrl() ?>"
                                         class="p-2 d-block mx-auto" alt="..."/>
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
                    <?php foreach ($imagesItem as $imageUrl): ?>
                        <img style="width: 80%; object-fit: contain"
                             src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block mx-auto"
                             alt="..."/>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else: ?>

                <img style="width: 80%; object-fit: contain"
                     src="<?= $defaultImage ?>" class="p-2 d-block mx-auto"
                     alt="..."/>
            <?php endif; ?>
        </div>
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6>Nom:</h6>
                            <p><?= $item->getName() ?></p>
                        </div>
                        <div class="col-12 mt-2">
                            <h6>Déscription :</h6>
                            <p><?= $item->getShortDescription() ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Avis</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-3">
                            <div class="card-in-card p-2">
                                <div class="flex items-center">
                                    <?= $review->getStars($item->getId()) ?>
                                    <span class="mx-1 "></span>
                                    <span><?= $review->getAverageRatingByItemId($item->getId()) ?> sur 5</span>
                                </div>
                                <p><?= $review->countTotalRatingByItemId($item->getId()) ?> avis</p>
                                <?php foreach ($review->getRatingsPercentageByItemId($item->getId()) as $rating): ?>
                                    <div class="d-flex items-center mb-2">
                                        <span class="text-sm font-medium"><?=$rating->getRating()?> étoiles</span>
                                        <div style="background-color: #b7abab; width: 50%" class="w-2/4 h-5 mx-4 bg-gray-200 rounded">
                                            <div class="h-5 rounded" style="height: 1.2rem; background-color: #FFD700; width: <?=$rating->getPercentage()?>%"></div>
                                        </div>
                                        <span class="text-sm font-medium"><?=$rating->getPercentage()?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-12 col-lg-9">
                                <?php foreach ($review->getShopReviewByItemId($item->getId()) as $reviewed): ?>
                                <div class="card-in-card p-2 mb-4">
                                        <div class="d-flex items-center mb-4 space-x-4">
                                            <img style="width: 4rem;" class="w-10 h-10 rounded-full" src="<?= $reviewed->getUser()->getUserPicture()->getImage() ?>" alt="">
                                            <div class="space-y-1 font-medium dark:text-white">
                                                <p><?= $reviewed->getUser()->getPseudo() ?> <span class="block text-sm text-gray-500"><?= $reviewed->getCreated() ?></span></p>
                                            </div>
                                        </div>
                                        <div class="mb-1">
                                            <?= $reviewed->getStarsReview() ?>
                                            <h3 class="ml-2 text-sm font-semibold text-gray-900"><?= $reviewed->getReviewRating() ?> sur 5</h3>
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900"><?= $reviewed->getReviewTitle() ?></h3>
                                        <p class="mb-2 font-light text-gray-500"><?= $reviewed->getReviewText() ?></p>
                                    <hr>
                                        <div class="text-center mt-2">
                                            <a class="btn btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $reviewed->getId() ?>">Supprimé</a>
                                        </div>
                                    <div class="modal fade text-left" id="delete-<?= $reviewed->getId() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title white" id="myModalLabel160">Suppression de l'avis ?</h5>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <p>Ne supprimez pas d'avis négatif pour améliorer vos notes !<br>Vos clients ont le droit de ne pas être satisfait.<br>Et ils ont aussi le droit de le faire savoir.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                        <span class="">Annuler</span>
                                                    </button>
                                                    <a href="<?= $item->getId() ?>/delete/<?= $reviewed->getId() ?>" class="btn btn-danger">
                                                        <span class="">Supprimer</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
