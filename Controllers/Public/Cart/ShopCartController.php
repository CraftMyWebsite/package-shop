<?php
namespace CMW\Controller\Shop\Public\Cart;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Shop\Carts\ShopCartItemEntity;
use CMW\Entity\Shop\Enum\Item\ShopItemType;
use CMW\Entity\Shop\Enum\Shop\ShopType;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Cart\ShopCartModel;
use CMW\Model\Shop\Cart\ShopCartVariantesModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\HistoryOrder\ShopHistoryOrdersModel;
use CMW\Model\Shop\Image\ShopImagesModel;
use CMW\Model\Shop\Item\ShopItemsModel;
use CMW\Model\Shop\Setting\ShopSettingsModel;
use CMW\Utils\Redirect;

/**
 * Class: @ShopCartController
 * @package shop
 * @author Zomblard
 * @version 0.0.1
 */
class ShopCartController extends AbstractController
{
    #[Link('/cart', Link::GET, [], '/shop')]
    private function publicCartView(): void
    {
        $maintenance = ShopSettingsModel::getInstance()->getSettingValue('maintenance');
        if ($maintenance) {
            if (UsersController::isAdminLogged()) {
                Flash::send(Alert::INFO, 'Boutique', 'Shop est en maintenance, mais vous y avez accès car vous êtes administrateur');
            } else {
                $maintenanceMessage = ShopSettingsModel::getInstance()->getSettingValue('maintenanceMessage');
                Flash::send(Alert::WARNING, 'Boutique', $maintenanceMessage);
                Redirect::redirectToHome();
            }
        }
        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        $sessionId = session_id();
        $cartContent = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $asideCartContent = ShopCartItemModel::getInstance()->getShopCartsItemsAsideByUserId($userId, $sessionId);
        $imagesItem = ShopImagesModel::getInstance();
        $defaultImage = ShopImagesModel::getInstance()->getDefaultImg();
        $itemsVariantes = ShopCartVariantesModel::getInstance();
        $appliedDiscounts = ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId);
        $showPublicStock = ShopSettingsModel::getInstance()->getSettingValue('showPublicStock');

        $this->handleSessionHealth($sessionId);

        $this->handleItemHealth($userId, $sessionId);
        $this->handleIncompatibleCartItems($userId, $sessionId);

        ShopDiscountModel::getInstance()->autoStatusChecker();

        if (!empty($appliedDiscounts)) {
            $cart = ShopCartModel::getInstance()->getShopCartsByUserOrSessionId($userId, $sessionId);
            $entityFound = 0;
            foreach ($appliedDiscounts as $appliedDiscount) {
                if ($appliedDiscount->getDiscount()->getStatus() == 0) {
                    ShopCartDiscountModel::getInstance()->removeCode($cart->getId(), $appliedDiscount->getDiscount()->getId());
                    $entityFound = 1;
                    foreach ($cartContent as $cartItem) {
                        ShopCartItemModel::getInstance()->removeCodeToItem($userId, $sessionId, $cartItem->getItem()->getId(), $appliedDiscount->getDiscount()->getId());
                    }
                }
            }
            if ($entityFound == 1) {
                Flash::send(Alert::INFO, 'Boutique', 'Certaines promotions ne sont plus disponible !');
                Redirect::redirect('shop/cart');
            }
        }

        foreach ($cartContent as $itemCart) {
            $itemId = $itemCart->getItem()->getId();
            $quantity = $itemCart->getQuantity();
            $this->handleStock($itemCart, $itemId, $quantity, $userId, $sessionId);
            $this->handleLimitePerUser($itemCart, $itemId, $quantity, $userId, $sessionId);
            $this->handleGlobalLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            $this->handleByOrderLimit($itemCart, $itemId, $quantity, $userId, $sessionId);
            $this->handleDraft($itemCart, $itemId, $userId, $sessionId);
        }

        View::createPublicView('Shop', 'Cart/cart')
            ->addVariableList(['showPublicStock' => $showPublicStock, 'cartContent' => $cartContent, 'imagesItem' => $imagesItem, 'defaultImage' => $defaultImage, 'asideCartContent' => $asideCartContent, 'itemsVariantes' => $itemsVariantes, 'appliedDiscounts' => $appliedDiscounts])
            ->addStyle('Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css')
            ->view();
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
    public function handleItemHealth(?int $userId, string $sessionId): void
    {
        if (ShopCartItemModel::getInstance()->cartItemIdAsNullValue($userId, $sessionId)) {
            ShopCartItemModel::getInstance()->removeUnreachableItem($userId, $sessionId);
            Flash::send(Alert::INFO, 'Boutique', "Certain article du panier n'existe plus. et nous ne somme malheureusement pas en mesure de le récupérer.");
        }
    }

    public function handleIncompatibleCartItems(?int $userId, string $sessionId): void
    {
        $cartItems = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $shopType = ShopSettingsModel::getInstance()->getShopTypeEnum();

        $removed = false;

        foreach ($cartItems as $itemCart) {
            $item = $itemCart->getItem();
            if (!$item) {
                continue;
            }

            $itemType = $item->getType();

            $isInvalid =
                ($shopType === ShopType::VIRTUAL_ONLY && $itemType === ShopItemType::PHYSICAL) ||
                ($shopType === ShopType::PHYSICAL_ONLY && $itemType === ShopItemType::VIRTUAL);

            if ($isInvalid) {
                ShopCartItemModel::getInstance()->removeItemFromCart($userId, $sessionId, $item->getId());
                $removed = true;
            }
        }

        if ($removed) {
            Flash::send(Alert::INFO, 'Boutique', "Certains articles ont été retirés du panier car ils ne sont plus compatibles avec la configuration actuelle de la boutique.");
            Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER').'shop/cart');
        }
    }


