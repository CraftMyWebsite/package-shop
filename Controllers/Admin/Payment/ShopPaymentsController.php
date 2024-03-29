<?php

namespace CMW\Controller\Shop\Admin\Payment;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Users\UserEntity;
use CMW\Event\Shop\ShopPaymentCancelEvent;
use CMW\Event\Shop\ShopPaymentCompleteEvent;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Events\Listener;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Order\ShopOrdersItemsVariantesModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Model\Shop\Payment\ShopPaymentMethodSettingsModel;
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
    public function getActivePaymentsMethods(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethod::class);
        return array_filter($allPaymentMethods, function($paymentMethod) {
            return $paymentMethod->isActive() && $paymentMethod->varName() !== "free";
        });
    }

    /**
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getFreePayment(): array
    {
        $allPaymentMethods = Loader::loadImplementations(IPaymentMethod::class);
        return array_filter($allPaymentMethods, function($paymentMethod) {
            return $paymentMethod->varName() == "free";
        });
    }

    /**
     * @param string $name
     * @return \CMW\Interface\Shop\IPaymentMethod|null
     */
    public function getPaymentByName(string $name): ?IPaymentMethod
    {
        foreach ($this->getPaymentsMethods() as $paymentsMethod) {
            if ($paymentsMethod->name() === $name){
                return $paymentsMethod;
            }
        }
        return null;
    }

    #[Link("/payments", Link::GET, [], "/cmw-admin/shop")]
    private function shopPayments(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");
        View::createAdminView('Shop', 'payments')
            ->addVariableList(['methods' => $this->getPaymentsMethods()])
            ->view();
    }

    #[NoReturn] #[Link("/payments/enable/:name", Link::GET, [], "/cmw-admin/shop")]
    private function shopEnablePayments(Request $request, string $name): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");
        $nameWithStatus = $name . '_is_active';
        if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($nameWithStatus, 1)){
            Flash::send(Alert::ERROR,'Boutique', "Impossible de d'activer la méthode de paiement'");
        }
        Flash::send(Alert::SUCCESS, "Boutique", "Méthode de paiement activé !");
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/payments/disable/:name", Link::GET, [], "/cmw-admin/shop")]
    private function shopDisablePayments(Request $request, string $name): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");
        $nameWithStatus = $name . '_is_active';
        if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($nameWithStatus, 0)){
            Flash::send(Alert::ERROR,'Boutique', "Impossible de désactiver la méthode de paiement'");
        }
        Flash::send(Alert::SUCCESS, "Boutique", "Méthode de paiement désactivé !");
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/payments/settings", Link::POST, [], "/cmw-admin/shop")]
    private function shopPaymentsSettingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");

        $settings = $_POST;

        foreach ($settings as $key => $value) {
            if ($key === 'security-csrf-token' || $key === 'honeyInput') {
                continue;
            }
            $key = FilterManager::filterData($key, 50);
            $value = FilterManager::filterData($value, 255);

            if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($key, $value)){
                Flash::send(Alert::ERROR,'Erreur',
                    "Impossible de mettre à jour le paramètre $key");
            }
        }

        Flash::send(Alert::SUCCESS,'Succès', "Les paramètres ont été mis à jour");
        Redirect::redirectPreviousRoute();
    }

    public function handleCreateOrder(UserEntity $user): void
    {
        //TODO : Gestion physique / virtuel
        //TODO : Baisser les stock
        $sessionId = session_id();
        $commandTunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());

        $order = ShopOrdersModel::getInstance()->createOrder($user->getId(), $commandTunnel->getShipping()?->getId(), $commandTunnel->getShopDeliveryUserAddress()->getId(), $commandTunnel->getPaymentName());

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($user->getId(), $sessionId);

        foreach ($cartContent as $cartItem) {
            $discountId = $cartItem->getDiscount()?->getId() ?? null;
            $orderItem = ShopOrdersItemsModel::getInstance()->createOrderItems($order->getOrderId(), $cartItem->getItem()->getId(), $cartItem->getQuantity(), $cartItem->getItem()->getPrice(), $cartItem->getItemTotalPriceAfterDiscount(), $discountId);
            $itemsVariantes = ShopCartVariantesModel::getInstance()->getShopItemVariantValueByCartId($cartItem->getId());
            if (!empty($itemsVariantes)) {
                foreach ($itemsVariantes as $itemVariantes) {
                    ShopOrdersItemsVariantesModel::getInstance()->setVariantToItemInOrder($orderItem->getOrderItemId(), $itemVariantes->getVariantValue()->getId());
                }
            }
            if ($cartItem->getItem()->getType() == 1) {
                $virtualItemVarName = ShopItemsVirtualMethodModel::getInstance()->getVirtualItemMethodByItemId($cartItem->getItem()->getId())->getVirtualMethod()->varName();
                $quantity = $cartItem->getQuantity();
                for ($i = 0; $i < $quantity; $i++) {
                    ShopItemsController::getInstance()->getVirtualItemsMethodsByVarName($virtualItemVarName)->execOnBuy($virtualItemVarName, $cartItem->getItem(), $user);
                }
            }
        }

        //handleNotification

        ShopCartModel::getInstance()->clearUserCart($user->getId());
        ShopCommandTunnelModel::getInstance()->clearTunnel($user->getId());
    }

    #[NoReturn] #[Listener(eventName: ShopPaymentCompleteEvent::class, times: 0, weight: 1)]
    private function onPaymentComplete(): void
    {
        $user = UsersModel::getCurrentUser();

        $this->handleCreateOrder($user);

        Flash::send(Alert::SUCCESS, "Achat effectué", "Merci pour votre achat " . $user->getPseudo());

        Redirect::redirect("shop/history");
    }

    #[NoReturn] #[Listener(eventName: ShopPaymentCancelEvent::class, times: 0, weight: 1)]
    private function onPaymentCancel(): void
    {
        Flash::send(Alert::WARNING, "Paiement annulé", "");
        Redirect::redirect("shop/command");
    }

}
