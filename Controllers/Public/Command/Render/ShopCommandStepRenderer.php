<?php

namespace CMW\Controller\Shop\Public\Command\Renderer;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Public\Command\Service\ShopCommandService;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Country\ShopCountryModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Type\Shop\Const\Payment\PaymentPriceTypeConst;
use CMW\Utils\Redirect;
use CMW\Manager\Views\View;

class ShopCommandStepRenderer extends AbstractController
{
    /**
     * @param int $userId
     * @param string $sessionId
     * @param array $cartContent
     * @param bool $cartOnlyVirtual
     * @param bool $cartIsFree
     * @param string $priceType
     * @param array $appliedCartDiscounts
     * @param array $userAddresses
     * @return void
     */
    public function render(int $userId, string $sessionId, array $cartContent, bool $cartOnlyVirtual, bool $cartIsFree, string $priceType, array $appliedCartDiscounts, array $userAddresses): void
    {
        $tunnel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
        $step = $tunnel->getStep();

        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = $imagesItem->getDefaultImg();

        if ($cartOnlyVirtual && ShopCommandService::getInstance()->isShopVirtualOnly()) {
            $this->renderPaymentChoice($cartContent, $imagesItem, $defaultImage, $appliedCartDiscounts, true, $cartIsFree, $priceType, $tunnel);
            return;
        }

        if (empty($userAddresses)) {
            $this->renderNewAddressForm($cartContent, $imagesItem, $defaultImage, $appliedCartDiscounts);
            return;
        }

        if ($step === 0) {
            $this->renderAddressChoice($cartContent, $imagesItem, $defaultImage, $appliedCartDiscounts, $userAddresses);
        } elseif ($step === 1) {
            if ($cartOnlyVirtual) {
                ShopCommandTunnelModel::getInstance()->skipShippingNext($userId);
                Redirect::redirectPreviousRoute();
            } else {
                $this->renderShippingChoice($userId, $cartContent, $imagesItem, $defaultImage, $appliedCartDiscounts, $tunnel);
            }
        } elseif ($step === 2) {
            $this->renderPaymentChoice($cartContent, $imagesItem, $defaultImage, $appliedCartDiscounts, $cartOnlyVirtual, $cartIsFree, $priceType, $tunnel);
        }
    }

    /**
     * @param array $cartContent
     * @param $imagesItem
     * @param $defaultImage
     * @param array $appliedCartDiscounts
     * @return void
     */
    private function renderNewAddressForm(array $cartContent, $imagesItem, $defaultImage, array $appliedCartDiscounts): void
    {
        $storedData = $_SESSION['cmw_shop_add_new_address'] ?? [];
        $country = ShopCountryModel::getInstance()->getCountry();

        View::createPublicView('Shop', 'Command/newAddress')
            ->addVariableList(compact(
                    'country', 'cartContent', 'imagesItem', 'defaultImage',
                    'appliedCartDiscounts', 'storedData'
                ) + ['userAddresses' => []])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }

    /**
     * @param array $cartContent
     * @param $imagesItem
     * @param $defaultImage
     * @param array $appliedCartDiscounts
     * @param array $userAddresses
     * @return void
     */
    private function renderAddressChoice(array $cartContent, $imagesItem, $defaultImage, array $appliedCartDiscounts, array $userAddresses): void
    {
        $storedData = $_SESSION['cmw_shop_add_new_address'] ?? [];
        $country = ShopCountryModel::getInstance()->getCountry();

        View::createPublicView('Shop', 'Command/address')
            ->addVariableList(compact(
                'country', 'cartContent', 'imagesItem', 'defaultImage',
                'userAddresses', 'appliedCartDiscounts', 'storedData'
            ))
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }

    /**
     * @param int $userId
     * @param array $cartContent
     * @param $imagesItem
     * @param $defaultImage
     * @param array $appliedCartDiscounts
     * @param $tunnel
     * @return void
     */
    private function renderShippingChoice(int $userId, array $cartContent, $imagesItem, $defaultImage, array $appliedCartDiscounts, $tunnel): void
    {
        $addressId = $tunnel->getShopDeliveryUserAddress()->getId();
        $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId);

