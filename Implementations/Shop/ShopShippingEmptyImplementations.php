<?php

namespace CMW\Implementation\Shop\Shop;


use CMW\Controller\Shop\Admin\Item\Virtual\ShopVirtualItemsDownloadableController;
use CMW\Entity\Shop\Items\ShopItemEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Shop\IShippingMethod;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Env\EnvManager;

class ShopShippingEmptyImplementations implements IShippingMethod
{
    public function name(): string
    {
        return "Aucun";
    }

    public function varName(): string
    {
        return "nothing";
    }

    public function execAfterCommandValidatedByAdmin(string $varName, ShopItemEntity $item, UserEntity $user): void
    {
        // TODO: Implement execAfterCommandValidatedByAdmin() method.
    }


}