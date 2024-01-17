<?php

namespace CMW\Interface\Shop;


use CMW\Manager\Env\EnvManager;

interface IPaymentMethod {
    /**
     * @return string
     * @desc The name of the payment method
     * @example "PayPal"
     */
    public function name(): string;

    /**
     * @return string
     * @desc Small description of the payment method
     */
    public function description(): string;

    /**
     * @return int|null
     * @desc Fees are optional
     */
    public function fees(): ?int;

    /**
     * @return void
     * @desc Include the config widgets of the payment method, like ClientId, ClientSecret, etc...
     * @example require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/paypal.config.inc.view.php";
     */
    public function includeConfigWidgets(): void;

    /**
     * @param int $amount
     * @return bool
     * @desc Je sais pas encore ce que ça va faire, mais oklm
     */
    public function action(int $amount): bool;
}
