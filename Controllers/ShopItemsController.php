<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCategoriesModel;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;


/**
 * Class: @ShopItemsController
 * @desc this controller manages: categories, items requirement, items actions, items tags
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopItemsController extends AbstractController
{
    #[Link("/items", Link::GET, [], "/cmw-admin/shop")]
    public function shopItems(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $categories = ShopCategoriesModel::getInstance()->getShopCategories();
        $items = ShopItemsModel::getInstance()->getShopItems();

        View::createAdminView('Shop', 'items')
            ->addVariableList(["categories" => $categories, "items" => $items])
            ->view();
    }

    #[Link("/items/add_cat", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddShopCategoryPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $description] = Utils::filterInput("name", "description");

        ShopCategoriesModel::getInstance()->createShopCategory($name, $description);

        Redirect::redirectPreviousRoute();
    }
}