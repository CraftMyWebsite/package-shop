<?php

namespace CMW\Controller\Shop\Admin\HistoryOrder;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesMessagesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopHistoryOrdersAfterSalesController
 * @package shop
 * @author Zomblard
 * @version 1.0
 */
class ShopHistoryOrdersAfterSalesController extends AbstractController
{
    #[Link('/afterSales', Link::GET, [], '/cmw-admin/shop')]
    private function shopOrders(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.sav');

        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSales();

        View::createAdminView('Shop', 'Orders/AfterSales/main')
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->addVariableList(['afterSales' => $afterSales])
            ->view();
    }

    #[Link('/afterSales/manage/:afterSalesId', Link::GET, [], '/cmw-admin/shop')]
    private function shopManageOrders(Request $request, int $afterSalesId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.sav');

        $afterSale = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesById($afterSalesId);
        $afterSaleMessages = ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->getHistoryOrdersAfterSalesMessageByAfterSalesId($afterSalesId);

        View::createAdminView('Shop', 'Orders/AfterSales/manage')
            ->addVariableList(['afterSale' => $afterSale, 'afterSaleMessages' => $afterSaleMessages])
            ->view();
    }

    #[NoReturn] #[Link('/afterSales/manage/:afterSalesId', Link::POST, [], '/cmw-admin/shop')]
    private function shopManageFinalStep(Request $request, int $afterSalesId): void
    {
        [$message] = Utils::filterInput('message');

        $author = UsersModel::getCurrentUser();

        if (!$author) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );

            Redirect::redirectPreviousRoute();
        }

        // TODO : Notifier l'utilisateur

        ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->addResponse($afterSalesId, $message, $author->getId());
        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSalesId, 1);

        Flash::send(Alert::SUCCESS, 'S.A.V', 'Réponse apporté.');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/afterSales/close/:afterSalesId', Link::GET, [], '/cmw-admin/shop')]
    private function shopCloseOrders(Request $request, int $afterSalesId): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.sav');

        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSalesId, 2);

        // TODO : Notifier l'utilisateur

        Redirect::redirect('cmw-admin/shop/afterSales');
    }
}
