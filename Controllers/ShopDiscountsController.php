<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;


/**
 * Class: @ShopDiscountsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopDiscountsController extends AbstractController
{
    #[Link("/discounts", Link::GET, [], "/cmw-admin/shop")]
    public function shopDiscounts(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'discounts')
            ->addVariableList([])
            ->view();
    }
}
