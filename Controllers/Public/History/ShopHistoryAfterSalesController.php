<?php

namespace CMW\Controller\Shop\Public\History;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesMessagesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersAfterSalesModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopHistoryAfterSalesController
 * @package shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryAfterSalesController extends AbstractController
{
    #[Link('/history/afterSales/request/:orderNumber', Link::GET, [], '/shop')]
    private function publicHistoryAfterSalesView(string $orderNumber): void
    {
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        if ($maintenance) {
            if (UsersController::isAdminLogged()) {
                Flash::send(Alert::INFO, 'Boutique', 'Shop est en maintenance, mais vous y avez accès car vous êtes administrateur');
            } else {
                $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
                Flash::send(Alert::WARNING, 'Boutique', $maintenanceMessage);
                Redirect::redirectToHome();
            }
        }

        $userId = UsersModel::getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login');
        }

        $historyOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByOrderNumber($orderNumber);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()?->getHistoryOrdersAfterSalesByOrderId($historyOrder->getId());
        if (is_null($afterSales)) {
            $view = new View('Shop', 'History/createAfterSales');
            $view->addVariableList(['historyOrder' => $historyOrder, 'defaultImage' => $defaultImage]);
        } else {
            $afterSalesMessages = ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->getHistoryOrdersAfterSalesMessageByAfterSalesId($afterSales->getId());
            $view = new View('Shop', 'History/afterSales');
            $view->addVariableList(['historyOrder' => $historyOrder, 'afterSales' => $afterSales, 'afterSalesMessages' => $afterSalesMessages, 'defaultImage' => $defaultImage]);
        }
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();

    }

    #[NoReturn] #[Link('/history/afterSales/request/:orderNumber/create', Link::POST, [], '/shop')]
    private function publicHistoryAfterSalesPostCreate(string $orderNumber): void
    {
        [$reason, $content] = Utils::filterInput('reason', 'content');

        $author = UsersModel::getCurrentUser();

        if (!$author) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );

            Redirect::redirectPreviousRoute();
        }

        $historyOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByOrderNumber($orderNumber);

        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->createHistoryOrdersAfterSales($author->getId(), $reason, $historyOrder->getId());
        $afterSalesMessages = ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->addResponse($afterSales->getId(), $content, $author->getId());

        if ($afterSales && $afterSalesMessages) {
            Flash::send(Alert::SUCCESS, 'S.A.V', 'Demande de S.A.V envoyé.');
            NotificationManager::notify('Demande de S.A.V', $author->getPseudo() . ' viens de faire une demande de S.A.V pour la commande ' . $historyOrder->getOrderNumber());
        } else {
            Flash::send(Alert::ERROR, 'S.A.V', 'Une erreur s\'est produite, merci de réessayer plus tard ...');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/history/afterSales/request/:orderNumber', Link::POST, [], '/shop')]
    private function publicHistoryAfterSalesPostResponse(string $orderNumber): void
    {
        [$content] = Utils::filterInput('content');

        $author = UsersModel::getCurrentUser();

        if (!$author) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );

            Redirect::redirectPreviousRoute();
        }

        $historyOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByOrderNumber($orderNumber);
        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesByOrderId($historyOrder->getId());

        if ($afterSales->getStatus() === 2) {
            Flash::send(Alert::INFO, 'S.A.V', 'Vous ne pouvez pas répondre à cette demande car elle est close !');
            Redirect::redirectPreviousRoute();
        }

        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSales->getId(), 0);
        $afterSalesMessages = ShopHistoryOrdersAfterSalesMessagesModel::getInstance()->addResponse($afterSales->getId(), $content, $author->getId());

        if ($afterSalesMessages) {
            Flash::send(Alert::SUCCESS, 'S.A.V', 'Réponse envoyé.');
            NotificationManager::notify('S.A.V', $author->getPseudo() . ' viens d\'apporter une réponse à la demande S.A.V pour la commande ' . $historyOrder->getOrderNumber());
        } else {
            Flash::send(Alert::ERROR, 'S.A.V', 'Une erreur s\'est produite, merci de réessayer plus tard ...');
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/history/afterSales/request/:orderNumber/close', Link::GET, [], '/shop')]
    private function publicHistoryAfterSalesClose(int $orderNumber): void
    {
        $author = UsersModel::getCurrentUser();

        if (!$author) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );

            Redirect::redirectPreviousRoute();
        }

        $historyOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByOrderNumber($orderNumber);
        $afterSales = ShopHistoryOrdersAfterSalesModel::getInstance()->getHistoryOrdersAfterSalesByOrderId($historyOrder->getId());

        if ($afterSales->getStatus() === 2) {
            Flash::send(Alert::INFO, 'S.A.V', 'Cette demande est déjà close !');
            Redirect::redirectPreviousRoute();
        }

        ShopHistoryOrdersAfterSalesModel::getInstance()->changeStatus($afterSales->getId(), 2);

        Flash::send(Alert::SUCCESS, 'S.A.V', 'Demande de S.A.V clôturer.');
        NotificationManager::notify('S.A.V', $author->getPseudo() . ' viens de clôturer sa demande de S.A.V pour la commande ' . $historyOrder->getOrderNumber());

        Redirect::redirectPreviousRoute();
    }
}
