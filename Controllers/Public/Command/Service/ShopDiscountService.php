<?php

namespace CMW\Controller\Shop\Public\Command\Service;

use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Utils\Redirect;

/**
 * Service pour la gestion des réductions panier.
 */
class ShopDiscountService extends AbstractController
{
    /**
     * Vérifie et supprime les réductions expirées.
     *
     * @param int|null $userId
     * @param string $sessionId
     * @param ShopCartItemEntity[] $cartContent
     * @return void
     */
    public function cleanupExpiredCartDiscounts(?int $userId, string $sessionId, array $cartContent): void
    {
        $appliedDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);
        if (empty($appliedDiscounts)) {
            return;
        }

        $cart = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId);
        $hasRemoved = false;

        foreach ($appliedDiscounts as $discountLink) {
            $discount = $discountLink->getDiscount();
            if ($discount->getStatus() === 0) {
                $hasRemoved = true;

                ShopCartDiscountModel::getInstance()->removeCode($cart->getId(), $discount->getId());

                foreach ($cartContent as $item) {
                    ShopCartItemModel::getInstance()->removeCodeToItem(
                        $userId,
                        $sessionId,
                        $item->getItem()->getId(),
                        $discount->getId()
                    );
                }
            }
        }

        if ($hasRemoved) {
            Flash::send(Alert::INFO, 'Boutique', 'Certaines promotions ne sont plus disponibles !');
            Redirect::redirect('shop/command');
        }
    }

    /**
     * Récupère uniquement les réductions appliquées liées à des paniers (linked = 3 ou 4).
     *
     * @param int|null $userId
     * @param string $sessionId
     * @return array
     */
    public function getAppliedCartDiscounts(?int $userId, string $sessionId): array
    {
        $cartDiscountModel = ShopCartDiscountModel::getInstance();
        $discounts = [];

        $cartDiscounts = $cartDiscountModel->getCartDiscountByUserId($userId, $sessionId);

        foreach ($cartDiscounts as $cartDiscount) {
            $discount = $cartDiscountModel->getCartDiscountById($cartDiscount->getId());
            if (in_array($discount->getDiscount()->getLinked(), [3, 4])) {
                $discounts[] = $discount->getDiscount();
            }
        }

        return $discounts;
    }

}