        try {
            $shippings = ShopShippingModel::getInstance()->getAvailableShipping($selectedAddress, $cartContent);
        } catch (\Exception $e) {
            Flash::send(Alert::ERROR, 'Boutique', 'Certains articles de votre panier n’ont pas de poids défini.');
            Redirect::redirect('shop/cart');
        }

        $withdrawPoints = ShopShippingModel::getInstance()->getAvailableWithdrawPoint($selectedAddress, $cartContent);

        // Trie les points de retrait par distance
        usort($withdrawPoints, fn($a, $b) =>
            $a->getDistance($selectedAddress->getLatitude(), $selectedAddress->getLongitude())
            <=> $b->getDistance($selectedAddress->getLatitude(), $selectedAddress->getLongitude())
        );

        if (empty($shippings) && empty($withdrawPoints)) {
            Flash::send(Alert::WARNING, 'Boutique', "Aucune méthode de livraison n'est disponible pour cette adresse.");
            NotificationManager::notify(
                'Adresse introuvable',
                $selectedAddress->getLine1() . ' ' . $selectedAddress->getCity() . ' ' .
                $selectedAddress->getPostalCode() . ' ' . $selectedAddress->getFormattedCountry() .
                ' ne trouve pas de méthode d\'envoi !'
            );
        }

        $useInteractiveMap = ShopSettingsModel::getInstance()->getGlobalSetting('withdraw_point_map_use', 'withdraw_point_map') ?? '1';

        View::createPublicView('Shop', 'Command/delivery')
            ->addStyle(
                'Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css',
                'App/Package/Shop/Resources/OST/leaflet.css'
            )
            ->addScriptBefore('App/Package/Shop/Resources/OST/leaflet.js')
            ->addVariableList(compact(
                'useInteractiveMap', 'cartContent', 'imagesItem', 'defaultImage',
                'selectedAddress', 'shippings', 'withdrawPoints', 'appliedCartDiscounts'
            ))
            ->addPhpAfter('App/Package/Shop/Resources/OST/map.php')
            ->view();
    }

    /**
     * @param array $cartContent
     * @param $imagesItem
     * @param $defaultImage
     * @param array $appliedCartDiscounts
     * @param bool $cartOnlyVirtual
     * @param bool $cartIsFree
     * @param string $priceType
     * @param $tunnel
     * @return void
     */
    private function renderPaymentChoice(array $cartContent, $imagesItem, $defaultImage, array $appliedCartDiscounts, bool $cartOnlyVirtual, bool $cartIsFree, string $priceType, $tunnel): void
    {
        $selectedAddress = null;
        $shippingMethod = null;
        $isVirtualOnly = ShopCommandService::getInstance()->isShopVirtualOnly();

        if (!$cartOnlyVirtual && !$isVirtualOnly) {
            $addressId = $tunnel->getShopDeliveryUserAddress()?->getId();
            $selectedAddress = $addressId ? ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId) : null;

            $shippingId = $tunnel->getShipping()?->getId();
            if (is_null($shippingId)) {
                Flash::send(Alert::ERROR, 'Boutique', 'Cette méthode d\'envoi n\'existe plus !');
                ShopCommandTunnelModel::getInstance()->clearTunnel($tunnel->getUser()->getId());
                Redirect::redirectPreviousRoute();
            }
            $shippingMethod = ShopShippingModel::getInstance()->getShopShippingById($shippingId);
        }

        if ($cartIsFree) {
            $paymentMethods = is_null($shippingMethod) || $shippingMethod->getPrice() == 0
                ? ShopPaymentsController::getInstance()->getFreePayment()
                : ShopPaymentsController::getInstance()->getRealActivePaymentsMethods();
        } elseif ($priceType === PaymentPriceTypeConst::MONEY) {
            $paymentMethods = ShopPaymentsController::getInstance()->getRealActivePaymentsMethods();
        } else {
            $paymentMethods = ShopPaymentsController::getInstance()->getVirtualPaymentByVarNameAsArray($priceType);
        }

        View::createPublicView('Shop', 'Command/payment')
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->addVariableList(compact(
                'cartContent', 'imagesItem', 'defaultImage', 'selectedAddress',
                'shippingMethod', 'paymentMethods', 'appliedCartDiscounts', 'isVirtualOnly'
            ))
            ->view();
    }
}
