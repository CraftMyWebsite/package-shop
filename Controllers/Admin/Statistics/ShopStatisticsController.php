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
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.stats');

        $statisticsModel = ShopStatisticsModel::getInstance();

        $symbol = ShopSettingsModel::getInstance()->getSettingValue('symbol');
        $symbolIsAfter = ShopSettingsModel::getInstance()->getSettingValue('after');

        $totalOrders = $statisticsModel->countTotalOrders();
        $refundedOrder = $statisticsModel->countOrdersStatus(-2);

        $gainTotal = number_format($statisticsModel->gainTotal(), 2, '.', ' ');
        $lostTotal = number_format($statisticsModel->lostTotal(), 2, '.', ' ');

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

        $monthlyGainsAndLossesLastYear = $statisticsModel->monthlyGainsAndLossesLastYear();
        $monthlyOrderCurrentCompletedAndLossesLastYear = $statisticsModel->monthlyOrderCurrentCompletedAndLossesLastYear();

        $orderDifferenceComparedToLastMonth = $statisticsModel->orderDifferenceComparedToLastMonth();
        $perfOrderDiff = $orderDifferenceComparedToLastMonth['difference'];
        $perfOrderPercent = round($orderDifferenceComparedToLastMonth['percentageChange'], 2);

        $revenueDifferenceComparedToLastMonth = $statisticsModel->revenueDifferenceComparedToLastMonth();
        $perfRevenueDiff = number_format($revenueDifferenceComparedToLastMonth['difference'], 2, '.', ' ');
        $perfRevenuePercent = round($revenueDifferenceComparedToLastMonth['percentageChange'], 2);

        $refundRate = round($statisticsModel->refundRate(), 2);

        $averageOrderProcessingTime = $statisticsModel->averageOrderProcessingTime();
        $averageOrderValue = number_format($statisticsModel->averageOrderValue(), 2, '.', ' ');

        View::createAdminView('Shop', 'Statistics/main')
            ->addScriptBefore('Admin/Resources/Vendors/Apexcharts/Js/apexcharts.js')
            ->addVariableList([
                'bestsBuyersThisMonth' => $bestsBuyersThisMonth,
                'bestsBuyers' => $bestsBuyers,
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
                'monthlyGainsAndLossesLastYear' => $monthlyGainsAndLossesLastYear,
                'monthlyOrderCurrentCompletedAndLossesLastYear' => $monthlyOrderCurrentCompletedAndLossesLastYear,
                'perfOrderDiff' => $perfOrderDiff,
                'perfOrderPercent' => $perfOrderPercent,
                'perfRevenueDiff' => $perfRevenueDiff,
                'perfRevenuePercent' => $perfRevenuePercent,
                'refundRate' => $refundRate,
                'averageOrderProcessingTime' => $averageOrderProcessingTime,
                'averageOrderValue' => $averageOrderValue,
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
