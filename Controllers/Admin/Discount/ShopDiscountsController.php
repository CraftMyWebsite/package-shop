<?php

namespace CMW\Controller\Shop\Admin\Discount;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Discount\ShopDiscountModel;


/**
 * Class: @ShopDiscountsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopDiscountsController extends AbstractController
{
    #[Link("/discounts", Link::GET, [], "/cmw-admin/shop")]
    public function shopDiscounts(): void
    {
        $discounts = ShopDiscountModel::getInstance()->getAllShopDiscounts();

        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.discounts");
        View::createAdminView('Shop', 'Discount/discounts')
            ->addVariableList(["discounts" => $discounts])
            ->view();
    }
}

//TODO Note : Lors de la suppression d'une promotion, on doit verifier que la promotion n'as pas encore été utilisée dans un order.