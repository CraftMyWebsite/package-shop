<?php


namespace CMW\Controller\Shop\Public;


use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Shop\ShopCategoriesModel;
use CMW\Model\Shop\ShopImagesModel;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Model\Users\UsersModel;


/**
 * Class: @ShopPublicController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopPublicController extends AbstractController
{
    #[Link("/", Link::GET, [], "/shop")]
    public function publicBaseView(): void
    {
        $categories = ShopCategoriesModel::getInstance()->getShopCategories();
        $items = ShopItemsModel::getInstance();
        $imagesItem = ShopImagesModel::getInstance();

        $sessionId = session_id();

        $itemInCart = ShopCartsModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), $sessionId) ?? 0;

        $view = new View("Shop", "main");
        $view->addVariableList(["categories" => $categories, "items" => $items, "imagesItem" =>
            $imagesItem, "itemInCart" => $itemInCart]);
        $view->view();
    }

    #[Link("/cat/:catSlug", Link::GET, ['.*?'], "/shop")]
    public function publicCatView(Request $request, string $catSlug): void
    {
        $categories = ShopCategoriesModel::getInstance()->getShopCategories();
        $thisCat = ShopCategoriesModel::getInstance()->getShopCategoryById(ShopCategoriesModel::getInstance()->getShopCategoryIdBySlug($catSlug));
        $items = ShopItemsModel::getInstance()->getShopItemByCatSlug($catSlug);
        $imagesItem = ShopImagesModel::getInstance();
        $itemInCart = ShopCartsModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $view = new View("Shop", "cat");
        $view->addVariableList(["items" => $items, "imagesItem" => $imagesItem, "itemInCart" => $itemInCart, "thisCat" => $thisCat, "categories" => $categories]);
        $view->view();
    }

    #[Link("/cat/:catSlug/item/:itemSlug", Link::GET, ['.*?'], "/shop")]
    public function publicItemView(Request $request, string $catSlug, string $itemSlug): void
    {
        $otherItemsInThisCat = ShopItemsModel::getInstance()->getShopItemByCatSlug($catSlug);
        $parentCat = ShopCategoriesModel::getInstance()->getShopCategoryById(ShopCategoriesModel::getInstance()->getShopCategoryIdBySlug($catSlug));
        $itemId = ShopItemsModel::getInstance()->getShopItemIdBySlug($itemSlug);
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);
        $imagesItem = ShopImagesModel::getInstance();
        $itemInCart = ShopCartsModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $view = new View("Shop", "item");
        $view->addVariableList(["otherItemsInThisCat" => $otherItemsInThisCat, "imagesItem" => $imagesItem, "parentCat" => $parentCat, "item" => $item, "itemInCart" => $itemInCart]);
        $view->view();
    }

    #[Link("/history", Link::GET, [], "/shop")]
    public function publicHistoryView(): void
    {
        $itemInCart = ShopCartsModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $view = new View("Shop", "history");
        $view->addVariableList(["itemInCart" => $itemInCart]);
        $view->view();
    }

    #[Link("/settings", Link::GET, [], "/shop")]
    public function publicSettingsView(): void
    {
        $itemInCart = ShopCartsModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $view = new View("Shop", "settings");
        $view->addVariableList(["itemInCart" => $itemInCart]);
        $view->view();
    }
}

