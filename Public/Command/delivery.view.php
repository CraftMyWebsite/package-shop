<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartContent */
/* @var CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity $selectedAddress */
/* @var CMW\Entity\Shop\Shippings\ShopShippingEntity[] $shippings */
/* @var CMW\Entity\Shop\Shippings\ShopShippingEntity[] $withdrawPoints */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $appliedCartDiscounts*/
/* @var bool $useInteractiveMap */

Website::setTitle("Boutique - Tunnel de commande");
Website::setDescription("Méthode de livraison");

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center">Commander</h3>
    <div class="shop-grid-reviews-596587">
        <div class="shop-col-span-2-668745">
            <div class="shop-cart-card-45854">
                <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Expédition / Point de retrait</h4>
                <form id="toPayment" action="command/toPayment" method="post">
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                    <?php if (!empty($shippings)): ?>
                        <h4 style="font-weight: bold; font-size: 1.15rem">Expédition</h4>
                    <small>Recevez vos colis directement chez vous</small>
                    <?php foreach ($shippings as $shipping): ?>
                        <div class="shop-radio-delivery-45854">
                            <div style="display: flex; justify-content: space-between; font-size: 1.1rem">
                                <div>
                                    <label>
                                    <input name="shippingId" type="radio" value="<?= $shipping->getId() ?>"> <?= $shipping->getName() ?>
                                    </label>
                                </div>
                                <div>
                                    <b><?= $shipping->getPriceFormatted() ?></b>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <hr>
                    <?php endif; ?>
                    <?php if (!empty($withdrawPoints)): ?>
                        <h4 style="font-weight: bold; font-size: 1.15rem; margin-top: 1rem">Point de retrait</h4>
                        <small>Venez chercher votre colis dans nos points de distribution</small>
                    <div class="<?= $useInteractiveMap ? 'shop-grid-3-command-input-47875' : '' ?>">
                        <div>
                            <?php foreach ($withdrawPoints as $withdrawPoint): ?>
                                <div class="shop-radio-delivery-45854" style="margin-bottom: 1rem">
                                    <div style="display: flex; justify-content: space-between">
                                        <div>
                                            <label>
                                                <input name="shippingId" type="radio" value="<?= $withdrawPoint->getId() ?>" data-id="<?= $withdrawPoint->getId() ?>" class="withdraw-radio"> <?= $withdrawPoint->getName() ?>
                                            </label>
                                        </div>
                                        <div>
                                            <b><?= $withdrawPoint->getPriceFormatted() ?></b>
                                        </div>
                                    </div>
                                    Distance du point : <b><?= $withdrawPoint->getDistance($selectedAddress->getLatitude(), $selectedAddress->getLongitude()) ?> km</b>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($useInteractiveMap): ?>
                        <div class="shop-col-span-2-668745">
                            <div id="map" style="height: 400px; border: 1px solid #cdc9c9; border-radius: 12px"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 1rem">
                <form action="command/toAddress" method="post">
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                    <button type="submit"  class="shop-button-48751">Précedent</button>
                </form>
                <button form="toPayment" type="submit" class="shop-button-48751">Suivant</button>
            </div>
        </div>

        <div>
            <div class="shop-cart-card-45854" style="height: fit-content; margin-bottom: 1rem">
                <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Adresse de livraison</h4>
                <div>
                    <div>
                        <b><?= $selectedAddress->getLabel() ?></b><br>
                        <b><?= $selectedAddress->getFirstName() . ' ' . $selectedAddress->getLastName() ?></b> <?= $selectedAddress->getPhone() ?><br>
                        <?= $selectedAddress->getLine1() ?><br>
                        <?= $selectedAddress->getLine2() ?>
                        <?= $selectedAddress->getPostalCode() . ' ' . $selectedAddress->getCity() ?><br>
                        <?= $selectedAddress->getFormattedCountry() ?>
                    </div>
                </div>
            </div>
            <div class="shop-cart-card-45854" style="height: fit-content">
                <div>
                    <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Vos articles</h4>
                </div>
                <?php foreach ($cartContent as $cart): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <div>
                            <table>
                                <td>
                                    <?php if ($cart->getFirstImageItemUrl() !== '/Public/Uploads/Shop/0'): ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $cart->getFirstImageItemUrl() ?>" alt="Panier">
                                    <?php else: ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $defaultImage ?>" alt="Panier">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <b><?= $cart->getQuantity() ?></b> <?= $cart->getItem()->getName() ?>
                                </td>
                            </table>
                        </div>
                        <div>
                            <?php if ($cart->getDiscount()): ?>
                                <s><?= $cart->getItemTotalPriceFormatted() ?></s> <span style="font-weight: bolder"><?= $cart->getItemTotalPriceAfterDiscountFormatted() ?></span>
                            <?php else: ?>
                                <span style="font-weight: bolder"><?= $cart->getItemTotalPriceFormatted() ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (!empty($appliedCartDiscounts)): ?>
                    <p style="font-weight: bolder; margin-top: 1rem">Réduction total :</p>
                    <?php foreach ($appliedCartDiscounts as $appliedCartDiscount): ?>
                        <div style="display: flex; justify-content: space-between">
                            <span><?= $appliedCartDiscount->getCode() ?></span>
                            <span><b>-<?= $appliedCartDiscount->getPriceFormatted() ?></b></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div style="margin-top: 1.6rem; text-align: center">
                    <p style="font-weight: bolder; font-size: 1.3rem">Total</p>
                    <p style="font-weight: bolder; font-size: 1.8rem"><?= $cart->getTotalCartPriceAfterDiscountFormatted() ?></p>
                </div>
            </div>
        </div>
    </div>
</section>