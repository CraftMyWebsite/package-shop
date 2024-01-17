<?php
namespace CMW\Controller\Shop\Public\Command;

use CMW\Controller\Shop\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Public\Cart\ShopCartController;
use CMW\Controller\Users\UsersController;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Shop\ShopCommandTunnelModel;
use CMW\Model\Shop\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\ShopImagesModel;
use CMW\Model\Shop\ShopShippingModel;
use CMW\Model\Users\UsersModel;
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
    #[Link("/command", Link::GET, [], "/shop")]
    public function publicCommandView(): void
    {
        if (!UsersController::isUserLogged()) {
            Redirect::redirect('login');
        }

        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        $cartContent = ShopCartsModel::getInstance()->getShopCartsByUserId($userId, $sessionId);
        $imagesItem = ShopImagesModel::getInstance();

        $userAddresses = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressByUserId($userId);

        if (empty($cartContent)) {
            Flash::send(Alert::ERROR, "Boutique", "Votre panier est vide.");
            Redirect::redirectPreviousRoute();
        }

        if (!ShopCommandTunnelModel::getInstance()->tunnelExist($userId)) {
            ShopCommandTunnelModel::getInstance()->createTunnel($userId);
        }

        ShopCartController::getInstance()->handleItemHealth($userId, $sessionId);

        $this->handleBeforeCommandCheck($userId, $sessionId, $cartContent);

        if (empty($userAddresses)) {
            $view = new View("Shop", "Command/newAddress");
            $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem, "userAddresses" => $userAddresses]);
            $view->view();
        } else {
            $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
            $currentStep = $commandTunnelModel->getStep();
            if ($currentStep === 0) {
                $view = new View("Shop", "Command/address");
                $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem, "userAddresses" => $userAddresses]);
                $view->view();
            }
            if ($currentStep === 1) {
                $commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
                $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);
                $shippings = ShopShippingModel::getInstance()->getShopShipping();
                $view = new View("Shop", "Command/delivery");
                $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem, "selectedAddress" => $selectedAddress, "shippings" => $shippings]);
                $view->view();
            }
            if ($currentStep === 2) {
                $commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
                $commandTunnelShippingId = $commandTunnelModel->getShipping()->getId();
                $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);
                $shippingMethod = ShopShippingModel::getInstance()->getShopShippingById($commandTunnelShippingId);
                $paymentMethods = ShopPaymentsController::getInstance()->getPaymentsMethods();
                $view = new View("Shop", "Command/payment");
                $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,
                    "selectedAddress" => $selectedAddress, "shippingMethod" => $shippingMethod,
                    "paymentMethods" => $paymentMethods]);
                $view->view();
            }
        }

    }

    #[Link("/command/createAddress", Link::POST, ['.*?'], "/shop")]
    public function publicCreateAddressPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        [$label, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label,1, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country);

        //TODO : Bien mais pour éviter la perte de l'attention de l'utilisateur devra rediriger à l'étape 2 avec selection auto de l'adresse
        Redirect::redirectPreviousRoute();
    }

    #[Link("/command/addAddress", Link::POST, ['.*?'], "/shop")]
    public function publicAddAddressPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        [$label, $fav, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'fav', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        $fav = is_null($fav) ? 0 : 1;

        if ($fav === 1) {
            ShopDeliveryUserAddressModel::getInstance()->removeOtherFav($userId);
        }

        ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label,$fav, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/command/toDelivery", Link::POST, ['.*?'], "/shop")]
    public function publicToDeliveryPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        [$addressId] = Utils::filterInput('addressId');

        if (is_null($addressId)) {
            Flash::send(Alert::ERROR, "Boutique", "Veuillez sélectionner une adresse.");
            Redirect::redirectPreviousRoute();
        }

        ShopCommandTunnelModel::getInstance()->addDelivery($userId, $addressId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/command/toAddress", Link::POST, ['.*?'], "/shop")]
    public function publicToAddressPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        ShopCommandTunnelModel::getInstance()->clearTunnel($userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/command/toPayment", Link::POST, ['.*?'], "/shop")]
    public function publicToPaymentPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        [$shippingId] = Utils::filterInput('shippingId');

        if (is_null($shippingId)) {
            Flash::send(Alert::ERROR, "Boutique", "Veuillez sélectionner un mode de livraison.");
            Redirect::redirectPreviousRoute();
        }

        ShopCommandTunnelModel::getInstance()->addShipping($userId, $shippingId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/command/toShipping", Link::POST, ['.*?'], "/shop")]
    public function publicToShippingPost(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        ShopCommandTunnelModel::getInstance()->clearShipping($userId);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/command/finalize", Link::POST, [], "/shop")]
    public function publicFinalizeCommand(): void
    {
        $user = UsersModel::getCurrentUser();

        if (!$user){
            //TODO Internal error.
            Redirect::redirectToHome();
        }

        $sessionId = session_id();

        if (!$sessionId){
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de récupérer votre session !');
            Redirect::redirectToHome();
        }

        $cartContent = ShopCartsModel::getInstance()->getShopCartsByUserId($user->getId(), $sessionId);

        $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($user->getId());

        if (!$commandTunnelModel){
            //TODO Internal error.
            Redirect::redirectToHome();
        }

        $addressId = $commandTunnelModel->getShopDeliveryUserAddress()?->getId();

        if (!$addressId){
            //TODO Error unable to reach delivery ID
            Redirect::redirectToHome();
        }

        $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId);

        if (!$selectedAddress){
            //TODO Error no address selected / valid
            Redirect::redirectToHome();
        }

        $shipping = $commandTunnelModel->getShipping();

        if (!$shipping){
            //TODO Error no delivery method selected
            Redirect::redirectToHome();
        }

        if (!isset($_POST['paymentName'])){
            Flash::send(Alert::ERROR, 'Erreur', 'Merci de sélectionner une méthode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        $paymentName = FilterManager::filterInputStringPost('paymentName');

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByName($paymentName);

        if (!$paymentMethod){
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de trouver ce mode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        try {
            $paymentMethod->doPayment($cartContent, $user, $shipping, $selectedAddress);
        }
        catch (ShopPaymentException $e) {
            Flash::send(Alert::ERROR, 'Erreur', "Erreur de paiement => $e");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param int $userId
     * @param string $sessionId
     * @param array $cartContent
     */
    private function handleBeforeCommandCheck(int $userId, string $sessionId, array $cartContent): void
    {
        foreach ($cartContent as $itemCart) {
            $itemId = $itemCart->getItem()->getId();
            $quantity = $itemCart->getQuantity();
            ShopCartController::getInstance()->handleStock($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleLimitePerUser($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleGlobalLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleByOrderLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
        }
    }
}