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
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @ShopPaymentMethodStripeController
 * @package Shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopPaymentMethodStripeController extends AbstractController
{
    private const string  STRIPE_URL = 'https://api.stripe.com/v1/checkout/sessions';

    /**
     * @param \CMW\Entity\Shop\Carts\ShopCartItemEntity[] $cartItems
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    public function sendStripePayment(array $cartItems, ShopDeliveryUserAddressEntity $address): void
    {
        if (!$this->isStripeConfigComplete()) {
            throw new ShopPaymentException(message: 'Stripe config is not complete');
        }

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName('stripe');
        $paymentFee = $paymentMethod?->fees();

        $cancelUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/stripe/cancel';
        $completeUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/stripe/complete';

        $currencyCode = ShopSettingsModel::getInstance()->getSettingValue('currency') ?? 'EUR';

        $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId(UsersSessionsController::getInstance()->getCurrentUser()?->getId());
        $commandTunnelShippingId = $commandTunnelModel->getShipping()?->getId();
        $shippingMethod = null;

        if (is_int($commandTunnelShippingId)) {
            $shippingMethod = ShopShippingModel::getInstance()->getShopShippingById($commandTunnelShippingId);
        }
        $shippingPrice = $shippingMethod?->getPrice() ?? 0;

        $totalCartCode = null;
        $cartDiscountModel = ShopCartDiscountModel::getInstance();
        $cartDiscounts = $cartDiscountModel->getCartDiscountByUserId(UsersSessionsController::getInstance()->getCurrentUser()?->getId(), session_id());
        foreach ($cartDiscounts as $cartDiscount) {
            $discountGiftCode = $cartDiscountModel->getCartDiscountById($cartDiscount->getId());
            if ($discountGiftCode?->getDiscount()->getLinked() === 3 || $discountGiftCode?->getDiscount()->getLinked() === 4) {
                $totalCartCode = $discountGiftCode->getDiscount();
                break;
            }
        }

        $Items = [];
        if (!is_null($totalCartCode)) {
            $totalCart = 0;
            foreach ($cartItems as $item) {
                $totalCart = $item->getTotalPriceComplete();
            }
            $Items[] = [
                'price_data' => [
                    'currency' => $currencyCode,
                    'product_data' => [
                        'name' => 'Total du panier',
                    ],
                    'unit_amount' => $totalCart * 100,
                ],
                'quantity' => 1,
            ];
            if ($paymentFee != 0) {
                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de paiement',
                        ],
                        'unit_amount' => $paymentFee * 100,
                    ],
                    'quantity' => 1,
                ];
            }
        } else {
            foreach ($cartItems as $item) {
                $lineItem = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => $item->getQuantity() . ' ' . $item->getItem()?->getName(),
                        ],
                        'unit_amount' => $item->getItemTotalPriceAfterDiscount() * 100,
                    ],
                    'quantity' => 1,
                ];
                $Items[] = $lineItem;
            }

            if ($shippingPrice != 0) {
                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de livraison',
                        ],
                        'unit_amount' => $shippingPrice * 100,
                    ],
                    'quantity' => 1,
                ];
            }

            if ($paymentFee != 0) {
                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de paiement',
                        ],
                        'unit_amount' => $paymentFee * 100,
                    ],
                    'quantity' => 1,
                ];
            }
        }

        $sessionData = [
            'payment_method_types' => [],  // if null is automatically handled by stripe and stripe payement account settings
            'line_items' => $Items,
            'mode' => 'payment',
            'success_url' => $completeUrl,
            'cancel_url' => $cancelUrl,
        ];

        $response = $this->createStripeSession($sessionData);

        if (!isset($response['id'])) {
            throw new ShopPaymentException('Failed to create Stripe payment session.');
        }

        $checkoutSessionId = $response['id'];

        header('Location: ' . $response['url']);
    }

    /**
     * Create a Stripe Checkout session
     *
     * @param array $sessionData
     * @return array
     * @throws ShopPaymentException
     */
    private function createStripeSession(array $sessionData): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => self::STRIPE_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($sessionData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer ' . $this->getStripeSecretKey(),
            ],
        ]);

        $response = curl_exec($curl);

        if (!$response) {
            throw new ShopPaymentException('Unable to contact Stripe API.');
        }

        curl_close($curl);

        try {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ShopPaymentException('Unable to decode JSON response from Stripe. Error: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve your Stripe secret key here
     *
     * @return string
     */
    private function getStripeSecretKey(): string
    {
        return ShopPaymentMethodSettingsModel::getInstance()->getSetting('stripe_secret_key');
    }

    /**
     * @return bool
     * @des We are checking if the Stripe config is complete.
     */
    private function isStripeConfigComplete(): bool
    {
        return !is_null(ShopPaymentMethodSettingsModel::getInstance()->getSetting('stripe_secret_key'));
    }

    #[Link('/complete', Link::GET, [], '/shop/command/stripe')]
    private function paypalCommandComplete(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        // TODO LOGS DATA

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Utilisateur introuvable');
            Redirect::redirectToHome();
        }

        Emitter::send(ShopPaymentCompleteEvent::class, []);
    }

    #[NoReturn]
    #[Link('/cancel', Link::GET, [], '/shop/command/stripe')]
    private function paypalCommandCancel(): void
    {
        Emitter::send(ShopPaymentCancelEvent::class, null);
    }
}
