<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopAddCatEvent extends AbstractEvent
{
    public function getName(): string
    {
        return "Shop-Add-Cat-Event-CraftMyWebsite";
    }
}