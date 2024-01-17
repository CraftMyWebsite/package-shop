<?php

namespace CMW\Controller\Shop;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Shop\IPaymentMethod;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\ShopPaymentMethodSettingsModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @ShopPaymentsController
 * @package shop
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ShopPaymentsController extends AbstractController
{

    /**
     * @return \CMW\Interface\Shop\IPaymentMethod[]
     */
    public function getPaymentsMethods(): array
    {
        return Loader::loadImplementations(IPaymentMethod::class);
    }

    #[Link("/payments", Link::GET, [], "/cmw-admin/shop")]
    private function shopPayments(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");
        View::createAdminView('Shop', 'payments')
            ->addVariableList(['methods' => $this->getPaymentsMethods()])
            ->view();
    }

    #[NoReturn] #[Link("/payments/settings", Link::POST, [], "/cmw-admin/shop")]
    private function shopPaymentsSettingsPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "shop.payments.settings");

        $settings = $_POST;

        foreach ($settings as $key => $value) {
            if ($key === 'security-csrf-token' || $key === 'honeyInput') {
                continue;
            }
            $key = FilterManager::filterData($key, 50);
            $value = FilterManager::filterData($value, 255);

            if (!ShopPaymentMethodSettingsModel::getInstance()->updateOrInsertSetting($key, $value)){
                Flash::send(Alert::ERROR,'Erreur',
                    "Impossible de mettre à jour le paramètre $key");
            }
        }

        Flash::send(Alert::SUCCESS,'Succès', "Les paramètres ont été mis à jour");
        Redirect::redirectPreviousRoute();
    }
}
