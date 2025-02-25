<?php
/* @var string $varName */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Shipping\ShopShippingRequirementModel;

$requirement = ShopShippingRequirementModel::getInstance();

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
    <label for="<?= $varName ?>_global"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.object') ?></label>
    <input value="<?= $requirement->getSetting($varName . '_global') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting_title') ?>" type="text" name="<?= $varName ?>_global" id="<?= $varName ?>_global" class="input" required>
</section>
<section class="grid-3">
    <div>
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.mail_setting') ?></h5>
        <label for="<?= $varName ?>_title_mail"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.title') ?></label>
        <input value="<?= $requirement->getSetting($varName . '_title_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting') ?>" type="text" name="<?= $varName ?>_title_mail" id="<?= $varName ?>_title_mail" class="input" required>
        <label for="<?= $varName ?>_text_mail"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.message') ?></label>
        <input value="<?= $requirement->getSetting($varName . '_text_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.ready_to_withdraw') ?>" type="text" name="<?= $varName ?>_text_mail" id="<?= $varName ?>_text_mail" class="input" required>
        <label for="<?= $varName ?>_use_mail"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.footer_message') ?></label>
        <input value="<?= $requirement->getSetting($varName . '_use_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.show_this') ?>" type="text" name="<?= $varName ?>_use_mail" id="<?= $varName ?>_use_mail" class="input" required>
        <label for="<?= $varName ?>_address"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.address') ?></label>
        <input value="<?= $requirement->getSetting($varName . '_address') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.center') ?>" type="text" name="<?= $varName ?>_address" id="<?= $varName ?>_address" class="input" required>
    </div>
    <div class="col-span-2">
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.preview') ?></h5>
        <div class="border dark:border-gray-700">
            <style>
                .my-gift-card-scope .gift-card {
                    font-family: Arial, sans-serif;
                    max-width: 600px;
                    margin: 20px auto;
                    padding: 20px;
                    background-color: <?= $requirement->getSetting($varName . '_card_color') ?? '#f8f9fa' ?>;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    text-align: center;
                }

                .my-gift-card-scope .gift-card h2 {
                    color: <?= $requirement->getSetting($varName . '_color_title') ?? '#2f2f2f' ?>;
                }

                .my-gift-card-scope .gift-card p {
                    color: <?= $requirement->getSetting($varName . '_color_p') ?? '#656565' ?>;
                }

                .my-gift-card-scope .code {
                    font-size: 18px;
                    color: <?= $requirement->getSetting($varName . '_code_color') ?? '#007bff' ?>;
                    margin: 20px 0;
                    padding: 10px;
                    background-color: <?= $requirement->getSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>;
                    border-radius: 5px;
                    display: inline-block;
                }
            </style>

            <div style="background-color: <?= $requirement->getSetting($varName . '_body_color') ?? '#214e7e' ?>">
                <div class="my-gift-card-scope p-4">
                    <div class="gift-card">
                        <h2><?= $requirement->getSetting($varName . '_title_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.waiting') ?></h2>
                        <p><?= $requirement->getSetting($varName . '_text_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.ready_to_withdraw') ?></p>
                        <div class="code"><?= $requirement->getSetting($varName . '_prefix') ?? '#' ?>XXXXXXXX</div><br>
                        <div style="text-align: left; margin-bottom: 15px">
                            <p><?= $requirement->getSetting($varName . '_address') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.center') ?></p>
                            <p>XXXXXXXXXXX</p>
                            <p>XXXXXXXXXXX</p>
                            <p>XXXXXXXXXXX</p>
                        </div>
                        <p style="font-size: 0.8rem"><?= $requirement->getSetting($varName . '_use_mail') ?? LangManager::translate('shop.views.elements.shipping.global.withdraw.placeholder.show_this') ?></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
<section class="mt-4">
    <h5><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.style') ?></h5>
    <div class="grid-3">
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_body_color" name="<?= $varName ?>_body_color" value="<?= $requirement->getSetting($varName . '_body_color') ?? '#214e7e' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_body_color"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.background') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_card_color" name="<?= $varName ?>_card_color" value="<?= $requirement->getSetting($varName . '_card_color') ?? '#f8f9fa' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_card_color"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.card') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_bg_color" name="<?= $varName ?>_code_bg_color" value="<?= $requirement->getSetting($varName . '_code_bg_color') ?? '#e9ecef' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_bg_color"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.background_code') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_code_color" name="<?= $varName ?>_code_color" value="<?= $requirement->getSetting($varName . '_code_color') ?? '#007bff' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_code_color"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.code') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_p" name="<?= $varName ?>_color_p" value="<?= $requirement->getSetting($varName . '_color_p') ?? '#656565' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_p"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.text') ?></label>
        </div>
        <div class="flex items-center">
            <input type="color" id="<?= $varName ?>_color_title" name="<?= $varName ?>_color_title" value="<?= $requirement->getSetting($varName . '_color_title') ?? '#2f2f2f' ?>">
            <label style="margin-left: 0.5rem" for="<?= $varName ?>_color_title"><?= LangManager::translate('shop.views.elements.shipping.global.withdraw.title_color') ?></label>
        </div>
    </div>
</section>
