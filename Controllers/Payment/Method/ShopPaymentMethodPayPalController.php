<?php

namespace CMW\Controller\Shop\Payment\Method;

use CMW\Controller\Shop\ShopCountryController;
use CMW\Entity\Shop\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\ShopShippingEntity;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;
use JsonException;


/**
 * Class: @ShopPaymentMethodPayPalController
 * @package Shop
 * @author Teyir
 * @version 1.0
 */
class ShopPaymentMethodPayPalController extends AbstractController
{
    private const url = 'https://api-m.paypal.com';
    private const sandBoxUrl = 'https://api-m.sandbox.paypal.com'; //Only for dev.

    /**
     * @param \CMW\Entity\Shop\ShopCartEntity[] $cartItems
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    public function sendPayPalPayment(array $cartItems, ShopShippingEntity $shipping, ShopDeliveryUserAddressEntity $address): void
    {
        if (!$this->isPayPalConfigComplete()) {
            throw new ShopPaymentException(message: "PayPal config is not complete");
        }

        $cancelUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/paypal/cancel';
        $completeUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/paypal/complete';

        $currencyCode = ShopSettingsModel::getInstance()->getSettingValue("currency");
        $totalCartPrice = $cartItems[0]->getTotalCartPriceAfterDiscount(); //TODO Improve that ??

        $postFields = $this->buildCheckoutJsonBody($cartItems, $address, $cancelUrl, $completeUrl, $currencyCode, $totalCartPrice);

        $accessToken = $this->getBearerToken();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::sandBoxUrl . "/v2/checkout/orders", //TODO Don't push sandBoxUrl.
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            throw new ShopPaymentException(message: "Unable to contact PayPal API.");
        }

        try {
            $json = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException(message: "Unable to decode JSON for PayPal Payment action. Err: " . $e->getMessage());
        }

        if (isset($json['error']) && $json['error'] === "invalid_token") {
            throw new ShopPaymentException(message: "Wrong PayPal configuration.");
        }

        if (!isset($json['links'][1]['href'])) {
            throw new ShopPaymentException(message: "Error " . $json['name'] . ". " . $json['message']);
        }

        $checkoutLink = $json['links'][1]['href'];

        header('location: ' . $checkoutLink);

        curl_close($curl);
    }

    /**
     * @return false|string
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    private function getBearerToken(): false|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::sandBoxUrl . '/v1/oauth2/token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . $this->getAuthorizationToken(),
            ],
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            return false;
        }

        try {
            $json = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException(message: "Unable to decode JSON for PayPal oAuth. Err: " . $e->getMessage());
        }

        if (!isset($json->access_token)) {
            throw new ShopPaymentException(message: "Unable to find access_token");
        }

        curl_close($curl);

        return $json->access_token;
    }

    /**
     * @return string
     * @desc We take clientId and client secret for oAuth Authorization.
     * @see https://developer.paypal.com/api/rest/
     */
    private function getAuthorizationToken(): string
    {
        $clientId = ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_id');
        $clientSecret = ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_secret');

        $completeToken = "$clientId:$clientSecret";

        return base64_encode($completeToken);
    }

    /**
     * @return bool
     * @des We are checking if the PayPal config is complete.
     */
    private function isPayPalConfigComplete(): bool
    {
        return !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_id'))
            && !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_secret'));
    }

    /**
     * @param \CMW\Entity\Shop\ShopCartEntity[] $cartItems
     * @param \CMW\Entity\Shop\ShopDeliveryUserAddressEntity $address
     * @param string $cancelUrl
     * @param string $completeUrl
     * @param string $currencyCode
     * @param double $totalCartPrice
     * @return string
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    private function buildCheckoutJsonBody(array  $cartItems, ShopDeliveryUserAddressEntity $address, string $cancelUrl,
                                           string $completeUrl, string $currencyCode, float $totalCartPrice): string
    {
        $data = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'shipping' => [
                        'address' => [
                            'address_line_1' => $address->getLine1(),
                            'address_line_2' => $address->getLine2(),
                            'admin_area_1' => $address->getCity(),
                            'admin_area_2' => $address->getCity(),
                            'postal_code' => $address->getPostalCode(),
                            'country_code' => ShopCountryController::getInstance()->findCountryCodeByName(
                                $address->getCountry(), //TODO DON'T WORK
                            ),
                        ],
                    ],
                    ...$this->buildItems($cartItems, $currencyCode),
                    'amount' => [
                        'currency_code' => $currencyCode,
                        'value' => $totalCartPrice,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $currencyCode,
                                'value' => $totalCartPrice,
                            ],
                        ],
                    ],
                ],
            ],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        'payment_method_preference' => 'IMMEDIATE_PAYMENT_REQUIRED',
                        'brand_name' => Website::getWebsiteName(),
                        'locale' => 'fr-FR', //TODO VAR
                        'landing_page' => 'LOGIN',
                        'shipping_preference' => 'SET_PROVIDED_ADDRESS',
                        'user_action' => 'PAY_NOW',
                        'return_url' => $completeUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ],
            ],
        ];

        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException(message: "Unable to encode JSON for PayPal checkout. Err: " . $e->getMessage());
        }
    }

    /**
     * @param \CMW\Entity\Shop\ShopCartEntity[] $cartItems
     * @param string $currencyCode
     * @return array
     */
    private function buildItems(array $cartItems, string $currencyCode): array
    {
        $data = [];

        foreach ($cartItems as $item) {
            $item = $item->getItem();
            if (!$item) {
                continue;
            }

            $data['items'][] = [
                'name' => $item->getName(),
                'quantity' => $item->getQuantityInCart(),
                'description' => $item->getDescription(),
                'unit_amount' => [
                    'value' => $item->getPrice() * $item->getQuantityInCart(),
                    'currency_code' => $currencyCode,
                ],
            ];
        }

        return $data;
    }

    #[Link("/complete", Link::GET, [], "/shop/command/paypal")]
    private function paypalCommandComplete(): void
    {
        $user = UsersModel::getCurrentUser();

        //TODO LOGS DATA

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Utilisateur introuvable');
            Redirect::redirectToHome();
        }

        Emitter::send(ShopPaymentCompleteEvent::class, []);
    }

    #[NoReturn] #[Link("/cancel", Link::GET, [], "/shop/command/paypal")]
    private function paypalCommandCancel(): void
    {
        Emitter::send(ShopPaymentCancelEvent::class, ['user' => UsersModel::getCurrentUser()]);
    }
}