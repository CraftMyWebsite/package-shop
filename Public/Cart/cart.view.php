<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
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
            <div style="height: fit-content; margin-bottom: 1rem" class="shop-col-span-3-157655 shop-cart-card-45854">
                <div style="overflow-x: auto; position: relative; height: fit-content">
                    <table style="width: 100%; text-align: left; font-size: .9rem">
                        <thead style="font-weight: bold">
                        <tr>
                            <th style="padding: 0.75rem 1.25rem">
                            </th>
                            <th style="padding: 0.75rem 1.25rem;">
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

                <div style="display: flex; justify-content: end;">
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


            <div class="shop-cart-card-45854" style="height: fit-content">
                <div style="font-weight: bold; font-size: 1.15rem; text-align: center">
                    <h5>Total panier</h5>
                </div>
                <div class="shop-grid-cart-total-47875">
                    <p>Sous total</p>
                    <p style="font-weight: bolder"><?= isset($cart) ? $cart->getTotalCartPriceBeforeDiscountFormatted() : 0 ?></p>
                </div>
                <div class="shop-grid-cart-total-47875">
                    <p>Réduction</p>
                </div>
                <div class="shop-grid-cart-total-47875">
                    <?php foreach ($appliedDiscounts as $appliedDiscount): ?>
                        <div style="font-weight: bolder">
                            Code : <b><?= $appliedDiscount->getDiscount()->getCode() ?></b>
                        </div>
                        <div style="display: flex; justify-content: space-between">
                            <span style="font-weight: bolder"><?= $appliedDiscount->getDiscountFormatted() ?></span>
                            <a href="<?= $appliedDiscount->getRemoveLink() ?>" style="color: #df3232"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="shop-grid-cart-total-47875" style="margin-top: 1.6rem">
                    <p style="font-weight: bolder; font-size: 1.2rem">Total</p>
                    <p style="font-weight: bolder; font-size: 1.2rem"><?= isset($cart) ? $cart->getTotalCartPriceAfterDiscountFormatted() : 0 ?></p>
                </div>
                <div style="display: flex; justify-content: center; margin-top: 1rem">
                    <a class="shop-button-48751" href="command">Commander</a>
                </div>
            </div>
        </div>


    <?php if ($asideCartContent !== []): ?>
    <h3 style="text-align: center; margin-top: 3rem">Article mis de côté</h3>
        <section class="shop-cart-card-45854">
                <div style="overflow-x: auto; position: relative; height: fit-content">
                    <table style="width: 100%; text-align: left; font-size: .9rem">
                        <thead style="font-weight: bold">
                        <tr>
                            <th style="padding: 0.75rem 1.25rem"></th>
                            <th style="padding: 0.75rem 1.25rem">Produit</th>
                            <?php if ($showPublicStock): ?>
                                <th style="padding: 0.75rem 1.25rem; text-align: center">Stock restant</th>
                            <?php endif ?>
                            <th style="padding: 0.75rem 1.25rem; text-align: center">Prix</th>
                            <th style="padding: 0.75rem 1.25rem"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($asideCartContent as $asideCart): ?>
                            <tr>
                                <td style="padding: 0.75rem 1.25rem">
                                    <?php if ($asideCart->getFirstImageItemUrl() !== '/Public/Uploads/Shop/0'): ?>
                                        <img class="mx-auto" style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $asideCart->getFirstImageItemUrl() ?>" alt="Panier">
                                    <?php else: ?>
                                        <img class="mx-auto" style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $defaultImage ?>" alt="Panier">
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 0.75rem 1.25rem">
                                    <?= $asideCart->getItem()->getName() ?>
                                </td>
                                <?php if ($showPublicStock): ?>
                                    <td style="padding: 0.75rem 1.25rem; text-align: center">
                                        <?= $asideCart->getItem()->getPublicFormattedStock() ?>
                                    </td>
                                <?php endif ?>
                                <td style="padding: 0.75rem 1.25rem; text-align: center">
                                    <?= $asideCart->getItem()->getPriceFormatted() ?>
                                </td>
                                <td style="padding: 0.75rem 1.25rem; text-align: center">
                                    <a href="<?= $asideCart->getUnAsideLink() ?>" style="color: #0a58ca; margin-right: .6rem">
                                        <i class="fa-solid fa-cart-arrow-down"></i></a>
                                    <a href="<?= $asideCart->getRemoveLink() ?>" style="color: #cf3434"><i
                                            class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
        </section>
    <?php endif; ?>
</section>

