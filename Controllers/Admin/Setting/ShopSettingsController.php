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


/**
 * Class: @ShopSettingsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopSettingsController extends AbstractController
{
    // Based on PayPal accepted currencies
    public static array $availableCurrencies = [
        "AUD" => ["name" => "Australian Dollar", "symbol" => "A$"],
        "BRL" => ["name" => "Brazilian Real", "symbol" => "R$"],
        "CAD" => ["name" => "Canadian Dollar", "symbol" => "CA$"],
        "CNY" => ["name" => "Chinese Renmenbi", "symbol" => "¥"],
        "CZK" => ["name" => "Czech Koruna", "symbol" => "Kč"],
        "DKK" => ["name" => "Danish Krone", "symbol" => "kr"],
        "EUR" => ["name" => "Euro", "symbol" => "€"],
        "HKD" => ["name" => "Hong Kong Dollar", "symbol" => "HK$"],
        "HUF" => ["name" => "Hungarian Forint", "symbol" => "ft"],
        "INR" => ["name" => "Indian Rupe", "symbol" => "₹"],
        "ILS" => ["name" => "Israeli New Shekel", "symbol" => "₪"],
        "JPY" => ["name" => "Japanese Yen", "symbol" => "¥"],
        "MYR" => ["name" => "Malaysian Ringgit", "symbol" => "RM"],
        "MXN" => ["name" => "Mexican Peso", "symbol" => "$"],
        "TWD" => ["name" => "New Taiwan Dollar", "symbol" => "NT$"],
        "NZD" => ["name" => "New Zealand Dollar", "symbol" => "$"],
        "NOK" => ["name" => "Norwegian Krone", "symbol" => "Kr"],
        "PHP" => ["name" => "Philippine Peso", "symbol" => "₱"],
        "PLN" => ["name" => "Polish złoty", "symbol" => "Zł"],
        "GBP" => ["name" => "Pound Sterling", "symbol" => "£"],
        "RUB" => ["name" => "Russian Ruble", "symbol" => "₽"],
        "SGD" => ["name" => "Singapore Dollar", "symbol" => "S$"],
        "SEK" => ["name" => "Swedish Krona", "symbol" => "kr"],
        "CHF" => ["name" => "Swiss Franc", "symbol" => "CHF"],
        "THB" => ["name" => "Thai Baht", "symbol" => "฿"],
        "USD" => ["name" => "United States Dollar", "symbol" => "$"]];


    /* ///////////////////// CONFIG /////////////////////*/

    #[Link("/settings", Link::GET, [], "/cmw-admin/shop")]
    public function shopSettings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");

        $currentCurrency = ShopSettingsModel::getInstance()->getSettingValue("currency");
        $currentSymbol = ShopSettingsModel::getInstance()->getSettingValue("symbol");
        $currentAfter = ShopSettingsModel::getInstance()->getSettingValue("after");
        $currentReviews = ShopSettingsModel::getInstance()->getSettingValue("reviews");
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $virtualMethods = ShopItemsController::getInstance()->getVirtualItemsMethods();

        View::createAdminView('Shop', 'Settings/settings')
            ->addVariableList(["currentCurrency" => $currentCurrency,"currentAfter" => $currentAfter,"currentSymbol" => $currentSymbol,"defaultImage" => $defaultImage, "virtualMethods" => $virtualMethods, "currentReviews" => $currentReviews])
            ->view();
    }

    #[Link("/settings/apply_default_image", Link::POST, [], "/cmw-admin/shop")]
    public function shopApplyDefaultImagePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");

        if (isset($_FILES['defaultPicture']) && $_FILES['defaultPicture']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['defaultPicture'];
            ShopImagesModel::getInstance()->setDefaultImage($image);
            Flash::send(Alert::SUCCESS, "Boutique", "Nouvelle image ajouté");
        } else {
            Flash::send(Alert::ERROR, "Boutique", "Une erreur est survenue lors de l'ajout de l'image");
        }
        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/reset_default_image", Link::POST, [], "/cmw-admin/shop")]
    public function shopResetDefaultImagePost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");

        ShopImagesModel::getInstance()->resetDefaultImage();

        Flash::send(Alert::SUCCESS, "Boutique", "Image par défaut réinitialisé");

        Redirect::redirectPreviousRoute();
    }

    #[Link("/settings/apply_global", Link::POST, [], "/cmw-admin/shop")]
    public function shopApplyCurrencyPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");
        [$currency, $showAfter, $allowReviews] = Utils::filterInput('currency', 'showAfter', 'allowReviews');
        $symbol = ShopSettingsController::$availableCurrencies[$currency]['symbol'] ?? '€';
        ShopSettingsModel::getInstance()->updateSetting("currency", $currency);
        ShopSettingsModel::getInstance()->updateSetting("symbol", $symbol);
        ShopSettingsModel::getInstance()->updateSetting("after", $showAfter);
        ShopSettingsModel::getInstance()->updateSetting("reviews", $allowReviews ?? 0);
        Flash::send(Alert::SUCCESS, "Boutique", "La monnaie utilisée est maintenant " . $currency . " (" . $symbol . ")");
        Redirect::redirectPreviousRoute();
    }

}