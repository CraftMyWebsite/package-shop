<?php

namespace CMW\Controller\Shop\Public\Command;

use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Public\Cart\ShopCartController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Country\ShopCountryModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopCommandController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopCommandController extends AbstractController
{
    #[Link('/command', Link::GET, [], '/shop')]
    private function publicCommandView(): void
    {
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        if ($maintenance) {
            if (UsersController::isAdminLogged()) {
                Flash::send(Alert::INFO, 'Boutique', 'Shop est en maintenance, mais vous y avez accès car vous êtes administrateur');
            } else {
                $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
                Flash::send(Alert::WARNING, 'Boutique', $maintenanceMessage);
                Redirect::redirectToHome();
            }
        }

        if (!UsersController::isUserLogged()) {
            Redirect::redirect('login');
        }

        ShopDiscountModel::getInstance()->autoStatusChecker();

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $appliedCartDiscounts = [];
        $cartDiscountModel = ShopCartDiscountModel::getInstance();
        $cartDiscounts = $cartDiscountModel->getCartDiscountByUserId($userId, $sessionId);
        foreach ($cartDiscounts as $cartDiscount) {
            $discount = $cartDiscountModel->getCartDiscountById($cartDiscount->getId());
            if ($discount->getDiscount()->getLinked() == 3 || $discount->getDiscount()->getLinked() == 4) {
                $appliedCartDiscounts[] = $discount->getDiscount();
            }
        }

        $userAddresses = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressByUserId($userId);

        if (empty($cartContent)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Votre panier est vide.');
            // TODO : Fix redirect after item change in cart like Physical To virtual
            Redirect::redirectPreviousRoute();
        }

        if (!ShopCommandTunnelModel::getInstance()->tunnelExist($userId)) {
            ShopCommandTunnelModel::getInstance()->createTunnel($userId);
        }

        ShopCartController::getInstance()->handleItemHealth($userId, $sessionId);

        $this->handleBeforeCommandCheck($userId, $sessionId, $cartContent);

        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        $cartIsFree = $this->handleCartIsFree($cartContent);
        $priceType = $this->handleCartPriceType($cartContent);

        // TODO: Verifier si les promotions appliquées au panier sont encore valides

        if (empty($userAddresses)) {
            $country = ShopCountryModel::getInstance()->getCountry();
            $view = new View('Shop', 'Command/newAddress');
            $view->addVariableList(['country' => $country, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'userAddresses' => $userAddresses, 'appliedCartDiscounts' => $appliedCartDiscounts]);
            $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
            $view->view();
        } else {
            $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
            $currentStep = $commandTunnelModel->getStep();
            if ($currentStep === 0) {
                $country = ShopCountryModel::getInstance()->getCountry();
                $view = new View('Shop', 'Command/address');
                $view->addVariableList(['country' => $country, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'userAddresses' => $userAddresses, 'appliedCartDiscounts' => $appliedCartDiscounts]);
                $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
                $view->view();
            }
            if ($currentStep === 1) {
                if ($cartOnlyVirtual) {
                    ShopCommandTunnelModel::getInstance()->skipShippingNext($userId);
                    Redirect::redirectPreviousRoute();
                } else {
                    $commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
                    $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);
                    $shippings = ShopShippingModel::getInstance()->getAvailableShipping($selectedAddress, $cartContent);
                    $withdrawPoints = ShopShippingModel::getInstance()->getAvailableWithdrawPoint($selectedAddress, $cartContent);
                    usort($withdrawPoints, function($a, $b) use ($selectedAddress) {
                        $distanceA = $a->getDistance($selectedAddress->getLatitude(), $selectedAddress->getLongitude());
                        $distanceB = $b->getDistance($selectedAddress->getLatitude(), $selectedAddress->getLongitude());
                        return $distanceA <=> $distanceB;
                    });
                    if (empty($shippings) && empty($withdrawPoints)) {
                        Flash::send(Alert::WARNING, 'Boutique', "Nous sommes désolé mais aucune méthode de livraison n'est disponible pour cette adresse.");
                        // TODO : Notify admin adresse cant throw shipping
                    }
                    $varName = 'withdraw_point_map';
                    $useInteractiveMap = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use') ?? '1';
                    $view = new View('Shop', 'Command/delivery');
                    $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css', 'App/Package/Shop/Resources/OST/leaflet.css');
                    $view->addScriptBefore('App/Package/Shop/Resources/OST/leaflet.js');
                    $view->addVariableList(['useInteractiveMap' => $useInteractiveMap, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'selectedAddress' => $selectedAddress, 'shippings' => $shippings, 'withdrawPoints' => $withdrawPoints, 'appliedCartDiscounts' => $appliedCartDiscounts]);
                    $view->addPhpAfter('App/Package/Shop/Resources/OST/map.php');
                    $view->view();
                }
            }
            if ($currentStep === 2) {
                if ($cartOnlyVirtual) {
                    $shippingMethod = null;
                } else {

                    $commandTunnelShippingId = $commandTunnelModel->getShipping()?->getId();
                    if (is_null($commandTunnelShippingId)) {
                        Flash::send(Alert::ERROR, 'Boutique', 'Cette méthode d\'envoie n\'existe plus !');
                        ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
                        Redirect::redirectPreviousRoute();
                    }
                    $shippingMethod = ShopShippingModel::getInstance()->getShopShippingById($commandTunnelShippingId);
                }
                $commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
                $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);

                if ($cartIsFree) {
                    if (is_null($shippingMethod)) {
                        $paymentMethods = ShopPaymentsController::getInstance()->getFreePayment();
                    } else {
                        if ($shippingMethod->getPrice() == 0) {
                            $paymentMethods = ShopPaymentsController::getInstance()->getFreePayment();
                        } else {
                            $paymentMethods = ShopPaymentsController::getInstance()->getRealActivePaymentsMethods();
                        }
                    }
                } elseif ($priceType == 'money') {
                    $paymentMethods = ShopPaymentsController::getInstance()->getRealActivePaymentsMethods();
                } else {
                    $paymentMethods = ShopPaymentsController::getInstance()->getVirtualPaymentByVarNameAsArray($priceType);
                }
                $view = new View('Shop', 'Command/payment');
                $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
                $view->addVariableList(['cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage,
                    'selectedAddress' => $selectedAddress, 'shippingMethod' => $shippingMethod,
                    'paymentMethods' => $paymentMethods, 'appliedCartDiscounts' => $appliedCartDiscounts]);
                $view->view();
            }
        }
    }

    #[NoReturn] #[Link('/command/createAddress', Link::POST, ['.*?'], '/shop')]
    private function publicCreateAddressPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        [$label, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, 1, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/command/addAddress', Link::POST, ['.*?'], '/shop')]
    private function publicAddAddressPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        [$label, $fav, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'fav', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        $fav = is_null($fav) ? 0 : 1;

        ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, $fav, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/command/toDelivery', Link::POST, ['.*?'], '/shop')]
    private function publicToDeliveryPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        [$addressId] = Utils::filterInput('addressId');

        if (is_null($addressId)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Veuillez sélectionner une adresse.');
            Redirect::redirectPreviousRoute();
        }

        ShopCommandTunnelModel::getInstance()->addDelivery($userId, $addressId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/command/toAddress', Link::POST, ['.*?'], '/shop')]
    private function publicToAddressPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        ShopCommandTunnelModel::getInstance()->clearTunnel($userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/command/toPayment', Link::POST, ['.*?'], '/shop')]
    private function publicToPaymentPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        [$shippingId] = Utils::filterInput('shippingId');

        if (is_null($shippingId)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Veuillez sélectionner un mode de livraison.');
            Redirect::redirectPreviousRoute();
        }

        ShopCommandTunnelModel::getInstance()->addShipping($userId, $shippingId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/command/toShipping', Link::POST, ['.*?'], '/shop')]
    private function publicToShippingPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        $sessionId = session_id();
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        if ($cartOnlyVirtual) {
            ShopCommandTunnelModel::getInstance()->skipShippingPrevious($userId);
        } else {
            ShopCommandTunnelModel::getInstance()->clearShipping($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/command/finalize', Link::POST, [], '/shop')]
    private function publicFinalizeCommand(): void
    {
        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (!$user) {
            // TODO Internal error.
            Redirect::redirectToHome();
        }

        $sessionId = session_id();

        if (!$sessionId) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de récupérer votre session !');
            Redirect::redirectToHome();
        }

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($user->getId(), $sessionId);

        $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());

        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        if (!$cartOnlyVirtual) {
            if (is_null($commandTunnelModel->getShipping()?->getId())) {
                Flash::send(Alert::ERROR, 'Boutique', 'Cette méthode d\'envoie n\'existe plus !');
                ShopCommandTunnelModel::getInstance()->clearTunnel($user->getId());
                Redirect::redirectPreviousRoute();
            }
        }

        if (!$commandTunnelModel) {
            // TODO Internal error.
            Redirect::redirectToHome();
        }

        $addressId = $commandTunnelModel->getShopDeliveryUserAddress()?->getId();

        if (!$addressId) {
            // TODO Error unable to reach delivery ID
            Redirect::redirectToHome();
        }

        $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId);

        if (!$selectedAddress) {
            // TODO Error no address selected / valid
            Redirect::redirectToHome();
        }

        if (!isset($_POST['paymentName'])) {
            Flash::send(Alert::ERROR, 'Erreur', 'Merci de sélectionner une méthode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        $paymentName = FilterManager::filterInputStringPost('paymentName');

        ShopCommandTunnelModel::getInstance()->setPaymentName($user->getId(), $paymentName);

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName($paymentName);

        if (!$paymentMethod) {
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de trouver ce mode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        try {
            $paymentMethod->doPayment($cartContent, $user, $selectedAddress);
        } catch (ShopPaymentException $e) {
            Flash::send(Alert::ERROR, 'Erreur', "Erreur de paiement => $e");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param int $userId
     * @param string $sessionId
     * @param ShopCartItemEntity[] $cartContent
     */
    private function handleBeforeCommandCheck(int $userId, string $sessionId, array $cartContent): void
    {
        foreach ($cartContent as $itemCart) {
            $itemId = $itemCart->getItem()->getId();
            $quantity = $itemCart->getQuantity();
            ShopCartController::getInstance()->handleStock($itemCart, $itemId, $quantity, $userId, $sessionId);
            ShopCartController::getInstance()->handleLimitePerUser($itemCart, $itemId, $quantity, $userId, $sessionId);
            ShopCartController::getInstance()->handleGlobalLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            ShopCartController::getInstance()->handleByOrderLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            ShopCartController::getInstance()->handleDraft($itemCart, $itemId, $userId, $sessionId);
        }
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function handleCartTypeContent(array $cartContent): bool
    {
        foreach ($cartContent as $item) {
            if ($item->getItem()->getType() != 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function handleCartIsFree(array $cartContent): bool
    {
        foreach ($cartContent as $item) {
            if ($item->getItem()->getPrice() != 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     * @return string // return the money var name
     */
    private function handleCartPriceType(array $cartContent): string
    {
        $priceType = '';
        foreach ($cartContent as $item) {
            $priceType = $item->getItem()->getPriceType();
            break;
        }
        return $priceType;
    }
}
