<?php

namespace CMW\Controller\Shop\Admin\Payment\Method;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\UserEntity;
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
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shop\Payment\ShopPaymentOrderModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Utils\Redirect;
use CMW\Utils\Website;
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
     * @throws \Random\RandomException
     */
    public function sendStripePayment(array $cartItems, UserEntity $user): void
    {
        if (!$this->isStripeConfigComplete()) {
            throw new ShopPaymentException(message: 'Stripe config is not complete');
        }

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName('stripe');
        $paymentFee = $paymentMethod?->fees();

        $cancelUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/stripe/cancel?session_id={CHECKOUT_SESSION_ID}';
        $completeUrl = EnvManager::getInstance()->getValue('PATH_URL') . 'shop/command/stripe/complete?session_id={CHECKOUT_SESSION_ID}';

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

        $amount = 0;
        $Items = [];

        if (!is_null($totalCartCode)) {
            $totalCart = 0;

            foreach ($cartItems as $item) {
                $totalCart += $item->getTotalPriceComplete();
            }

            $amount += $totalCart;

            $Items[] = [
                'price_data' => [
                    'currency' => $currencyCode,
                    'product_data' => [
                        'name' => 'Total du panier',
                    ],
                    'unit_amount' => (int) round($totalCart * 100),
                ],
                'quantity' => 1,
            ];

            if ($paymentFee != 0) {
                $amount += $paymentFee;

                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de paiement',
                        ],
                        'unit_amount' => (int) round($paymentFee * 100),
                    ],
                    'quantity' => 1,
                ];
            }

        } else {
            foreach ($cartItems as $item) {
                $linePrice = $item->getItemTotalPriceAfterDiscount();
                $amount += $linePrice;

                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => $item->getQuantity() . ' ' . $item->getItem()?->getName(),
                        ],
                        'unit_amount' => (int) round($linePrice * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            if ($shippingPrice != 0) {
                $amount += $shippingPrice;

                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de livraison',
                        ],
                        'unit_amount' => (int) round($shippingPrice * 100),
                    ],
                    'quantity' => 1,
                ];
            }

            if ($paymentFee != 0) {
                $amount += $paymentFee;

                $Items[] = [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => 'Frais de paiement',
                        ],
                        'unit_amount' => (int) round($paymentFee * 100),
                    ],
                    'quantity' => 1,
                ];
            }
        }

        $paymentOrder = ShopPaymentOrderModel::getInstance()->createPending($user->getId(), $amount * 100, $currencyCode, bin2hex(random_bytes(16)));

        $sessionData = [
            'payment_method_types' => [],  // if null is automatically handled by stripe and stripe payement account settings
            'line_items' => $Items,
            'customer_email' => $user->getMail(),
            'mode' => 'payment',
            'payment_intent_data' => [
                'statement_descriptor' => $_SERVER['HTTP_HOST'],
                'statement_descriptor_suffix' => 'PAIEMENT'
            ],
            'success_url' => $completeUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => $paymentOrder->getId(),
            'metadata' => [
                'order_id'   => $paymentOrder->getId(),
                'user_id'   => $user->getId(),
                'nonce'     => $paymentOrder->getNonce(),
            ],
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

    /**
     * @throws \JsonException
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     */
    private function stripeGet(string $path, array $query = []): array
    {
        $url = self::STRIPE_URL . $path;
        if ($query) {
            $url .= '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->getStripeSecretKey()],
            CURLOPT_HTTPGET => true,
        ]);
        $res = curl_exec($ch);
        if ($res === false) {
            throw new ShopPaymentException('Unable to contact Stripe API.');
        }
        curl_close($ch);

        return json_decode($res, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \ReflectionException
     * @throws \CMW\Exception\Shop\Payment\ShopPaymentException
     * @throws \JsonException
     */
    #[Link('/complete', Link::GET, [], '/shop/command/stripe')]
    private function paypalCommandComplete(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        // TODO LOGS DATA

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Utilisateur introuvable');
            Redirect::redirectToHome();
        }

        $sessionId = $_GET['session_id'] ?? null;
        if (!$sessionId) {
            Flash::send(Alert::ERROR, 'Erreur', 'Session Stripe manquante');
            Redirect::redirectToHome();
        }

        $session = $this->stripeGet("/$sessionId", ['expand' => ['payment_intent','line_items']]);

        // 1) États Stripe
        if (($session['status'] ?? null) !== 'complete') {
            Flash::send(Alert::ERROR, 'Paiement', 'Session non complète');
            Redirect::redirectToHome();
        }
        if (($session['payment_status'] ?? null) !== 'paid') {
            Flash::send(Alert::ERROR, 'Paiement', 'Paiement non réglé');
            Redirect::redirectToHome();
        }

        // 2) Récup ordre local
        $orderId = (int)($session['client_reference_id'] ?? 0);
        $order   = ShopPaymentOrderModel::getInstance()->getPaymentOrderById($orderId);
        if (!$order || $order->getStatus() !== 'PENDING') {
            Flash::send(Alert::ERROR, 'Paiement', 'Ordre introuvable ou déjà traité');
            Redirect::redirectToHome();
        }

        // 3) Anti-substitution: compare metadata et ordre
        $md = $session['metadata'] ?? [];
        $checks = [
            (int)($md['order_id'] ?? 0) === (int)$order->getId(),
            (int)($md['user_id'] ?? 0)  === (int)$user->getId(),
            ($md['nonce'] ?? '')        === $order->getNonce(),
        ];
        if (in_array(false, $checks, true)) {
            Flash::send(Alert::ERROR, 'Paiement', 'Mismatch de métadonnées');
            Redirect::redirectToHome();
        }

        // 4) Vérifie montants + devise
        $expected = $order->getAmount();
        if ((int)$session['amount_total'] !== $expected || strtoupper($session['currency']) !== strtoupper($order->getCurrency())) {
            Flash::send(Alert::ERROR, 'Paiement', 'Montant ou devise incohérents');
            Redirect::redirectToHome();
        }

        // 5) Vérif PaymentIntent (redondance utile)
        $pi = $session['payment_intent'] ?? null;
        if (!$pi || ($pi['status'] ?? null) !== 'succeeded' || (int)($pi['amount_received'] ?? 0) !== $expected) {
            Flash::send(Alert::ERROR, 'Paiement', 'PaymentIntent invalide');
            Redirect::redirectToHome();
        }

        ShopPaymentOrderModel::getInstance()->markPaid($orderId, $sessionId, $pi['id'], date('Y-m-d H:i:s'));

        Emitter::send(ShopPaymentCompleteEvent::class, []);
    }

    #[NoReturn]
    #[Link('/cancel', Link::GET, [], '/shop/command/stripe')]
    private function paypalCommandCancel(): void
    {
        $sessionId = $_GET['session_id'] ?? null;
        if ($sessionId) {
            ShopPaymentOrderModel::getInstance()->markCanceled($sessionId);
        }

        Emitter::send(ShopPaymentCancelEvent::class, null);
    }
}
