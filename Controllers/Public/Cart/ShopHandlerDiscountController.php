<?php
namespace CMW\Controller\Shop\Public\Cart;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Cart\ShopCartDiscountModel;
use CMW\Model\Shop\Cart\ShopCartItemModel;
use CMW\Model\Shop\Discount\ShopDiscountCategoriesModel;
use CMW\Model\Shop\Discount\ShopDiscountItemsModel;
use CMW\Model\Shop\Discount\ShopDiscountModel;
use CMW\Model\Shop\Order\ShopOrdersItemsModel;
use CMW\Model\Shop\Order\ShopOrdersModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ShopHandlerDiscountController
 * @package shop
 * @author Zomb
 * @version 1.0
 */
class ShopHandlerDiscountController extends AbstractController
{
    #[NoReturn] public function discountMasterHandler(?int $userId, string $sessionId, string $code) :void
    {
        $this->healthCode($code);

        $itemInCart = ShopCartItemModel::getInstance()->getShopCartsItemsByUserId($userId, $sessionId);
        $codeExist = ShopDiscountModel::getInstance()->codeExist($code);

        if ($codeExist) {
            $discountCode = ShopDiscountModel::getInstance()->getShopDiscountsByCode($code);
            $this->handleBasicVerification($userId, $sessionId, $code, $discountCode);
            $this->handleLinkedDiscount($userId, $sessionId, $discountCode, $itemInCart);
            $this->handleNonLinkedDiscount($userId, $sessionId, $discountCode, $itemInCart);
        } else {
            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas valable");
            Redirect::redirectPreviousRoute();
        }
    }

