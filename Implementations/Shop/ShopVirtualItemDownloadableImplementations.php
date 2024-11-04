<?php

namespace CMW\Implementation\Shop\Shop;

use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsDownloadableController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopVirtualItemDownloadableImplementations implements IVirtualItems
{
    public function name(): string
    {
        return 'Téléchargeable';
    }

    public function varName(): string
    {
        return 'downloadable';
    }

    public function documentationURL(): ?string
    {
        return null;
    }

    public function description(): ?string
    {
        return 'Permet à vos utilisateurs de télécharger tout type de documents, pour augmenter la taille de transfert des fichiers merci de modifier votre php.ini';
    }

    public function includeItemConfigWidgets(?int $itemId): void
    {
        $varName = $this->varName();
        require_once EnvManager::getInstance()->getValue('DIR') . 'App/Package/Shop/Views/Elements/Virtual/Item/downloadable.config.inc.view.php';
    }

    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        ShopVirtualItemsDownloadableController::getInstance()->sedMailWithDownloadLink($varName, $item, $user);
    }

    public function execOnCancel(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        return;
    }
}
