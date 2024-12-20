<?php

namespace CMW\Controller\Shop\Admin\HistoryOrder;

use CMW\Controller\Shop\Admin\Notify\ShopNotifyController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesMessagesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopHistoryOrdersAfterSalesController
 * @package shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryOrdersAfterSalesController extends AbstractController
{
    #[Link('/afterSales', Link::GET, [], '/cmw-admin/shop')]
    private function shopOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.afterSales');

        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSales();

        View::createAdminView('Shop', 'Orders/AfterSales/main')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['afterSales' => $afterSales])
            ->view();
    }

    #[Link('/afterSales/manage/:afterSalesId', Link::GET, [], '/cmw-admin/shop')]
    private function shopManageOrders(int $afterSalesId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.afterSales.manage');

        $afterSale = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesById($afterSalesId);
        $afterSaleMessages = ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->getHistoryOrdersAfterSalesMessageByAfterSalesId($afterSalesId);

        View::createAdminView('Shop', 'Orders/AfterSales/manage')
            ->addVariableList(['afterSale' => $afterSale, 'afterSaleMessages' => $afterSaleMessages])
            ->view();
    }

    #[NoReturn] #[Link('/afterSales/manage/:afterSalesId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageFinalStep(int $afterSalesId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.afterSales.manage');

        [$message] = Utils::filterInput('message');

        $author = UsersSessionsController::getInstance()->getCurrentUser();

        if (!$author) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );

            Redirect::redirectPreviousRoute();
        }

        ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->addResponse($afterSalesId, $message, $author->getId());
        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSalesId, 1);

        $afterSale = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesById($afterSalesId);
        $url = Website::getUrl() . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . 'shop/history/afterSales/request/' . $afterSale->getOrder()->getOrderNumber();
        $htmlMessage =<<<HTML
            <p>Vous avez reçu une réponse à votre demande de S.A.V !</p>
            <p><b>Réponse : </b> %MESSAGE%</p>
            <p>Pour apporter une réponse, <a href="%URL%">cliquez ici !</a></p>
        HTML;
        $finalMessage = str_replace(['%MESSAGE%', '%URL%'],
            [$message, $url], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($afterSale->getAuthor()->getMail(), "Services après-ventes", "Services après-ventes", $finalMessage);

        Flash::send(Alert::SUCCESS, 'S.A.V', 'Réponse apporté.');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/afterSales/close/:afterSalesId', Link::GET, [], '/cmw-admin/shop')]
    private function shopCloseOrders(int $afterSalesId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.afterSales.manage');

        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSalesId, 2);

        $afterSale = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesById($afterSalesId);
        $htmlMessage =<<<HTML
            <p>Votre demande de S.A.V est désormais close</p>
            <p>Demande faite pour la commande N°<b>%NUMBER%</b></p>
        HTML;
        $finalMessage = str_replace(['%NUMBER%'], [$afterSale->getOrder()->getOrderNumber()], $htmlMessage);
        ShopNotifyController::getInstance()->notifyUser($afterSale->getAuthor()->getMail(), "Services après-ventes", "Services après-ventes", $finalMessage);

        Redirect::redirect('cmw-admin/shop/afterSales');
    }
}
