<?php

namespace CMW\Controller\Shop\Public\History;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopHistoryAfterSalesController
 * @package shop
 * @author Zomb
 * @version 0.0.1
 */
class ShopHistoryAfterSalesController extends AbstractController
{
    #[Link('/history/afterSales/request/:orderNumber', Link::GET, [], '/shop')]
    private function publicHistoryAfterSalesView(int $orderNumber): void
    {
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        if ($maintenance) {
            $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
            Flash::send(Alert::WARNING, 'Boutique', $maintenanceMessage);
            Redirect::redirectToHome();
        }

        $userId = UsersModel::getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login');
        }

        $historyOrder = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByOrderNumber($orderNumber);
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $view = new View('Shop', 'History/afterSales');
        $view->addVariableList(['historyOrder' => $historyOrder, 'defaultImage' => $defaultImage]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }
}
