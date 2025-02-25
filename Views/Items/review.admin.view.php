<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MailModel;
use CMW\Utils\Website;

$title = LangManager::translate('shop.views.items.review.title', ['name' => $item->getName()]);
$description = '';

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */
/* @var CMW\Entity\Shop\Items\ShopItemEntity $item */
/* @var CMW\Entity\Shop\Images\ShopImageEntity[] $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */

?>
<h3><i class="fa-solid fa-star"></i> <?= LangManager::translate('shop.views.items.review.title', ['name' => $item->getName()]) ?></h3>

<?php $mailConfig = MailModel::getInstance()->getConfig(); if ($mailConfig === null || !$mailConfig->isEnable()): ?>
    <div class="alert-danger mb-4">
        <b><?= LangManager::translate('shop.alert.mail.title') ?></b>
        <p><?= LangManager::translate('shop.alert.mail.config') ?><br>
            <?= LangManager::translate('shop.alert.mail.notify') ?></p>
        <p><?= LangManager::translate('shop.alert.mail.link') ?></p>
    </div>
<?php endif; ?>

<div class="grid-5">
    <div class="card">
        <?php
            $v = 0;
            foreach ($imagesItem as $countImage) {
                $v++;
            }
        ?>
        <?php if ($imagesItem): ?>
            <?php if ($v !== 1): ?>
                <div class="slider-container relative w-full max-w-2xl mx-auto" data-height="30rem">
                    <?php foreach ($imagesItem as $imagesUrl): ?>
                        <img src="<?= $imagesUrl->getImageUrl() ?>" alt="..">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php foreach ($imagesItem->getShopImagesByItem($item->getId()) as $imageUrl): ?>
                    <img style="width: 30rem; max-height: 30rem; object-fit: contain"
                         src="<?= $imageUrl->getImageUrl() ?>" class="p-2 d-block"
                         alt="..."/>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php else: ?>
            <img style="width: 30rem; max-height: 30rem; object-fit: contain"
                 src="<?= $defaultImage ?>" class="p-2 d-block"
                 alt="..."/>
        <?php endif; ?>
        <h6><?= LangManager::translate('shop.views.items.review.name') ?>:</h6>
        <p><?= $item->getName() ?></p>
        <h6><?= LangManager::translate('shop.views.items.review.desc') ?> :</h6>
        <p><?= $item->getShortDescription() ?></p>
    </div>
    <div class="col-span-3">
            <?php foreach ($review->getShopReviewByItemId($item->getId()) as $reviewed): ?>
                <div class="mb-4 card">
                    <div class="flex items-center mb-4 space-x-4">
                        <img class="w-10 h-10 rounded-full" src="<?= $reviewed->getUser()->getUserPicture()->getImage() ?>" alt="">
                        <div class="space-y-1 font-medium dark:text-white">
                            <p><?= $reviewed->getUser()->getPseudo() ?> <span class="block text-sm text-gray-500"><?= $reviewed->getCreated() ?></span></p>
                        </div>
                    </div>
                    <div class="mb-3 flex items-center">
                        <?= $reviewed->getStarsReview() ?>
                        <h3 class="ml-2 text-sm font-semibold text-gray-900"><?= $reviewed->getReviewRating() ?> sur 5</h3>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900"><?= $reviewed->getReviewTitle() ?></h3>
                    <p class="mb-2 font-light text-gray-500"><?= $reviewed->getReviewText() ?></p>
                    <hr>
                    <div class="text-center mt-2">
                        <button class="btn btn-danger" type="button" data-modal-toggle="modal-delete-<?= $reviewed->getId() ?>"><?= LangManager::translate('core.btn.delete') ?></button>
                    </div>

                    <div id="modal-delete-<?= $reviewed->getId() ?>" class="modal-container">
                        <div class="modal">
                            <div class="modal-header-danger">
                                <h6><?= LangManager::translate('shop.views.items.review.remove') ?></h6>
                                <button type="button" data-modal-hide="modal-delete-<?= $reviewed->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                            <div class="modal-body">
                                <p><?= LangManager::translate('shop.views.items.review.remove-text') ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="<?= $item->getId() ?>/delete/<?= $reviewed->getId() ?>" class="btn btn-danger">
                                    <span class=""><?= LangManager::translate('core.btn.delete') ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
    </div>
    <div class="card">
        <div class="flex items-center">
            <?= $review->getStars($item->getId()) ?>
            <span class="mx-1 "></span>
            <span><?= $review->getAverageRatingByItemId($item->getId()) ?> <?= LangManager::translate('shop.views.items.review.5') ?></span>
        </div>
        <p><?= $review->countTotalRatingByItemId($item->getId()) ?> <?= LangManager::translate('shop.views.items.review.reviews') ?></p>
        <?php foreach ($review->getRatingsPercentageByItemId($item->getId()) as $rating): ?>
            <div class="flex items-center mb-2">
                <span class="text-sm font-medium"><?= $rating->getRating() ?> <?= LangManager::translate('shop.views.items.review.stars') ?></span>
                <div style="background-color: #b7abab; width: 50%" class="w-2/4 h-5 mx-4 bg-gray-200 rounded">
                    <div class="h-5 rounded" style="height: 1.2rem; background-color: #FFD700; width: <?= $rating->getPercentage() ?>%"></div>
                </div>
                <span class="text-sm font-medium"><?= $rating->getPercentage() ?>%</span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
