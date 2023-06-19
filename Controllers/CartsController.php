<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;


/**
 * Class: @CartsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class CartsController extends AbstractController
{
    #[Link("/carts", Link::GET, [], "/cmw-admin/shop")]
    public function shopCarts(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'carts')
            ->addVariableList([])
            ->view();
    }
}