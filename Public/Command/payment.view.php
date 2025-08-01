<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartContent */
/* @var CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity $selectedAddress */
/* @var CMW\Entity\Shop\Shippings\ShopShippingEntity $shippingMethod */
/* @var \CMW\Interface\Shop\IPaymentMethodV2[] $paymentMethods */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $appliedCartDiscounts*/
/* @var bool $isVirtualOnly */

Website::setTitle("Boutique - Tunnel de commande");
Website::setDescription("Méthode de paiement");

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center">Commander</h3>
    <div class="shop-grid-reviews-596587">
        <div class="shop-col-span-2-668745">
                <form id="payment" action="command/finalize" method="post">
                    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                    <div class="shop-cart-card-45854">
                        <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Méthode de paiement</h4>
                        <?php if (!empty($paymentMethods)): ?>
                        <?php foreach ($paymentMethods as $paymentMethod): ?>
                            <div class="shop-radio-delivery-45854" style="margin-top: 1rem">
                                <div style="display: flex; justify-content: space-between">
                                    <div>
                                        <label>
                                            <input name="paymentName" id="paymentName" type="radio"
                                                   value="<?= $paymentMethod->varName() ?>" required>
                                            <?= $paymentMethod->faIcon("fa-xl shop-blue-542154") ?> <?= $paymentMethod->name() ?>
                                        </label>
                                    </div>
                                    <div>
                                        <b>Frais <span id="fee_<?= $paymentMethod->varName() ?>"><?= $paymentMethod->getFeesFormatted() ?></span></b>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <p>Aucun moyen de paiement actif ou compatible !</p>
                        <?php endif; ?>
                    </div>
                </form>
                <div class="flex justify-between mt-4">
                    <form action="command/toShipping" method="post">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <button type="submit"  class="shop-button-48751">Précedent</button>
                    </form>
                    <button id="payment-button" form="payment" type="submit" class="shop-button-48751">
                        Payer
                    </button>
                </div>

        </div>

        <div>
            <?php if (!$isVirtualOnly && $selectedAddress): ?>
                <div class="shop-cart-card-45854" style="height: fit-content; margin-bottom: 1rem">
                    <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Adresse de livraison</h4>
                    <div>
                        <b><?= $selectedAddress->getLabel() ?></b><br>
                        <b><?= $selectedAddress->getFirstName() . ' ' . $selectedAddress->getLastName() ?></b> <?= $selectedAddress->getPhone() ?><br>
                        <?= $selectedAddress->getLine1() ?><br>
                        <?= $selectedAddress->getLine2() ?>
                        <?= $selectedAddress->getPostalCode() . ' ' . $selectedAddress->getCity() ?><br>
                        <?= $selectedAddress->getFormattedCountry() ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="shop-cart-card-45854" style="height: fit-content">
                <div>
                    <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Vos articles</h4>
                </div>
                <?php foreach ($cartContent as $cart): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center">
                        <div>
                            <table>
                                <td>
                                    <?php if ($cart->getFirstImageItemUrl() !== "/Public/Uploads/Shop/0"): ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $cart->getFirstImageItemUrl() ?>" alt="Panier">
                                    <?php else: ?>
                                        <img style="width: 3rem; height: 3rem; object-fit: cover"
                                             src="<?= $defaultImage ?>" alt="Panier">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $cart->getQuantity() ?> <?= $cart->getItem()->getName() ?>
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

                <?php if (!$isVirtualOnly && !is_null($shippingMethod)): ?>
                    <p style="font-weight: bolder; margin-top: 1rem">Livraison</p>
                    <div style="display: flex; justify-content: space-between">
                        <span><?= $shippingMethod->getName() ?></span>
                        <span><b><?= $shippingMethod->getPriceFormatted() ?></b></span>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 1.6rem; text-align: center">
                    <p style="font-weight: bolder; font-size: 1.3rem">Total</p>
                    <p id="total" style="font-weight: bolder; font-size: 1.8rem" data-total="<?= $cart->getTotalPriceComplete() ?>">
                        <?php
                        $price = number_format($cart->getTotalPriceComplete(), 2, '.', '');
                        $formatted = $cart->getTotalPriceCompleteFormatted();
                        echo str_replace($price, '<span id="price-value">' . $price . '</span>', $formatted);
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById("payment").addEventListener("submit", function(event) {
        const button = document.getElementById("payment-button");
        button.disabled = true;
        const icon = document.createElement("i");
        icon.className = "fa-solid fa-spinner fa-spin";
        icon.style.marginRight = "10px";
        button.textContent = "Paiement en cours...";
        button.prepend(icon);

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const paymentMethods = document.querySelectorAll('input[name="paymentName"]');
        const totalElement = document.getElementById('total');
        const priceSpan = document.getElementById('price-value');
        const originalTotal = parseFloat(totalElement.dataset.total);

        function updateTotal() {
            const selected = document.querySelector('input[name="paymentName"]:checked');
            if (!selected) return;

            const feeElement = document.getElementById(`fee_${selected.value}`);
            if (!feeElement) return;

            const fee = parseFloat(feeElement.textContent.replace(/[^\d.]/g, '')) || 0;
            const newTotal = originalTotal + fee;

            priceSpan.textContent = newTotal.toFixed(2);
        }

        paymentMethods.forEach(method => method.addEventListener('change', updateTotal));
    });

</script>