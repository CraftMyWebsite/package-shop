<?php

namespace CMW\Controller\Shop\Admin\Payment;

use CMW\Controller\Shop\Admin\HistoryOrder\ShopHistoryOrdersController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Shop\Const\Payment\PaymentMethodConst;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Interface\Shop\IPaymentMethodV2;
use CMW\Manager\Events\Listener;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopPaymentsController
 * @package Shop
 * @author Zomblard & Teyir
 * @version 0.0.1
 */
class ShopPaymentsController extends AbstractController
{
    /**
     * @return \CMW\Interface\Shop\IPaymentMethodV2[]
     */
    public function getPaymentsMethods(): array
    {
        return Loader::loadImplementations(IPaymentMethodV2::class);
    }

    /**
     * @return \CMW\Interface\Shop\IPaymentMethodV2[]
     */
    public function getRealActivePaymentsMethods(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethodV2::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) {
            return $paymentMethod->isVirtualCurrency() === false && $paymentMethod->isActive() && $paymentMethod->varName() !== PaymentMethodConst::FREE;
        });
    }

    /**
     * @return \CMW\Interface\Shop\IPaymentMethodV2[]
     */
    public function getFreePayment(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethodV2::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) {
            return $paymentMethod->varName() === PaymentMethodConst::FREE;
        });
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IPaymentMethodV2|null
     */
    public function getPaymentByVarName(string $varName): ?IPaymentMethodV2
    {
        foreach ($this->getPaymentsMethods() as $paymentsMethod) {
            if ($paymentsMethod->varName() === $varName) {
                return $paymentsMethod;
            }
        }
        return null;
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IPaymentMethodV2[]
     */
    public function getVirtualPaymentByVarNameAsArray(string $varName): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethodV2::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) use ($varName) {
            return $paymentMethod->isVirtualCurrency() && $paymentMethod->isActive() && $paymentMethod->varName() === $varName;
        });
    }

    #[Link('/payments', Link::GET, [], '/cmw-admin/shop')]
    private function shopPayments(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payment');
        View::createAdminView('Shop', 'payments')
            ->addVariableList(['methods' => $this->getPaymentsMethods()])
            ->view();
    }

    #[NoReturn]
    #[Link('/payments/enable/:name', Link::GET, [], '/cmw-admin/shop')]
    private function shopEnablePayments(string $name): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payment.edit');
        $nameWithStatus = $name . '_is_active';
        if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($nameWithStatus, 1)) {
            Flash::send(Alert::ERROR, 'Boutique', "Impossible de d'activer la méthode de paiement'");
        }
        Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de paiement activé !');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/payments/disable/:name', Link::GET, [], '/cmw-admin/shop')]
    private function shopDisablePayments(string $name): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payment.edit');
        $nameWithStatus = $name . '_is_active';
        if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($nameWithStatus, 0)) {
            Flash::send(Alert::ERROR, 'Boutique', "Impossible de désactiver la méthode de paiement'");
        }
        Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de paiement désactivé !');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/payments/settings', Link::POST, [], '/cmw-admin/shop')]
    private function shopPaymentsSettingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payment.edit');

        $settings = $_POST;

        foreach ($settings as $key => $value) {
            if ($key === 'security-csrf-token' || $key === 'honeyInput') {
                continue;
            }
            $key = FilterManager::filterData($key, 50);
            $value = FilterManager::filterData($value, 255);

            if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($key, $value)) {
                Flash::send(Alert::ERROR, 'Erreur',
                    "Impossible de mettre à jour le paramètre $key");
            }
        }

        Flash::send(Alert::SUCCESS, 'Succès', 'Les paramètres ont été mis à jour');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Listener(eventName: ShopPaymentCompleteEvent::class, times: 0, weight: 1)]
    private function onPaymentComplete(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (!$user) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de trouver l\'utilisateur');
            Redirect::redirectPreviousRoute();
        }

        ShopHistoryOrdersController::getInstance()->handleCreateOrder($user);

        Flash::send(Alert::SUCCESS, 'Achat effectué', 'Merci pour votre achat ' . $user->getPseudo());

        Redirect::redirect('shop/history');
    }

    #[NoReturn]
    #[Listener(eventName: ShopPaymentCancelEvent::class, times: 0, weight: 1)]
    private function onPaymentCancel(mixed $message): void
    {
        Flash::send(Alert::WARNING, 'Paiement annulé', $message ?? '');
        Redirect::redirect('shop/command');
    }
}
