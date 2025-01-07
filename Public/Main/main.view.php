<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */
/* @var \CMW\Entity\Shop\Items\ShopItemEntity [] $items */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */
/* @var CMW\Entity\Shop\Categories\ShopCategoryEntity|null $thisCat */

Website::setTitle('Boutique');
Website::setDescription('Découvrez la boutique !');

?>
<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <div class="shop-header-4578544">
        <div>
            <select onchange="location = this.value;" class="shop-select-5872154">
                <option selected
                        value="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop' ?>">
                    Catégorie : Tout afficher
                </option>
                <?php foreach ($categoryModel->getShopCategories() as $category): ?>
                    <option <?= $category->getName() === $thisCat?->getName() ? 'selected' : '' ?>
                        value="<?= $category->getCatLink() ?>">Catégorie : <?= $category->getName() ?></option>
                    <?php foreach ($categoryModel->getSubsCat($category->getId()) as $subCategory): ?>
                        <option <?= $subCategory->getSubCategory()->getName() === $thisCat?->getName() ? 'selected' : '' ?>
                            value="<?= $subCategory->getSubCategory()->getCatLink() ?>"> <?= str_repeat("\u{00A0}\u{00A0}", $subCategory->getDepth()) . ' Sous-Cat :  ' . $subCategory->getSubCategory()->getName() ?></option>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="display:flex;">
            <form action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>shop/search" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <div style="position: relative; width: 20rem">
                    <input name="for" type="search" id="search-dropdown" class="shop-input-search-587254"
                           placeholder="Rechercher" required>
                    <button type="submit" class="shop-button-search-565787">
                        <svg aria-hidden="true" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="shop-grid-main-5548754">
        <?php if (empty($items)) : ?>
            <h3>Aucun article trouvé !</h3>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="shop-scale-hover-5987548">
                    <?php if ($item->getDiscountImpactDefaultApplied()): ?>
                        <div class="shop-discount-badge-45787">
                            <p style="text-align: center"><?= $item->getDiscountImpactDefaultApplied() ?></p>
                        </div>
                    <?php endif; ?>
                    <div>
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
                            <img alt="items image" class="shop-solo-img-5678451" src="<?= $defaultImage ?>">
                        <?php endif; ?>
                        <a style="all: inherit; cursor: pointer" href="<?= $item->getItemLink() ?>">
                            <div class="shop-link-container-45754">
                                <h4 style="text-align: center"><?= $item->getName() ?></h4>
                                <?php if ($allowReviews): ?>
                                    <div class="shop-review-4578211">
                                        <div><?= $review->getStars($item->getId()) ?></div>
                                        <p style="font-weight: bold"><?= $review->countTotalRatingByItemId($item->getId()) ?>  avis</p>
                                    </div>
                                <?php endif; ?>
                                <p style="margin-top: .4rem"><?= mb_strimwidth($item->getShortDescription(), 0, 106, '...') ?></p>
                            </div>
                        </a>
                    </div>
                    <div style="margin-top: auto">
                        <a href="<?= $item->getItemLink() ?>">
                            <div style="display: flex; justify-content: end">
                                <p style="margin: 0 .8rem .4rem .4rem; font-size: small">Voir l'article en détails</p>
                            </div>
                        </a>
                        <div class="shop-price-section-457854">
                            <?php if ($item->getPriceDiscountDefaultApplied()): ?>
                                <p><s style="font-size: small"><?= $item->getPriceFormatted() ?></s> <span
                                        style="font-size: larger"><?= $item->getPriceDiscountDefaultAppliedFormatted() ?></span>
                                </p>
                            <?php else: ?>
                                <p style="font-size: larger"><?= $item->getPriceFormatted() ?></p>
                            <?php endif; ?>
                            <a style="font-size: larger" href="<?= $item->getAddToCartLink() ?>">
                                <i class="fa-solid fa-cart-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script
    src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/carousel.js"></script>