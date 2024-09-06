<?php
/* @var string $varName */

use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
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
    <label for="<?= $varName ?>_global">Intitulé de la carte :</label>
    <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_global') ?? 'Carte cadeau' ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center">Paramétrage du mail :</h5>
        <label for="<?= $varName ?>_title_mail">Titre :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Félicitations !' ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail">Message :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous avez reçu une carte cadeau d'une valeur de" ?>" type="text" name="<?= $varName ?>_text_mail" id="<?= $varName ?>_text_mail" class="input" required>
        <label for="<?= $varName ?>_prefix">Préfixe du code :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_prefix') ?? 'GC_' ?>" type="text" name="<?= $varName ?>_prefix" id="<?= $varName ?>_prefix" class="input" required>
        <label for="<?= $varName ?>_use_mail">Message footer :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_use_mail') ?? 'Utilisez ou partager ce code lors de votre prochain achat sur' ?>" type="text" name="<?= $varName ?>_use_mail" id="<?= $varName ?>_use_mail" class="input" required>
        <label for="<?= $varName ?>_url_mail">Url du site :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_url_mail') ?? Website::getUrl() . 'shop' ?>" type="text" name="<?= $varName ?>_url_mail" id="<?= $varName ?>_url_mail" class="input" required>
        <label for="<?= $varName ?>_time_mail">Temps restant :</label>
        <input value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_time_mail') ?? "Ce code est valable jusqu'au" ?>" type="text" name="<?= $varName ?>_time_mail" id="<?= $varName ?>_time_mail" class="input" required>
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
                    background-color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa' ?>;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    text-align: center;
                }

                .my-gift-card-scope .gift-card h2 {
                    color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f' ?>;
                }

                .my-gift-card-scope .gift-card p {
                    color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565' ?>;
                }

                .my-gift-card-scope .code {
                    font-size: 18px;
                    color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff' ?>;
                    margin: 20px 0;
                    padding: 10px;
                    background-color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>;
                    border-radius: 5px;
                    display: inline-block;
                }
            </style>

            <div style="background-color: <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff' ?>">
                <div class="my-gift-card-scope p-4">
                    <div class="gift-card">
                        <h2><?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_title_mail') ?? 'Félicitations !' ?></h2>
                        <p><?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_text_mail') ?? "Vous avez reçu une carte cadeau d'une valeur de" ?> <strong>XX</strong></p>
                        <div class="code"><?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_prefix') ?? 'GC_' ?>XXXXXXXX</div><br>
                        <p style="font-size: 0.8rem"><?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_use_mail') ?? 'Utilisez ou partager ce code lors de votre prochain achat sur' ?> <a href="%URL%"><?= Website::getWebsiteName() ?></a>.<br>
                            <?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_time_mail') ?? "Ce code est valable jusqu'au" ?> XX/XX/XX</p>
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
            <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_body_color') ?? '#ffffff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color">Couleur du fond</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_card_color" name="<?= $varName ?>_card_color" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_card_color') ?? '#f8f9fa' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_card_color">Couleur du fond du cadre</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_bg_color" name="<?= $varName ?>_code_bg_color" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_bg_color">Couleur de fond du code</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_color" name="<?= $varName ?>_code_color" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_code_color') ?? '#007bff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_color">Couleur du code</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_p" name="<?= $varName ?>_color_p" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_p') ?? '#656565' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_p">Couleur des textes</label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_title" name="<?= $varName ?>_color_title" value="<?= ShopItemsVirtualRequirementModel::getInstance()->getGlobalSetting($varName . '_color_title') ?? '#2f2f2f' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_title">Couleur du titre</label>
        </div>
    </div>
</section>
