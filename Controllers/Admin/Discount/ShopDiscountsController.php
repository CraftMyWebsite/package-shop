<?php

namespace CMW\Controller\Shop\Admin\Discount;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsGiftCodeController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Category\ShopCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use DateTime;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopDiscountsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopDiscountsController extends AbstractController
{
    /**
     * @throws \CMW\Manager\Router\RouterException
     * @throws \Exception
     */
    #[Link('/discounts', Link::GET, [], '/cmw-admin/shop')]
    private function shopDiscounts(): void
    {
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $discounts = ShopDiscountModel::getInstance()->getAllDiscounts();

        $sortedDiscounts = $this->sortDiscountsByDate($discounts);

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');
        View::createAdminView('Shop', 'Discount/discounts')
            ->addVariableList(['ongoingDiscounts' => $sortedDiscounts['ongoing'],
                'upcomingDiscounts' => $sortedDiscounts['upcoming'],
                'pastDiscounts' => $sortedDiscounts['past']])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[Link('/discounts/add', Link::GET, [], '/cmw-admin/shop')]
    private function shopDiscountsAdd(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        $categories = ShopCategoriesModel::getInstance()->getShopCategories();
        $items = ShopItemsModel::getInstance()->getShopItems();

        View::createAdminView('Shop', 'Discount/add')
            ->addVariableList(['categories' => $categories, 'items' => $items])
            ->view();
    }

    #[NoReturn] #[Link('/discounts/add', Link::POST, [], '/cmw-admin/shop')]
    private function shopDiscountsAddPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts.add');
        $discountModel = ShopDiscountModel::getInstance();

        [$name, $startDate, $endDate, $multiplePerUsers, $maxUses, $impact, $price, $percent, $defaultActive, $code, $test, $needPurchase, $applyQuantity, $link] = Utils::filterInput('name', 'startDate', 'endDate', 'multiplePerUsers', 'maxUses', 'impact', 'price', 'percent', 'defaultActive', 'code', 'test', 'needPurchase', 'applyQuantity', 'link');

        $maxUses = ($maxUses === '') ? null : $maxUses;
        $currentUses = is_null($maxUses) ? null : 0;
        $multiplePerUsers = $multiplePerUsers ?? 0;
        $defaultActive = $defaultActive ?? 0;
        $test = $test ?? 0;
        $needPurchase = $needPurchase ?? 0;
        $applyQuantity = $applyQuantity ?? 0;
        $price = ($price === '') ? null : $price;
        $percent = ($percent === '') ? null : $percent;
        $startDate = empty($startDate) ? null : date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = empty($endDate) ? null : date('Y-m-d H:i:s', strtotime($endDate));
        $code = $defaultActive ? null : $code;

        $currentDateTime = date('Y-m-d H:i:s');

        if (!is_null($endDate)) {
            if ($endDate <= $currentDateTime) {
                Flash::send(Alert::ERROR, 'Discount', 'La date de fin ne peut pas être inférieure à la date actuelle.');
                Redirect::redirectPreviousRoute();
            }
        }

        if ($impact === '1') {
            $price = null;
            if ($percent > 99) {
                Flash::send(Alert::ERROR, 'Discount', 'Vous ne pouvez pas appliquer une réduction de plus de 99% !');
                Redirect::redirectPreviousRoute();
            }
        }

        if ($impact === '2') {
            $percent = null;
        }

        if ($code) {
            $codeFound = false;
            foreach ($discountModel->getAllDiscounts() as $discount) {
                if ($discount->getCode() === $code) {
                    $codeFound = true;
                    break;
                }
            }
            if ($codeFound) {
                Flash::send(Alert::WARNING, 'Discount', 'Ce code est déja utiliser pas une promotion (active ou non).');
                Redirect::redirectPreviousRoute();
            }
        }

        // Evite les double promotion active par defaut sur tout les articles
        if ($defaultActive && empty($_POST['linkedItems']) && empty($_POST['linkedCats'])) {
            $discountFound = false;
            foreach ($discountModel->getAllDiscounts() as $discount) {
                if ($discount->getLinked() === 0 && $discount->getDefaultActive() === 1 && $this->isDiscountActive($currentDateTime, $discount->getStartDate(), $discount->getEndDate())) {
                    $discountFound = true;
                    break;
                }
            }
            if ($discountFound) {
                Flash::send(Alert::WARNING, 'Discount', "Impossible d'ajouter cette promotion car il y en à déjà une qui applique une reduction à tout les articles de manière automatique.");
                Redirect::redirectPreviousRoute();
            }
        }

        $thisDiscount = ShopDiscountModel::getInstance()->createDiscount($name, $link, $startDate, $endDate, $maxUses, $currentUses, $percent, $price, $multiplePerUsers, 0, $test, $code, $defaultActive, $needPurchase, $applyQuantity);

        if (!$thisDiscount) {
            Flash::send(
                Alert::ERROR,
                'Discount',
                'Une erreur est survenue lors de la création de la promotion.',
            );
            Redirect::redirectPreviousRoute();
        }

        if ($link === '1') {
            if (!empty($_POST['linkedItems'])) {
                foreach ($_POST['linkedItems'] as $itemId) {
                    $itemFound = false;
                    foreach ($discountModel->getAllDiscounts() as $discount) {
                        if ($defaultActive) {
                            if ($this->isDiscountActive($currentDateTime, $discount->getStartDate(), $discount->getEndDate()) && $discount->getDefaultActive() === 1) {
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à tout"
                                if ($discount->getLinked() === 0) {
                                    Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur tout les articles ! (' . $discount->getName() . ')');
                                    $itemFound = true;
                                    break;
                                }
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à un article"
                                if ($discount->getLinked() === 1) {
                                    if (!empty(ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByItemId($itemId))) {
                                        Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur un de vos article (' . $discount->getName() . ')');
                                        $itemFound = true;
                                        break;
                                    }
                                }
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à une catégorie"
                                if ($discount->getLinked() === 2) {
                                    $itemCatId = ShopItemsModel::getInstance()->getShopItemsById($itemId)->getCategory()->getId();
                                    if (!empty(ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesByCategoryId($itemCatId))) {
                                        Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur un de vos article via une promotion sur les catégories (' . $discount->getName() . ')');
                                        $itemFound = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($itemFound) {
                        ShopDiscountModel::getInstance()->deleteDiscount($thisDiscount->getId());
                        Redirect::redirectPreviousRoute();
                    } else {
                        ShopDiscountItemsModel::getInstance()->addDiscountItem($thisDiscount->getId(), $itemId);
                    }
                }
            } else {
                ShopDiscountModel::getInstance()->deleteDiscount($thisDiscount->getId());
                Flash::send(Alert::ERROR, 'Discount', 'Veuillez définir au moins un article !');
                Redirect::redirectPreviousRoute();
            }
        }

        if ($link === '2') {
            if (!empty($_POST['linkedCats'])) {
                foreach ($_POST['linkedCats'] as $categoryId) {
                    $itemsInThisCat = ShopItemsModel::getInstance()->getShopItemByCat($categoryId);
                    $itemCatFound = false;
                    foreach ($discountModel->getAllDiscounts() as $discount) {
                        if ($defaultActive) {
                            if ($this->isDiscountActive($currentDateTime, $discount->getStartDate(), $discount->getEndDate()) && $discount->getDefaultActive() === 1) {
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à tout"
                                if ($discount->getLinked() === 0) {
                                    Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur tout les articles ! (' . $discount->getName() . ')');
                                    $itemCatFound = true;
                                    break;
                                }
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à un article"
                                if ($discount->getLinked() === 1) {
                                    foreach ($itemsInThisCat as $item) {
                                        if (!empty(ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByItemId($item->getId()))) {
                                            Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur un de vos article (' . $discount->getName() . ')');
                                            $itemCatFound = true;
                                            break;
                                        }
                                    }
                                }
                                // Verification que l'article n'est pas déja lié à une promotion de type "lié à une catégorie"
                                if ($discount->getLinked() === 2) {
                                    if (!empty(ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesByCategoryId($categoryId))) {
                                        Flash::send(Alert::WARNING, 'Discount', 'Une promotion est déjà appliquer par défaut sur un de vos article via une promotion sur les catégories (' . $discount->getName() . ')');
                                        $itemCatFound = true;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    if ($itemCatFound) {
                        ShopDiscountModel::getInstance()->deleteDiscount($thisDiscount->getId());
                        Redirect::redirectPreviousRoute();
                    } else {
                        ShopDiscountCategoriesModel::getInstance()->addDiscountCategory($thisDiscount->getId(), $categoryId);
                    }
                }
            } else {
                ShopDiscountModel::getInstance()->deleteDiscount($thisDiscount->getId());
                Flash::send(Alert::ERROR, 'Discount', 'Veuillez définir au moins une catégorie !');
                Redirect::redirectPreviousRoute();
            }
        }

        Flash::send(Alert::SUCCESS, 'Discount', 'Promotion ajouté !');
        Redirect::redirect('cmw-admin/shop/discounts');
    }

    #[Link('/discounts/edit/:id', Link::GET, [], '/cmw-admin/shop')]
    private function shopDiscountsEdit(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        $discount = ShopDiscountModel::getInstance()->getAllShopDiscountById($id);

        View::createAdminView('Shop', 'Discount/edit')
            ->addVariableList(['discount' => $discount])
            ->view();
    }

    #[Link('/discounts/edit/:id', Link::POST, [], '/cmw-admin/shop')]
    private function shopDiscountsEditPost(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts.add');
        $discountModel = ShopDiscountModel::getInstance();

        [$name, $startDate, $endDate, $multiplePerUsers, $maxUses, $impact, $price, $percent, $defaultActive, $code, $test, $needPurchase, $applyQuantity, $link] = Utils::filterInput('name', 'startDate', 'endDate', 'multiplePerUsers', 'maxUses', 'impact', 'price', 'percent', 'defaultActive', 'code', 'test', 'needPurchase', 'applyQuantity', 'link');

        $maxUses = ($maxUses === '') ? null : $maxUses;
        $currentUses = is_null($maxUses) ? null : 0;
        $multiplePerUsers = $multiplePerUsers ?? 0;
        $defaultActive = $defaultActive ?? 0;
        $test = $test ?? 0;
        $needPurchase = $needPurchase ?? 0;
        $applyQuantity = $applyQuantity ?? 0;
        $price = ($price === '') ? null : $price;
        $percent = ($percent === '') ? null : $percent;
        $startDate = empty($startDate) ? null : date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = empty($endDate) ? null : date('Y-m-d H:i:s', strtotime($endDate));
        $code = $defaultActive ? null : $code;

        $currentDateTime = date('Y-m-d H:i:s');

        if (!is_null($endDate)) {
            if ($endDate <= $currentDateTime) {
                Flash::send(Alert::ERROR, 'Discount', 'La date de fin ne peut pas être inférieure à la date actuelle.');
                Redirect::redirectPreviousRoute();
            }
        }

        if ($impact === '1') {
            $price = null;
            if ($percent > 99) {
                Flash::send(Alert::ERROR, 'Discount', 'Vous ne pouvez pas appliquer une réduction de plus de 99% !');
                Redirect::redirectPreviousRoute();
            }
        }

        if ($impact === '2') {
            $percent = null;
        }

        if ($code) {
            $codeFound = false;
            foreach ($discountModel->getAllDiscounts() as $discount) {
                if ($discount->getCode() === $code) {
                    if (!$discount->getId() === $id) {
                        $codeFound = true;
                        break;
                    }
                }
            }
            if ($codeFound) {
                Flash::send(Alert::WARNING, 'Discount', 'Ce code est déja utiliser pas une promotion (active ou non).');
                Redirect::redirectPreviousRoute();
            }
        }

        ShopDiscountModel::getInstance()->editDiscount($id, $name, $endDate, $maxUses, $currentUses, $percent, $price, $multiplePerUsers, 0, $test, $code, $needPurchase, $applyQuantity);

        Flash::send(Alert::SUCCESS, 'Discount', 'Promotion modifié !');
        Redirect::redirect('cmw-admin/shop/discounts');
    }

    #[NoReturn] #[Link('/discounts/delete/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function adminDeleteShopDiscount(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        ShopDiscountModel::getInstance()->deleteDiscount($id);

        Flash::send(Alert::SUCCESS, 'Promotions', 'Promotion supprimé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/discounts/stop/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function adminStopShopDiscount(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        ShopDiscountModel::getInstance()->stopDiscount($id);

        Flash::send(Alert::SUCCESS, 'Promotions', 'Promotion supprimé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/discounts/start/:id', Link::GET, ['[0-9]+'], '/cmw-admin/shop')]
    private function adminStartShopDiscount(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        ShopDiscountModel::getInstance()->startDiscount($id);

        Flash::send(Alert::SUCCESS, 'Promotions', 'Promotion activé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link('/discounts/report', Link::POST, [], '/cmw-admin/shop')]
    private function shopReportDiscountPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        [$id, $startDate] = Utils::filterInput('id', 'startDate');

        $startDate = date('Y-m-d H:i:s', strtotime($startDate));

        ShopDiscountModel::getInstance()->reportDiscount($id, $startDate);

        Flash::send(Alert::SUCCESS, 'Discount', 'Report appliqué !');

        Redirect::redirectPreviousRoute();
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     * @throws \Exception
     */
    #[Link('/giftCard', Link::GET, [], '/cmw-admin/shop')]
    private function shopGiftCard(): void
    {
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $giftCard = ShopDiscountModel::getInstance()->getAllGiftCard();

        $sortedDiscounts = $this->sortDiscountsByDate($giftCard);

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');
        View::createAdminView('Shop', 'Discount/giftCard')
            ->addVariableList(['ongoingDiscounts' => $sortedDiscounts['ongoing'],
                'upcomingDiscounts' => $sortedDiscounts['upcoming'],
                'pastDiscounts' => $sortedDiscounts['past']])
            ->addStyle('Admin/Resources/Assets/Css/simple-datatables.css')
            ->addScriptAfter('Admin/Resources/Vendors/Simple-datatables/simple-datatables.js',
                'Admin/Resources/Vendors/Simple-datatables/config-datatables.js')
            ->view();
    }

    #[NoReturn] #[Link('/giftCard/generate', Link::POST, [], '/cmw-admin/shop')]
    private function shopGenerateGiftCard(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'shop.discounts');

        [$amount] = Utils::filterInput('amount');

        ShopVirtualItemsGiftCodeController::getInstance()->adminGenerateCode($amount);

        Flash::send(Alert::SUCCESS, 'Boutique', 'Code généré !');

        Redirect::redirectPreviousRoute();
    }

    /**
     * @return ShopDiscountEntity[]
     * @throws \Exception
     */
    private function sortDiscountsByDate($discounts): array
    {
        $currentDate = new DateTime();  // Date actuelle
        $ongoingDiscounts = [];
        $upcomingDiscounts = [];
        $pastDiscounts = [];

        foreach ($discounts as $discount) {
            $startDate = new DateTime($discount->getStartDate());
            $endDateString = $discount->getEndDate();  // Récupère la date de fin comme chaîne
            $endDate = $endDateString ? new DateTime($endDateString) : null;
            $status = $discount->getStatus();  // Supposons que c'est ainsi que vous accédez au statut

            // Promotion en cours : a commencé, pas fini ou pas de date de fin, et statut actif
            if ($startDate <= $currentDate && ($endDate >= $currentDate || empty($endDateString)) && $status != 0) {
                $ongoingDiscounts[] = $discount;
            } // Promotion à venir : n'a pas encore commencé
            elseif ($currentDate < $startDate) {
                $upcomingDiscounts[] = $discount;
            } // Toutes autres conditions, considérées comme promotions passées
            else {
                $pastDiscounts[] = $discount;
            }
        }

        return [
            'ongoing' => $ongoingDiscounts,
            'upcoming' => $upcomingDiscounts,
            'past' => $pastDiscounts,
        ];
    }

    private function isDiscountActive($currentDateTime, $startDate, $endDate = null): bool
    {
        if ($startDate <= $currentDateTime && ($endDate === null || $currentDateTime <= $endDate)) {
            return true;
        }

        if ($startDate > $currentDateTime) {
            return true;
        }

        return false;
    }
}
