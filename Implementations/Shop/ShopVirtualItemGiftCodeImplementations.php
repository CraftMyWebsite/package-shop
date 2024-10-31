<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsGiftCodeController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopVirtualItemGiftCodeImplementations implements IVirtualItems
{
    public function name(): string
    {
        return 'Carte cadeau';
    }

    public function varName(): string
    {
        return 'gift_code';
    }

    public function documentationURL(): ?string
    {
        return '';
    }

    public function description(): ?string
    {
        return "<div class='alert-warning'><b>Attention !</b> les cartes cadeaux ne sont que compatible avec les articles qui ont une monnaie réelle !<br>Si vous créez une carte cadeau avec un autre monnaie ceci va créer une carte cadeau en monnaie réel</div><br>1 - Ceci va créer un code promotionnel de la valeur de cet article<br>2 - Ce code est valable 1 an<br>3 - Le code promotionnel est généré automatique par CraftMyWebsite<br>4 - L'acheteur reçoit le code par mail, et peut le retrouver dans la liste des commandes<br>5 - Il est ensuite libre de donner ce code à qui bon lui semble<br>";
    }

    public function includeItemConfigWidgets(?int $itemId): void
    {
        return;
    }

    public function includeGlobalConfigWidgets(): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Virtual/Global/giftCode.config.inc.view.php';
    }

    public function useGlobalConfigWidgetsInShopConfig(): bool
    {
        return true;
    }

    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        ShopVirtualItemsGiftCodeController::getInstance()->sedMailWithGiftCode($varName, $item, $user);
    }

    public function execOnCancel(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }
}
