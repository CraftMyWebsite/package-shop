<?php


namespace CMW\Controller\Shop;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
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
        $view = new View("Shop", "main");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/cat/:catSlug", Link::GET, ['.*?'], "/shop")]
    public function publicCatView(Request $request, string $catSlug): void
    {
        $view = new View("Shop", "cat");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/cat/:catSlug/item/:itemSlug", Link::GET, ['.*?'], "/shop")]
    public function publicItemView(Request $request, string $itemSlug): void
    {
        $view = new View("Shop", "item");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/cart/:userId", Link::GET, [], "/shop")]
    public function publicCartView(Request $request, int $userId): void
    {

        if (UsersModel::getCurrentUser()->getId() !== $userId){
            Flash::send(Alert::ERROR,"Erreur","Vous ne pouvez pas consulter ce panier !");
            Redirect::redirect('shop');
        }

        $view = new View("Shop", "cart");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/history/:userId", Link::GET, [], "/shop")]
    public function publicHistoryView(Request $request, int $userId): void
    {

        if (UsersModel::getCurrentUser()->getId() !== $userId){
            Flash::send(Alert::ERROR,"Erreur","Vous ne pouvez pas consulter cette historique !");
            Redirect::redirect('shop');
        }

        $view = new View("Shop", "history");
        $view->addVariableList([]);
        $view->view();
    }

    #[Link("/settings/:userId", Link::GET, [], "/shop")]
    public function publicSettingsView(Request $request, int $userId): void
    {

        if (UsersModel::getCurrentUser()->getId() !== $userId){
            Flash::send(Alert::ERROR,"Erreur","Vous ne pouvez pas modifier les paramètres de cet utilisateur");
            Redirect::redirect('shop');
        }

        $view = new View("Shop", "settings");
        $view->addVariableList([]);
        $view->view();
    }
}