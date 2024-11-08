<?php

namespace CMW\Controller\Shop\Public\Setting;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\Country\ShopCountryModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopSettingController
 * @package shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopSettingController extends AbstractController
{
    #[Link('/settings', Link::GET, [], '/shop')]
    private function publicSettingsView(): void
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
        $userId = UsersModel::getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login');
        }
        $userAddresses = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressByUserId($userId);
        $country = ShopCountryModel::getInstance()->getCountry();

        $view = new View('Shop', 'Users/settings');
        $view->addVariableList(['userAddresses' => $userAddresses, 'country' => $country]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/settings/fav/:id', Link::GET, [], '/shop')]
    private function publicSettingsUpdateFav(int $addressId): void
    {
        ShopDeliveryUserAddressModel::getInstance()->makeAsFav($addressId);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Favoris mis à jours');
        Redirect::redirectPreviousRoute();
    }

    #[Link('/settings/editAddress/:id', Link::GET, [], '/shop')]
    private function publicEditAddressGet(int $addressId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login');
        }
        $userAddress = ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressById($addressId);
        $country = ShopCountryModel::getInstance()->getCountry();

        $view = new View('Shop', 'Users/editAddress');
        $view->addVariableList(['userAddress' => $userAddress, 'country' => $country]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }


    #[NoReturn] #[Link('/settings/editAddress/:id', Link::POST, ['.*?'], '/shop')]
    private function publicEditAddressPost(int $addressId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();

        [$label, $fav, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country] = Utils::filterInput('address_label', 'fav', 'first_name', 'last_name', 'phone', 'line_1', 'line_2', 'city', 'postal_code', 'country');

        $fav = is_null($fav) ? 0 : 1;

        ShopDeliveryUserAddressModel::getInstance()->editDeliveryUserAddress($addressId, $label, $fav, $userId, $firstName, $lastName, $phone, $line1, $line2, $city, $postalCode, $country);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Adresse mises à jour !');

        Redirect::redirect('shop/settings');
    }

    #[NoReturn] #[Link('/settings/deleteAddress/:id', Link::GET, ['.*?'], '/shop')]
    private function publicRemoveAddress(int $addressId): void
    {
        ShopDeliveryUserAddressModel::getInstance()->deleteAddress($addressId);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Adresse supprimé !');

        Redirect::redirect('shop/settings');
    }
}
