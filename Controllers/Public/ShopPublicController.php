<?php

namespace CMW\Controller\Shop\Public;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Notification\NotificationManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemsPhysicalRequirementModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Item\ShopItemVariantValueModel;
use CMW\Model\Shop\Review\ShopReviewsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopPublicController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopPublicController extends AbstractController
{
    #[Link('/', Link::GET, [], '/shop')]
    private function publicBaseView(): void
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

        $categoryModel = ShopCategoriesModel::getInstance();
        if (UsersController::isAdminLogged()) {
            $items = ShopItemsModel::getInstance()->getAdminShopItems();
        } else {
            $items = ShopItemsModel::getInstance()->getPublicShopItems();
        }
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersSessionsController::getInstance()->getCurrentUser()?->getId(), session_id());
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View('Shop', 'Main/main');
        $view->addVariableList(['categoryModel' => $categoryModel, 'items' => $items, 'imagesItem' =>
            $imagesItem, 'defaultImage' => $defaultImage, 'itemInCart' => $itemInCart, 'review' => $review, 'allowReviews' => $allowReviews]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/cat/:catSlug', Link::GET, ['.*?'], '/shop')]
    private function publicCatView(string $catSlug): void
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
        $categoryModel = ShopCategoriesModel::getInstance();
        $thisCat = ShopCategoriesModel::getInstance()->getShopCategoryById(ShopCategoriesModel::getInstance()->getShopCategoryIdBySlug($catSlug));
        if (UsersController::isAdminLogged()) {
            $items = ShopItemsModel::getInstance()->getAdminShopItemByCatSlug($catSlug);
        } else {
            $items = ShopItemsModel::getInstance()->getPublicShopItemByCatSlug($catSlug);
        }
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemInCart = ShopCartItemModel::getInstance()->countItemsByUserId(UsersSessionsController::getInstance()->getCurrentUser()?->getId(), session_id());
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View('Shop', 'Main/main');
        $view->addVariableList(['items' => $items, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'itemInCart' => $itemInCart, 'thisCat' => $thisCat, 'categoryModel' => $categoryModel, 'review' => $review, 'allowReviews' => $allowReviews]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[Link('/cat/:catSlug/item/:itemSlug', Link::GET, ['.*?'], '/shop')]
    private function publicItemView(string $catSlug, string $itemSlug): void
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
        if (UsersController::isAdminLogged()) {
            $otherItemsInThisCat = ShopItemsModel::getInstance()->getAdminShopItemByCatSlug($catSlug);
        } else {
            $otherItemsInThisCat = ShopItemsModel::getInstance()->getPublicShopItemByCatSlug($catSlug);
        }
        $parentCat = ShopCategoriesModel::getInstance()->getShopCategoryById(ShopCategoriesModel::getInstance()->getShopCategoryIdBySlug($catSlug));
        if (UsersController::isAdminLogged()) {
            $itemId = ShopItemsModel::getInstance()->getAdminShopItemIdBySlug($itemSlug);
        } else {
            $itemId = ShopItemsModel::getInstance()->getPublicShopItemIdBySlug($itemSlug);
        }
        $showPublicStock = ShopSettingsModel::getInstance()->getSettingValue('showPublicStock');
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemVariants = ShopItemVariantModel::getInstance()->getShopItemVariantByItemId($itemId);
        $variantValuesModel = ShopItemVariantValueModel::getInstance();
        $physicalInfo = ShopItemsPhysicalRequirementModel::getInstance()->getShopItemPhysicalRequirementByItemId($itemId);
        $review = ShopReviewsModel::getInstance();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $view = new View('Shop', 'Main/item');
        $view->addVariableList(['showPublicStock' => $showPublicStock, 'otherItemsInThisCat' => $otherItemsInThisCat, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'parentCat' => $parentCat, 'item' => $item, 'itemVariants' => $itemVariants, 'variantValuesModel' => $variantValuesModel, 'physicalInfo' => $physicalInfo ?? null, 'review' => $review, 'allowReviews' => $allowReviews]);
        $view->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css');
        $view->view();
    }

    #[NoReturn] #[Link('/cat/:catSlug/item/:itemSlug/addReview', Link::POST, ['.*?'], '/shop')]
    private function publicPostReview(string $catSlug, string $itemSlug): void
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

        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');

        if (UsersController::isAdminLogged()) {
            $itemId = ShopItemsModel::getInstance()->getAdminShopItemIdBySlug($itemSlug);
        } else {
            $itemId = ShopItemsModel::getInstance()->getPublicShopItemIdBySlug($itemSlug);
        }
        if (is_null($itemId)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Impossible de trouver cet article !');
            Redirect::redirectToHome();
        }
        [$rating, $title, $content] = Utils::filterInput('rating', 'title', 'content');

        $this->handleOrderBeforeReview($itemId, $userId);
        $this->handleReviewBeforeReview($itemId, $userId);

        if (is_null($rating)) {
            Flash::send(Alert::WARNING, 'Boutique', "Vous n'avez pas sélectionner le nombre d'étoile(s) que vous attribué à cet article.");
            Redirect::redirectPreviousRoute();
        }

        if (!is_null($userId)) {
            ShopReviewsModel::getInstance()->createReview($itemId, $userId, $rating, $title, $content);
            Flash::send(Alert::SUCCESS, 'Boutique', 'Merci pour votre avis, il nous aide à nous améliorer !');
            NotificationManager::notify('Avis produit', UsersSessionsController::getInstance()->getCurrentUser()?->getPseudo() . ' viens de laisser un avis sur ' . ShopItemsModel::getInstance()->getShopItemsById($itemId)->getName() . ' (' . $rating . ' étoiles)', 'shop/items');
        }

        Redirect::redirectPreviousRoute();
    }

    private function handleOrderBeforeReview($itemId, $userId): void
    {
        $orders = ShopHistoryOrdersModel::getInstance()->getHistoryOrdersByUserId($userId);
        if (empty($orders)) {
            Flash::send(Alert::WARNING, 'Boutique', 'Achetez cet article avant de pouvoir laisser un avis.');
            Redirect::redirectPreviousRoute();
        } else {
            foreach ($orders as $order) {
                foreach ($order->getOrderedItems() as $orderedItem) {
                    if ($orderedItem->getItem()->getId() == $itemId) {
                        return;
                    }
                }
            }
            Flash::send(Alert::WARNING, 'Boutique', 'Achetez cet article avant de pouvoir laisser un avis.');
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleReviewBeforeReview($itemId, $userId): void
    {
        $reviews = ShopReviewsModel::getInstance()->getShopReviewByItemId($itemId);
        foreach ($reviews as $review) {
            if ($review->getUser()->getId() === $userId) {
                Flash::send(Alert::WARNING, 'Boutique', 'Vous avez déjà laissé un avis pour cet article.');
                Redirect::redirectPreviousRoute();
            }
        }
    }

    #[Link('/search', Link::POST, ['.*?'], '/shop')]
    private function publicShopResearch(): void
    {
        [$for] = Utils::filterInput('for');

        $items = ShopItemsModel::getInstance()->getItemByResearch($for);

        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $allowReviews = ShopSettingsModel::getInstance()->getSettingValue('reviews');
        $review = ShopReviewsModel::getInstance();
        $categoryModel = ShopCategoriesModel::getInstance();

        View::createPublicView('Shop', 'Main/main')
            ->addVariableList(['items' => $items
                , 'imagesItem' => $imagesItem
                , 'defaultImage' => $defaultImage
                , 'allowReviews' => $allowReviews
                , 'review' => $review
                , 'categoryModel' => $categoryModel])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
    }
}
