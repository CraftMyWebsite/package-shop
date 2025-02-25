<?php

namespace CMW\Controller\Shop\Admin\Payment\Method;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @ShopPaymentMethodPayPalController
 * @package Shop
 * @author Zomblard & Teyir
 * @version 0.0.1
 */
class ShopPaymentMethodPayPalController extends AbstractController
{
    private const string  PAYPAL_API_URL = 'https://api.paypal.com';
    private const string  PAYPAL_SANDBOX_API_URL = 'https://api.sandbox.paypal.com';  // Only for dev.

    /**
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartItems
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    public function sendPayPalPayment(array $cartItems, ShopDeliveryUserAddressEntity $address): void
    {
        if (!$this->isPayPalConfigComplete()) {
            throw new ShopPaymentException(message: 'PayPal config is not complete');
        }

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName('paypal');
        $paymentFee = $paymentMethod?->fees();

        $cancelUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/paypal/cancel';
        $completeUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/paypal/complete';

        $currencyCode = ShopSettingsModel::getInstance()->getSettingValue('currency') ?? 'EUR';

        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $totalAmount = $item->getTotalPriceComplete();
        }

        if ($paymentFee !== 0) {
            $totalAmount += $paymentFee;
        }

        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currencyCode,
                        'value' => number_format($totalAmount, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $currencyCode,
                                'value' => number_format(0.0, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => [],
                ],
            ],
            'application_context' => [
                'cancel_url' => $cancelUrl,
                'return_url' => $completeUrl,
            ],
        ];

        $response = $this->createPayPalOrder($orderData);

        if (!isset($response['id'])) {
            throw new ShopPaymentException('Failed to create PayPal payment session.');
        }

        header('Location: ' . $response['links'][1]['href']);
    }

    /**
     * Crée une commande PayPal en utilisant cURL.
     *
     * @param array $orderData Les données de la commande à créer.
     * @return array La réponse de l'API PayPal.
     * @throws ShopPaymentException Si la création de la commande échoue.
     */
    private function createPayPalOrder(array $orderData): array
    {
        $accessToken = $this->getPayPalAccessToken();

        $curl = curl_init(self::PAYPAL_API_URL . '/v2/checkout/orders');
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $accessToken",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($orderData),
        ]);

        $response = curl_exec($curl);
        if (!$response) {
            curl_close($curl);
            throw new ShopPaymentException('Unable to contact PayPal API.');
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode < 200 || $statusCode >= 300) {
            curl_close($curl);
            throw new ShopPaymentException("Received HTTP status code $statusCode from PayPal API.");
        }

        curl_close($curl);

        try {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException('Unable to decode JSON response from PayPal. Error: ' . $e->getMessage());
        }
    }

    /**
     * Récupère un token d'accès OAuth pour l'API PayPal.
     *
     * @return string Le token d'accès.
     * @throws ShopPaymentException Si la récupération du token échoue.
     */
    private function getPayPalAccessToken(): string
    {
        $clientId = ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_id');
        $clientSecret = ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_secret');

        $curl = curl_init(self::PAYPAL_API_URL . '/v1/oauth2/token');
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Accept-Language: en_US',
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_USERPWD => $clientId . ':' . $clientSecret,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        ]);

        $response = curl_exec($curl);
        if (!$response) {
            curl_close($curl);
            throw new ShopPaymentException('Unable to contact PayPal API for access token.');
        }

        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($statusCode != 200) {
            curl_close($curl);
            throw new ShopPaymentException("Received HTTP status code $statusCode from PayPal API while requesting access token.");
        }

        curl_close($curl);
        $responseDecoded = json_decode($response, true);
        return $responseDecoded['access_token'];
    }

    /**
     * Capture le paiement PayPal après l'approbation de l'utilisateur.
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    public function capturePayPalPayment($orderId): array
    {
        $accessToken = $this->getPayPalAccessToken();

        $curl = curl_init(self::PAYPAL_API_URL . "/v2/checkout/orders/$orderId/capture");
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer $accessToken",
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response) {
            throw new ShopPaymentException('Unable to capture PayPal payment.');
        }

        try {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException('Error decoding PayPal capture response: ' . $e->getMessage());
        }
    }

    /**
     * @return bool
     * @des We are checking if the PayPal config is complete.
     */
    private function isPayPalConfigComplete(): bool
    {
        return !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_id')) &&
            !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('paypal_client_secret'));
    }

    #[Link('/complete', Link::GET, [], '/shop/command/paypal')]
    private function paypalCommandComplete(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Utilisateur introuvable');
            Redirect::redirectToHome();
        }

        // Auto validation côté Paypal pour autoriser le prélèvement
        $orderId = $_GET['token'];
        try {
            $captureResponse = $this->capturePayPalPayment($orderId);
            if ($captureResponse['status'] == 'COMPLETED') {
                Emitter::send(ShopPaymentCompleteEvent::class, []);
            } else {
                Emitter::send(ShopPaymentCancelEvent::class, ['user' => $user]);
            }
        } catch (ShopPaymentException $e) {
            Flash::send(Alert::ERROR, 'Erreur', 'Échec de la capture du paiement: ' . $e->getMessage());
        }
    }

    #[NoReturn]
    #[Link('/cancel', Link::GET, [], '/shop/command/paypal')]
    private function paypalCommandCancel(): void
    {
        Emitter::send(ShopPaymentCancelEvent::class, null);
    }
}
