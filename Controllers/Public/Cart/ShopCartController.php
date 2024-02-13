<?php
namespace CMW\Controller\Shop\Public\Cart;

use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopCartController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopCartController extends AbstractController
{
    #[Link("/cart", Link::GET, [], "/shop")]
    public function publicCartView(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $asideCartContent = ShopCartItemModel::getInstance()->getShopCartsItemsAsideByUserId($userId,$sessionId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemsVariantes = ShopCartVariantesModel::getInstance();
        $appliedDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);

        $this->handleSessionHealth($sessionId);

        $this->handleItemHealth($userId, $sessionId);

        //TODO: Verifier si les promotions appliquées au panier sont encore valides

        foreach ($cartContent as $itemCart) {
            $itemId = $itemCart->getItem()->getId();
            $quantity = $itemCart->getQuantity();
            $this->handleStock($itemCart,$itemId,$quantity,$userId,$sessionId);
            $this->handleLimitePerUser($itemCart,$itemId,$quantity,$userId,$sessionId);
            $this->handleGlobalLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
            $this->handleByOrderLimit($itemCart,$itemId,$quantity,$userId,$sessionId);
        }

        $view = new View("Shop", "Cart/cart");
        $view->addVariableList(["cartContent" => $cartContent, "imagesItem" => $imagesItem,"defaultImage" => $defaultImage, "asideCartContent" => $asideCartContent, "itemsVariantes" => $itemsVariantes, "appliedDiscounts" => $appliedDiscounts]);
        $view->view();
    }

    /**
     * @param string $sessionId
     */
    private function handleSessionHealth(string $sessionId): void
    {
        if (!$sessionId) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param ?int $userId
     * @param string $sessionId
     */
    public function handleItemHealth(?int $userId, string $sessionId) : void
    {
        if (ShopCartItemModel::getInstance()->cartItemIdAsNullValue($userId, $sessionId)) {
            ShopCartItemModel::getInstance()->removeUnreachableItem($userId, $sessionId);
            Flash::send(Alert::ERROR, "Boutique", "Certain article du panier n'existe plus. et nous ne somme malheureusement pas en mesure de le récupérer.");
        }
    }

    public function handleStock (ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        $currentStock = ShopItemsModel::getInstance()->getItemCurrentStock($itemId);
        if ($quantity > $currentStock) {
            $quantity = $currentStock;
            if ($currentStock == 0) {
                ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                ShopCartItemModel::getInstance()->addToAsideCart($itemId, $userId, $sessionId);
                ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, 1);
                Flash::send(Alert::ERROR, "Boutique", "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est plus en stock, Nous l'avons mis dans votre panier 'Mise de côté'.");
                Redirect::redirect("shop/cart");
            } else {
                ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                Flash::send(Alert::ERROR, "Boutique", "Les stock pour <b>" . $itemCart->getItem()->getName() . "</b> on changé. Il n'en reste que $quantity en stock, Nous avons mis automatiquement à jour votre panier");
                Redirect::redirect("shop/cart");
            }
        }
    }

    public function handleLimitePerUser (ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        if (ShopItemsModel::getInstance()->itemHaveUserLimit($itemId)) {
            if (is_null($userId)) {
                Flash::send(Alert::ERROR, "Boutique", $itemCart->getItem()->getName() . " à besoin d'une vérification supplémentaire.");
                Redirect::redirect("login");
            }
            $numberBoughtByUser = ShopOrdersModel::getInstance()->countOrderByUserIdAndItemId($userId, $itemId);
            if ($numberBoughtByUser) {
                if ($quantity + $numberBoughtByUser > ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                    $quantity = ShopItemsModel::getInstance()->getItemUserLimit($itemId) - $numberBoughtByUser;
                    if ($quantity <= 0) {
                        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                        Flash::send(Alert::ERROR, "Boutique", "Vous n'êtes plus en mesure d'acheter <b>" . $itemCart->getItem()->getName() . "</b>.");
                        Redirect::redirect("shop/cart");
                    } else {
                        ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                        Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas acheter autant de " . $itemCart->getItem()->getName() . ". Nous avons mis à jour votre panier");
                        Redirect::redirect("shop/cart");
                    }
                }
            } else {
                if ($quantity > ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                    $quantity = ShopItemsModel::getInstance()->getItemUserLimit($itemId);
                    if ($quantity <= 0) {
                        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                        Flash::send(Alert::ERROR, "Boutique", "Vous n'êtes plus en mesure d'acheter <b>" . $itemCart->getItem()->getName() . "</b>.");
                        Redirect::redirect("shop/cart");
                    } else {
                        ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                        Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas acheter autant de " . $itemCart->getItem()->getName() . ". Nous avons mis à jour votre panier");
                        Redirect::redirect("shop/cart");
                    }
                }
            }
        }
    }

    public function handleGlobalLimit (ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        if (ShopItemsModel::getInstance()->itemHaveGlobalLimit($itemId)) {
            $itemGlobalLimit = ShopItemsModel::getInstance()->getItemGlobalLimit($itemId);
            if ($quantity > $itemGlobalLimit) {
                $quantity = $itemGlobalLimit;
                if ($itemGlobalLimit == 0) {
                    ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                    Flash::send(Alert::ERROR, "Boutique", "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est malheuresement plus à vendre");
                    Redirect::redirect("shop/cart");
                } else {
                    ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                    Flash::send(Alert::ERROR, "Boutique", "Les stock pour <b>" . $itemCart->getItem()->getName() . "</b> on changé.");
                    Redirect::redirect("shop/cart");
                }
            }
        }
    }

    public function handleByOrderLimit(ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        if (ShopItemsModel::getInstance()->itemHaveByOrderLimit($itemId)) {
            $itemByOrderLimit = ShopItemsModel::getInstance()->getItemByOrderLimit($itemId);
            if ($quantity > $itemByOrderLimit) {
                $quantity = $itemByOrderLimit;
                if ($itemByOrderLimit == 0) {
                    ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                    Flash::send(Alert::ERROR, "Boutique", "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est malheuresement plus à vendre");
                    Redirect::redirect("shop/cart");
                } else {
                    ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                    Flash::send(Alert::ERROR, "Boutique", "Les stock pour <b>" . $itemCart->getItem()->getName() . "</b> on changé.");
                    Redirect::redirect("shop/cart");
                }
            }
        }
    }
}