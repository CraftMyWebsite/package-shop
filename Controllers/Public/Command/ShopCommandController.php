<?php
namespace CMW\Controller\Shop\Public\Command;

use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Shop\Public\Cart\ShopCartController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Exception\Shop\Payment\ShopPaymentException;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Delivery\ShopShippingModel;
use CMW\Model\Shop\Item\ShopItemsVirtualMethodModel;
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

        ShopDiscountModel::getInstance()->autoStatusChecker();

        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $giftCodes = [];
        $cartDiscountModel = ShopCartDiscountModel::getInstance();
        $cartDiscounts = $cartDiscountModel->getCartDiscountByUserId($userId, $sessionId);
        foreach ($cartDiscounts as $cartDiscount) {
            $discountGiftCode = $cartDiscountModel->getCartDiscountById($cartDiscount->getId());
            if ($discountGiftCode->getDiscount()->getLinked() == 3) {
                $giftCodes[] = $discountGiftCode->getDiscount();
            }
        }

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

        $cartOnlyVirtual = $this->handleCartTypeContent($cartContent);
        $cartIsFree = $this->handleCartIsFree($cartContent);
        $priceType = $this->handleCartPriceType($cartContent);

        //TODO: Verifier si les promotions appliquées au panier sont encore valides

        if (empty($userAddresses)) {
            $view = new View("Shop", "Command/newAddress");
            $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "userAddresses" => $userAddresses, "giftCodes" => $giftCodes]);
            $view->view();
        } else {
            $commandTunnelModel = ShopCommandTunnelModel::getInstance()->getShopCommandTunnelByUserId($userId);
            $currentStep = $commandTunnelModel->getStep();
            if ($currentStep === 0) {
                $view = new View("Shop", "Command/address");
                $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "userAddresses" => $userAddresses, "giftCodes" => $giftCodes]);
                $view->view();
            }
            if ($currentStep === 1) {
                if ($cartOnlyVirtual) {
                    ShopCommandTunnelModel::getInstance()->skipShippingNext($userId);
                    Redirect::redirectPreviousRoute();
                } else {
                    $commandTunnelAddressId = $commandTunnelModel->getShopDeliveryUserAddress()->getId();
                    $selectedAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($commandTunnelAddressId);
                    $shippings = ShopShippingModel::getInstance()->getShopShipping();
                    $view = new View("Shop", "Command/delivery");
                    $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "selectedAddress" => $selectedAddress, "shippings" => $shippings, "giftCodes" => $giftCodes]);
                    $view->view();
                }
            }
            if ($currentStep === 2) {
                if ($cartOnlyVirtual) {
                    $shippingMethod = null;
                } else {
                    $commandTunnelShippingId = $commandTunnelModel->getShipping()->getId();
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
                } elseif ($priceType == "money") {
                    $paymentMethods = ShopPaymentsController::getInstance()->getRealActivePaymentsMethods();
                } else {
                    $paymentMethods = ShopPaymentsController::getInstance()->getVirtualPaymentByVarNameAsArray($priceType);
                }
                $view = new View("Shop", "Command/payment");
                $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage,
                    "selectedAddress" => $selectedAddress, "shippingMethod" => $shippingMethod,
                    "paymentMethods" => $paymentMethods, "giftCodes" => $giftCodes]);
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

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($user->getId(), $sessionId);

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

        if (!isset($_POST['paymentName'])){
            Flash::send(Alert::ERROR, 'Erreur', 'Merci de sélectionner une méthode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        $paymentName = FilterManager::filterInputStringPost('paymentName');

        ShopCommandTunnelModel::getInstance()->setPaymentName($user->getId(), $paymentName);

        $paymentMethod = ShopPaymentsController::getInstance()->getPaymentByVarName($paymentName);

        if (!$paymentMethod){
            Flash::send(Alert::ERROR, 'Erreur', 'Impossible de trouver ce mode de paiement !');
            Redirect::redirectPreviousRoute();
        }

        try {
            $paymentMethod->doPayment($cartContent, $user, $selectedAddress);
        }
        catch (ShopPaymentException $e) {
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
            ShopCartController::getInstance()->handleStock($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleLimitePerUser($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleGlobalLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
            ShopCartController::getInstance()->handleByOrderLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
        }
    }

    /**
     * @param ShopCartItemEntity[] $cartContent
     */
    private function handleCartTypeContent(array $cartContent) : bool
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
    private function handleCartIsFree(array $cartContent) : bool
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
    private function handleCartPriceType(array $cartContent) : string
    {
        $priceType = "";
        foreach ($cartContent as $item) {
            $priceType = $item->getItem()->getPriceType();
            break;
        }
        return $priceType;
    }
}