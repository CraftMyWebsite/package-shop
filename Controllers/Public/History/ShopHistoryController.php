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
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Order\ShopOrdersItemsVariantesModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopHistoryController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopHistoryController extends AbstractController
{
    #[Link('/history', Link::GET, [], '/shop')]
    private function publicHistoryView(): void
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

        $historyOrders = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByUserId($userId);

        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        $view = new View('Shop', 'Users/history');
        $view->addVariableList(['historyOrders' => $historyOrders, 'defaultImage' => $defaultImage]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }
}
