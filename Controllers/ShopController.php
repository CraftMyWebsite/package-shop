<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\ShopConfigModel;
use CMW\Manager\Views\View;


/**
 * Class: @ShopController
 * @package shop
 * @author Teyir
 * @version 1.0
 */
class ShopController extends AbstractController
{


    // Based on PayPal accepted currencies
    public static array $availableCurrencies = ["AUD" => "Australian Dollar", "BRL" => "Brazilian Real" ,
        "CAD" => "Canadian Dollar", "CNY" => "Chinese Renmenbi", "CZK" => "Czech Koruna", "DKK" => "Danish Krone",
        "EUR" => "EURO", "HKD" => "Hong Kong Dollar", "HUF" => "Hungarian Forint", "INR" => "Indian Rupe",
        "ILS" => "Israeli New Shekel", "JPY" => "Japanese Yen", "MYR" => "Malaysian Ringgit", "MXN" => "Mexican Peso",
        "TWD" => "New Taiwan Dollar", "NZD" => "New Zealand Dollar", "NOK" => "Norwegian Krone", "PHP" => "Philippine Peso",
        "PLN" => "Polish złoty", "GBP" => "Pound Sterling", "RUB" => "Russian Ruble", "SGD" => "Singapore Dollar",
        "SEK" => "Swedish Krona", "CHF" => "Swiss Franc", "THB" => "Thai Baht", "USD" => "United States Dollar"];


    /* ///////////////////// CONFIG /////////////////////*/

    #[Link("/config", Link::GET, [], "/cmw-admin/shop")]
    public function shopConfig(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.configuration");

        $config = ShopConfigModel::getInstance()->getConfigs();

        View::createAdminView('Shop', 'config')
            ->addVariableList(["config" => $config])
            ->view();
    }






    /* ///////////////////// UTILS FUNCTIONS /////////////////////*/

    /**
     * @return array
     * @desc Return an array with all the currencies code config
     */
    public static function getLocalCurrenciesCode(): array
    {
        $toReturn = [];
        $config = (new ShopConfigModel())->getConfigCurrencies();

        foreach ($config as $currency){
            $toReturn[$currency->getCode()] = true;
        }

        return $toReturn;
    }

}