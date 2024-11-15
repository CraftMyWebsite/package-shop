<?php

namespace CMW\Controller\Shop\Admin\Shipping;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Shop\IShippingMethod;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Country\ShopCountryModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Shipping\ShopShippingModel;
use CMW\Model\Shop\Shipping\ShopShippingRequirementModel;
use CMW\Model\Shop\Shipping\ShopShippingWithdrawPointModel;
use CMW\Model\Shop\Shipping\ShopShippingZoneModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopShippingController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopShippingController extends AbstractController
{
    #[Link('/shipping', Link::GET, [], '/cmw-admin/shop')]
    private function shopShippingViews(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping');

        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $shippingMethods = $this->getShippingMethods();
        $configShippingMethods = $this->getGlobalVarShippingMethods();
        $shippings = ShopShippingModel::getInstance()->getShopShippings();
        $withdrawPoints = ShopShippingWithdrawPointModel::getInstance()->getShopShippingWithdrawPoint();
        $shippingZones = ShopShippingZoneModel::getInstance()->getShopShippingZone();
        $countries = ShopCountryModel::getInstance()->getCountry();

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.carts');
        View::createAdminView('Shop', 'Shipping/main')
            ->addVariableList(['symbol' => $symbol, 'shippings' => $shippings, 'withdrawPoints' => $withdrawPoints, 'configShippingMethods' => $configShippingMethods, 'shippingMethods' => $shippingMethods, 'shippingZones' => $shippingZones, 'countries' => $countries])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js', 'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[NoReturn] #[Link('/shipping/zone', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingAddZone(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.add');

        [$name, $zone] = Utils::filterInput(  'name', 'zone');

        $shippingZones = ShopShippingZoneModel::getInstance()->getShopShippingZone();
        if ($this->checkIfZoneExists($shippingZones, $zone)) {
            Flash::send(Alert::WARNING, 'Boutique', 'Cette zone existe déjà !');
        } else {
            ShopShippingZoneModel::getInstance()->createZone($name, $zone);
            Flash::send(Alert::SUCCESS, 'Boutique', 'Zone ajouté !');
        }
        Redirect::redirectPreviousRoute();
    }

    private function checkIfZoneExists(array $zones, string $country): bool
    {
        foreach ($zones as $zone) {
            if ($zone->getCountry() === $country) {
                return true;
            }
        }
        return false;
    }

    #[NoReturn] #[Link('/shipping/zone/edit/:zoneId', Link::POST, ['[0-9]+'], '/cmw-admin/shop')]
    private function shopShippingEditZone(int $zoneId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.edit');

        [$name, $zone] = Utils::filterInput(  'name', 'zone');

        ShopShippingZoneModel::getInstance()->editZone($zoneId, $name, $zone);
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/zone/delete/:zoneId', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function shopShippingDeleteZone(int $zoneId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.delete');

        $shippings = ShopShippingModel::getInstance()->getShopShippings();
        foreach ($shippings as $shipping) {
            if ($shipping->getZone() && $shipping->getZone()->getId() === $zoneId) {
                Flash::send(Alert::WARNING, 'Boutique', 'La zone est liée à une ou des méthodes d\'envoi.');
                Redirect::redirectPreviousRoute();
            }
        }

        ShopShippingZoneModel::getInstance()->deleteZone($zoneId);
        Flash::send(Alert::SUCCESS, 'Boutique', 'Zone supprimé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/depot', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingAddDepot(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.add');

        [$name, $distance, $address , $city, $postalCode, $country] = Utils::filterInput(  'name', 'distance', 'address', 'city', 'postalCode', 'country');

        if (ShopShippingWithdrawPointModel::getInstance()->createWithdrawPoint($distance, $name, $address, $city, $postalCode, $country)) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Dépôts ajouté !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible d\'ajouter le dépot !');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/depot/edit/:depotId', Link::POST, ['[0-9]+'], '/cmw-admin/shop')]
    private function shopShippingEditDepot(int $depotId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.edit');

        [$name, $distance, $address , $city, $postalCode, $country] = Utils::filterInput(  'name', 'distance', 'address', 'city', 'postalCode', 'country');

        if (ShopShippingWithdrawPointModel::getInstance()->editWithdrawPoint($depotId, $name, $distance, $address, $city, $postalCode, $country)) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Dépôts modifié !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible de modifier le dépot !');
        }
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/depot/delete/:depotId', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function shopShippingDeleteDepot(int $depotId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.delete');

        $shippingMethods = ShopShippingModel::getInstance()->getShopShippings();

        foreach ($shippingMethods as $method) {
            if ($method->getWithdrawPoint()?->getId() === $depotId) {
                Flash::send(Alert::WARNING, 'Boutique', 'Ce dépôt est lié à des méthodes de retrait, vous ne pouvez pas supprimer pour le moment !');
                Redirect::redirectPreviousRoute();
            }
        }

        ShopShippingWithdrawPointModel::getInstance()->deleteWithdrawPoint($depotId);
        Flash::send(Alert::SUCCESS, 'Boutique', 'Dépôt supprimé !');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/delivery', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingAddDelivery(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.add');

        [$name, $price, $zoneId, $methodVarName, $weight, $minPrice, $maxPrice] = Utils::filterInput(  'shipping_name', 'shipping_price', 'shipping_zone', 'shipping_method', 'shipping_weight', 'shipping_min_price', 'shipping_max_price');

        if (ShopShippingModel::getInstance()->createShipping($name, ($price === '' ? 0 : $price), $zoneId, 0, null, $methodVarName, ($weight === '' ? null : $weight), ($minPrice === '' ? null : $minPrice), ($maxPrice === '' ? null : $maxPrice))) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de livraison ajouté !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible d\'ajouter la méthode de livraison !');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/delivery/edit/:shippingId', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingEditDelivery(int $shippingId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.edit');

        [$name, $price, $zoneId, $methodVarName, $weight, $minPrice, $maxPrice] = Utils::filterInput(  'shipping_name', 'shipping_price', 'shipping_zone', 'shipping_method', 'shipping_weight', 'shipping_min_price', 'shipping_max_price');

        $commands = ShopHistoryOrdersModel::getInstance()->getInProgressOrders();
        foreach ($commands as $command) {
            if ($command->getShippingMethod()?->getShipping()->getId() === $shippingId) {
                Flash::send(Alert::WARNING, 'Boutique', 'Cette méthode est lié à une commande en cours vous ne pouvez pas la modifier pour le moment !');
                Redirect::redirectPreviousRoute();
            }
        }

        if (ShopShippingModel::getInstance()->editShipping($shippingId, $name, ($price === '' ? 0 : $price), $zoneId, 0, null, $methodVarName, ($weight === '' ? null : $weight), ($minPrice === '' ? null : $minPrice), ($maxPrice === '' ? null : $maxPrice))) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de livraison mise à jour !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible de mettre à jour la méthode de livraison !');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/withdraw', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingAddWithdraw(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.add');

        [$name, $price, $depotId, $zoneId, $methodVarName, $weight, $minPrice, $maxPrice] = Utils::filterInput(  'withdraw_name', 'withdraw_price', 'withdraw_depot', 'withdraw_zone', 'withdraw_method', 'withdraw_weight', 'withdraw_min_price', 'withdraw_max_price');

        if (ShopShippingModel::getInstance()->createShipping($name, ($price === '' ? 0 : $price), $zoneId, 1, $depotId, $methodVarName, ($weight === '' ? null : $weight), ($minPrice === '' ? null : $minPrice), ($maxPrice === '' ? null : $maxPrice))) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de retrait ajouté !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible d\'ajouter la méthode de retrait !');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/withdraw/edit/:shippingId', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingEditWithdraw(int $shippingId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.edit');

        [$name, $price, $depotId, $zoneId, $methodVarName, $weight, $minPrice, $maxPrice] = Utils::filterInput(  'withdraw_name', 'withdraw_price', 'withdraw_depot', 'withdraw_zone', 'withdraw_method', 'withdraw_weight', 'withdraw_min_price', 'withdraw_max_price');

        $commands = ShopHistoryOrdersModel::getInstance()->getInProgressOrders();
        foreach ($commands as $command) {
            if ($command->getShippingMethod()?->getShipping()->getId() === $shippingId) {
                Flash::send(Alert::WARNING, 'Boutique', 'Cette méthode est lié à une commande en cours vous ne pouvez pas la modifier pour le moment !');
                Redirect::redirectPreviousRoute();
            }
        }

        if (ShopShippingModel::getInstance()->editShipping($shippingId, $name, ($price === '' ? 0 : $price), $zoneId, 1, $depotId, $methodVarName, ($weight === '' ? null : $weight), ($minPrice === '' ? null : $minPrice), ($maxPrice === '' ? null : $maxPrice))) {
            Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode de livraison mise à jour !');
        } else {
            Flash::send(Alert::WARNING, 'Boutique', 'Impossible de mettre à jour la méthode de livraison !');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/method/delete/:shippingId', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function shopShippingDeleteShipping(int $shippingId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.delete');

        $commands = ShopHistoryOrdersModel::getInstance()->getInProgressOrders();
        foreach ($commands as $command) {
            if ($command->getShippingMethod()?->getShipping()->getId() === $shippingId) {
                Flash::send(Alert::WARNING, 'Boutique', 'Cette méthode est lié à une commande en cours vous ne pouvez pas la supprimer pour le moment !');
                Redirect::redirectPreviousRoute();
            }
        }
        ShopShippingModel::getInstance()->deleteShipping($shippingId);
        Flash::send(Alert::SUCCESS, 'Boutique', 'Méthode supprimé !');
        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/shipping/method', Link::POST, [], '/cmw-admin/shop')]
    private function shopShippingGlobalSettingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.shipping.add');

        $settings = $_POST;

        foreach ($settings as $key => $value) {
            if ($key === 'security-csrf-token' || $key === 'honeyInput') {
                continue;
            }
            $key = FilterManager::filterData($key, 50);
            $value = FilterManager::filterData($value, 255);

            if (!ShopShippingRequirementModel::getInstance()->updateOrInsertSetting($key, $value)) {
                Flash::send(Alert::ERROR, 'Erreur', "Impossible de mettre à jour le paramètre $key");
            }
        }

        Flash::send(Alert::SUCCESS, 'Succès', 'Les paramètres ont été mis à jour');
        Redirect::redirectPreviousRoute();
    }

    /**
     * @return \CMW\Interface\Shop\IShippingMethod[]
     */
    public function getShippingMethods(): array
    {
        return Loader::loadImplementations(IShippingMethod::class);
    }

    /**
     * @return \CMW\Interface\Shop\IShippingMethod[]
     */
    public function getGlobalVarShippingMethods(): array
    {
        $allShippingMethods = Loader::loadImplementations(IShippingMethod::class);
        return array_filter($allShippingMethods, static function ($shippingMethods) {
            return $shippingMethods->useGlobalConfigWidgetsInShopDeliveryConfig();
        });
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IShippingMethod|null
     */
    public function getShippingMethodsByVarName(string $varName): ?IShippingMethod
    {
        foreach ($this->getShippingMethods() as $shippingMethod) {
            if ($shippingMethod->varName() === $varName) {
                return $shippingMethod;
            }
        }
        return null;
    }
}
