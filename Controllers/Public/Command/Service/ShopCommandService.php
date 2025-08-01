<?php

namespace CMW\Controller\Shop\Public\Command\Service;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopCommandService
 * @package Shop
 */
class ShopCommandService extends AbstractController
{
    /**
     * Vérifie si la boutique est en maintenance. Si oui, redirige sauf si admin.
     *
     */
    public function checkMaintenanceOrRedirect(): void
    {
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        if (!$maintenance) return;

        if (UsersController::isAdminLogged()) {
            Flash::send(Alert::INFO, 'Boutique', 'Shop est en maintenance, mais vous y avez accès car vous êtes administrateur');
        } else {
            $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
            Flash::send(Alert::WARNING, 'Boutique', $maintenanceMessage);
            Redirect::redirectToHome();
        }
    }

    /**
     * Vérifie que l'utilisateur est connecté, sinon redirige vers la page de login.
     */
    public function checkUserSessionOrRedirect(): void
    {
        if (!UsersController::isUserLogged()) {
            Redirect::redirect('login');
        }
    }

    /**
     * Récupère les items du panier pour l'utilisateur ou la session en cours.
     *
     * @param int|null $userId
     * @param string $sessionId
     * @return ShopCartItemEntity[]
     */
    public function getCartContent(?int $userId, string $sessionId): array
    {
        return ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
    }
}