    public function handleStock(ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        $currentStock = ShopItemsModel::getInstance()->getItemCurrentStock($itemId);
        if ($quantity > $currentStock) {
            $quantity = $currentStock;
            if ($currentStock == 0) {
                ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                ShopCartItemModel::getInstance()->addToAsideCart($itemId, $userId, $sessionId);
                ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, 1);
                Flash::send(Alert::INFO, 'Boutique', "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est plus en stock, Nous l'avons mis dans votre panier 'Mise de côté'.");
                Redirect::redirect('shop/cart');
            } else {
                ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                Flash::send(Alert::INFO, 'Boutique', 'Les stock pour <b>' . $itemCart->getItem()->getName() . "</b> on changé. Il n'en reste que $quantity en stock, Nous avons mis automatiquement à jour votre panier");
                Redirect::redirect('shop/cart');
            }
        }
    }

    public function handleDraft(ShopCartItemEntity $itemCart, int $itemId, ?int $userId, string $sessionId): void
    {
        $item = ShopItemsModel::getInstance()->getShopItemsById($itemId);
        if ($item->isDraft() && !UsersController::isAdminLogged()) {
            ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
            Flash::send(Alert::INFO, 'Boutique', "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est plus disponible pour l'instant.");
            Redirect::redirect('shop/cart');
        }
    }

    public function handleLimitePerUser(ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        if (ShopItemsModel::getInstance()->itemHaveUserLimit($itemId)) {
            if (is_null($userId)) {
                Flash::send(Alert::INFO, 'Boutique', $itemCart->getItem()->getName() . " à besoin d'une vérification supplémentaire.");
                Redirect::redirect('login');
            }
            $numberBoughtByUser = ShopHistoryOrdersModel::getInstance()->countOrderByUserIdAndItemId($userId, $itemId);
            if ($numberBoughtByUser) {
                if ($quantity + $numberBoughtByUser > ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                    $quantity = ShopItemsModel::getInstance()->getItemUserLimit($itemId) - $numberBoughtByUser;
                    if ($quantity <= 0) {
                        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                        Flash::send(Alert::INFO, 'Boutique', "Vous n'êtes plus en mesure d'acheter <b>" . $itemCart->getItem()->getName() . '</b>.');
                        Redirect::redirect('shop/cart');
                    } else {
                        ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                        Flash::send(Alert::INFO, 'Boutique', 'Vous ne pouvez pas acheter autant de ' . $itemCart->getItem()->getName() . '. Nous avons mis à jour votre panier');
                        Redirect::redirect('shop/cart');
                    }
                }
            } else {
                if ($quantity > ShopItemsModel::getInstance()->getItemUserLimit($itemId)) {
                    $quantity = ShopItemsModel::getInstance()->getItemUserLimit($itemId);
                    if ($quantity <= 0) {
                        ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                        Flash::send(Alert::INFO, 'Boutique', "Vous n'êtes plus en mesure d'acheter <b>" . $itemCart->getItem()->getName() . '</b>.');
                        Redirect::redirect('shop/cart');
                    } else {
                        ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                        Flash::send(Alert::INFO, 'Boutique', 'Vous ne pouvez pas acheter autant de ' . $itemCart->getItem()->getName() . '. Nous avons mis à jour votre panier');
                        Redirect::redirect('shop/cart');
                    }
                }
            }
        }
    }

    public function handleGlobalLimit(ShopCartItemEntity $itemCart, int $itemId, int $quantity, ?int $userId, string $sessionId): void
    {
        if (ShopItemsModel::getInstance()->itemHaveGlobalLimit($itemId)) {
            $itemGlobalLimit = ShopItemsModel::getInstance()->getItemGlobalLimit($itemId);
            if ($quantity > $itemGlobalLimit) {
                $quantity = $itemGlobalLimit;
                if ($itemGlobalLimit == 0) {
                    ShopCartItemModel::getInstance()->removeItem($itemId, $userId, $sessionId);
                    Flash::send(Alert::INFO, 'Boutique', "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est malheuresement plus à vendre");
                    Redirect::redirect('shop/cart');
                } else {
                    ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                    Flash::send(Alert::INFO, 'Boutique', 'Les stock pour <b>' . $itemCart->getItem()->getName() . '</b> on changé.');
                    Redirect::redirect('shop/cart');
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
                    Flash::send(Alert::INFO, 'Boutique', "L'article <b>" . $itemCart->getItem()->getName() . "</b> n'est malheuresement plus à vendre");
                    Redirect::redirect('shop/cart');
                } else {
                    ShopCartItemModel::getInstance()->updateQuantity($userId, $sessionId, $itemId, $quantity);
                    Flash::send(Alert::INFO, 'Boutique', 'Les stock pour <b>' . $itemCart->getItem()->getName() . '</b> on changé.');
                    Redirect::redirect('shop/cart');
                }
            }
        }
    }
}
