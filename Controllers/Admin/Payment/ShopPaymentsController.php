<?php

namespace CMW\Controller\Shop\Admin\Payment;

use CMW\Controller\Shop\Admin\HistoryOrder\ShopHistoryOrdersController;
use CMW\Controller\Users\UsersController;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Events\Listener;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
use CMW\Model\Shopextendedtoken\ShopExtendedTokenInventoryModels;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopPaymentsController
 * @package Shop
 * @author Teyir
 * @version 1.0
 */
class ShopPaymentsController extends AbstractController
{
    /**
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getPaymentsMethods(): array
    {
        return Loader::loadImplementations(IPaymentMethod::class);
    }

    /**
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getRealActivePaymentsMethods(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethod::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) {
            return $paymentMethod->isVirtualCurrency() === 0 && $paymentMethod->isActive() && $paymentMethod->varName() !== 'free';
        });
    }

    /**
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getFreePayment(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethod::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) {
            return $paymentMethod->varName() === 'free';
        });
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IPaymentMethod|null
     */
    public function getPaymentByVarName(string $varName): ?IPaymentMethod
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
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getVirtualPaymentByVarNameAsArray(string $varName): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethod::class);
        return array_filter($allPaymentMethods, static function ($paymentMethod) use ($varName) {
            return $paymentMethod->isVirtualCurrency() === 1 && $paymentMethod->isActive() && $paymentMethod->varName() === $varName;
        });
    }

    #[Link('/payments', Link::GET, [], '/cmw-admin/shop')]
    private function shopPayments(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payments.settings');
        View::createAdminView('Shop', 'payments')
            ->addVariableList(['methods' => $this->getPaymentsMethods()])
            ->view();
    }

    #[NoReturn]
    #[Link('/payments/enable/:name', Link::GET, [], '/cmw-admin/shop')]
    private function shopEnablePayments(string $name): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payments.settings');
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
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payments.settings');
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
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.payments.settings');

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
        $user = UsersModel::getCurrentUser();

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
