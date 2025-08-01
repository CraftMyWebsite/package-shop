<?php

namespace CMW\Controller\Shop\Public\Command\Service;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Controller\Shop\Public\Command\Analyzer\ShopCartAnalyzer;
use JetBrains\PhpStorm\NoReturn;

class ShopCommandStepService extends AbstractController
{
    #[NoReturn] public function createAddress(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

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
            Flash::send(Alert::ERROR, "Erreur", "Merci de remplir tous les champs obligatoires !");
            Redirect::redirectPreviousRoute();
        }

        [$label, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] =
            Utils::filterInput('address_label', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        if (ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, 1, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country)) {
            unset($_SESSION['cmw_shop_add_new_address']);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] public function addAddress(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

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
            Flash::send(Alert::ERROR, "Erreur", "Merci de remplir tous les champs obligatoires !");
            Redirect::redirectPreviousRoute();
        }

        [$label, $fav, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] =
            Utils::filterInput('address_label', 'fav', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        $fav = is_null($fav) ? 0 : 1;

        if (ShopDeliveryUserAddressModel::getInstance()->createDeliveryUserAddress($label, $fav, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country)) {
            unset($_SESSION['cmw_shop_add_new_address']);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] public function toDelivery(): void
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

    #[NoReturn] public function toAddress(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] public function toPayment(): void
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

    #[NoReturn] public function toShipping(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $cartOnlyVirtual = ShopCartAnalyzer::isOnlyVirtual($cartContent);

        if ($cartOnlyVirtual) {
            ShopCommandTunnelModel::getInstance()->skipShippingPrevious($userId);
        } else {
            ShopCommandTunnelModel::getInstance()->clearShipping($userId);
        }

        Redirect::redirectPreviousRoute();
    }
}
