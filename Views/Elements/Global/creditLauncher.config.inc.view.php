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
    <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global') ?? Website::getWebsiteName().' - Votre avoir pour la commande ' ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
    <small>Le numéro de commande sera toujours afficher dans l'objet du mail !</small>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center">Paramétrage du mail :</h5>
        <label for="<?= $varName ?>_title_mail">Titre :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Avoir pour ' ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail">Message :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous venez de recevoir un avoir suite à l'annulation d'une commande non réalisable" ?>" type="text" name="<?= $varName ?>_text_mail" id="<?= $varName ?>_text_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail_value">Message valeur :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail_value') ?? "Ce code à une valeur total de " ?>" type="text" name="<?= $varName ?>_text_mail_value" id="<?= $varName ?>_text_mail_value" class="input" required>
        <label for="<?= $varName ?>_footer_1_mail">Message footer 1 :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail') ?? 'Vous pouvez utiliser cet avoir sur toute la boutique !' ?>" type="text" name="<?= $varName ?>_footer_1_mail" id="<?= $varName ?>_footer_1_mail" class="input" required>
        <label for="<?= $varName ?>_footer_2_mail">Message footer 2 :</label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail') ?? "Rendez-vous sur la boutique " . Website::getWebsiteName() ?>" type="text" name="<?= $varName ?>_footer_2_mail" id="<?= $varName ?>_footer_2_mail" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center">Aperçu du mail :</h5>
        <div class="border dark:border-gray-700">
            <style>
                .my-gift-card-scope .gift-card {
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa' ?>;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    text-align: center;
                }

                .my-gift-card-scope .gift-card h2 {
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f' ?>;
                }

                .my-gift-card-scope .gift-card p {
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565' ?>;
                }

                .my-gift-card-scope .code {
                    font-size: 18px;
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff' ?>;
                    margin: 20px 0;
                    padding: 10px;
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>;
                    border-radius: 5px;
                    display: inline-block;
                }
            </style>

            <div style="background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff' ?>">
                <div class="my-gift-card-scope p-4">
                    <div class="gift-card">
                        <h2><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Avoir pour ' ?>XXXXXXX</h2>
                        <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous venez de recevoir un avoir suite à l'annulation d'une commande non réalisable" ?></p>
                        <p><strong><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail_value') ?? "Ce code à une valeur total de " ?> XX.XX€</strong></p>
                        <div class="code">XXXXXXXXX</div><br>
                        <a href="#"><p style="font-size: 0.8rem"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail') ?? 'Vous pouvez utiliser cet avoir sur toute la boutique !' ?><br>
                            <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail') ?? "Rendez-vous sur la boutique " . Website::getWebsiteName() ?></p></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="mt-4">
    <h5>Apparences :</h5>
    <div class="grid-3">
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color">Couleur du fond</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_card_color" name="<?= $varName ?>_card_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_card_color">Couleur du fond du cadre</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_bg_color" name="<?= $varName ?>_code_bg_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_bg_color">Couleur de fond du code</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_color" name="<?= $varName ?>_code_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_color">Couleur du code</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_p" name="<?= $varName ?>_color_p" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_p">Couleur des textes</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_title" name="<?= $varName ?>_color_title" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_title">Couleur du titre</label>
        </div>
    </div>
</section>
