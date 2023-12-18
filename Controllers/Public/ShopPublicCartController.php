<?php


namespace CMW\Controller\Shop\Public;

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
use CMW\Model\Shop\ShopImagesModel;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @ShopPublicCartController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopPublicCartController extends CoreController
{
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

        if (ShopItemsModel::getInstance()->itemHaveUserLimit($itemId)) {
            Flash::send(Alert::SUCCESS, "Boutique", "Cet article est limité à ". ShopItemsModel::getInstance()->getItemUserLimit($itemId). " achat par utilisateur.");
            if (is_null($userId)) {
                Flash::send(Alert::ERROR, "Boutique", ShopItemsModel::getInstance()->getShopItemsById($itemId)->getName() ." à besoin d'une vérification supplémentaire pour être ajouté au panier.");
                Redirect::redirect("login");
            }
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

