<?php

namespace CMW\Controller\Shop\Public\Command\Service;

use CMW\Controller\Shop\Public\Cart\ShopCartController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Package\AbstractController;

/**
 * Service de validation des articles dans le panier avant commande.
 */
class ShopCartValidationService extends AbstractController
{
    /**
     * Applique toutes les règles de validation panier (stock, limites, brouillon...).
     *
     * @param int $userId
     * @param string $sessionId
     * @param ShopCartItemEntity[] $cartContent
     */
    public function validateBeforeCommand(int $userId, string $sessionId, array $cartContent): void
    {
        foreach ($cartContent as $itemCart) {
            $itemId = $itemCart->getItem()->getId();
            $quantity = $itemCart->getQuantity();

            $cartCtrl = ShopCartController::getInstance();

            $cartCtrl->handleStock($itemCart, $itemId, $quantity, $userId, $sessionId);
            $cartCtrl->handleLimitePerUser($itemCart, $itemId, $quantity, $userId, $sessionId);
            $cartCtrl->handleGlobalLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            $cartCtrl->handleByOrderLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            $cartCtrl->handleDraft($itemCart, $itemId, $userId, $sessionId);
        }
    }
}
