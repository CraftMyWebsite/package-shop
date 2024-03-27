<?php

namespace CMW\Implementation\Shop;


use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopVirtualItemDownloadableImplementations implements IVirtualItems
{
    public function name(): string
    {
        return "Téléchargeable";
    }

    public function varName(): string
    {
        return "downloadable";
    }

    public function documentationURL(): ?string
    {
        return "";
    }

    public function description(): string
    {
        return "Permet à vos utilisateurs de télécharger tout type de documents, pour augmenter la taille de transfert des fichiers merci de modifier votre php.ini";
    }

    public function includeConfigWidgets(): void
    {
        require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Virtual/downloadable.config.inc.view.php";
    }

    public function execOnBuy(UserEntity $user): void
    {
        // TODO: Implement execOnBuy() method.
    }

}