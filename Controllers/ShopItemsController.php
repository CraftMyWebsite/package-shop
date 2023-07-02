<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
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
        $items = ShopItemsModel::getInstance();

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

    #[Link("/items/add_item", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddShopItemPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $category, $description, $type, $stock, $price, $globalLimit, $userLimit] = Utils::filterInput("shop_item_name", "shop_category_id", "shop_item_description", "shop_item_type", "shop_item_default_stock", "shop_item_price", "shop_item_global_limit", "shop_item_user_limit");

        ShopItemsModel::getInstance()->createShopItem($name, $category, $description, $type, ($stock === "" ? null : $stock) , ($price === "" ? 0 : $price), ($globalLimit === "" ? null : $globalLimit), ($userLimit === "" ? null : $userLimit));

        Flash::send(Alert::SUCCESS,"Success","Items ajouté !");

        Redirect::redirectPreviousRoute();
    }

    #[Link("/items/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/shop")]
    public function adminDeleteShopItem(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        ShopItemsModel::getInstance()->deleteShopItem($id);

        Flash::send(Alert::SUCCESS, "Success", "C'est chao");

        Redirect::redirectPreviousRoute();
    }
}