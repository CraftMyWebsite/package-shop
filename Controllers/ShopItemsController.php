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
use CMW\Model\Shop\ShopImagesModel;
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
        $imagesItem = ShopImagesModel::getInstance();

        View::createAdminView('Shop', 'Items/manage')
            ->addVariableList(["categories" => $categories, "items" => $items, "imagesItem" => $imagesItem])
            ->view();
    }

    #[Link("/items/add_item/:categoryId", Link::GET, [], "/cmw-admin/shop")]
    public function adminAddShopItem(Request $request, int $categoryId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $category = ShopCategoriesModel::getInstance()->getShopCategoryById($categoryId);

        View::createAdminView('Shop', 'Items/add')
            ->addVariableList(["category" => $category])
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js",
                "Admin/Resources/Vendors/Tinymce/Config/full.js"
            )
            ->view();
    }

    #[Link("/items/add_item/:categoryId", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddShopItemPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $category, $description, $type, $stock, $price, $globalLimit, $userLimit] = Utils::filterInput("shop_item_name", "shop_category_id", "shop_item_description", "shop_item_type", "shop_item_default_stock", "shop_item_price", "shop_item_global_limit", "shop_item_user_limit");

        $itemId = ShopItemsModel::getInstance()->createShopItem($name, $category, $description, $type, ($stock === "" ? null : $stock) , ($price === "" ? 0 : $price), ($globalLimit === "" ? null : $globalLimit), ($userLimit === "" ? null : $userLimit));

        [$numberOfImage] = Utils::filterInput("numberOfImage");
        if ($numberOfImage !== "")
        {
            $i=0;
            while ($numberOfImage !== 0 ) {
                $image = $_FILES['image-'.$i];
                if ($image != null) {
                    ShopImagesModel::getInstance()->addShopItemImage($image, $itemId);
                }
                $i++;
                $numberOfImage--;
            }
        }

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

    /*
     * METHODE
     * */

    public function isArchived(int $itemId): bool {

    }

    public function inStock(int $itemId): bool {

    }

    public function addToWishList(int $itemId): bool {

    }

    public function isUserBuyLimitReached(int $userId, int $itemId): bool {

    }

    public function isUserOrderLimitReached(int $userId, int $itemId): bool {

    }

    public function isUserAlreadyHaveBuyItem(int $userId, int $itemId): bool {

    }

    public function isUserAlreadyHaveWishItem(int $userId, int $itemId): bool {

    }

    public function isGlobalLimitReached(int $itemId): bool {

    }
}