<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;


/**
 * Class: @ShopPaymentsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopPaymentsController extends AbstractController
{
    #[Link("/payments", Link::GET, [], "/cmw-admin/shop")]
    public function shopPayments(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'payments')
            ->addVariableList([])
            ->view();
    }
}
