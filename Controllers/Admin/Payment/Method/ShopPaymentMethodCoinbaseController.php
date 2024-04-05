<?php

namespace CMW\Controller\Shop\Admin\Payment\Method;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\ShopCountryController;
use CMW\Entity\Shop\Deliveries\ShopDeliveryUserAddressEntity;
use CMW\Entity\Shop\Deliveries\ShopShippingEntity;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopShippingModel;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @ShopPaymentMethodCoinbaseController
 * @package Shop
 * @author Zomblard
 * @version 1.0
 */
class ShopPaymentMethodCoinbaseController extends AbstractController
{
    private const COINBASE_COMMERCE_API_URL = 'https://api.commerce.coinbase.com/charges';
    /**
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartItems
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    public function sendCoinbasePayment(array $cartItems, ShopDeliveryUserAddressEntity $address): void
    {
        if (!$this->isCryptoConfigComplete()) {
            throw new ShopPaymentException(message: "Stripe config is not complete");
        }

        $cancelUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/coinbase/cancel';
        $completeUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/coinbase/complete';

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByName("CoinBase");
        $paymentFee = $paymentMethod->fees();

        $currencyCode = ShopSettingsModel::getInstance()->getSettingValue("currency") ?? "EUR";

        $totalAmount = 0;

        foreach ($cartItems as $item) {
            $totalAmount = $item->getTotalPriceComplete();
        }

        if ($paymentFee != 0) {
            $totalAmount += $paymentFee;
        }

        $chargeData = [
            'name' => 'Commande Boutique',
            'description' => 'Achat de produits',
            'pricing_type' => 'fixed_price',
            'local_price' => [
                'amount' => $totalAmount,
                'currency' => $currencyCode
            ],
            'metadata' => [
                'order_id' => 'Votre ID de commande ici',
                // Autres métadonnées utiles
            ],
            'redirect_url' => $completeUrl,
            'cancel_url' => $cancelUrl,
        ];

        $response = $this->createCoinbaseCharge($chargeData);

        // Vérifiez la réponse et redirigez l'utilisateur vers l'URL de paiement Coinbase
        if (isset($response['data']['hosted_url'])) {
            header('Location: ' . $response['data']['hosted_url']);
            exit;
        } else {
            throw new ShopPaymentException("Échec de la création de la charge Coinbase.");
        }
    }

    /**
     * Créez une charge Coinbase Commerce
     *
     * @param array $chargeData Les données de la charge à créer.
     * @return array
     * @throws ShopPaymentException
     */
    private function createCoinbaseCharge(array $chargeData): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::COINBASE_COMMERCE_API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($chargeData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-CC-Api-Key: ' . $this->getCoinbaseApiKey(),
                'X-CC-Version: 2018-03-22',
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        if (!$response) {
            throw new ShopPaymentException("Impossible de contacter l'API Coinbase Commerce.");
        }

        try{
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException("Impossible de décoder la réponse JSON de Coinbase Commerce. Erreur : " . $e->getMessage());
        }
    }

    /**
    Récupérer votre clé secrète Coinbase ici
    @return string
     */
    private function getCoinbaseApiKey(): string
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting('coinbase_api_key');
    }

    /**
     * Check if the Crypto payment configuration is complete
     *
     * @return bool
     * @description We are checking if the Crypto config is complete.
     */
    private function isCryptoConfigComplete(): bool
    {
        return !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('coinbase_api_key'));
    }

    #[Link("/complete", Link::GET, [], "/shop/command/coinbase")]
    private function cryptoCommandComplete(): void
    {
        $user = UsersModel::getCurrentUser();

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Utilisateur introuvable');
            Redirect::redirectToHome();
        }

        Emitter::send(ShopPaymentCompleteEvent::class, []);
    }

    #[NoReturn] #[Link("/cancel", Link::GET, [], "/shop/command/coinbase")]
    private function cryptoCommandCancel(): void
    {
        Emitter::send(ShopPaymentCancelEvent::class, ['user' => UsersModel::getCurrentUser()]);
    }
}