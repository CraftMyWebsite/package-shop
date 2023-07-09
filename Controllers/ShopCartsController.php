<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopCartsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopCartsController extends AbstractController
{
    #[Link("/carts", Link::GET, [], "/cmw-admin/shop")]
    public function shopCarts(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'Carts/carts')
            ->view();
    }

    #[Link("/carts/:userId", Link::GET, [], "/cmw-admin/shop")]
    public function shopCartUser(Request $request, int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");

        $carts = ShopCartsModel::getInstance()->getShopCartsByUserId($userId);
        $user = UsersModel::getInstance()->getUserById($userId);

        View::createAdminView('Shop', 'Carts/userCart')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["carts" => $carts, "user" => $user])
            ->view();
    }
}