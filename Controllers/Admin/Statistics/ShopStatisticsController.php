<?php

namespace CMW\Controller\Shop\Admin\Statistics;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Model\Shop\Statistics\ShopStatisticsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;

/**
 * Class: @ShopStatisticsController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopStatisticsController extends AbstractController
{
    #[Link('/statistics', Link::GET, [], '/cmw-admin/shop')]
    private function shopStatistics(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.statistics');

        $statisticsModel = ShopStatisticsModel::getInstance();

        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');

        $numberOrderThisMonth = $statisticsModel->countTotalOrdersThisMonth();
        $refundedOrderThisMonth = $statisticsModel->countOrdersStatusThisMonth(-2);

        $gainThisMonth = $statisticsModel->gainThisMonth();
        $lostThisMonth = $statisticsModel->lostThisMonth();

        $totalOrders = $statisticsModel->countTotalOrders();
        $refundedOrder = $statisticsModel->countOrdersStatus(-2);

        $gainTotal = $statisticsModel->gainTotal();
        $lostTotal = $statisticsModel->lostTotal();

        $activeItems = $statisticsModel->countActiveItems();
        $archivedItems = $statisticsModel->countArchivedItems();
        $draftItems = $statisticsModel->countDraftItems();
        $itemInCart = $statisticsModel->countItemsInCart();

        $bestsSeller = $statisticsModel->bestSellers();
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        $bestsBuyersThisMonth = $statisticsModel->bestBuyersThisMonth();
        $bestsBuyers = $statisticsModel->bestBuyers();
        $limit = ShopSettingsModel::getInstance()->getSettingValue('topBestBuyer');

        $gains = [];
        $losses = [];

        for ($i = 0; $i < 3; $i++) {
            $gains[] = $statisticsModel->gainByMonth($i);
            $losses[] = $statisticsModel->lostByMonth($i);
        }

        View::createAdminView('Shop', 'Statistics/main')
            ->addScriptBefore('Admin/Resources/Vendors/Apexcharts/Js/apexcharts.js')
            ->addVariableList(['numberOrderThisMonth' => $numberOrderThisMonth,
                'refundedOrderThisMonth' => $refundedOrderThisMonth,
                'bestsBuyersThisMonth' => $bestsBuyersThisMonth,
                'bestsBuyers' => $bestsBuyers,
                'gainThisMonth' => $gainThisMonth,
                'lostThisMonth' => $lostThisMonth,
                'totalOrders' => $totalOrders,
                'refundedOrder' => $refundedOrder,
                'activeItems' => $activeItems,
                'archivedItems' => $archivedItems,
                'bestsSeller' => $bestsSeller,
                'imagesItem' => $imagesItem,
                'defaultImage' => $defaultImage,
                'review' => $review,
                'allowReviews' => $allowReviews,
                'draftItems' => $draftItems,
                'itemInCart' => $itemInCart,
                'gainTotal' => $gainTotal,
                'lostTotal' => $lostTotal,
                'limit' => $limit,
                'symbol' => $symbol,
                'symbolIsAfter' => $symbolIsAfter,
                'gains' => $gains,
                'losses' => $losses,
            ])
            ->view();
    }

    #[NoReturn] #[Link('/statistics', Link::POST, [], '/cmw-admin/shop')]
    private function shopStatisticsEditLimit(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.statistics');

        [$limit] = Utils::filterInput('limit');

        ShopSettingsModel::getInstance()->updateSetting('topBestBuyer', $limit);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Top modifié !');
        Redirect::redirectPreviousRoute();
    }
}
