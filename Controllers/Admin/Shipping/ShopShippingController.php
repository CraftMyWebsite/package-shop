<?php

namespace CMW\Controller\Shop\Admin\Shipping;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Shop\IShippingMethod;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;


/**
 * Class: @ShopShippingController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopShippingController extends AbstractController
{
    #[Link("/shipping", Link::GET, [], "/cmw-admin/shop")]
    public function shopDiscounts(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'Shipping/main')
            ->addVariableList([])
            ->view();
    }

    /**
     * @return \CMW\Interface\Shop\IShippingMethod[]
     */
    public function getShippingMethods(): array
    {
        return Loader::loadImplementations(IShippingMethod::class);
    }

    /**
     * @param string $varName
     * @return \CMW\Interface\Shop\IShippingMethod|null
     */
    public function getShippingMethodsByVarName(string $varName): ?IShippingMethod
    {
        foreach ($this->getShippingMethods() as $shippingMethod) {
            if ($shippingMethod->varName() === $varName){
                return $shippingMethod;
            }
        }
        return null;
    }
}
