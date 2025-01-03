<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartContent */
/* @var CMW\Entity\Shop\Carts\ShopCartItemEntity[] $asideCartContent */
/* @var CMW\Model\Shop\Cart\ShopCartVariantesModel $itemsVariantes */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Entity\Shop\Carts\ShopCartDiscountEntity[] $appliedDiscounts */
/* @var bool $showPublicStock */

Website::setTitle('Boutique - Panier');
Website::setDescription('Votre panier');
?>
<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center">Panier</h3>
        <div class="shop-grid-main-5548754">
            <div class="shop-col-span-3-157655">

                <div style="overflow-x: auto; position: relative; height: fit-content">
                    <table style="width: 100%; text-align: left; font-size: .9rem">
                        <thead style="font-weight: bold">
                        <tr>
                            <th style="padding: 0.75rem 1.25rem">
                            </th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center">
                                Produit
                            </th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center">
                                Quantité
                            </th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center">
                                Prix
                            </th>
                            <th></th>
                            <th style="padding: 0.75rem 1.25rem; text-align: center">
                                Sous total
                            </th>
                            <th style="padding: 0.75rem 1.25rem">

                            </th>
                        </tr>
                        </thead>
                        <tbody style="text-align: center">
                        <?php foreach ($cartContent as $cart): ?>
                            <tr>
                                <td style="padding: 0.75rem 1.25rem">
                                    <?php if ($cart->getFirstImageItemUrl() !== '/Public/Uploads/Shop/0'): ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover; margin: auto"
                                             src="<?= $cart->getFirstImageItemUrl() ?>" alt="Panier">
                                    <?php else: ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover; margin: auto"
                                             src="<?= $defaultImage ?>" alt="Panier">
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem 1.25rem">
                                    <span style="font-weight: bold"><a style="color: #0A58CA" href="<?= $cart->getItem()->getItemLink() ?>"><?= $cart->getItem()->getName() ?></a></span><br>
                                    <small>
                                        <?php foreach ($itemsVariantes->getShopItemVariantValueByCartId($cart->getId()) as $itemVariant): ?>
                                            <?= $itemVariant->getVariantValue()->getVariant()->getName() ?> : <?= $itemVariant->getVariantValue()->getValue() ?><br>
                                        <?php endforeach; ?>
                                    </small>
                                </td>
                                <td style="padding: 0.75rem 1.25rem">
                                    <div style="display: flex; justify-content: center; align-items: center; gap: .2rem">
                                        <a href="<?= $cart->getDecreaseQuantityLink() ?>"><i
                                                class="fa-solid fa-minus"></i> </a>
                                        <b style="font-size: 1rem"><?= $cart->getQuantity() ?></b> <a
                                            href="<?= $cart->getIncreaseQuantityLink() ?>"><i
                                                class="fa-solid fa-plus"></i></a>
                                    </div>
                                </td>
                                <td style="padding: 0.75rem 1.25rem">
                                    <?= $cart->getItem()->getPriceFormatted() ?>
                                </td>
                                <td style="padding: 0.75rem 1.25rem; font-weight: bold">
                                    <?= $cart->getDiscountFormatted() ?> <?= $cart->getItem()->getDiscountImpactDefaultApplied() ?>
                                </td>
                                <td style="padding: 0.75rem 1.25rem">
                                    <?php if ($cart->getDiscount()): ?>
                                        <s><?= $cart->getItemTotalPriceFormatted() ?></s> <span style="font-weight: bold"><?= $cart->getItemTotalPriceAfterDiscountFormatted() ?></span>
                                    <?php else: ?>
                                        <span style="font-weight: bold"><?= $cart->getItemTotalPriceFormatted() ?></span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center">
                                    <a href="<?= $cart->getAsideLink() ?>" style="color: #5c5ce8"><i class="fa-solid fa-arrow-up-from-bracket"></i></a>
                                    <a href="<?= $cart->getRemoveLink() ?>" style="color: #c31a1a; margin-left: 1rem"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: end; margin-top: 1rem">
                    <div style="width: 30%">
                        <div style="display: flex">
                            <div style="position: relative; width: 100%">
                                <form action="cart/discount/apply" method="post">
                                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                    <input type="text" autocomplete="off" name="code"
                                       class="shop-input-search-587254"
                                       placeholder="Code promo / Carte cadeau">
                                    <button type="submit" class="shop-button-search-565787">
                                        Appliquer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div style="height: fit-content; margin-top: 1rem">
                <div style="font-weight: bold; font-size: 1.15rem; text-align: center" >
                    <h5>Total panier</h5>
                </div>
                <div class="grid grid-cols-2 bg-white">
                    <div class="font-medium text-center">
                        <p class="py-2 border-b">Sous total</p>
                    </div>
                    <div class="text-center">
                        <p class="py-2 border-b">
                            <?= isset($cart) ? $cart->getTotalCartPriceBeforeDiscountFormatted() : 0 ?>
                        </p>
                    </div>
                </div>
                <div class="grid grid-cols-2 bg-white">
                    <div class="font-medium text-center">
                        <p class="py-2">Réduction</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 bg-white">
                    <?php foreach ($appliedDiscounts as $appliedDiscount): ?>
                        <div class="text-center mb-2">
                            Code : <b><?= $appliedDiscount->getDiscount()->getCode() ?></b>
                        </div>
                        <div class="text-center">
                            <b><?= $appliedDiscount->getDiscountFormatted() ?></b>
                            <a href="<?= $appliedDiscount->getRemoveLink() ?>" class="font-medium ml-4 text-red-600"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="grid grid-cols-2 bg-white border-t">
                    <div class="font-medium text-center">
                        <p class="py-2 ">Total</p>
                    </div>
                    <div class="text-center">
                        <p class="py-2 font-medium">
                            <?= isset($cart) ? $cart->getTotalCartPriceAfterDiscountFormatted() : 0 ?>
                        </p>
                    </div>
                </div>
                <a href="command">
                    <div
                        class="bg-blue-700 rounded-b-lg text-white hover:bg-blue-800 font-medium text-sm px-2 py-3 text-center">
                        Commander
                    </div>
                </a>
            </div>
        </div>


    <?php if ($asideCartContent !== []): ?>
        <section class="bg-white rounded-lg shadow my-8 sm:mx-12 lg:mx-72">
            <div class="container p-4">
                <h4 class="text-center mb-2">Article mis de côté</h4>
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg h-fit">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="font-medium text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="py-3 px-6">
                            </th>
                            <th class="text-center py-3 px-6">
                                Produit
                            </th>
                            <?php if ($showPublicStock): ?>
                                <th class="text-center py-3 px-6">
                                    Stock restant
                                </th>
                            <?php endif ?>
                            <th class="text-center py-3 px-6">
                                Prix
                            </th>
                            <th class="py-3 px-6">

                            </th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        <?php foreach ($asideCartContent as $asideCart): ?>
                            <tr class="bg-white border-b text-center">
                                <td class="py-2">
                                    <?php if ($asideCart->getFirstImageItemUrl() !== '/Public/Uploads/Shop/0'): ?>
                                        <img class="mx-auto" style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $asideCart->getFirstImageItemUrl() ?>" alt="Panier">
                                    <?php else: ?>
                                        <img class="mx-auto" style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $defaultImage ?>" alt="Panier">
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 font-semibold text-gray-900">
                                    <?= $asideCart->getItem()->getName() ?>
                                </td>
                                <?php if ($showPublicStock): ?>
                                    <td class="py-4 px-6 text-center">
                                        <?= $asideCart->getItem()->getPublicFormattedStock() ?>
                                    </td>
                                <?php endif ?>
                                <td class="py-4 px-6 text-gray-900">
                                    <?= $asideCart->getItem()->getPriceFormatted() ?>
                                </td>
                                <td>
                                    <a href="<?= $asideCart->getUnAsideLink() ?>" class="mr-4 font-medium text-blue-700">
                                        <i class="fa-solid fa-cart-arrow-down"></i></a>
                                    <a href="<?= $asideCart->getRemoveLink() ?>" class="font-medium ml-4 text-red-600"><i
                                            class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    <?php endif; ?>
</section>

