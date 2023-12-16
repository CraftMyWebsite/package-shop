<?php


namespace CMW\Controller\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Event\Users\LoginEvent;
use CMW\Manager\Events\Listener;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Shop\ShopCategoriesModel;
use CMW\Model\Shop\ShopImagesModel;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @ShopPublicController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopPublicController extends CoreController
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

    #[Link("/cart", Link::GET, [], "/shop")]
    public function publicCartView(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        if (ShopCartsModel::getInstance()->cartItemIdAsNullValue($userId, $sessionId)) {
            ShopCartsModel::getInstance()->removeUnreachableItem($userId, $sessionId);
            Flash::send(Alert::ERROR, "Boutique", "Certain article du panier n'existe plus. et nous ne somme malheureusement pas en mesure de le récupérer.");
        }

        $cartContent = ShopCartsModel::getInstance()->getShopCartsByUserId($userId, session_id());
        $asideCartContent = ShopCartsModel::getInstance()->getShopCartsAsideByUserId($userId, session_id());
        $imagesItem = ShopImagesModel::getInstance();

        $view = new View("Shop", "cart");
        $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem, "asideCartContent" => $asideCartContent]);
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

    /*
     * ACTIONS
     * */
    #[NoReturn] #[Link("/add_to_cart/:itemId", Link::GET, [], "/shop")]
    public function publicAddCart(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        if (ShopItemsModel::getInstance()->itemStillExist($itemId) || ShopItemsModel::getInstance()->isArchivedItem($itemId)) {
            Flash::send(Alert::ERROR, "Boutique", "Nous somme désolé mais l'article que vous essayez d'ajouter au panier n'existe plus.");
            Redirect::redirectPreviousRoute();
        }

        if (ShopItemsModel::getInstance()->itemNotInStock($itemId)) {
            if (ShopCartsModel::getInstance()->isAlreadyAside($itemId, $userId, $sessionId)) {
                Flash::send(Alert::ERROR, "Boutique", "Cet article est déjà dans le panier 'Mise de côté', les stock ne sont pas mis à jour.");
                Redirect::redirectPreviousRoute();
            } else {
                ShopCartsModel::getInstance()->addToAsideCart($itemId, $userId, $sessionId);
                Flash::send(Alert::SUCCESS, "Boutique", "Cet article n'est plus en stock. Mais nous l'avons ajouté au panier 'Mise de côté'.");
                Redirect::redirectPreviousRoute();
            }
        }

        if (ShopCartsModel::getInstance()->isAlreadyAside($itemId, $userId, $sessionId)) {
            ShopCartsModel::getInstance()->switchAsideToCart($itemId, $userId, $sessionId);
            Flash::send(Alert::SUCCESS, "Boutique", "Cet article est dans le panier 'Mise de côté' nous le déplaçons dans le panier principal.");
            Redirect::redirectPreviousRoute();
        }

        if (!$sessionId) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        if (ShopCartsModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
            ShopCartsModel::getInstance()->addToCart($itemId, $userId, $sessionId);
            Flash::send(Alert::SUCCESS, "Boutique",
                "Nouvel article ajouté au panier !");
        } else {
            ShopCartsModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);
            Flash::send(Alert::SUCCESS, "Boutique",
                "Vous aviez déjà cet article, nous avons rajouté une quantité pour vous");
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cat/:catSlug/item/:itemSlug", Link::POST, ['.*?'], "/shop")]
    public function publicAddCartQuantity(Request $request, string $catSlug, string $itemSlug): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        if (!$sessionId) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        $itemId = ShopItemsModel::getInstance()->getShopItemIdBySlug($itemSlug);
        //TODO : Verifier la quantité max qu'il peut mettre
        [$quantity] = Utils::filterInput('quantity');

        if (ShopCartsModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
            ShopCartsModel::getInstance()->addToCartWithQuantity($itemId, $quantity);
            Flash::send(Alert::SUCCESS, "Boutique", "Nouvel article ajouté au panier !");
        } else {
            Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà cet article dans le panier !");
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/increase_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicAddQuantity(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        if (!$sessionId) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        ShopCartsModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/decrease_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveQuantity(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        $currentQuantity = ShopCartsModel::getInstance()->getQuantity($itemId, $userId, $sessionId);

        if ($currentQuantity === 1) {
            ShopCartsModel::getInstance()->removeItem($itemId, $userId, $sessionId);
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                "Article " . ShopItemsModel::getInstance()->getShopItemsById($itemId)?->getName() . " enlevé de votre panier");
        }

        if ($currentQuantity <= 0) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                "Hep hep hep, pas de nombres négatifs mon chère");
            Redirect::redirectPreviousRoute();
        }

        if (!$sessionId) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        ShopCartsModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, false);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/remove/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveItem(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        ShopCartsModel::getInstance()->removeItem($itemId, $userId, $sessionId);

        Flash::send(Alert::SUCCESS, "Boutique", "Cet article n'est plus dans votre panier");

        Redirect::redirectPreviousRoute();
    }

    #[Listener(eventName: LoginEvent::class, times: 0, weight: 1)]
    public static function onLogin(mixed $userId): void
    {
        //Migrate cart
        $sessionId = session_id();
        if ($sessionId) {
            ShopCartsModel::getInstance()->switchSessionToUserCart($sessionId, $userId);
        }
    }
}

