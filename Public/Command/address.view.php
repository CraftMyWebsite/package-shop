<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

/* @var array $storedData */
/* @var CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartContent */
/* @var CMW\Entity\Shop\Country\ShopCountryEntity[] $country */
/* @var CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity[] $userAddresses */
/* @var \CMW\Model\Shop\Image\ShopImagesModel $defaultImage */
/* @var \CMW\Entity\Shop\Discounts\ShopDiscountEntity [] $appliedCartDiscounts*/

Website::setTitle("Boutique - Tunnel de commande");
Website::setDescription("Adresse de facturation et livraison");

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center">Commander</h3>
    <div class="shop-grid-reviews-596587">
        <div class="shop-col-span-2-668745">
            <form action="command/toDelivery" method="post">
                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                <div class="shop-cart-card-45854">
                    <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Mes adresses</h4>
                        <div class="shop-grid-3-command-input-47875">
                                <?php foreach ($userAddresses as $userAddress): ?>
                                <div class="shadow p-2">
                                    <input value="<?= $userAddress->getId() ?>" name="addressId" type="radio" <?php if ($userAddress->getIsFav()) {echo "checked";} ?>>
                                    <?= $userAddress->getLabel() ?><br>
                                    <b><?= $userAddress->getFirstName() . ' ' . $userAddress->getLastName() ?></b><br>
                                    <?= $userAddress->getPhone() ?><br>
                                    <?= $userAddress->getLine1() ?><br>
                                    <?= $userAddress->getLine2() ?>
                                    <?= $userAddress->getPostalCode() . " " . $userAddress->getCity() ?><br>
                                    <?= $userAddress->getFormattedCountry() ?>
                                </div>
                                <?php endforeach; ?>
                        </div>
                </div>
                <div style="display: flex; justify-content: end;">
                    <button type="submit"  class="shop-button-48751">Suivant</button>
                </div>
            </form>
            <div class="shop-cart-card-45854" style="margin-top: 1.6rem">
                <h4 style="font-weight: bold; font-size: 1.15rem; text-align: center">Nouvelle adresse</h4>
                    <form action="command/addAddress" method="post">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <div>
                            <div style="display: inline-flex; align-items: baseline; gap: .2rem; margin-bottom: .4rem" >
                                <div >
                                    <input name="fav" value="1" id="fav" type="checkbox" style="height: 1rem; width: 1rem; border-radius: .3rem">
                                </div>
                                <label for="fav" class="shop-label-478541">Définir comme favoris</label>
                            </div>
                            <div>
                                <label for="address_label" class="shop-label-478541">Nom de l'adresse <small>(Optionnel)</small> :</label>
                                <input value="<?= $storedData['address_label'] ?? '' ?>" name="address_label" id="address_label" type="text" class="shop-input-45753" placeholder="Domicile">
                            </div>
                            <div class="shop-grid-3-command-input-47875">
                                <div>
                                    <label for="first_name" class="shop-label-478541">Prénom<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['first_name'] ?? '' ?>" name="first_name" id="first_name" type="text" class="shop-input-45753" placeholder="Jean" required>
                                </div>
                                <div>
                                    <label for="last_name" class="shop-label-478541">Nom<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['last_name'] ?? '' ?>" name="last_name" type="text" id="last_name" class="shop-input-45753" placeholder="Dupont">
                                </div>
                                <div>
                                    <label for="phone" class="shop-label-478541">Téléphone<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['phone'] ?? '' ?>" name="phone" type="text" id="phone" class="shop-input-45753" placeholder="+33 601020304">
                                </div>
                            </div>
                            <div class="shop-grid-2-command-input-47875">
                                <div>
                                    <label for="line_1" class="shop-label-478541">Adresse<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['line_1'] ?? '' ?>" name="line_1" id="line_1" type="text" class="shop-input-45753" placeholder="12 avenue du paradis" required>
                                </div>
                                <div>
                                    <label for="line_2" class="shop-label-478541">Complément d'adresse <small>(Optionnel)</small> :</label>
                                    <input value="<?= $storedData['line_2'] ?? '' ?>" name="line_2" id="line_2" type="text" class="shop-input-45753" placeholder="Bâtiment, Lieu Dit" >
                                </div>
                            </div>
                            <div class="shop-grid-3-command-input-47875">
                                <div>
                                    <label for="city" class="shop-label-478541">Ville<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['city'] ?? '' ?>" name="city" id="city" type="text" class="shop-input-45753" placeholder="Paradis" required>
                                </div>
                                <div>
                                    <label for="postal_code" class="shop-label-478541">Code postale<span style="color: red">*</span> :</label>
                                    <input value="<?= $storedData['postal_code'] ?? '' ?>" name="postal_code" type="text" id="postal_code" class="shop-input-45753" placeholder="00001">
                                </div>
                                <div>
                                    <label for="country" class="shop-label-478541">Pays<span style="color: red">*</span> :</label>
                                    <select name="country" id="country" class="shop-input-45753">
                                        <?php foreach ($country as $countryEntity) : ?>
                                            <option value="<?= $countryEntity->getCode() ?>"
                                                <?= $storedData['country'] === $countryEntity->getCode() ? 'selected' : '' ?>>
                                                <?= $countryEntity->getName() ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center; margin-top: 1rem">
                            <button type="submit" class="shop-button-48751">Ajouter</button>
                        </div>
                    </form>
            </div>
        </div>

        <div>
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

