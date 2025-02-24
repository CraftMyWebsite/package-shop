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
    <label for="<?= $varName ?>_global"><?= LangManager::translate('shop.views.elements.global.creditLauncher.object') ?></label>
    <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_global', $varName) ?? Website::getWebsiteName().LangManager::translate('shop.views.elements.global.creditLauncher.object-default') ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
    <small><?= LangManager::translate('shop.views.elements.global.creditLauncher.object-info') ?></small>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.global.creditLauncher.mail-setting') ?></h5>
        <label for="<?= $varName ?>_title_mail"><?= LangManager::translate('shop.views.elements.global.creditLauncher.title') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.title-default') ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail"><?= LangManager::translate('shop.views.elements.global.creditLauncher.message') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.message-default') ?>" type="text" name="<?= $varName ?>_text_mail" id="<?= $varName ?>_text_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail_value"><?= LangManager::translate('shop.views.elements.global.creditLauncher.message-value') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail_value', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.message-value-default') ?>" type="text" name="<?= $varName ?>_text_mail_value" id="<?= $varName ?>_text_mail_value" class="input" required>
        <label for="<?= $varName ?>_footer_1_mail"><?= LangManager::translate('shop.views.elements.global.creditLauncher.footer-1') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.footer-1-default') ?>" type="text" name="<?= $varName ?>_footer_1_mail" id="<?= $varName ?>_footer_1_mail" class="input" required>
        <label for="<?= $varName ?>_footer_2_mail"><?= LangManager::translate('shop.views.elements.global.creditLauncher.footer-2') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.footer-2-default') . Website::getWebsiteName() ?>" type="text" name="<?= $varName ?>_footer_2_mail" id="<?= $varName ?>_footer_2_mail" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.global.creditLauncher.preview') ?></h5>
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
                        <h2><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_title_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.title-default') ?> XXXXXXX</h2>
                        <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.message-default') ?></p>
                        <p><strong><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_text_mail_value', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.message-value-default') ?> XX.XX€</strong></p>
                        <div class="code">XXXXXXXXX</div><br>
                        <a href="#"><p style="font-size: 0.8rem"><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_1_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.footer-1-default') ?><br>
                            <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_2_mail', $varName) ?? LangManager::translate('shop.views.elements.global.creditLauncher.footer-2-default') . Website::getWebsiteName() ?></p></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="mt-4">
    <h5><?= LangManager::translate('shop.views.elements.global.creditLauncher.appearances') ?></h5>
    <div class="grid-3">
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_body_color', $varName) ?? '#214e7e' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color"><?= LangManager::translate('shop.views.elements.global.creditLauncher.back') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_card_color" name="<?= $varName ?>_card_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_card_color', $varName) ?? '#f8f9fa' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_card_color"><?= LangManager::translate('shop.views.elements.global.creditLauncher.card') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_bg_color" name="<?= $varName ?>_code_bg_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_bg_color', $varName) ?? '#e9ecef' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_bg_color"><?= LangManager::translate('shop.views.elements.global.creditLauncher.code') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_color" name="<?= $varName ?>_code_color" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_code_color', $varName) ?? '#007bff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_color"><?= LangManager::translate('shop.views.elements.global.creditLauncher.code-color') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_p" name="<?= $varName ?>_color_p" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_p', $varName) ?? '#656565' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_p"><?= LangManager::translate('shop.views.elements.global.creditLauncher.text') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_title" name="<?= $varName ?>_color_title" value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_color_title', $varName) ?? '#2f2f2f' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_title"><?= LangManager::translate('shop.views.elements.global.creditLauncher.title-color') ?></label>
        </div>
    </div>
</section>
