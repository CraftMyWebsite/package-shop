<?php

namespace CMW\Controller\Shop\Admin\Discount;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Discounts\ShopDiscountEntity;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use DateTime;


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
    #[Link("/discounts", Link::GET, [], "/cmw-admin/shop")]
    public function shopDiscounts(): void
    {
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $discounts = ShopDiscountModel::getInstance()->getAllDiscounts();

        $sortedDiscounts = $this->sortDiscountsByDate($discounts);

        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.discounts");
        View::createAdminView('Shop', 'Discount/discounts')
            ->addVariableList(["ongoingDiscounts" => $sortedDiscounts['ongoing'],
                "upcomingDiscounts" => $sortedDiscounts['upcoming'],
                "pastDiscounts" => $sortedDiscounts['past']])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    /**
     * @throws \CMW\Manager\Router\RouterException
     * @throws \Exception
     */
    #[Link("/giftCard", Link::GET, [], "/cmw-admin/shop")]
    public function shopGiftCard(): void
    {
        ShopDiscountModel::getInstance()->autoStatusChecker();

        $giftCard = ShopDiscountModel::getInstance()->getAllGiftCard();

        $sortedDiscounts = $this->sortDiscountsByDate($giftCard);

        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.discounts");
        View::createAdminView('Shop', 'Discount/giftCard')
            ->addVariableList(["ongoingDiscounts" => $sortedDiscounts['ongoing'],
                "upcomingDiscounts" => $sortedDiscounts['upcoming'],
                "pastDiscounts" => $sortedDiscounts['past']])
            ->addStyle("Admin/Resources/Vendors/Simple-datatables/style.css","Admin/Resources/Assets/Css/Pages/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/Umd/simple-datatables.js","Admin/Resources/Assets/Js/Pages/simple-datatables.js")
            ->view();
    }

    /**
     * @throws \Exception
     * @return ShopDiscountEntity[]
     */
    private function sortDiscountsByDate($discounts): array
    {
        $currentDate = new DateTime(); // Date actuelle
        $ongoingDiscounts = [];
        $upcomingDiscounts = [];
        $pastDiscounts = [];

        foreach ($discounts as $discount) {
            $startDate = new DateTime($discount->getStartDate());
            $endDateString = $discount->getEndDate(); // Récupère la date de fin comme chaîne
            $endDate = $endDateString ? new DateTime($endDateString) : null;
            $status = $discount->getStatus(); // Supposons que c'est ainsi que vous accédez au statut

            // Promotion en cours : a commencé, pas fini ou pas de date de fin, et statut actif
            if ($startDate <= $currentDate && ($endDate >= $currentDate || empty($endDateString)) && $status != 0) {
                $ongoingDiscounts[] = $discount;
            }
            // Promotion à venir : n'a pas encore commencé
            elseif ($currentDate < $startDate) {
                $upcomingDiscounts[] = $discount;
            }
            // Toutes autres conditions, considérées comme promotions passées
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
}

//TODO Note : Lors de la suppression d'une promotion, on doit verifier que la promotion n'as pas encore été utilisée dans un order.