<?php

namespace CMW\Implementation\Shop;


use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopVirtualItemGiftCodeImplementations implements IVirtualItems
{
    public function name(): string
    {
        return "Carte cadeau";
    }

    public function varName(): string
    {
        return "gift_code";
    }

    public function documentationURL(): ?string
    {
        return "";
    }

    public function description(): string
    {
        return "Créer un code promotionnel unique du montant du prix de l'article";
    }

    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Virtual/giftCode.config.inc.view.php";
    }

    public function execOnBuy(UserEntity $user): void
    {
        // TODO: Implement execOnBuy() method.
    }

}