<?php

namespace CMW\Controller\Shop\Admin\Item\Virtual;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Shop\Item\ShopItemsVirtualRequirementModel;

/**
 * Class: @ShopVirtualItemsDownloadableController
 * @package Shop
 * @author Zomblard
 * @version 1.0
 */
class ShopVirtualItemsDownloadableController extends AbstractController
{
    /**
     * @param string $varName
     * @param ShopItemEntity $item
     * @param UserEntity $user
     */
    public function sedMailWithDownloadLink(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        // TODO : Uniquement à des fin de test :
        $object = ShopItemsVirtualRequirementModel::getInstance()->getSetting($varName . 'object', $item->getId());
        $body = ShopItemsVirtualRequirementModel::getInstance()->getSetting($varName . 'text', $item->getId());
        MailManager::getInstance()->sendMail($user->getMail(), $object, $body);
    }
}
