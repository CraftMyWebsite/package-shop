<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity $userAddress */
/* @var CMW\Entity\Shop\Country\ShopCountryEntity[] $country */

Website::setTitle('Boutique - Paramètres');
Website::setDescription('Gérer vos paramètres de boutique');

?>

<link rel="stylesheet"
      href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>App/Package/Shop/Public/Resources/style.css">

<section class="shop-section-45875487">
    <h3 style="text-align: center; margin-bottom: 1rem">Paramètres de la boutique</h3>

    <div class="shop-cart-card-45854">
        <div style="font-weight: bold; font-size: 1.15rem; text-align: center">
            <h5>Édition de <?= $userAddress->getLabel() ?></h5>
        </div>
    <div class="container mx-auto rounded-md shadow-lg p-4 h-fit mt-4">
        <form action="" method="post">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div>
                <div style="display: inline-flex; align-items: baseline; gap: .2rem; margin-bottom: .4rem" >
                    <div >
                        <input <?= $userAddress->getIsFav() ? 'checked' : '' ?> name="fav" value="1" id="fav" type="checkbox" style="height: 1rem; width: 1rem; border-radius: .3rem">
                    </div>
                    <label for="fav" class="shop-label-478541">Définir comme favoris</label>
                </div>
                <div>
                    <label for="address_label" class="shop-label-478541">Nom de l'adresse <small>(Optionnel)</small> :</label>
                    <input value="<?= $userAddress->getLabel() ?>" name="address_label" id="address_label" type="text" class="shop-input-45753" placeholder="Domicile">
                </div>
                <div class="shop-grid-3-command-input-47875">
                    <div>
                        <label for="first_name" class="shop-label-478541">Prénom<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getFirstName() ?>" name="first_name" id="first_name" type="text" class="shop-input-45753" placeholder="Jean" required>
                    </div>
                    <div>
                        <label for="last_name" class="shop-label-478541">Nom<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getLastName() ?>" name="last_name" type="text" id="last_name" class="shop-input-45753" placeholder="Dupont">
                    </div>
                    <div>
                        <label for="phone" class="shop-label-478541">Téléphone<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getPhone() ?>" name="phone" type="text" id="phone" class="shop-input-45753" placeholder="+33 601020304">
                    </div>
                </div>
                <div class="shop-grid-2-command-input-47875">
                    <div>
                        <label for="line_1" class="shop-label-478541">Adresse<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getLine1() ?>" name="line_1" id="line_1" type="text" class="shop-input-45753" placeholder="12 avenue du paradis" required>
                    </div>
                    <div>
                        <label for="line_2" class="shop-label-478541">Complément d'adresse <small>(Optionnel)</small> :</label>
                        <input value="<?= $userAddress->getLine2() ?>" name="line_2" id="line_2" type="text" class="shop-input-45753" placeholder="Bâtiment, Lieu Dit" >
                    </div>
                </div>
                <div class="shop-grid-3-command-input-47875">
                    <div>
                        <label for="city" class="shop-label-478541">Ville<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getCity() ?>" name="city" id="city" type="text" class="shop-input-45753" placeholder="Paradis" required>
                    </div>
                    <div>
                        <label for="postal_code" class="shop-label-478541">Code postale<span style="color: red">*</span> :</label>
                        <input value="<?= $userAddress->getPostalCode() ?>" name="postal_code" type="text" id="postal_code" class="shop-input-45753" placeholder="00001">
                    </div>
                    <div>
                        <label for="country" class="shop-label-478541">Pays<span style="color: red">*</span> :</label>
                        <select name="country" id="country" class="shop-input-45753">
                            <?php foreach ($country as $countryEntity) : ?>
                                <option value="<?= $countryEntity->getCode() ?>"
                                    <?= $userAddress->getCountry() === $countryEntity->getCode() ? 'selected' : '' ?>>
                                    <?= $countryEntity->getName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div style="display: flex; justify-content: center; margin-top: 1rem">
                <button type="submit" class="shop-button-48751">Appliquer</button>
            </div>
        </form>
    </div>
</section>