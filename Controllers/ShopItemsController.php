<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Event\Shop\ShopAddItemEvent;
use CMW\Event\Shop\ShopDeleteCatEvent;
use CMW\Event\Shop\ShopDeleteItemEvent;
use CMW\Manager\Events\Emitter;
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

        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();

        View::createAdminView('Shop', 'Items/manage')
            ->addVariableList(["items" => $items, "imagesItem" => $imagesItem])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/items/archived", Link::GET, [], "/cmw-admin/shop")]
    public function shopItemsArchived(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();

        View::createAdminView('Shop', 'Items/archived')
            ->addVariableList(["items" => $items, "imagesItem" => $imagesItem])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/items/cat/:catId", Link::GET, ['.*?'], "/cmw-admin/shop")]
    public function shopItemsByCat(Request $request, string $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $thisCat = ShopCategoriesModel::getInstance()->getShopCategoryById($catId);
        $items = ShopItemsModel::getInstance()->getShopItemByCat($catId);
        $imagesItem = ShopImagesModel::getInstance();

        View::createAdminView('Shop', 'Items/filterCat')
            ->addVariableList(["items" => $items, "imagesItem" => $imagesItem, "thisCat" => $thisCat])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    #[Link("/items/add", Link::GET, [], "/cmw-admin/shop")]
    public function adminAddShopItem(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        if (ShopCategoriesModel::getInstance()->getShopCategories()) {
            $categoryModel = ShopCategoriesModel::getInstance();

            View::createAdminView('Shop', 'Items/add')
                ->addVariableList(["categoryModel" => $categoryModel])
                ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js",
                    "Admin/Resources/Vendors/Tinymce/Config/full.js"
                )
                ->view();
        } else {
            Redirect::redirect("cmw-admin/shop/cat");
        }
    }

    #[Link("/items/add", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddShopItemPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $shortDesc, $category, $description, $type, $stock, $price, $globalLimit, $userLimit] = Utils::filterInput("shop_item_name", "shop_item_short_desc", "shop_category_id", "shop_item_description", "shop_item_type", "shop_item_default_stock", "shop_item_price", "shop_item_global_limit", "shop_item_user_limit");

        $itemId = ShopItemsModel::getInstance()->createShopItem($name, $shortDesc, $category, $description, $type, ($stock === "" ? null : $stock) , ($price === "" ? 0 : $price), ($globalLimit === "" ? null : $globalLimit), ($userLimit === "" ? null : $userLimit));


        [$numberOfImage] = Utils::filterInput("numberOfImage");
        if ($numberOfImage !== "") {
            for ($i = 0; $i < $numberOfImage; $i++) {
                $imageKey = 'image-' . $i;

                if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                    $image = $_FILES[$imageKey];
                    ShopImagesModel::getInstance()->addShopItemImage($image, $itemId);
                }
            }
        }

        Flash::send(Alert::SUCCESS,"Success","Items ajouté !");

        Emitter::send(ShopAddItemEvent::class, $itemId);

        Redirect::redirect("cmw-admin/shop/items");
    }

    #[Link("/items/edit/:id", Link::GET, [], "/cmw-admin/shop")]
    public function adminEditShopItem(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $categoryModel = ShopCategoriesModel::getInstance();
        $item = ShopItemsModel::getInstance()->getShopItemsById($id);
        $imagesItem = ShopImagesModel::getInstance()->getShopImagesByItem($id);

        View::createAdminView('Shop', 'Items/edit')
            ->addVariableList(["categoryModel" => $categoryModel, "item" => $item, "imagesItem" => $imagesItem])
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js",
                "Admin/Resources/Vendors/Tinymce/Config/full.js"
            )
            ->view();
    }

    #[Link("/items/delete/:id", Link::GET, ['[0-9]+'], "/cmw-admin/shop")]
    public function adminDeleteShopItem(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        ShopItemsModel::getInstance()->deleteShopItem($id);

        Flash::send(Alert::SUCCESS, "Success", "C'est chao");

        Emitter::send(ShopDeleteItemEvent::class, $id);

        Redirect::redirectPreviousRoute();
    }
}