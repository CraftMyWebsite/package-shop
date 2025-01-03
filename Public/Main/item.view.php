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
                    <p>Catégorie : <a href="<?= $parentCat->getCatLink() ?>" style="color: #0A58CA"><?= $parentCat->getName() ?></a></p>
                </div>
            </div>
        </div>

        <div class="shop-tabs-457854">
            <nav class="shop-tabs-nav-698554">
                <button class="shop-tab-link-487721 active" data-tab="tab1">Description</button>
                <?php if (!empty($physicalInfo)): ?>
                <button class="shop-tab-link-487721" data-tab="tab2">Informations sur le produit</button>
                <?php endif; ?>
                <?php if ($allowReviews): ?>
                <button class="shop-tab-link-487721" data-tab="tab3">Avis</button>
                <?php endif; ?>
            </nav>
            <div class="">
                <div class="shop-tab-pane-697154 active" id="tab1"><?= $item->getDescription() ?></div>
                <?php if (!empty($physicalInfo)): ?>
                <div class="shop-tab-pane-697154" id="tab2">
                    <p>
                        Poids : <?= $physicalInfo->getWeight() ?> grammes<br>
                        Longueur : <?= $physicalInfo->getLength() ?> cm<br>
                        Largeur : <?= $physicalInfo->getWidth() ?> cm<br>
                        Hauteur : <?= $physicalInfo->getHeight() ?> cm<br>
                    </p>
                </div>
                <?php endif; ?>
                <?php if ($allowReviews): ?>
                <div class="shop-tab-pane-697154" id="tab3">
                    <div class="shop-grid-reviews-596587">
                        <div>
                            <div style="display: flex; justify-items: center; align-items: center">
                                <?= $review->getStars($item->getId()) ?>
                                <p style="margin-left: 1rem; font-size: .9rem"><?= $review->getAverageRatingByItemId($item->getId()) ?> sur 5</p>
                            </div>
                            <p style="font-size: .8rem; font-weight: bolder"><?= $review->countTotalRatingByItemId($item->getId()) ?> avis</p>
                            <?php foreach ($review->getRatingsPercentageByItemId($item->getId()) as $rating): ?>
                                <div style="display: flex; align-items: center; margin-top: 1rem; gap: .5rem">
                                    <span style="font-size: 1rem; font-weight: bolder; color: #307ae6"><?= $rating->getRating() ?> étoiles</span>
                                    <div style="width: 50%; height: 1.25rem; background: #a7aaac; border-radius: 9px">
                                        <div style="height: 1.25rem; border-radius: 8px; background-color: #FFD700; width: <?= $rating->getPercentage() ?>%"></div>
                                    </div>
                                    <span style="font-size: 1rem; font-weight: bolder; color: #307ae6"><?= $rating->getPercentage() ?>%</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="shop-col-span-2-668745">
                            <?php foreach ($review->getShopReviewByItemId($item->getId()) as $reviewed): ?>
                                <article style="margin-bottom: .8rem; border: 1px solid #ddd; border-radius: 9px; padding: .3rem">
                                    <div style="display: flex; align-items: center; margin-bottom: .2rem">
                                        <img style="width: 2.5rem; height: 2.5rem; border-radius: 100%; margin-right: .5rem" src="<?= $reviewed->getUser()->getUserPicture()->getImage() ?>" alt="">
                                        <div>
                                            <p>
                                                <?= $reviewed->getUser()->getPseudo() ?>
                                                <span style="display: block; font-size: small"><?= $reviewed->getCreated() ?></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div style="display: flex; align-items: center; margin-bottom: .2rem">
                                        <?= $reviewed->getStarsReview() ?>
                                        <p style="margin-left: .2rem; font-size: small; font-weight: bolder"><?= $reviewed->getReviewRating() ?> sur 5</p>
                                    </div>
                                    <p style="font-weight: bolder"><?= $reviewed->getReviewTitle() ?></p>
                                    <p><?= $reviewed->getReviewText() ?></p>
                                </article>
                            <?php endforeach; ?>

                            <div style="margin-bottom: .8rem; border: 1px solid #ddd; border-radius: 9px; padding: .3rem">
                                <form method="post" action="<?= $item->getSlug() ?>/addReview" class="">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <p style="font-size: 1.5rem">Donner votre avis.</p>
                                    <label for="title">Note</label>
                                    <?= $review->getInputStars() ?>
                                    <label for="title">Titre</label>
                                    <input type="text" name="title" id="title" style="width: 100%; padding: 0 .3rem; border-radius: 9px" required>
                                    <label for="content" >Contenue :</label>
                                    <textarea minlength="20" name="content" id="content" rows="4" style="width: 100%; padding: 0 .3rem; border-radius: 9px" required></textarea>
                                    <div class="text-center mt-4">
                                        <?php if (UsersController::isUserLogged()): ?>
                                            <button type="submit" class="shop-btn-4875421">Envoyer <i class="fa-solid fa-paper-plane"></i></button>
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
            </div>
        </div>
    </div>
</section>

<script
    src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/carousel.js"></script>
<script>
    document.querySelectorAll('.shop-tab-link-487721').forEach((tab) => {
        tab.addEventListener('click', function () {
            document.querySelectorAll('.shop-tab-link-487721').forEach((btn) => btn.classList.remove('active'));
            document.querySelectorAll('.shop-tab-pane-697154').forEach((pane) => pane.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
</script>