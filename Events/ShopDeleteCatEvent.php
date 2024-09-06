<?php
namespace CMW\Event\Shop;

use CMW\Manager\Events\AbstractEvent;

class ShopDeleteCatEvent extends AbstractEvent
{
    public function getName(): string
    {
        return 'Shop-Delete-Cat-Event-CraftMyWebsite';
    }
}
