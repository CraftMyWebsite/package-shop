<?php

namespace CMW\Controller\Shop\Public\Command\Analyzer;

use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Package\AbstractController;

/**
 * Analyseur du contenu du panier.
 */
class ShopCartAnalyzer extends AbstractController
{
    /**
     * Vérifie si tous les articles du panier sont virtuels.
     *
     * @param ShopCartItemEntity[] $cartContent
     * @return bool
     */
    public static function isOnlyVirtual(array $cartContent): bool
    {
        foreach ($cartContent as $item) {
            if ($item->getItem()->getType() != 1) {
                return false;
            }
        }
        return true;
    }

    /**
     * Vérifie si tous les articles du panier sont gratuits.
     *
     * @param ShopCartItemEntity[] $cartContent
     * @return bool
     */
    public static function isCartFree(array $cartContent): bool
    {
        foreach ($cartContent as $item) {
            if ($item->getItem()->getPrice() > 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Récupère le type de prix utilisé dans le panier (money, token, etc.).
     *
     * @param ShopCartItemEntity[] $cartContent
     * @return string
     */
    public static function getPriceType(array $cartContent): string
    {
        foreach ($cartContent as $item) {
            return $item->getItem()->getPriceType() ?? 'money';
        }
        return 'money';
    }
}
