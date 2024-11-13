<?php
namespace CMW\Controller\Shop\Public\Cart;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Event\Users\LoginEvent;
use CMW\Manager\Events\Listener;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopActionsCartController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopActionsCartController extends AbstractController
{
    #[NoReturn]
    #[Link('/add_to_cart/:itemId', Link::GET, ['itemId' => '[0-9]+'], '/shop')]
    private function publicAddCart(int $itemId): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();
        $quantity = 1;

        if (ShopItemVariantModel::getInstance()->itemHasVariant($itemId)) {
            Flash::send(Alert::INFO, 'Boutique', "Vous devez sélectionner une variante avant de pouvoir ajouter l'article à votre panier");
            $itemUrl = ShopItemsModel::getInstance()->getShopItemsById($itemId)?->getItemLink();
            header('Location:' . $itemUrl);
            die();
        }

        $this->handleSessionHealth($sessionId);

        $this->handlePriceType($userId, $sessionId, $itemId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        $this->handleAddToCart($itemId, $userId, $sessionId, $quantity, null);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/cat/:catSlug/item/:itemSlug', Link::POST, ['.*?'], '/shop')]
    private function publicAddCartQuantity(string $catSlug, string $itemSlug): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        if (UsersController::isAdminLogged()) {
            $itemId = ShopItemsModel::getInstance()->getAdminShopItemIdBySlug($itemSlug);
        } else {
            $itemId = ShopItemsModel::getInstance()->getPublicShopItemIdBySlug($itemSlug);
        }
        [$quantity] = Utils::filterInput('quantity');

        $this->handlePriceType($userId, $sessionId, $itemId);

        $selectedVariants = $_POST['selected_variantes'];

        $this->handleSessionHealth($sessionId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        $this->handleAddToCart($itemId, $userId, $sessionId, $quantity, $selectedVariants);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/cart/discount/apply', Link::POST, [], '/shop')]
    private function publicTestAndApplyDiscountCode(): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();
        [$code] = Utils::filterInput('code');

        $itemsInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        foreach ($itemsInCart as $itemInCart) {
            if ($itemInCart->getItem()->getPriceType() !== 'money') {
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas appliqué de réduction sur ce type de monnaie');
                Redirect::redirectPreviousRoute();
            }
        }

        ShopHandlerDiscountController::getInstance()->discountMasterHandler($userId, $sessionId, $code);
    }

    #[NoReturn]
    #[Link('/cart/discount/remove/:discountId', Link::GET, [], '/shop')]
    private function publicRemoveDiscountCode(int $discountId): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        $itemInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);

        ShopCartDiscountModel::getInstance()->removeCode($cartId, $discountId);

        foreach ($itemInCart as $cartItem) {
            ShopCartItemModel::getInstance()->removeCodeToItem($userId, $sessionId, $cartItem->getItem()->getId(), $discountId);
        }

        Flash::send(Alert::SUCCESS, 'Boutique', 'Code promotionnel supprimé !');

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/cart/increase_quantity/:itemId', Link::GET, [], '/shop')]
    private function publicAddQuantity(int $itemId): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();
        $quantity = 1;

        $this->handleSessionHealth($sessionId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        $this->handleTotalCartDiscount($userId , $sessionId);

        ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/cart/decrease_quantity/:itemId', Link::GET, [], '/shop')]
    private function publicRemoveQuantity(int $itemId): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        $this->handleTotalCartDiscount($userId , $sessionId);

        $currentQuantity = ShopCartItemModel::getInstance()->getQuantity($itemId, $userId, $sessionId);

        if ($currentQuantity === 1) {
            ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
            if ($this->handleForceClearAllDiscount($userId, $sessionId)) {
                Flash::send(Alert::SUCCESS, 'Boutique', 'Article ' . ShopItemsModel::getInstance()->getShopItemsById($itemId)?->getName() . ' enlevé de votre panier (Vos promotions ont été réinitialisées)');
            } else {
                Flash::send(Alert::SUCCESS, 'Boutique', 'Article ' . ShopItemsModel::getInstance()->getShopItemsById($itemId)?->getName() . ' enlevé de votre panier');
            }
        }

        if ($currentQuantity <= 0) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                'Hep hep hep, pas de nombres négatifs mon chère');
            Redirect::redirectPreviousRoute();
        }

        ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, false);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/cart/remove/:itemId', Link::GET, [], '/shop')]
    private function publicRemoveItem(int $itemId): void
    {
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);

        if ($this->handleForceClearAllDiscount($userId, $sessionId)) {
            Flash::send(Alert::SUCCESS, 'Boutique', "Cet article n'est plus dans votre panier (Vos promotions ont été réinitialisées)");
        } else {
            Flash::send(Alert::SUCCESS, 'Boutique', "Cet article n'est plus dans votre panier");
        }

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    /*
     * -----------------------------------------------------------------------------------------------*\
     * ------------------------------------------HANDELER--------------------------------------------*\
     * -----------------------------------------------------------------------------------------------
     */

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     */
    private function handleAddToCart(int $itemId, ?int $userId, string $sessionId, int $quantity, ?array $selectedVariants): void
    {
        if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
            $cart = ShopCartItemModel::getInstance()->addToCart($itemId, $userId, $sessionId, $quantity);
            if (!empty($selectedVariants)) {
                foreach ($selectedVariants as $selectedVariant) {
                    ShopCartVariantesModel::getInstance()->setVariantToItemInCart($cart->getId(), $selectedVariant);
                }
            }

            if ($this->handleForceClearAllDiscount($userId, $sessionId)) {
                Flash::send(Alert::SUCCESS, 'Boutique', 'Article ajouté ! (Vos promotions ont été réinitialisées)');
            } else {
                Flash::send(Alert::SUCCESS, 'Boutique', 'Nouvel article ajouté au panier !');
            }
        } else {
            // TODO : Si l'article est une variante il faut verifier que l'utilisateur à choisis la même variante, si ce n'est pas le cas il faut ajouter l'article en plus !
            ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);
            Flash::send(Alert::SUCCESS, 'Boutique',
                'Vous aviez déjà cet article, nous avons rajouté une quantité pour vous');
        }
    }

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     */
    private function handleAddToCartVerification(int $itemId, ?int $userId, string $sessionId, int $quantity): void
    {
        $this->handleItemHealth($itemId);

        $stockStatus = $this->handleStock($itemId, $userId, $sessionId, $quantity);
        switch ($stockStatus) {
            case StockStatus::NOT_IN_STOCK_NOT_ASIDE:
                ShopCartItemModel::getInstance()->addToAsideCart($itemId, $userId, $sessionId);
                Flash::send(Alert::SUCCESS, 'Boutique', "Cet article n'est plus en stock. Mais nous l'avons ajouté au panier 'Mise de côté'.");
                Redirect::redirectPreviousRoute();
            case StockStatus::NOT_IN_STOCK_ASIDE:
                Flash::send(Alert::WARNING, 'Boutique', "Cet article est déjà dans le panier 'Mise de côté', les stock ne sont pas mis à jour.");
                Redirect::redirectPreviousRoute();
            case StockStatus::IN_STOCK_ASIDE:
                ShopCartItemModel::getInstance()->switchAsideToCart($itemId, $userId, $sessionId);
                Redirect::redirectPreviousRoute();
            case StockStatus::CART_OVER_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', 'Navré mais il ne reste que ' . ShopItemsModel::getInstance()->getItemCurrentStock($itemId) . ' articles en stock.');
                Redirect::redirectPreviousRoute();
            case StockStatus::CART_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas ajouter plus de quantité pour cet article dans le panier.');
                Redirect::redirectPreviousRoute();
            case StockStatus::PASS:
                break;
        }

        $limitPerUserStatus = $this->handleLimitPerUser($itemId, $userId, $sessionId, $quantity);
        switch ($limitPerUserStatus) {
            case LimitPerUserStatus::USER_NOT_CONNECTED:
                Flash::send(Alert::WARNING, 'Boutique', ShopItemsModel::getInstance()->getShopItemsById($itemId)->getName() . " à besoin d'une vérification supplémentaire pour être ajouté au panier.");
                Redirect::redirect('login');
            case LimitPerUserStatus::BOUGHT_NOT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', "Vous avez déjà atteint le nombre maximum d'achat de cet article.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::BOUGHT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::NOT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas ajouter ' . $quantity . ' quantité pour cet article dans le panier.');
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::PASS:
                break;
        }

        $globalLimitStatus = $this->handleGlobalLimit($itemId, $userId, $sessionId, $quantity);
        switch ($globalLimitStatus) {
            case GlobalLimitStatus::NOT_IN_CART_GLOBAL_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas en ajouter autant dans votre panier.');
                Redirect::redirectPreviousRoute();
            case GlobalLimitStatus::IN_CART_GLOBAL_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case GlobalLimitStatus::PASS:
                break;
        }

        $orderLimitStatus = $this->handleByOrderLimit($itemId, $userId, $sessionId, $quantity);
        switch ($orderLimitStatus) {
            case ByOrderLimitStatus::NOT_IN_CART_ORDER_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas en ajouter autant par commande dans votre panier.');
                Redirect::redirectPreviousRoute();
            case ByOrderLimitStatus::IN_CART_ORDER_LIMIT_REACHED:
                Flash::send(Alert::WARNING, 'Boutique', "Vous ne pouvez pas en rajouter d'avantage dans votre panier. Veuillez contacter le support pour plus d'informations.");
                Redirect::redirectPreviousRoute();
            case ByOrderLimitStatus::PASS:
                break;
        }
    }

    private function handlePriceType(?int $userId, string $sessionId, int $itemId): void
    {
        $itemsInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $thisItem = ShopItemsModel::getInstance()->getShopItemsById($itemId);
        foreach ($itemsInCart as $itemInCart) {
            if ($thisItem->getPriceType() !== $itemInCart->getItem()->getPriceType()) {
                Flash::send(Alert::WARNING, 'Boutique', 'Vous ne pouvez pas acheter des articles avec des monnaies différentes.');
                Redirect::redirectPreviousRoute();
            }
        }
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
     * @param int $itemId
     */
    private function handleItemHealth(int $itemId): void
    {
        if (ShopItemsModel::getInstance()->itemStillExist($itemId) || ShopItemsModel::getInstance()->isArchivedItem($itemId)) {
            Flash::send(Alert::WARNING, 'Boutique', "Nous somme désolé mais l'article que vous essayez d'ajouter au panier n'est plus en vente.");
            Redirect::redirectPreviousRoute();
        }
    }

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     * @return \CMW\Controller\Shop\Public\Cart\StockStatus
     */
    private function handleStock(int $itemId, ?int $userId, string $sessionId, int $quantity): StockStatus
    {
        $itemNotInStock = ShopItemsModel::getInstance()->itemNotInStock($itemId);
        $alreadyAside = ShopCartItemModel::getInstance()->isAlreadyAside($itemId, $userId, $sessionId);
        $itemInCart = ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId);
        $currentStock = ShopItemsModel::getInstance()->getItemCurrentStock($itemId);

        if ($itemNotInStock) {
            return $alreadyAside ? StockStatus::NOT_IN_STOCK_ASIDE : StockStatus::NOT_IN_STOCK_NOT_ASIDE;
        }

        if ($alreadyAside) {
            return StockStatus::IN_STOCK_ASIDE;
        }

        if (!$itemInCart) {
            $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId, $userId, $sessionId);
            if ($cartItem->getQuantity() + $quantity > $currentStock) {
                return StockStatus::CART_LIMIT_REACHED;
            }
        } else {
            if ($quantity > $currentStock) {
                return StockStatus::CART_OVER_LIMIT_REACHED;
            }
        }

        return StockStatus::PASS;
    }

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     * @return \CMW\Controller\Shop\Public\Cart\LimitPerUserStatus
     */
    private function handleLimitPerUser(int $itemId, ?int $userId, string $sessionId, int $quantity): LimitPerUserStatus
    {
        if (ShopItemsModel::getInstance()->itemHaveUserLimit($itemId)) {
            if (is_null($userId)) {
                return LimitPerUserStatus::USER_NOT_CONNECTED;
            }
            $numberBoughtByUser = ShopHistoryOrdersModel::getInstance()->countOrderByUserIdAndItemId($userId, $itemId);
            if ($numberBoughtByUser) {
                if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                    if ($numberBoughtByUser >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::BOUGHT_NOT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                } else {
                    $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId, $userId, $sessionId);
                    if ($cartItem->getQuantity() + $numberBoughtByUser >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::BOUGHT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                }
            } else {
                if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                    if ($quantity > ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::NOT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                } else {
                    $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId, $userId, $sessionId);
                    if ($cartItem->getQuantity() >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::IN_CART_ITEM_LIMIT_REACHED;
                    }
                }
            }
        }
        return LimitPerUserStatus::PASS;
    }

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     * @return \CMW\Controller\Shop\Public\Cart\GlobalLimitStatus
     */
    private function handleGlobalLimit(int $itemId, ?int $userId, string $sessionId, int $quantity): GlobalLimitStatus
    {
        $itemGlobalLimit = ShopItemsModel::getInstance()->getItemGlobalLimit($itemId);
        if (ShopItemsModel::getInstance()->itemHaveGlobalLimit($itemId)) {
            if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                if ($quantity > $itemGlobalLimit) {
                    return GlobalLimitStatus::NOT_IN_CART_GLOBAL_LIMIT_REACHED;
                }
            } else {
                $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId, $userId, $sessionId);
                if ($cartItem->getQuantity() >= $itemGlobalLimit) {
                    return GlobalLimitStatus::IN_CART_GLOBAL_LIMIT_REACHED;
                }
            }
        }
        return GlobalLimitStatus::PASS;
    }

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     * @return \CMW\Controller\Shop\Public\Cart\ByOrderLimitStatus
     */
    private function handleByOrderLimit(int $itemId, ?int $userId, string $sessionId, int $quantity): ByOrderLimitStatus
    {
        $itemByOrderLimit = ShopItemsModel::getInstance()->getItemByOrderLimit($itemId);
        if (ShopItemsModel::getInstance()->itemHaveByOrderLimit($itemId)) {
            if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                if ($quantity > $itemByOrderLimit) {
                    return ByOrderLimitStatus::NOT_IN_CART_ORDER_LIMIT_REACHED;
                }
            } else {
                // est dans le panier
                $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId, $userId, $sessionId);
                if ($cartItem->getQuantity() >= $itemByOrderLimit) {
                    return ByOrderLimitStatus::IN_CART_ORDER_LIMIT_REACHED;
                }
            }
        }
        return ByOrderLimitStatus::PASS;
    }

    private function handleForceClearAllDiscount(?int $userId, string $sessionId): bool
    {
        $appliedDiscount = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);
        if (!empty($appliedDiscount)) {
            $itemInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
            foreach ($appliedDiscount as $discount) {
                ShopCartDiscountModel::getInstance()->removeCode($discount->getCart()->getId(), $discount->getDiscount()->getId());
                foreach ($itemInCart as $cartItem) {
                    ShopCartItemModel::getInstance()->removeCodeToItem($userId, $sessionId, $cartItem->getItem()->getId(), $discount->getDiscount()->getId());
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param ?int $userId
     * @param string $sessionId
     */
    private function handleTotalCartDiscount(?int $userId, string $sessionId): void
    {
        $discountCart = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);
        $entityFound = 0;
        foreach ($discountCart as $discount) {
            if ($discount->getDiscount()->getLinked() == 4 || $discount->getDiscount()->getLinked() == 3) {
                $entityFound = 1;
            }
        }
        if ($entityFound == 1) {
            if ($this->handleForceClearAllDiscount($userId, $sessionId)) {
                Flash::send(Alert::INFO, 'Boutique', 'Vos promotions de panier ont été réinitialisées, appliquer la à nouveau pour verifier si vous pouvez toujours en bénéficier');
            }
        }
    }

    /*
     * -----------------------------------------------------------------------------------------------*\
     * ------------------------------------------EVENTS----------------------------------------------*\
     * -----------------------------------------------------------------------------------------------
     */
    #[Listener(eventName: LoginEvent::class, times: 0, weight: 1)]
    public static function onLogin(mixed $userId): void
    {
        $sessionId = session_id();
        if ($sessionId) {
            $cart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId(null, $sessionId);
            foreach ($cart as $car) {
                if (!ShopCartItemModel::getInstance()->userHaveAlreadyItemInCart($car->getItem()->getId(), $userId)) {
                    ShopCartModel::getInstance()->switchSessionToUserCart($sessionId, $userId);
                    ShopCartModel::getInstance()->removeSessionCart($sessionId);
                    if (!is_null($userId)) {
                        ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
                    }
                }
            }
        }
    }
}

/*
 * ENUMS
 */
enum StockStatus
{
    case NOT_IN_STOCK_NOT_ASIDE;
    case NOT_IN_STOCK_ASIDE;
    case IN_STOCK_ASIDE;
    case CART_LIMIT_REACHED;
    case CART_OVER_LIMIT_REACHED;
    case PASS;
}

enum LimitPerUserStatus
{
    case USER_NOT_CONNECTED;
    case BOUGHT_NOT_IN_CART_ITEM_LIMIT_REACHED;
    case BOUGHT_IN_CART_ITEM_LIMIT_REACHED;
    case NOT_IN_CART_ITEM_LIMIT_REACHED;
    case IN_CART_ITEM_LIMIT_REACHED;
    case PASS;
}

enum GlobalLimitStatus
{
    case NOT_IN_CART_GLOBAL_LIMIT_REACHED;
    case IN_CART_GLOBAL_LIMIT_REACHED;
    case PASS;
}

enum ByOrderLimitStatus
{
    case NOT_IN_CART_ORDER_LIMIT_REACHED;
    case IN_CART_ORDER_LIMIT_REACHED;
    case PASS;
}
