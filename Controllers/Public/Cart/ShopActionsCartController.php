<?php
namespace CMW\Controller\Shop\Public\Cart;

use CMW\Event\Users\LoginEvent;
use CMW\Manager\Events\Listener;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Command\ShopCommandTunnelModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Item\ShopItemVariantModel;
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Model\Users\UsersModel;
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
    #[NoReturn] #[Link("/add_to_cart/:itemId", Link::GET, [], "/shop")]
    public function publicAddCart(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        $quantity = 1;

        if (ShopItemVariantModel::getInstance()->itemHasVariant($itemId)) {
            Flash::send(Alert::ERROR ,"Boutique", "Vous devez sélectionner une variante avant de pouvoir ajouter l'article à votre panier");
            $itemUrl = ShopItemsModel::getInstance()->getShopItemsById($itemId)->getItemLink();
            header("Location:" . $itemUrl);
            die();
        }

        $this->handleSessionHealth($sessionId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        $this->handleAddToCart($itemId, $userId, $sessionId, $quantity, null);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cat/:catSlug/item/:itemSlug", Link::POST, ['.*?'], "/shop")]
    public function publicAddCartQuantity(Request $request, string $catSlug, string $itemSlug): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        $itemId = ShopItemsModel::getInstance()->getShopItemIdBySlug($itemSlug);
        [$quantity] = Utils::filterInput('quantity');

        $selectedVariants = $_POST['selected_variantes'];

        $this->handleSessionHealth($sessionId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        $this->handleAddToCart($itemId, $userId, $sessionId, $quantity, $selectedVariants);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/discount/apply", Link::POST, [], "/shop")]
    public function publicTestAndApplyDiscountCode(): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        [$code] = Utils::filterInput('code');
        $itemInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);

        if ($code == "") {
            Flash::send(Alert::ERROR, "Boutique", "Vous n'avez pas entré de code");
            Redirect::redirectPreviousRoute();
        }

        $codeExist = ShopDiscountModel::getInstance()->codeExist($code);

        if ($codeExist) {
            // handle if the code is already used for this cart
            foreach (ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId) as $discount) {
                if ($discount->getDiscount()->getCode() == $code) {
                    Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà appliqué ce code de réduction !");
                    Redirect::redirectPreviousRoute();
                }
                if ($discount->getDiscount()->getCumulative() !== 1) {
                    Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà une promotion non cumulable appliquée");
                    Redirect::redirectPreviousRoute();
                }
            }

            $discountCode = ShopDiscountModel::getInstance()->getShopDiscountsByCode($code);

            // handle if the code is between start and end date?
            if (!$this->checkDate($discountCode->getStartDate(), $discountCode->getEndDate())) {
                Flash::send(Alert::ERROR, "Boutique", "Ce code n'est plus actif ou ne l'est pas encore");
                Redirect::redirectPreviousRoute();
            }

            // handle if the code have left uses?
            if ($discountCode->getUsesLeft() !== null && $discountCode->getUsesLeft() <= 0) {
                Flash::send(Alert::ERROR, "Boutique", "Ce code n'est plus utilisable");
                Redirect::redirectPreviousRoute();
            }

            // handle if the code can be used multiple by user and if user have used it in past order?
            if ($discountCode->getUsesMultipleByUser() == 1) {
                $orders = ShopOrdersModel::getInstance()->getOrdersByUserId($userId);
                foreach ($orders as $order) {
                    $orderItems = ShopOrdersItemsModel::getInstance()->getOrdersItemsByOrderId($order->getOrderId());
                        foreach ($orderItems as $orderItem) {
                            if ($orderItem->getDiscount() !== null && $code == $orderItem->getDiscount()->getCode()) {
                                Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà utiliser ce code");
                                Redirect::redirectPreviousRoute();
                            }
                    }
                }
            }

            // handle if the code need to have ordered before use
            if ($discountCode->getUserHaveOrderBeforeUse() == 1) {
                $orders = ShopOrdersModel::getInstance()->getOrdersByUserId($userId);
                if (empty($orders)) {
                    Flash::send(Alert::ERROR, "Boutique", "Vous devez avoir passé au moins une commande avant de pour pouvoir utiliser ce code");
                    Redirect::redirectPreviousRoute();
                }
            }

            // handle if the code can be applied to items in cart?
            if ($discountCode->getLinked() != 0) {
                //linked to items
                if ($discountCode->getLinked() == 1) {
                    $itemFounded = 0;
                    foreach ($itemInCart as $cartItem) {
                        $discountItems = ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByItemId($cartItem->getItem()->getId());
                        if (empty($discountItems)) {
                            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas lié à " . $cartItem->getItem()->getName());
                        } else {
                            foreach ($discountItems as $discountItem) {
                                Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getName());
                                ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                $itemFounded = 1;
                            }
                        }
                    }
                    if ($itemFounded === 1) {
                        ShopCartDiscountModel::getInstance()->applyCode($userId, $sessionId, $discountCode->getId());
                    }
                }
                //linked to categories
                if ($discountCode->getLinked() == 2) {
                    $catFounded = 0;
                    foreach ($itemInCart as $cartItem) {
                        $discountCategories = ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesByCategoryId($cartItem->getItem()->getCategory()->getId());
                        if (empty($discountCategories)) {
                            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas lié à " . $cartItem->getItem()->getCategory()->getName());
                        } else {
                            foreach ($discountCategories as $discountCategory) {
                                Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getCategory()->getName());
                                ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                $catFounded = 1;
                            }
                        }
                    }
                    if ($catFounded === 1) {
                        ShopCartDiscountModel::getInstance()->applyCode($userId, $sessionId, $discountCode->getId());
                    }
                }
                Redirect::redirectPreviousRoute();
            }

            foreach ($itemInCart as $cartItem) {
                ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
            }
            ShopCartDiscountModel::getInstance()->applyCode($userId, $sessionId, $discountCode->getId());
            Flash::send(Alert::SUCCESS, "Boutique", "Code promotionnel appliqué avec succès !");
            Redirect::redirectPreviousRoute();
        } else {
            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas valable");
            Redirect::redirectPreviousRoute();
        }
    }

    private function checkDate($startDate, $endDate): bool
    {
        $currentTime = time();
        $startDate = strtotime($startDate);
        if ($endDate !== null) {
            $endDate = strtotime($endDate);
            return ($currentTime >= $startDate && $currentTime <= $endDate);
        } else {
            return ($currentTime >= $startDate);
        }
    }

    #[NoReturn] #[Link("/cart/discount/remove/:discountId", Link::GET, [], "/shop")]
    public function publicRemoveDiscountCode(Request $request, int $discountId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        $cartId = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId)->getId();
        $itemInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);

        ShopCartDiscountModel::getInstance()->removeCode($cartId ,$discountId);

        foreach ($itemInCart as $cartItem) {
            ShopCartItemModel::getInstance()->removeCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(),$discountId);
        }

        Flash::send(Alert::SUCCESS, "Boutique", "Code promotionnel supprimé avec succès !");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/increase_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicAddQuantity(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();
        $quantity = 1;

        $this->handleSessionHealth($sessionId);

        $this->handleAddToCartVerification($itemId, $userId, $sessionId, $quantity);

        //TODO : Gérer les promotions

        ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/decrease_quantity/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveQuantity(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        //TODO : Gérer les promotions

        $currentQuantity = ShopCartItemModel::getInstance()->getQuantity($itemId, $userId, $sessionId);

        if ($currentQuantity === 1) {
            ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                "Article " . ShopItemsModel::getInstance()->getShopItemsById($itemId)?->getName() . " enlevé de votre panier");
        }

        if ($currentQuantity <= 0) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                "Hep hep hep, pas de nombres négatifs mon chère");
            Redirect::redirectPreviousRoute();
        }

        ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, false);

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/cart/remove/:itemId", Link::GET, [], "/shop")]
    public function publicRemoveItem(Request $request, int $itemId): void
    {
        $userId = UsersModel::getCurrentUser()?->getId();
        $sessionId = session_id();

        $this->handleSessionHealth($sessionId);

        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);

        //TODO : Virer les pormotions

        Flash::send(Alert::SUCCESS, "Boutique", "Cet article n'est plus dans votre panier");

        if (!is_null($userId)) {
            ShopCommandTunnelModel::getInstance()->clearTunnel($userId);
        }

        Redirect::redirectPreviousRoute();
    }

    /*-----------------------------------------------------------------------------------------------*\
     * ------------------------------------------HANDELER--------------------------------------------*\
     *-----------------------------------------------------------------------------------------------*/

    /**
     * @param int $itemId
     * @param ?int $userId
     * @param string $sessionId
     * @param int $quantity
     */
    private function handleAddToCart(int $itemId, ?int $userId, string $sessionId, int $quantity, ?array $selectedVariants) : void
    {
        if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
            $cart = ShopCartItemModel::getInstance()->addToCart($itemId, $userId, $sessionId, $quantity);
            if (!empty($selectedVariants)) {
                foreach ($selectedVariants as $selectedVariant) {
                    ShopCartVariantesModel::getInstance()->setVariantToItemInCart($cart->getId(), $selectedVariant);
                }
            }
            Flash::send(Alert::SUCCESS, "Boutique",
                "Nouvel article ajouté au panier !");
        } else {
            // TODO : Si l'article est une variante il faut verifier que l'utilisateur à choisis la même variante, si ce n'est pas le cas il faut ajouter l'article en plus !
            ShopCartItemModel::getInstance()->increaseQuantity($itemId, $userId, $sessionId, true);
            Flash::send(Alert::SUCCESS, "Boutique",
                "Vous aviez déjà cet article, nous avons rajouté une quantité pour vous");
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
                Flash::send(Alert::SUCCESS, "Boutique", "Cet article n'est plus en stock. Mais nous l'avons ajouté au panier 'Mise de côté'.");
                Redirect::redirectPreviousRoute();
            case StockStatus::NOT_IN_STOCK_ASIDE:
                Flash::send(Alert::ERROR, "Boutique", "Cet article est déjà dans le panier 'Mise de côté', les stock ne sont pas mis à jour.");
                Redirect::redirectPreviousRoute();
            case StockStatus::IN_STOCK_ASIDE:
                ShopCartItemModel::getInstance()->switchAsideToCart($itemId, $userId, $sessionId);
                Redirect::redirectPreviousRoute();
            case StockStatus::CART_OVER_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Navré mais il ne reste que " . ShopItemsModel::getInstance()->getItemCurrentStock($itemId) . " articles en stock.");
                Redirect::redirectPreviousRoute();
            case StockStatus::CART_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas ajouter plus de quantité pour cet article dans le panier.");
                Redirect::redirectPreviousRoute();
            case StockStatus::PASS:
                break;
        }

        $limitPerUserStatus = $this->handleLimitPerUser($itemId, $userId, $sessionId, $quantity);
        switch ($limitPerUserStatus) {
            case LimitPerUserStatus::USER_NOT_CONNECTED:
                Flash::send(Alert::ERROR, "Boutique", ShopItemsModel::getInstance()->getShopItemsById($itemId)->getName() ." à besoin d'une vérification supplémentaire pour être ajouté au panier.");
                Redirect::redirect("login");
            case LimitPerUserStatus::BOUGHT_NOT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà atteint le nombre maximum d'achat de cet article.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::BOUGHT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::NOT_IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas ajouter " . $quantity. " quantité pour cet article dans le panier.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::IN_CART_ITEM_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case LimitPerUserStatus::PASS:
                break;
        }

        $globalLimitStatus = $this->handleGlobalLimit($itemId, $userId, $sessionId, $quantity);
        switch ($globalLimitStatus) {
            case GlobalLimitStatus::NOT_IN_CART_GLOBAL_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en ajouter autant dans votre panier.");
                Redirect::redirectPreviousRoute();
            case GlobalLimitStatus::IN_CART_GLOBAL_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en rajouter d'avantage dans votre panier.");
                Redirect::redirectPreviousRoute();
            case GlobalLimitStatus::PASS:
                break;
        }

        $orderLimitStatus = $this->handleByOrderLimit($itemId, $userId, $sessionId, $quantity);
        switch ($orderLimitStatus) {
            case ByOrderLimitStatus::NOT_IN_CART_ORDER_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en ajouter autant par commande dans votre panier.");
                Redirect::redirectPreviousRoute();
            case ByOrderLimitStatus::IN_CART_ORDER_LIMIT_REACHED:
                Flash::send(Alert::ERROR, "Boutique", "Vous ne pouvez pas en rajouter d'avantage dans votre panier. Veuillez contacter le support pour plus d'informations.");
                Redirect::redirectPreviousRoute();
            case ByOrderLimitStatus::PASS:
                break;
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
    private function handleItemHealth(int $itemId) : void
    {
        if (ShopItemsModel::getInstance()->itemStillExist($itemId) || ShopItemsModel::getInstance()->isArchivedItem($itemId)) {
            Flash::send(Alert::ERROR, "Boutique", "Nous somme désolé mais l'article que vous essayez d'ajouter au panier n'existe plus.");
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
            $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId,$userId,$sessionId);
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
    private function handleLimitPerUser(int $itemId, ?int $userId, string $sessionId, int $quantity) : LimitPerUserStatus
    {
        if (ShopItemsModel::getInstance()->itemHaveUserLimit($itemId)) {
            if (is_null($userId)) {
                return LimitPerUserStatus::USER_NOT_CONNECTED;
            }
            $numberBoughtByUser = ShopOrdersModel::getInstance()->countOrderByUserIdAndItemId($userId, $itemId);
            if ($numberBoughtByUser) {
                if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                    if ($numberBoughtByUser >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::BOUGHT_NOT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                } else {
                    $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId,$userId,$sessionId);
                    if ($cartItem->getQuantity() + $numberBoughtByUser >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::BOUGHT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                }
            } else {
                if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                    if ($quantity >= ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                        return LimitPerUserStatus::NOT_IN_CART_ITEM_LIMIT_REACHED;
                    }
                } else {
                    $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId,$userId,$sessionId);
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
    private function handleGlobalLimit(int $itemId, ?int $userId, string $sessionId, int $quantity) : GlobalLimitStatus
    {
        $itemGlobalLimit = ShopItemsModel::getInstance()->getItemGlobalLimit($itemId);
        if (ShopItemsModel::getInstance()->itemHaveGlobalLimit($itemId)) {
            if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                if ($quantity > $itemGlobalLimit) {
                    return GlobalLimitStatus::NOT_IN_CART_GLOBAL_LIMIT_REACHED;
                }
            } else {
                $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId,$userId,$sessionId);
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
    private function handleByOrderLimit(int $itemId, ?int $userId, string $sessionId, int $quantity) : ByOrderLimitStatus
    {
        $itemByOrderLimit = ShopItemsModel::getInstance()->getItemByOrderLimit($itemId);
        if (ShopItemsModel::getInstance()->itemHaveByOrderLimit($itemId)) {
            if (ShopCartItemModel::getInstance()->itemIsInCart($itemId, $userId, $sessionId)) {
                if ($quantity > $itemByOrderLimit) {
                    return ByOrderLimitStatus::NOT_IN_CART_ORDER_LIMIT_REACHED;
                }
            } else {
                //est dans le panier
                $cartItem = ShopCartItemModel::getInstance()->getShopCartsByItemIdAndUserId($itemId,$userId,$sessionId);
                if ($cartItem->getQuantity() >= $itemByOrderLimit) {
                    return ByOrderLimitStatus::IN_CART_ORDER_LIMIT_REACHED;
                }
            }
        }
        return ByOrderLimitStatus::PASS;
    }


    /*-----------------------------------------------------------------------------------------------*\
     * ------------------------------------------EVENTS----------------------------------------------*\
     *-----------------------------------------------------------------------------------------------*/
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
     * */
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