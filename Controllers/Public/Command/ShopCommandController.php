<?php

namespace CMW\Controller\Shop\Public\Command;

use CMW\Controller\Shop\Public\Cart\ShopCartController;
use CMW\Controller\Shop\Public\Command\Analyzer\ShopCartAnalyzer;
use CMW\Controller\Shop\Public\Command\Renderer\ShopCommandStepRenderer;
use CMW\Controller\Shop\Public\Command\Service\ShopCartValidationService;
use CMW\Controller\Shop\Public\Command\Service\ShopCommandFinalizerService;
use CMW\Controller\Shop\Public\Command\Service\ShopCommandService;
use CMW\Controller\Shop\Public\Command\Service\ShopCommandStepService;
use CMW\Controller\Shop\Public\Command\Service\ShopDiscountService;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Delivery\ShopDeliveryUserAddressModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopCommandController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopCommandController extends AbstractController
{
    #[Link('/command', Link::GET, [], '/shop')]
    private function publicCommandView(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        // Maintenance & Auth
        ShopCommandService::getInstance()->checkMaintenanceOrRedirect();
        ShopCommandService::getInstance()->checkUserSessionOrRedirect();

        // Vérifie les statuts des promos expirées
        ShopDiscountModel::getInstance()->autoStatusChecker();

        // Récupère le panier
        $cartContent = ShopCommandService::getInstance()->getCartContent($userId, $sessionId);

        if (empty($cartContent)) {
            Flash::send(Alert::ERROR, 'Boutique', 'Votre panier est vide.');
            Redirect::redirect('shop/cart');
        }

        // Réductions appliquées
        $appliedCartDiscounts = ShopDiscountService::getInstance()->getAppliedCartDiscounts($userId, $sessionId);

        // Récupère les adresses
        $userAddresses = ShopCommandService::getInstance()->isShopVirtualOnly() ? [] : ShopDeliveryUserAddressModel::getInstance()->getShopDeliveryUserAddressByUserId($userId);

        // Initialise le tunnel si besoin
        if (!ShopCommandTunnelModel::getInstance()->tunnelExist($userId)) {
            ShopCommandTunnelModel::getInstance()->createTunnel($userId);
        }

        // Vérifie la santé des items
        ShopCartController::getInstance()->handleItemHealth($userId, $sessionId);
        ShopCartController::getInstance()->handleIncompatibleCartItems($userId, $sessionId);

        // Vérifications pré-commande
        ShopCartValidationService::getInstance()->validateBeforeCommand($userId,$sessionId,$cartContent);

        // Analyse du panier
        $cartOnlyVirtual = ShopCartAnalyzer::isOnlyVirtual($cartContent);
        $cartIsFree = ShopCartAnalyzer::isCartFree($cartContent);
        $priceType = ShopCartAnalyzer::getPriceType($cartContent);

        // Vérifie si des réductions doivent être supprimées
        ShopDiscountService::getInstance()->cleanupExpiredCartDiscounts($userId, $sessionId , $cartContent);

        // Gère les étapes du tunnel
        ShopCommandStepRenderer::getInstance()->render($userId, $sessionId, $cartContent, $cartOnlyVirtual, $cartIsFree, $priceType, $appliedCartDiscounts, $userAddresses);
    }

    #[NoReturn] #[Link('/command/createAddress', Link::POST, ['.*?'], '/shop')]
    private function publicCreateAddressPost(): void
    {
        if (ShopCommandService::getInstance()->isShopVirtualOnly()) {
            Redirect::redirectPreviousRoute();
        }
        ShopCommandStepService::getInstance()->createAddress();
    }

    #[NoReturn] #[Link('/command/addAddress', Link::POST, ['.*?'], '/shop')]
    private function publicAddAddressPost(): void
    {
        if (ShopCommandService::getInstance()->isShopVirtualOnly()) {
            Redirect::redirectPreviousRoute();
        }
        ShopCommandStepService::getInstance()->addAddress();
    }

    #[NoReturn]
    #[Link('/command/toDelivery', Link::POST, ['.*?'], '/shop')]
    private function publicToDeliveryPost(): void
    {
        if (ShopCommandService::getInstance()->isShopVirtualOnly()) {
            Redirect::redirectPreviousRoute();
        }
        ShopCommandStepService::getInstance()->toDelivery();
    }

    #[NoReturn]
    #[Link('/command/toAddress', Link::POST, ['.*?'], '/shop')]
    private function publicToAddressPost(): void
    {
        if (ShopCommandService::getInstance()->isShopVirtualOnly()) {
            Redirect::redirectPreviousRoute();
        }
        ShopCommandStepService::getInstance()->toAddress();
    }

    #[NoReturn]
    #[Link('/command/toShipping', Link::POST, ['.*?'], '/shop')]
    private function publicToShippingPost(): void
    {
        ShopCommandStepService::getInstance()->toShipping();
    }

    #[NoReturn]
    #[Link('/command/toPayment', Link::POST, ['.*?'], '/shop')]
    private function publicToPaymentPost(): void
    {
        ShopCommandStepService::getInstance()->toPayment();
    }

    #[NoReturn]
    #[Link('/command/finalize', Link::POST, [], '/shop')]
    private function publicFinalizeCommand(): void
    {
        ShopCommandFinalizerService::getInstance()->finalize();
    }
}
