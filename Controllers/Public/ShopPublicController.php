<?php


namespace CMW\Controller\Shop\Public;


use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;


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
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View("Shop", "Main/main");
        $view->addVariableList(["categories" => $categories, "items" => $items, "imagesItem" =>
            $imagesItem,"defaultImage" => $defaultImage, "itemInCart" => $itemInCart]);
        $view->view();
    }

    #[Link("/cat/:catSlug", Link::GET, ['.*?'], "/shop")]
    public function publicCatView(Request $request, string $catSlug): void
    {
        $categories = ShopCategoriesModel::getInstance()->getShopCategories();
        $thisCat = ShopCategoriesModel::getInstance()->getShopCategoryById(ShopCategoriesModel::getInstance()->getShopCategoryIdBySlug($catSlug));
        $items = ShopItemsModel::getInstance()->getShopItemByCatSlug($catSlug);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View("Shop", "Main/cat");
        $view->addVariableList(["items" => $items, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "itemInCart" => $itemInCart, "thisCat" => $thisCat, "categories" => $categories]);
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
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());
        $itemVariants = ShopItemVariantModel::getInstance()->getShopItemVariantByItemId($itemId);
        $variantValuesModel = ShopItemVariantValueModel::getInstance();
        $physicalInfo = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($itemId);
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View("Shop", "Main/item");
        $view->addVariableList(["otherItemsInThisCat" => $otherItemsInThisCat, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "parentCat" => $parentCat, "item" => $item, "itemInCart" => $itemInCart, "itemVariants" => $itemVariants, "variantValuesModel" => $variantValuesModel, "physicalInfo" => $physicalInfo ?? null]);
        $view->view();
    }

    #[Link("/settings", Link::GET, [], "/shop")]
    public function publicSettingsView(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue("PATH_SUBFOLDER")."login");
        }
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersModel::getCurrentUser()?->getId(), session_id());

        $view = new View("Shop", "Users/settings");
        $view->addVariableList(["itemInCart" => $itemInCart]);
        $view->view();
    }
}

