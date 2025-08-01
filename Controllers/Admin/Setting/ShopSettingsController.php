<?php

namespace CMW\Controller\Shop\Admin\Setting;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopSettingsController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopSettingsController extends AbstractController
{
    // Based on PayPal accepted currencies
    public static array $availableCurrencies = [
        'AUD' => ['name' => 'Australian Dollar', 'symbol' => 'A$'],
        'BRL' => ['name' => 'Brazilian Real', 'symbol' => 'R$'],
        'CAD' => ['name' => 'Canadian Dollar', 'symbol' => 'CA$'],
        'CNY' => ['name' => 'Chinese Renmenbi', 'symbol' => '¥'],
        'CZK' => ['name' => 'Czech Koruna', 'symbol' => 'Kč'],
        'DKK' => ['name' => 'Danish Krone', 'symbol' => 'kr'],
        'EUR' => ['name' => 'Euro', 'symbol' => '€'],
        'HKD' => ['name' => 'Hong Kong Dollar', 'symbol' => 'HK$'],
        'HUF' => ['name' => 'Hungarian Forint', 'symbol' => 'ft'],
        'INR' => ['name' => 'Indian Rupe', 'symbol' => '₹'],
        'ILS' => ['name' => 'Israeli New Shekel', 'symbol' => '₪'],
        'JPY' => ['name' => 'Japanese Yen', 'symbol' => '¥'],
        'MYR' => ['name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
        'MXN' => ['name' => 'Mexican Peso', 'symbol' => '$'],
        'TWD' => ['name' => 'New Taiwan Dollar', 'symbol' => 'NT$'],
        'NZD' => ['name' => 'New Zealand Dollar', 'symbol' => '$'],
        'NOK' => ['name' => 'Norwegian Krone', 'symbol' => 'Kr'],
        'PHP' => ['name' => 'Philippine Peso', 'symbol' => '₱'],
        'PLN' => ['name' => 'Polish złoty', 'symbol' => 'Zł'],
        'GBP' => ['name' => 'Pound Sterling', 'symbol' => '£'],
        'RUB' => ['name' => 'Russian Ruble', 'symbol' => '₽'],
        'SGD' => ['name' => 'Singapore Dollar', 'symbol' => 'S$'],
        'SEK' => ['name' => 'Swedish Krona', 'symbol' => 'kr'],
        'CHF' => ['name' => 'Swiss Franc', 'symbol' => 'CHF'],
        'THB' => ['name' => 'Thai Baht', 'symbol' => '฿'],
        'USD' => ['name' => 'United States Dollar', 'symbol' => '$']
    ];

    /* ///////////////////// CONFIG ///////////////////// */

    #[Link('/settings/global', Link::GET, [], '/cmw-admin/shop')]
    private function shopSettings(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config');

        $currentCurrency = ShopSettingsModel::getInstance()->getSettingValue('currency');
        $currentSymbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $currentAfter = ShopSettingsModel::getInstance()->getSettingValue('after');
        $currentReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        $stockAlert = ShopSettingsModel::getInstance()->getSettingValue('stockAlert');
        $perPage = ShopSettingsModel::getInstance()->getSettingValue('perPage');
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
        $autoValidateVirtual = ShopSettingsModel::getInstance()->getSettingValue('autoValidateVirtual');
        $showPublicStock = ShopSettingsModel::getInstance()->getSettingValue('showPublicStock');
        $shopType = ShopSettingsModel::getInstance()->getSettingValue('shopType');
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $globalConfigMethod = ShopItemsController::getInstance()->getGlobalConfigMethods();

        View::createAdminView('Shop', 'Settings/settings')
            ->addVariableList(['showPublicStock' => $showPublicStock, 'currentCurrency' => $currentCurrency, 'currentAfter' => $currentAfter, 'currentSymbol' => $currentSymbol, 'defaultImage' => $defaultImage, 'globalConfigMethod' => $globalConfigMethod, 'currentReviews' => $currentReviews, 'stockAlert' => $stockAlert, 'perPage' => $perPage, 'shopType' => $shopType, 'maintenance' => $maintenance, 'maintenanceMessage' => $maintenanceMessage, 'autoValidateVirtual' => $autoValidateVirtual])
            ->view();
    }

    #[NoReturn] #[Link('/settings/global', Link::POST, [], '/cmw-admin/shop')]
    private function shopApplyGlobalPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config.edit');

        if (isset($_FILES['defaultPicture'])) {
            if ($_FILES['defaultPicture']['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES['defaultPicture'];
                ShopImagesModel::getInstance()->setDefaultImage($image);
            }
        }

        [$currency, $showAfter, $allowReviews, $stockAlert, $perPage, $shopType, $maintenance, $maintenanceMessage, $autoValidateVirtual, $showPublicStock] = Utils::filterInput('currency', 'showAfter', 'allowReviews', 'stockAlert', 'perPage', 'shopType' ,'maintenance', 'maintenanceMessage', 'autoValidateVirtual', 'showPublicStock');
        $symbol = self::$availableCurrencies[$currency]['symbol'] ?? '€';
        ShopSettingsModel::getInstance()->updateSetting('currency', $currency);
        ShopSettingsModel::getInstance()->updateSetting('symbol', $symbol);
        ShopSettingsModel::getInstance()->updateSetting('after', $showAfter);
        ShopSettingsModel::getInstance()->updateSetting('reviews', $allowReviews ?? 0);
        ShopSettingsModel::getInstance()->updateSetting('stockAlert', $stockAlert);
        ShopSettingsModel::getInstance()->updateSetting('perPage', $perPage);
        ShopSettingsModel::getInstance()->updateSetting('shopType', $shopType);
        ShopSettingsModel::getInstance()->updateSetting('maintenance', $maintenance ?? 0);
        ShopSettingsModel::getInstance()->updateSetting('maintenanceMessage', $maintenanceMessage);
        ShopSettingsModel::getInstance()->updateSetting('autoValidateVirtual', $autoValidateVirtual ?? 0);
        ShopSettingsModel::getInstance()->updateSetting('showPublicStock', $showPublicStock ?? 0);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Configuration appliqué !');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/settings/global/reset_default_image', Link::GET, [], '/cmw-admin/shop')]
    private function shopResetDefaultImagePost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.config.edit');

        ShopImagesModel::getInstance()->resetDefaultImage();

        Flash::send(Alert::SUCCESS, 'Boutique', 'Image par défaut réinitialisé');

        Redirect::redirectPreviousRoute();
    }
}
