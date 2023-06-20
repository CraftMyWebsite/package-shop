<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopSettingsModel;
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
        "AUD" => "Australian Dollar",
        "BRL" => "Brazilian Real" ,
        "CAD" => "Canadian Dollar",
        "CNY" => "Chinese Renmenbi",
        "CZK" => "Czech Koruna",
        "DKK" => "Danish Krone",
        "EUR" => "EURO",
        "HKD" => "Hong Kong Dollar",
        "HUF" => "Hungarian Forint",
        "INR" => "Indian Rupe",
        "ILS" => "Israeli New Shekel",
        "JPY" => "Japanese Yen",
        "MYR" => "Malaysian Ringgit",
        "MXN" => "Mexican Peso",
        "TWD" => "New Taiwan Dollar",
        "NZD" => "New Zealand Dollar",
        "NOK" => "Norwegian Krone",
        "PHP" => "Philippine Peso",
        "PLN" => "Polish złoty",
        "GBP" => "Pound Sterling",
        "RUB" => "Russian Ruble",
        "SGD" => "Singapore Dollar",
        "SEK" => "Swedish Krona",
        "CHF" => "Swiss Franc",
        "THB" => "Thai Baht",
        "USD" => "United States Dollar"];


    /* ///////////////////// CONFIG /////////////////////*/

    #[Link("/settings", Link::GET, [], "/cmw-admin/shop")]
    public function shopSettings(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");

        $currentCurrency = ShopSettingsModel::getInstance()->getSettingValue("currency");

        View::createAdminView('Shop', 'settings')
            ->addVariableList(["currentCurrency" => $currentCurrency])
            ->view();
    }


    #[Link("/settings/apply_currency", Link::POST, [], "/cmw-admin/shop")]
    public function shopApplyCurrencyPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.settings");
        [$code] = Utils::filterInput('code');
        ShopSettingsModel::getInstance()->updateSetting("currency", $code);
        Flash::send(Alert::SUCCESS, "Boutique", "La monnaie utilisé est maintenant ". $code);
        Redirect::redirectPreviousRoute();
    }

}