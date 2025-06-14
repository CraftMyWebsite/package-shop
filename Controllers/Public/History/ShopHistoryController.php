<?php

namespace CMW\Controller\Shop\Public\History;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopHistoryController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopHistoryController extends AbstractController
{
    #[Link('/history', Link::GET, [], '/shop')]
    private function publicHistoryView(): void
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

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        if (!$userId) {
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'login');
        }

        $historyOrders = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByUserId($userId);

        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();

        View::createPublicView('Shop', 'History/main')
            ->addVariableList(['historyOrders' => $historyOrders, 'defaultImage' => $defaultImage])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }

    #[Link('/orders/download/:order', Link::GET, [], '/shop')]
    private function shopOrdersDownload(string $order): void
    {
        $pdfDir = EnvManager::getInstance()->getValue('DIR') . 'Public/Uploads/Shop/Invoices';
        $pdfPath = $pdfDir . "/$order.pdf";

        if (!file_exists($pdfPath)) {
            http_response_code(404);
            echo json_encode(['error' => 'Facture introuvable']);
            exit;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="invoice_' . $order . '.pdf"');
        header('Content-Length: ' . filesize($pdfPath));

        readfile($pdfPath);
        exit;
    }
}
