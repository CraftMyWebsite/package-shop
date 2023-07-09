<?php


namespace CMW\Controller\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopCartsModel;
use CMW\Model\Shop\ShopCategoriesModel;
use CMW\Model\Shop\ShopImagesModel;
use CMW\Model\Shop\ShopItemsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;


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

        $view = new View("Shop", "main");
        $view->addVariableList(["categories" => $categories, "items" => $items, "imagesItem" => $imagesItem]);
        $view->view();
    }

    #[Link("/cat/:catSlug", Link::GET, ['.*?'], "/shop")]
    public function publicCatView(Request $request, string $catSlug): void
    {
        $items = ShopItemsModel::getInstance()->getShopItemByCatSlug($catSlug);

        $view = new View("Shop", "cat");
        $view->addVariableList(["items" => $items]);
        $view->view();
    }

    #[Link("/cat/:catSlug/item/:itemSlug", Link::GET, ['.*?'], "/shop")]
    public function publicItemView(Request $request, string $catSlug, string $itemSlug): void
    {
        $otherItemsInThisCat = ShopItemsModel::getInstance()->getShopItemByCatSlug($catSlug);
        $itemId = ShopItemsModel::getInstance()->getShopItemIdBySlug($itemSlug);
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);

        $view = new View("Shop", "item");
        $view->addVariableList(["otherItemsInThisCat" => $otherItemsInThisCat, "item" => $item]);
        $view->view();
    }

    #[Link("/cart", Link::GET, [], "/shop")]
    public function publicCartView(): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de consulter votre panier");
            Redirect::redirect('login');
        }

        $cartContent = ShopCartsModel::getInstance()->getShopCartsByUserId(UsersModel::getCurrentUser()->getId());

        $view = new View("Shop", "cart");
        $view->addVariableList(["cartContent" => $cartContent]);
        $view->view();
    }

    #[Link("/history", Link::GET, [], "/shop")]
    public function publicHistoryView(): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de consulter votre historique d'achat");
            Redirect::redirect('login');
        }

        $view = new View("Shop", "history");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/settings", Link::GET, [], "/shop")]
    public function publicSettingsView(): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de modifier vos paramètres boutique");
            Redirect::redirect('login');
        }

        $view = new View("Shop", "settings");
        $view->addVariableList([]);
        $view->view();
    }

    /*
     * ACTIONS
     * */
    #[Link("/add_to_cart/:itemId", Link::GET, [], "/shop")]
    public function publicAddCart(Request $request, int $itemId): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant d'ajouter cet article dans votre panier'");
            Redirect::redirect('login');
        }

        if (ShopCartsModel::getInstance()->itemIsInCart($itemId)) {
            ShopCartsModel::getInstance()->addToCart($itemId);
            Flash::send(Alert::SUCCESS,"Boutique","Nouvel article ajouté au panier !");
        } else {
            //TODO : Verif setting boutique : La quantité d'article s'ajoute tout seul ou l'utilisateur reçois simplement une alerte ?
            ShopCartsModel::getInstance()->increaseQuantity($itemId);
            Flash::send(Alert::SUCCESS,"Boutique","Vous aviez déjà cet article, nous avons rajouté une quantité pour vous");
        }

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cart/increase_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicAddQuantity(Request $request, int $itemId): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de modifier la quantité d'article");
            Redirect::redirect('login');
        }

        //TODO : getItemUserLimit for stop increase and alert user (need to know if user has already purchased getItemUserLimit value can be change before this)
        ShopCartsModel::getInstance()->increaseQuantity($itemId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cart/decrease_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveQuantity(Request $request, int $itemId): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de modifier la quantité d'article");
            Redirect::redirect('login');
        }

        //TODO : getItemUserLimit for stop increase and alert user (need to know if user has already purchased getItemUserLimit value can be change before this)
        ShopCartsModel::getInstance()->decreaseQuantity($itemId);

        Redirect::redirectPreviousRoute();
    }

    #[Link("/cart/remove/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveItem(Request $request, int $itemId): void
    {
        if (!UsersController::isUserLogged()){
            Flash::send(Alert::ERROR,"Boutique","Connectez-vous avant de supprimé l'articel dans le panier");
            Redirect::redirect('login');
        }

        ShopCartsModel::getInstance()->removeItem($itemId);

        Flash::send(Alert::SUCCESS,"Boutique","Cet article n'est plus dans votre panier");

        Redirect::redirectPreviousRoute();
    }
}

