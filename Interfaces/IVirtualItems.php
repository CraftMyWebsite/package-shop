<?php

namespace CMW\Interface\Shop;

use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;

interface IVirtualItems
{
    /**
     * @return string
     * @desc The name showed in shop add items
     * @example "Downloadable"
     */
    public function name(): string;

    /**
     * @return string
     * @desc The variable name defined automatically
     */
    public function varName(): string;

    /**
     * @return ?string
     * @desc The quick start documentation
     * @example "https://craftmywebsite.fr/docs/shop/minecraft"
     */
    public function documentationURL(): ?string;

    /**
     * @return string
     * @desc Small description
     */
    public function description(): ?string;

    /**
     * @return void
     * @desc Include the config widgets for the shop add items
     * @example require_once EnvManager::getInstance()->getValue("DIR") . "App/Package/Shop/Views/Elements/Virtual/downloadable.config.inc.view.php";
     */
    public function includeConfigWidgets(?int $itemId): void;

    /**
     * @param string $varName
     * @param ShopItemEntity $item
     * @param UserEntity $user
     * @return void
     * @desc Do exec on buy items
     */
    public function execOnBuy(string $varName, ShopItemEntity $item, UserEntity $user): void;
}
