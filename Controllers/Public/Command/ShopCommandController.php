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
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
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

        if (empty($cartContent)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Votre panier est vide.');
            Redirect::redirect('shop/cart');
        }

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

        if (!ShopCommandTunnelModel::getInstance()->tunnelExist($userId)) {
            ShopCommandTunnelModel::getInstance()->createTunnel($userId);
        }

        ShopCartController::getInstance()->handleItemHealth($userId, $sessionId);

        $this->handleBeforeCommandCheck($userId, $sessionId, $cartContent);

        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        $cartIsFree = $this->handleCartIsFree($cartContent);
        $priceType = $this->handleCartPriceType($cartContent);

        $appliedDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);
        ShopDiscountModel::getInstance()->autoStatusChecker();

        if (!empty($appliedDiscounts)) {
            $cart = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId);
            $entityFound = 0;
            foreach ($appliedDiscounts as $appliedDiscount) {
                if ($appliedDiscount->getDiscount()->getStatus() == 0) {
                    ShopCartDiscountModel::getInstance()->removeCode($cart->getId(), $appliedDiscount->getDiscount()->getId());
                    $entityFound = 1;
                    foreach ($cartContent as $cartItem) {
                        ShopCartItemModel::getInstance()->removeCodeToItem($userId, $sessionId, $cartItem->getItem()->getId(), $appliedDiscount->getDiscount()->getId());
                    }
                }
            }
            if ($entityFound == 1) {
                Flash::send(Alert::INFO, 'Boutique', 'Certaines promotions ne sont plus disponible !');
                Redirect::redirect('shop/command');
            }
        }

        if (empty($userAddresses)) {
            $storedData = $_SESSION['cmw_shop_add_new_address'] ?? [];
            $country = ShopCountryModel::getInstance()->getCountry();
            $view = new View('Shop', 'Command/newAddress');
            $view->addVariableList(['country' => $country, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'userAddresses' => $userAddresses, 'appliedCartDiscounts' => $appliedCartDiscounts, 'storedData' => $storedData]);
            $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
            $view->view();
        } else {
            $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
            $currentStep = $commandTunnelModel->getStep();
            if ($currentStep === 0) {
                $storedData = $_SESSION['cmw_shop_add_new_address'] ?? [];
                $country = ShopCountryModel::getInstance()->getCountry();
                $view = new View('Shop', 'Command/address');
                $view->addVariableList(['country' => $country, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'userAddresses' => $userAddresses, 'appliedCartDiscounts' => $appliedCartDiscounts, 'storedData' => $storedData]);
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
                        NotificationManager::notify('Adresse introuvable', $selectedAddress->getLine1() . ' ' . $selectedAddress->getCity() . ' ' . $selectedAddress->getPostalCode() . ' ' . $selectedAddress->getFormattedCountry() . ' ne trouve pas de méthode d\'envoie !');
                    }
                    $varName = 'withdraw_point_map';
                    $useInteractiveMap = ShopSettingsModel::getInstance()->getGlobalSetting($varName . '_use', $varName) ?? '1';
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

        //Store data in case of error
        $_SESSION['cmw_shop_add_new_address'] = [
            'address_label' => $_POST['address_label'] ?? null,
            'first_name' => $_POST['first_name'] ?? null,
            'last_name' => $_POST['last_name'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'line_1' => $_POST['line_1'] ?? null,
            'line_2' => $_POST['line_2'] ?? null,
            'city' => $_POST['city'] ?? null,
            'postal_code' => $_POST['postal_code'] ?? null,
            'country' => $_POST['country'] ?? null,
        ];

        if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['line_1'], $_POST['city'], $_POST['postal_code'], $_POST['country'])) {
            Flash::send(Alert::ERROR, "Erreur", "Merci de remplir tous les champs obligatoir !");
            Redirect::redirectPreviousRoute();
        }

        [$label, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        if (ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, 1, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country)) {
            unset($_SESSION['cmw_shop_add_new_address']);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/command/addAddress', Link::POST, ['.*?'], '/shop')]
    private function publicAddAddressPost(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        //Store data in case of error
        $_SESSION['cmw_shop_add_new_address'] = [
            'address_label' => $_POST['address_label'] ?? null,
            'first_name' => $_POST['first_name'] ?? null,
            'last_name' => $_POST['last_name'] ?? null,
            'phone' => $_POST['phone'] ?? null,
            'line_1' => $_POST['line_1'] ?? null,
            'line_2' => $_POST['line_2'] ?? null,
            'city' => $_POST['city'] ?? null,
            'postal_code' => $_POST['postal_code'] ?? null,
            'country' => $_POST['country'] ?? null,
        ];

        if (!isset($_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['line_1'], $_POST['city'], $_POST['postal_code'], $_POST['country'])) {
            Flash::send(Alert::ERROR, "Erreur", "Merci de remplir tous les champs obligatoir !");
            Redirect::redirectPreviousRoute();
        }

        [$label, $fav, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'fav', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        $fav = is_null($fav) ? 0 : 1;

        if (ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, $fav, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country)) {
            unset($_SESSION['cmw_shop_add_new_address']);
        }

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
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traité la commande, veuillez-vous connecter !');
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
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traité la commande !');
            Redirect::redirectToHome();
        }

        $addressId = $commandTunnelModel->getShopDeliveryUserAddress()?->getId();

        if (!$addressId) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traité la commande, adresse introuvable !');
            Redirect::redirectToHome();
        }

        $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId);

        if (!$selectedAddress) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de traité la commande, adresse introuvable !');
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
