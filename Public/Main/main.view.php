<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var CMW\Model\Shop\Category\ShopCategoriesModel $categoryModel */
/* @var \CMW\Entity\Shop\Items\ShopItemEntity [] $items */
/* @var CMW\Model\Shop\Review\ShopReviewsModel $review */
/* @var CMW\Model\Shop\Image\ShopImagesModel $imagesItem */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Model\Shop\Setting\ShopSettingsModel $allowReviews */

Website::setTitle('Boutique');
Website::setDescription('Découvrez la boutique !');

?>

<style>
    .shop-section-45875487 {
        width: 70%;
        padding-bottom: 6rem;
        margin: 1rem auto auto;
    }

    .shop-header-4578544 {
        display: flex;
        justify-content: space-between
    }

    .shop-select-5872154 {
        display: block;
        padding: 0 .6rem 0 .6rem;
        font-size: small;
        background: white;
        border-radius: 9px;
        color: black
    }

    .shop-input-search-587254 {
        display: block;
        padding: 0 .6rem 0 .6rem;
        width: 100%;
        z-index: 20;
        font-size: small;
        background: white;
        border-radius: 9px;
        color: black
    }

    .shop-button-search-565787 {
        position: absolute;
        top: 0;
        right: 0;
        padding: .2rem .6rem .2rem .6rem;
        font-size: small;
        background: #0a58ca;
        color: white;
        border-radius: 0 9px 9px 0
    }

    .shop-grid-main-5548754 {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-top: 1.6rem;
    }

    .shop-scale-hover-5987548 {
        transition: transform 0.3s ease;
        position: relative;
        background: transparent;
        border-radius: 9px;
        border: 1px solid #d8d8d8;
    }

    .shop-link-container-45754 {
        padding: 0 .6rem .6rem .6rem;
    }

    .shop-scale-hover-5987548:hover {
        transform: scale(1.09);
    }

    .shop-discount-badge-45787 {
        z-index: 5000;
        position: absolute;
        top: 0;
        left: 0;
        transform: translate(5%, 10%) rotate(-10deg);
        background-color: #f44336;
        color: white;
        padding: 8px 16px;
        border-radius: 0 16px 0 16px;
    }


    @media (max-width: 1024px) {
        .shop-grid-main-5548754 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .shop-grid-main-5548754 {
            grid-template-columns: 1fr;
        }
    }


    .shop-carousel-568945 {
        position: relative;
        width: 100%;
    }

    .shop-carousel-wrapper-48721565 {
        position: relative;
        height: 18.6rem;
        overflow: hidden;
        border-radius: 0.5rem;
    }

    .shop-carousel-item-698757845 {
        display: none !important;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: auto;
    }

    .shop-carousel-item-698757845.active {
        display: block !important;
    }

    .shop-carousel-img-548754 {
        width: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        top: 50%;
        left: 50%
    }

    .shop-carousel-indicators-6478454 {
        position: absolute;
        bottom: 10px;
        left: 50%;
        transform: translateX(-50%);
        display: flex !important;
        gap: 0.5rem;
        z-index: 50;
    }

    .shop-carousel-indicator-9675487 {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #ccc;
        cursor: pointer;
    }

    .shop-carousel-indicator-span-9675487 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2rem;
        height: 2rem;
        background: #4c6ce8;
        border-radius: 100%
    }

    .shop-carousel-indicator-9675487.active {
        background-color: #555;
    }

    .shop-carousel-control-3588745 {
        position: absolute;
        top: 0;
        height: 100%;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 20;
    }

    .shop-carousel-control-3588745.prev {
        left: 0;
    }

    .shop-carousel-control-3588745.next {
        right: 0;
    }

    .shop-solo-img-5678451 {
        width: auto;
        height: 18.6rem;
        border-radius: 0.5rem;
        margin: auto;
        object-fit: cover;
        object-position: center;
        display: block;
        top: 50%;
        left: 50%
    }

</style>


