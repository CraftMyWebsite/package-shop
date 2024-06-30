<?php

namespace CMW\Controller\Shop\Admin\Cart;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;


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

        $cartModel = ShopCartModel::getInstance();
        $cartItemsModel = ShopCartItemModel::getInstance();

        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");
        View::createAdminView('Shop', 'Carts/carts')
            ->addVariableList(["cartModel" => $cartModel, "cartItemsModel" => $cartItemsModel])
            ->view();
    }

    #[Link("/carts/user/:userId", Link::GET, [], "/cmw-admin/shop")]
    public function shopCartUser(Request $request, int $userId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");

        $carts = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, "");
        $user = UsersModel::getInstance()->getUserById($userId);

        View::createAdminView('Shop', 'Carts/userCart')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["carts" => $carts, "user" => $user])
            ->view();
    }

    #[Link("/carts/session/:sessionId", Link::GET, [], "/cmw-admin/shop")]
    public function shopCartSession(Request $request, string $sessionId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.carts");

        $carts = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId(null, $sessionId);

        View::createAdminView('Shop', 'Carts/sessionCart')
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->addVariableList(["carts" => $carts, "sessionId" => $sessionId])
            ->view();
    }

    #[NoReturn] #[Link("/carts/session/delete/:sessionId", Link::GET, ['[0-9]+'], "/cmw-admin/shop")]
    public function adminDeleteShopSessionCart(Request $request, string $sessionId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        ShopCartModel::getInstance()->removeSessionCart($sessionId);

        Flash::send(Alert::SUCCESS, "Success", "Panier supprimé");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/carts/session/delete/all/sessions", Link::GET, ['[0-9]+'], "/cmw-admin/shop")]
    public function adminDeleteAllShopSessionCart(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $cartSessions = ShopCartModel::getInstance()->getShopCartsForSessions();

        foreach ($cartSessions as $cartSession) {
            ShopCartModel::getInstance()->removeSessionCart($cartSession->getSession());
        }

        Flash::send(Alert::SUCCESS, "Success", "Tous les paniers de sessions sont nettoyé !");

        Redirect::redirectPreviousRoute();
    }
}