    private function healthCode($code) :void
    {
        if ($code == "") {
            Flash::send(Alert::ERROR, "Boutique", "Vous n'avez pas entré de code");
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleBasicVerification($userId, $sessionId, $code, $discountCode) :void
    {
        $this->handleAlreadyApplied($userId, $sessionId, $code);
        $this->handleStatus($discountCode);
        $this->handleDate($discountCode);
        $this->handleTestMode($discountCode);
        $this->handleUsesLeft($discountCode);
        $orders = ShopOrdersModel::getInstance()->getOrdersByUserId($userId);
        $this->handleLimitByUser($discountCode, $orders, $code);
        $this->handleOrderBeforeUse($discountCode, $orders);
    }

    private function handleAlreadyApplied($userId, $sessionId, $code) :void
    {
        foreach (ShopCartDiscountModel::getInstance()->getCartDiscountByUserId($userId, $sessionId) as $discount) {
            if ($discount->getDiscount()->getCode() == $code) {
                Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà appliqué ce code de réduction !");
                Redirect::redirectPreviousRoute();
            }
        }
    }

    private function handleDate($discountCode) :void
    {
        if (!$this->checkDate($discountCode->getStartDate(), $discountCode->getEndDate())) {
            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est plus actif ou ne l'est pas encore (time)");
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleStatus($discountCode) :void
    {
        if ($discountCode->getStatus() != 1) {
            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est plus actif ou ne l'est pas encore (status)");
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleTestMode($discountCode) :void
    {
        if ($discountCode->getTestMode() == 1) {
            if (!UsersController::isAdminLogged()) {
                Flash::send(Alert::ERROR, "Boutique", "Ce code est en mode test, vous devez être administrateur pour pouvoir l'utiliser");
                Redirect::redirectPreviousRoute();
            }
        }
    }

    private function handleUsesLeft($discountCode) :void
    {
        if ($discountCode->getUsesLeft() !== null && $discountCode->getUsesLeft() <= 0) {
            Flash::send(Alert::ERROR, "Boutique", "Ce code n'est plus utilisable");
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleLimitByUser($discountCode, $orders, $code) :void
    {
        if ($discountCode->getUsesMultipleByUser() == 1) {
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
    }

    private function handleOrderBeforeUse($discountCode, $orders) :void
    {
        if ($discountCode->getUserHaveOrderBeforeUse() == 1) {
            if (empty($orders)) {
                Flash::send(Alert::ERROR, "Boutique", "Vous devez avoir passé au moins une commande avant de pour pouvoir utiliser ce code");
                Redirect::redirectPreviousRoute();
            }
        }
    }

    private function handleLinkedDiscount($userId, $sessionId, $discountCode, $itemInCart) : void
    {
        if ($discountCode->getLinked() != 0) {
            $entityFound = 0;
            foreach ($itemInCart as $cartItem) {
                $linkedDiscount = null;
                if ($discountCode->getLinked() == 1) {
                    $linkedDiscount =  ShopDiscountItemsModel::getInstance()->getShopDiscountItemsByItemId($cartItem->getItem()->getId());
                }
                if ($discountCode->getLinked() == 2) {
                    $linkedDiscount =  ShopDiscountCategoriesModel::getInstance()->getShopDiscountCategoriesByCategoryId($cartItem->getItem()->getCategory()->getId());
                }
                if (is_null($cartItem->getDiscount()?->getCode())) {
                    if ($cartItem->getItem()->getPriceDiscountDefaultApplied()) {
                        Flash::send(Alert::ERROR, "Boutique", "Il y as deja un code de réduction par défaut pour " . $cartItem->getItem()->getName());
                    } else {
                        if ($discountCode->getPrice()) {
                            if ($cartItem->getItem()->getPrice() - $discountCode->getPrice() <= 0) {
                                Flash::send(Alert::ERROR, "Boutique", "Ce code va impacter négativement le prix de " . $cartItem->getItem()->getName());
                            } else {
                                if (!empty($linkedDiscount)) {
                                    foreach ($linkedDiscount as $_) {
                                        Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getName());
                                        ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                        $entityFound = 1;
                                    }
                                }
                            }
                        } elseif ($discountCode->getPercentage()) {
                            if ($cartItem->getItem()->getPrice() == 0) {
                                Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas applicable à des articles gratuit");
                            } else {
                                if (!empty($linkedDiscount)) {
                                    foreach ($linkedDiscount as $_) {
                                        Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getName());
                                        ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                        $entityFound = 1;
                                    }
                                }
                            }
                        } else {
                            Flash::send(Alert::ERROR, "Boutique", "Ce code est fonctionnel mais n'applique aucune reduction");
                        }
                    }
                } else {
                    Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà appliqué un code à " . $cartItem->getItem()->getName());
                }
            }
            if ($entityFound === 1) {
                ShopCartDiscountModel::getInstance()->applyCode($userId, $sessionId, $discountCode->getId());
                Flash::send(Alert::SUCCESS, "Boutique", "Code promotionnel appliqué avec succès !");
            }
            Redirect::redirectPreviousRoute();
        }
    }

    private function handleNonLinkedDiscount($userId, $sessionId, $discountCode, $itemInCart) : void
    {
        if ($discountCode->getLinked() == 0) {
            $entityFound = 0;
            foreach ($itemInCart as $cartItem) {
                if (is_null($cartItem->getDiscount()?->getCode())) {
                    if ($cartItem->getItem()->getPriceDiscountDefaultApplied()) {
                        Flash::send(Alert::ERROR, "Boutique", "Il y as deja un code de réduction par défaut pour " . $cartItem->getItem()->getName());
                    } else {

                        if ($discountCode->getPrice()) {
                            if ($cartItem->getItem()->getPrice() - $discountCode->getPrice() <= 0) {
                                Flash::send(Alert::ERROR, "Boutique", "Ce code va impacter négativement le prix de " . $cartItem->getItem()->getName());
                            } else {
                                Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getName());
                                ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                $entityFound = 1;
                            }
                        } elseif ($discountCode->getPercentage()) {
                            if ($cartItem->getItem()->getPrice() == 0) {
                                Flash::send(Alert::ERROR, "Boutique", "Ce code n'est pas applicable à des articles gratuit");
                            } else {
                                Flash::send(Alert::SUCCESS, "Boutique", "Ce code est lié à " . $cartItem->getItem()->getName());
                                ShopCartItemModel::getInstance()->applyCodeToItem($userId, $sessionId,$cartItem->getItem()->getId(), $discountCode->getId());
                                $entityFound = 1;
                            }
                        } else {
                            Flash::send(Alert::ERROR, "Boutique", "Ce code est fonctionnel mais n'applique aucune reduction");
                        }

                    }
                } else {
                    Flash::send(Alert::ERROR, "Boutique", "Vous avez déjà appliqué un code à " . $cartItem->getItem()->getName());
                }

            }
            if ($entityFound === 1) {
                ShopCartDiscountModel::getInstance()->applyCode($userId, $sessionId, $discountCode->getId());
                Flash::send(Alert::SUCCESS, "Boutique", "Code promotionnel appliqué avec succès !");
            }
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
}