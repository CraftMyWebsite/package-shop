<?php
/* @var string $varName */

use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

?>
<style>
    input[type='color'] {
        -webkit-appearance: none;
        border: black solid 1px;
        width: 20px;
        height: 20px;
        cursor: pointer;
        padding: 0;
    }

    input[type='color']::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    input[type='color']::-webkit-color-swatch {
        border: none;
    }
    input[type='color']::-moz-color-swatch {
        border: none;
    }
</style>

<section>
    <label for="<?= $varName ?>_global">Objet du mail :</label>
    <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_object', $varName) ?? Website::getWebsiteName() . ' - Récapitulatif de Commande' ?>" type="text" name="<?= $varName ?>_object" id="<?= $varName ?>_object" class="input" required>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center">Paramétrage du mail :</h5>
        <label for="<?= $varName ?>_title_mail">Titre :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? 'Votre commande sur ' . Website::getWebsiteName() ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_command_number">N° de commande :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_command_number', $varName) ?? "Numéro de commande :" ?>" type="text" name="<?= $varName ?>_command_number" id="<?= $varName ?>_command_number" class="input" required>
        <label for="<?= $varName ?>_date">Date de commande :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_date', $varName) ?? 'Date de la commande :' ?>" type="text" name="<?= $varName ?>_date" id="<?= $varName ?>_date" class="input" required>
        <div class="grid-3">
            <div>
                <label for="<?= $varName ?>_item">Article :</label>
                <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_item', $varName) ?? "Article" ?>" type="text" name="<?= $varName ?>_item" id="<?= $varName ?>_item" class="input" required>
            </div>
            <div>
                <label for="<?= $varName ?>_quantity">Quantité :</label>
                <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_quantity', $varName) ?? "Quantité" ?>" type="text" name="<?= $varName ?>_quantity" id="<?= $varName ?>_quantity" class="input" required>
            </div>
            <div>
                <label for="<?= $varName ?>_price">Prix :</label>
                <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_price', $varName) ?? "Prix" ?>" type="text" name="<?= $varName ?>_price" id="<?= $varName ?>_price" class="input" required>
            </div>
        </div>
        <label for="<?= $varName ?>_payment">Méthode de paiement :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_payment', $varName) ?? 'Méthode de paiement :' ?>" type="text" name="<?= $varName ?>_payment" id="<?= $varName ?>_payment" class="input" required>
        <label for="<?= $varName ?>_history">Rappel de l'historique :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_history', $varName) ?? 'Consultez mes commandes sur ' .Website::getWebsiteName()  ?>" type="text" name="<?= $varName ?>_history" id="<?= $varName ?>_history" class="input" required>
        <label for="<?= $varName ?>_invoice">Facture :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_invoice', $varName) ?? 'Télécharger votre facture'  ?>" type="text" name="<?= $varName ?>_invoice" id="<?= $varName ?>_invoice" class="input" required>
        <label for="<?= $varName ?>_footer">Footer :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer', $varName) ?? "Merci pour votre achat !" ?>" type="text" name="<?= $varName ?>_footer" id="<?= $varName ?>_footer" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center">Aperçu du mail :</h5>
        <div class="border dark:border-gray-700">
            <style>
                .container-recap {
                    font-family: Helvetica, serif;
                    width: 600px;
                    margin: 20px auto;
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                .header-recap {
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_head_color', $varName) ?? '#214e7e' ?>;
                    color: white;
                    padding: 10px;
                    text-align: center;
                    border-radius: 5px 5px 0 0;
                }
                .summary-item-recap {
                    border-bottom: 1px solid #eee;
                    padding: 10px 0;
                }
                .summary-item:last-child-recap {
                    border-bottom: none;
                }
                .summary-title-recap {
                    font-weight: bold;
                }
                .footer-recap {
                    text-align: center;
                    margin-top: 20px;
                    color: #777;
                }
            </style>

            <div class="container-recap">
                <div class="header-recap">
                    <h2><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? 'Votre commande sur ' . Website::getWebsiteName() ?></h2>
                </div>
                <div class="summary-recap">
                    <div class="summary-item-recap">
                        <span class="summary-title-recap"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_command_number', $varName) ?? "Numéro de commande :" ?></span> XXXXXXX
                    </div>
                    <div class="summary-item-recap">
                        <span class="summary-title-recap"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_date', $varName) ?? 'Date de la commande :' ?></span> XXXXXXX
                    </div>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_item', $varName) ?? "Article" ?> : XXXXXXX<br>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_quantity', $varName) ?? "Quantité" ?> : XXXXXXX<br>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_price', $varName) ?? "Prix" ?> : XXXXXXX<br>
                    <div class="summary-item-recap">
                        <span class="summary-title-recap">Total :</span> <b>XXXXXXX</b>
                        <br>
                        <span class="summary-title-recap"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_payment', $varName) ?? 'Méthode de paiement :' ?></span> XXXXXXX
                        <a><p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_history', $varName) ?? 'Consultez mes commandes sur ' .Website::getWebsiteName()  ?></p></a>
                        <a><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_invoice', $varName) ?? 'Télécharger votre facture'  ?></a>
                    </div>
                </div>
                <div class="footer-recap">
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer', $varName) ?? "Merci pour votre achat !" ?>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="mt-4">
    <h5>Apparences :</h5>
    <div class="grid-3">
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_head_color" name="<?= $varName ?>_head_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_head_color', $varName) ?? '#214e7e' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_head_color">Couleur de l'en tête</label>
        </div>
    </div>
</section>
