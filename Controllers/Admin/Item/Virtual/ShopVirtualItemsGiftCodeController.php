<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Controller\Core\MailController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;
use CMW\Utils\Website;


/**
 * Class: @ShopVirtualItemsGiftCodeController
 * @package Shop
 * @author Zomblard
 * @version 1.0
 */
class ShopVirtualItemsGiftCodeController extends AbstractController
{
    /**
     * @param ShopItemEntity $item
     * @param UserEntity $user
     */
    public function sedMailWithGiftCode(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        //TODO : Uniquement à des fin de test :
        $object = Website::getWebsiteName()." - Carte cadeau";
        $body = "Voici votre code à usage unique valable 1 ans sur la boutique de ".Website::getWebsiteName();
        MailController::getInstance()->sendMail($user->getMail(), $object, $body);
    }
}