<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\Categories\ShopCategoryEntity $parentCat */
/* @var CMW\Entity\Shop\Items\ShopItemEntity $item */
/* @var CMW\Entity\Shop\Items\ShopItemVariantEntity[] $itemVariants */
/* @var CMW\Model\Shop\Item\ShopItemVariantValueModel $variantValuesModel */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Entity\Shop\Items\ShopItemPhysicalRequirementEntity $physicalInfo */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */
/* @var CMW\Entity\Shop\Items\ShopItemEntity [] $otherItemsInThisCat */
/* @var bool $showPublicStock */

Website::setTitle('Boutique - Article');
Website::setDescription("Venez découvrir l'article !");

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section style="position: relative" class="shop-section-45875487">
    <?php if ($item->getDiscountImpactDefaultApplied()): ?>
        <div class="shop-discount-badge-45787">
            <p class="text-center text-xl"><?= $item->getDiscountImpactDefaultApplied() ?></p>
        </div>
    <?php endif; ?>
    <div>
        <div class="shop-grid-item-596587">
            <div style="height: fit-content" class="shop-col-span-2-668745">
                <?php $getImagesItem = $imagesItem->getShopImagesByItem($item->getId());
                $v = 0;
                foreach ($getImagesItem as $countImage) {
                    $v++;
                } ?>
                <?php if ($getImagesItem): ?>
                    <?php if ($v !== 1): ?>
                        <div id="indicators-carousel-<?= $uniqueId ?>" class="shop-carousel-568945"
                             data-carousel="static">
                            <div class="shop-carousel-wrapper-48721565">
                                <?php $x = 0;
                                foreach ($getImagesItem as $imagesUrl): ?>
                                    <div class="shop-carousel-item-698757845 <?= $x === 0 ? 'active' : '' ?>"
                                         data-carousel-item>
                                        <img style="all: inherit" src="<?= $imagesUrl->getImageUrl() ?>"
                                             class="shop-carousel-img-548754" alt="Image <?= $x ?>">
                                    </div>
                                    <?php $x++;
                                endforeach; ?>
                            </div>
                            <!-- Indicators -->
                            <div class="shop-carousel-indicators-6478454">
                                <?php $i = 0;
                                foreach ($getImagesItem as $imageId): ?>
                                    <button type="button"
                                            class="shop-carousel-indicator-9675487 <?= $i === 0 ? 'active' : '' ?>"
                                            aria-current="<?= $i === 0 ? 'true' : 'false' ?>"
                                            aria-label="Slide <?= $i + 1 ?>"
                                            data-carousel-slide-to="<?= $i ?>"></button>
                                    <?php $i++;
                                endforeach; ?>
                            </div>
                            <button type="button"
                                    class="shop-carousel-control-3588745 prev group"
                                    data-carousel-prev>
                                        <span class="shop-carousel-indicator-span-9675487">
                                            <svg style="width: 1rem; height: 1rem; color: white"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 6 10">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                      stroke-linejoin="round" stroke-width="2"
                                                      d="M5 1 1 5l4 4"/>
                                            </svg>
                                            <span class="sr-only">Previous</span>
                                        </span>
                            </button>
                            <button type="button"
                                    class="shop-carousel-control-3588745 next group"
                                    data-carousel-next>
                                        <span class="shop-carousel-indicator-span-9675487">
                                            <svg style="width: 1rem; height: 1rem; color: white"
                                                 xmlns="http://www.w3.org/2000/svg" fill="none"
                                                 viewBox="0 0 6 10">
                                                <path stroke="currentColor" stroke-linecap="round"
                                                      stroke-linejoin="round" stroke-width="2"
                                                      d="m1 9 4-4-4-4"/>
                                            </svg>
                                            <span class="sr-only">Next</span>
                                        </span>
                            </button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($imagesItem->getShopImagesByItem($item->getId()) as $imageUrl): ?>
                            <img alt="shop product" class="shop-solo-img-5678451"
                                 src="<?= $imageUrl->getImageUrl() ?>">
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <img alt="shop product" class="shop-solo-img-5678451"
                         src="<?= $defaultImage ?>">
                <?php endif; ?>
            </div>
            <div style="height: fit-content" class="shop-col-span-4-157875">
                <h2 style="font-weight: bold"><?= $item->getName() ?></h2>
                <?php if ($allowReviews): ?>
                    <div style="display: flex; gap: .3rem">
                        <div><?= $review->getStars($item->getId()) ?></div>
                        <p style="font-weight: bold"><?= $review->countTotalRatingByItemId($item->getId()) ?> avis</p>
                    </div>
                <?php endif; ?>
                <?php if ($showPublicStock): ?>
                <b>Stock :</b> <?= $item->getPublicFormattedStock() ?>
                <?php endif; ?>

                <?php if ($item->getPriceDiscountDefaultApplied()): ?>
                    <h3><s style="font-size: large"><?= $item->getPriceFormatted() ?></s> <?= $item->getPriceDiscountDefaultAppliedFormatted() ?></h3>
                <?php else: ?>
                    <h3><?= $item->getPriceFormatted() ?></h3>
                <?php endif; ?>
                <p><?= $item->getShortDescription() ?></p>

                <form method="post">
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                    <div style="display: flex;">
                        <?php foreach ($itemVariants as $itemVariant): ?>
                            <div style="margin-right: .6rem">
                                <div style="margin-right: .3rem">
                                    <?= $itemVariant->getName() ?> :
                                </div>
                                <select name="selected_variantes[]" class="shop-select-5872154">
                                    <?php foreach ($variantValuesModel->getShopItemVariantValueByVariantId($itemVariant->getId()) as $variantValue): ?>
                                        <option value="<?= $variantValue->getId() ?>"><?= $variantValue->getValue() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="display: flex; align-items: center; margin-top: .5rem">
                        <div style="position: relative; width: 12rem">
                            <input value="1" name="quantity" type="number" class="shop-input-number-587254"
                                   placeholder="Rechercher" required>
                            <button type="submit" class="shop-button-number-565787">
                                Ajouter au panier
                            </button>
                        </div>
                    </div>
                </form>
                <div style="margin-top: .5rem">
                    <p>Catégorie : <a href="<?= $parentCat->getCatLink() ?>" class="text-blue-600 hover:text-blue-400"><?= $parentCat->getName() ?></a></p>
                </div>
            </div>
        </div>

        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 rounded-t-lg border-b-2" id="description-tab" data-tabs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="false">Description</button>
                </li>
                <?php if (!empty($physicalInfo)): ?>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="info-tab" data-tabs-target="#info" type="button" role="tab" aria-controls="info" aria-selected="false">Informations sur le produit</button>
                </li>
                <?php endif; ?>
                <?php if ($allowReviews): ?>
                <li class="mr-2" role="presentation">
                    <button class="inline-block p-4 rounded-t-lg border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="reviews-tab" data-tabs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Avis</button>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <div id="myTabContent">
            <div class="hidden p-4 bg-gray-50 rounded-lg" id="description" role="tabpanel" aria-labelledby="description-tab">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <?= $item->getDescription() ?>
                </p>
            </div>
            <?php if ($allowReviews): ?>
            <div class="hidden p-4 bg-gray-50 rounded-lg" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                <div class="xl:grid grid-cols-3">
                    <div>
                        <div class="flex items-center">
                            <?= $review->getStars($item->getId()) ?>
                            <span class="mx-1 "></span>
                            <p class="ml-2 text-sm font-medium text-gray-900 dark:text-white"><?= $review->getAverageRatingByItemId($item->getId()) ?> sur 5</p>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400"><?= $review->countTotalRatingByItemId($item->getId()) ?> avis</p>
                        <?php foreach ($review->getRatingsPercentageByItemId($item->getId()) as $rating): ?>
                        <div class="flex items-center mt-4">
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-500"><?= $rating->getRating() ?> étoiles</span>
                            <div class="w-2/4 h-5 mx-4 bg-gray-200 rounded dark:bg-gray-700">
                                <div class="h-5 rounded" style="background-color: #FFD700; width: <?= $rating->getPercentage() ?>%"></div>
                            </div>
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-500"><?= $rating->getPercentage() ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col-span-2">

                        <?php foreach ($review->getShopReviewByItemId($item->getId()) as $reviewed): ?>
                        <article class="rounded-lg bg-white p-4 mb-4">
                            <div class="flex items-center mb-4 space-x-4">
                                <img class="w-10 h-10 rounded-full" src="<?= $reviewed->getUser()->getUserPicture()->getImage() ?>" alt="">
                                <div class="space-y-1 font-medium dark:text-white">
                                    <p><?= $reviewed->getUser()->getPseudo() ?> <span class="block text-sm text-gray-500"><?= $reviewed->getCreated() ?></span></p>
                                </div>
                            </div>
                            <div class="flex items-center mb-1">
                                <?= $reviewed->getStarsReview() ?>
                                <h3 class="ml-2 text-sm font-semibold text-gray-900"><?= $reviewed->getReviewRating() ?> sur 5</h3>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900"><?= $reviewed->getReviewTitle() ?></h3>
                            <p class="mb-2 font-light text-gray-500"><?= $reviewed->getReviewText() ?></p>
                        </article>
                        <?php endforeach; ?>

                        <div class="rounded-lg bg-white p-4 mb-4">
                            <form method="post" action="<?= $item->getSlug() ?>/addReview" class="">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <h3 class="text-base font-semibold text-gray-900 mb-2">Donner votre avis.</h3>
                                <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Note</label>
                                <?= $review->getInputStars() ?>
                                <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Titre</label>
                                <input type="text" name="title" id="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2" required>
                                <label for="content" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400">Contenue :</label>
                                <textarea minlength="20" name="content" id="content" rows="4" class="tinymce block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " placeholder="Bonjour," required></textarea>
                                <div class="text-center mt-4">
                                    <?php if (UsersController::isUserLogged()): ?>
                                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg px-4 py-2 md:px-5 md:py-2.5 mr-1 md:mr-2 focus:outline-none">Envoyer <i class="fa-solid fa-paper-plane"></i></button>
                                    <?php else: ?>
                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg px-4 py-2 md:px-5 md:py-2.5 mr-1 md:mr-2 focus:outline-none">Connectez-vous <i class="fa-solid fa-paper-plane"></i></a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($physicalInfo)): ?>
            <div class="hidden p-4 bg-gray-50 rounded-lg" id="info" role="tabpanel" aria-labelledby="info-tab">
                <p>
                    Poids : <?= $physicalInfo->getWeight() ?> grammes<br>
                    Longueur : <?= $physicalInfo->getLength() ?> cm<br>
                    Largeur : <?= $physicalInfo->getWidth() ?> cm<br>
                    Hauteur : <?= $physicalInfo->getHeight() ?> cm<br>
                </p>
            </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<script
    src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/carousel.js"></script>