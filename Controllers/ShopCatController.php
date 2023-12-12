<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Event\Shop\ShopAddCatEvent;
use CMW\Event\Shop\ShopDeleteCatEvent;
use CMW\Event\Shop\ShopEditCatEvent;
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
class ShopCatController extends AbstractController
{

    #[Link("/cat", Link::GET, [], "/cmw-admin/shop")]
    public function adminShopCat(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $categoryModel = ShopCategoriesModel::getInstance();

        View::createAdminView('Shop', 'Cat/manage')
            ->addVariableList(["categoryModel" => $categoryModel])
            ->view();
    }

    #[Link("/cat/add", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddShopCategoryPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $description, $icon] = Utils::filterInput("name", "description", "icon");

        $cat = ShopCategoriesModel::getInstance()->createShopCategory($name, $description, $icon);
        $catId = $cat->getId();

        Emitter::send(ShopAddCatEvent::class, $catId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cat/addSubCat/:catId", Link::GET, [], "/cmw-admin/shop")]
    public function adminAddSubCat(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $category = ShopCategoriesModel::getInstance()->getShopCategoryById($catId);

        View::createAdminView("Shop", "Cat/addSubCat")
            ->addVariableList(["category" => $category])
            ->view();
    }

    #[Link("/cat/addSubCat/:catId", Link::POST, [], "/cmw-admin/shop")]
    public function adminAddSubCatPost(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $description, $icon] = Utils::filterInput("name", "description", "icon");

        ShopCategoriesModel::getInstance()->createShopSubCategory($name, $description, $icon, $catId);

        Emitter::send(ShopAddCatEvent::class, $catId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cat/edit/:catId", Link::GET, [], "/cmw-admin/shop")]
    public function adminEditCat(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        $category = ShopCategoriesModel::getInstance()->getShopCategoryById($catId);
        $categoryModel = ShopCategoriesModel::getInstance();

        View::createAdminView("Shop", "Cat/edit")
            ->addVariableList(["categoryModel" => $categoryModel, "category" => $category])
            ->view();
    }

    #[Link("/cat/edit/:catId", Link::POST, [], "/cmw-admin/shop")]
    public function adminEditCatPost(Request $request, string $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        [$name, $description, $icon, $move] = Utils::filterInput("name", "description", "icon", "move");

        $move = ($move === $catId) ? (is_null(ShopCategoriesModel::getInstance()->getShopCategoryById($catId)->getParent()) ? null : ShopCategoriesModel::getInstance()->getShopCategoryById($catId)->getParent()->getId()) : (($move === "") ? null : $move);

        ShopCategoriesModel::getInstance()->editCategory($name, $description, $icon, $move, $catId);

        Emitter::send(ShopEditCatEvent::class, $catId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cat/delete/:catId", Link::GET, ['[0-9]+'], "/cmw-admin/shop")]
    public function adminDeleteShopCat(Request $request, int $catId): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.items");

        ShopCategoriesModel::getInstance()->deleteShopCat($catId);

        Emitter::send(ShopDeleteCatEvent::class, $catId);

        Flash::send(Alert::SUCCESS, "Success", "C'est chao");

        Redirect::redirectPreviousRoute();
    }
}