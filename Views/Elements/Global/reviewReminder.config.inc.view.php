<?php
/* @var string $varName */

use CMW\Manager\Lang\LangManager;
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
    <label for="<?= $varName ?>_global"><?= LangManager::translate('shop.views.elements.global.reviewReminder.object') ?></label>
    <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global', $varName) ?? Website::getWebsiteName().LangManager::translate('shop.views.elements.global.reviewReminder.object-default') ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.global.reviewReminder.mail') ?></h5>
        <label for="<?= $varName ?>_title_mail"><?= LangManager::translate('shop.views.elements.global.reviewReminder.title') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.title-default') ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail"><?= LangManager::translate('shop.views.elements.global.reviewReminder.message') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.message-default') ?>" type="text" name="<?= $varName ?>_text_mail" id="<?= $varName ?>_text_mail" class="input" required>
        <label for="<?= $varName ?>_footer_1_mail"><?= LangManager::translate('shop.views.elements.global.reviewReminder.footer-1') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.footer-1-default') ?>" type="text" name="<?= $varName ?>_footer_1_mail" id="<?= $varName ?>_footer_1_mail" class="input" required>
        <label for="<?= $varName ?>_footer_2_mail"><?= LangManager::translate('shop.views.elements.global.reviewReminder.footer-2') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.footer-2-default') ?>" type="text" name="<?= $varName ?>_footer_2_mail" id="<?= $varName ?>_footer_2_mail" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.global.reviewReminder.preview') ?></h5>
        <div class="border dark:border-gray-700">
            <style>
                .my-gift-card-scope .gift-card {
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color', $varName) ?? '#f8f9fa' ?>;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    text-align: center;
                }

                .my-gift-card-scope .gift-card h2 {
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title', $varName) ?? '#2f2f2f' ?>;
                }

                .my-gift-card-scope .gift-card p {
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p', $varName) ?? '#656565' ?>;
                }

                .my-gift-card-scope .code {
                    font-size: 18px;
                    color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color', $varName) ?? '#007bff' ?>;
                    margin: 20px 0;
                    padding: 10px;
                    background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color', $varName) ?? '#e9ecef' ?>;
                    border-radius: 5px;
                    display: inline-block;
                }
            </style>

            <div style="background-color: <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e' ?>">
                <div class="my-gift-card-scope p-4">
                    <div class="gift-card">
                        <h2><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.title-default') ?></h2>
                        <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.message-default') ?></p>
                        <div class="code">PRODUCT_NAME</div><br>
                        <p style="font-size: 0.8rem"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.footer-1-default') ?><br>
                            <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail', $varName) ?? LangManager::translate('shop.views.elements.global.reviewReminder.footer-2-default') ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="mt-4">
    <h5><?= LangManager::translate('shop.views.elements.global.reviewReminder.appearances') ?></h5>
    <div class="grid-3">
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color"><?= LangManager::translate('shop.views.elements.global.reviewReminder.back') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_card_color" name="<?= $varName ?>_card_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color', $varName) ?? '#f8f9fa' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_card_color"><?= LangManager::translate('shop.views.elements.global.reviewReminder.card') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_bg_color" name="<?= $varName ?>_code_bg_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color', $varName) ?? '#e9ecef' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_bg_color"><?= LangManager::translate('shop.views.elements.global.reviewReminder.code') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_color" name="<?= $varName ?>_code_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color', $varName) ?? '#007bff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_color"><?= LangManager::translate('shop.views.elements.global.reviewReminder.code-color') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_p" name="<?= $varName ?>_color_p" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p', $varName) ?? '#656565' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_p"><?= LangManager::translate('shop.views.elements.global.reviewReminder.text-color') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_title" name="<?= $varName ?>_color_title" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title', $varName) ?? '#2f2f2f' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_title"><?= LangManager::translate('shop.views.elements.global.reviewReminder.title-color') ?></label>
        </div>
    </div>
</section>
