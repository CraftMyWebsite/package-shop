<?php
/* @var string $varName */

use CMW\Manager\Lang\LangManager;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Website;

?>

<section class="grid-3">
    <div>
        <label for="<?= $varName ?>_use"><?= LangManager::translate('shop.views.elements.global.invoice.create') ?></label>
        <select name="<?= $varName ?>_use" id="<?= $varName ?>_use">
            <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) === "1" ? 'selected' : '' ?> value="1"><?= LangManager::translate('shop.views.elements.global.invoice.yes') ?></option>
            <option <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) === "0" ? 'selected' : ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) ?? 'selected' ?> value="0"><?= LangManager::translate('shop.views.elements.global.invoice.no') ?></option>
        </select>
        <label for="<?= $varName ?>_logo"><?= LangManager::translate('shop.views.elements.global.invoice.logo') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl()."App/Package/Shop/Views/Settings/Images/default.png" ?>" type="text" name="<?= $varName ?>_logo" id="<?= $varName ?>_logo" class="input" required>
        <label for="<?= $varName ?>_footer_text"><?= LangManager::translate('shop.views.elements.global.invoice.footer') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_text', $varName) ?? LangManager::translate('shop.views.elements.global.invoice.footer-default') ?>" type="text" name="<?= $varName ?>_footer_text" id="<?= $varName ?>_footer_text" class="input" required>

        <h5><?= LangManager::translate('shop.views.elements.global.invoice.building-address') ?></h5>
        <label for="<?= $varName ?>_address"><?= LangManager::translate('shop.views.elements.global.invoice.address') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address', $varName) ?? "N/A" ?>" type="text" name="<?= $varName ?>_address" id="<?= $varName ?>_address" class="input" required>
        <label for="<?= $varName ?>_address_pc"><?= LangManager::translate('shop.views.elements.global.invoice.cp') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_pc', $varName) ?? "N/A" ?>" type="text" name="<?= $varName ?>_address_pc" id="<?= $varName ?>_address_pc" class="input" required>
        <label for="<?= $varName ?>_address_city"><?= LangManager::translate('shop.views.elements.global.invoice.city') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_city', $varName) ?? "N/A" ?>" type="text" name="<?= $varName ?>_address_city" id="<?= $varName ?>_address_city" class="input" required>
        <label for="<?= $varName ?>_address_country"><?= LangManager::translate('shop.views.elements.global.invoice.country') ?></label>
        <input value="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_country', $varName) ?? "N/A" ?>" type="text" name="<?= $varName ?>_address_country" id="<?= $varName ?>_address_country" class="input" required>
        </div>
    <div class="col-span-2 h-fit">
        <h5 class="text-center"><?= LangManager::translate('shop.views.elements.global.invoice.preview') ?></h5>
        <div class="border dark:border-gray-700">


            <style>
                .facture {
                    font-family: Helvetica, sans-serif;
                }
                .section {
                    margin-top: 20px;
                }

                .section h3 {
                    margin: 0 0 10px;
                    font-size: 1.1em;
                }

                .info-table,
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                }

                .info-table td {
                    padding: 5px;
                }

                .info-table td:nth-child(2) {
                    text-align: right;
                }

                .items-table th,
                .items-table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }

                .items-table th {
                    background-color: #f9f9f9;
                }

                .totals {
                    margin-top: 20px;
                    float: right;
                    text-align: right;
                }

                .totals td {
                    padding: 5px 0;
                }

                .footer {
                    margin-top: 200px;
                    font-size: 0.9em;
                    color: #666;
                }
            </style>
            <div class="facture">
                <table class="info-table">
                    <tr>
                        <td>
                            <img class="logo" src="<?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_logo', $varName) ?? Website::getUrl()."App/Package/Shop/Views/Settings/Images/default.png" ?>" width="120px" alt="Logo">
                            <br>
                            <span><b><?= Website::getWebsiteName() ?></b><br>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address', $varName) ?? "N/A" ?><br>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_pc', $varName) ?? "N/A" ?> <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_city', $varName) ?? "N/A" ?><br>
                    <?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_address_country', $varName) ?? "N/A" ?><br>
                    </span>
                        </td>
                        <td align="right">
                            <h5 style="color: #c16374"><?= LangManager::translate('shop.views.elements.global.invoice.title') ?> N° XXXXXXXXX</h5>
                            <div class="section">
                                <b><?= LangManager::translate('shop.views.elements.global.invoice.address-invoice') ?></b>
                                <p>
                                    XXXXXXXXX<br>
                                    XXXXXXXXX<br>
                                    XXXXXXXXX<br>
                                    XXXXXXXXX
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>


                <div class="section">
                    <table class="info-table">
                        <thead>
                        <tr>
                            <th><?= LangManager::translate('shop.views.elements.global.invoice.shipping-invoice') ?></th>
                            <th align="center"><?= LangManager::translate('shop.views.elements.global.invoice.date-invoice') ?></th>
                            <th align="right"><?= LangManager::translate('shop.views.elements.global.invoice.payment-invoice') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>XXXXXXXXX</td>
                            <td align="center">XXXXXXXXX</td>
                            <td align="right">XXXXXXXXX</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="section">
                    <table class="items-table">
                        <thead>
                        <tr>
                            <th><strong><?= LangManager::translate('shop.views.elements.global.invoice.item-invoice') ?></strong></th>
                            <th><strong><?= LangManager::translate('shop.views.elements.global.invoice.quantity-invoice') ?></strong></th>
                            <th><strong><?= LangManager::translate('shop.views.elements.global.invoice.unit-price-invoice') ?></strong></th>
                            <th><strong><?= LangManager::translate('shop.views.elements.global.invoice.item-discount-invoice') ?></strong></th>
                            <th><strong><?= LangManager::translate('shop.views.elements.global.invoice.st-invoice') ?></strong></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>XXXXXXXXX</td>
                            <td>XXXXXXXXX</td>
                            <td>XXXXXXXXX</td>
                            <td>XXXXXXXXX</td>
                            <td>XXXXXXXXX</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="totals">
                    <table>
                        <thead>
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?= LangManager::translate('shop.views.elements.global.invoice.shipping-cost-invoice') ?></td>
                            <td>XXXXXXXXX</td>
                        </tr>
                        <tr>
                            <td><?= LangManager::translate('shop.views.elements.global.invoice.payment-fee-invoice') ?></td>
                            <td>XXXXXXXXX</td>
                        </tr>
                        <tr>
                            <td><?= LangManager::translate('shop.views.elements.global.invoice.total-discount-invoice') ?></td>
                            <td>XXXXXXXXX</td>
                        </tr>
                        <tr>
                            <td><strong><?= LangManager::translate('shop.views.elements.global.invoice.total-invoice') ?></strong></td>
                            <td><strong>XXXXXXXXX</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div class="footer">
                    <p><?= ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_footer_text', $varName) ?? LangManager::translate('shop.views.elements.global.invoice.footer-default') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>