<section class="shop-section-45875487">
    <div class="shop-header-4578544">
        <div>
            <select onchange="location = this.value;" class="shop-select-5872154">
                <option selected
                        value="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'shop' ?>">
                    Catégorie : Tout afficher
                </option>
                <?php foreach ($categoryModel->getShopCategories() as $category): ?>
                    <option value="<?= $category->getCatLink() ?>">Catégorie : <?= $category->getName() ?></option>
                    <?php foreach ($categoryModel->getSubsCat($category->getId()) as $subCategory): ?>
                        <option
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
                                <!-- Carousel wrapper -->
                                <div class="shop-carousel-wrapper-48721565">
                                    <!-- Items -->
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
                                <!-- Controls -->
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
                                <img alt="shop product" class="shop-solo-img-5678451" src="<?= $imageUrl->getImageUrl() ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <img class="shop-solo-img-5678451" src="<?= $defaultImage ?>">
                    <?php endif; ?>
                    <a style="all: inherit" href="<?= $item->getItemLink() ?>">
                        <div class="shop-link-container-45754">
                            <h4 style="text-align: center"><?= $item->getName() ?></h4>
                            <?php if ($allowReviews): ?>
                                <div class="flex justify-center items-center">
                                    <?= $review->getStars($item->getId()) ?>
                                    <span class="mx-1 "></span>
                                    <p class="text-sm font-medium text-gray-900 underline"><?= $review->countTotalRatingByItemId($item->getId()) ?>
                                        avis</p>
                                </div>
                            <?php endif; ?>
                            <p><?= $item->getShortDescription() ?></p>
                            <p class="text-xs text-center hover:text-blue-600">Lire la suite</p>
                        </div>
                    </a>
                </div>
                <div class="grid grid-cols-2 border rounded-b py-2">
                    <?php if ($item->getPriceDiscountDefaultApplied()): ?>
                        <p class="text-center"><s><?= $item->getPriceFormatted() ?></s> <b
                                class="text-xl"><?= $item->getPriceDiscountDefaultAppliedFormatted() ?></b></p>
                    <?php else: ?>
                        <p class="text-center text-xl"><?= $item->getPriceFormatted() ?></p>
                    <?php endif; ?>

                    <a href="<?= $item->getAddToCartLink() ?>"
                       class="border-l text-center text-2xl hover:text-blue-600"><i
                            class="fa-solid fa-cart-plus"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const carousels = document.querySelectorAll("[data-carousel='static']");

        carousels.forEach((carousel) => {
            const items = carousel.querySelectorAll("[data-carousel-item]");
            const indicators = carousel.querySelectorAll("[data-carousel-slide-to]");
            const prevButton = carousel.querySelector("[data-carousel-prev]");
            const nextButton = carousel.querySelector("[data-carousel-next]");
            let currentIndex = 0;
            let autoplayInterval;

            const showItem = (index) => {
                items.forEach((item, i) => {
                    item.classList.toggle("active", i === index);
                });
                indicators.forEach((indicator, i) => {
                    indicator.classList.toggle("active", i === index);
                });
            };

            const autoplay = () => {
                autoplayInterval = setInterval(() => {
                    currentIndex = (currentIndex + 1) % items.length;
                    showItem(currentIndex);
                }, 3000);
            };

            const stopAutoplay = () => {
                clearInterval(autoplayInterval);
            };

            nextButton.addEventListener("click", () => {
                stopAutoplay();
                currentIndex = (currentIndex + 1) % items.length;
                showItem(currentIndex);
                autoplay();
            });

            prevButton.addEventListener("click", () => {
                stopAutoplay();
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                showItem(currentIndex);
                autoplay();
            });

            indicators.forEach((indicator, index) => {
                indicator.addEventListener("click", () => {
                    stopAutoplay();
                    currentIndex = index;
                    showItem(currentIndex);
                    autoplay();
                });
            });

            // Initialisation
            showItem(currentIndex);
            autoplay();
        });
    });

</script>